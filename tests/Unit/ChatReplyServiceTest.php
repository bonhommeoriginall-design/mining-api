<?php

namespace Tests\Unit;

use App\Services\ChatReplyService;
use App\Services\DocumentSearchService;
use App\Services\LlmChatService;
use PHPUnit\Framework\TestCase;
use ReflectionClassConstant;

class ChatReplyServiceTest extends TestCase
{
    public function test_prompt_uses_expert_format_and_utf8_french(): void
    {
        $path = dirname(__DIR__, 2).'/app/Services/LlmChatService.php';
        $raw = file_get_contents($path);
        $this->assertIsString($raw);
        $this->assertTrue(mb_check_encoding($raw, 'UTF-8'));
        $this->assertStringNotContainsString('rǸglementation', $raw);
        $this->assertStringNotContainsString('Ã©', $raw);

        $prompt = (new ReflectionClassConstant(LlmChatService::class, 'SYSTEM_PROMPT'))->getValue();
        $this->assertIsString($prompt);
        $this->assertStringContainsString('réglementation', $prompt);
        $this->assertStringContainsString('Base légale', $prompt);
        $this->assertStringContainsString('Limites', $prompt);
        $this->assertStringContainsString('2 à 4 phrases', $prompt);
        $this->assertStringContainsString('pas de Markdown', $prompt);
        $this->assertStringContainsString('ne dresse pas d\'inventaire', mb_strtolower($prompt));
    }

    public function test_off_topic_question_does_not_dump_excerpts(): void
    {
        $search = $this->createMock(DocumentSearchService::class);
        $search->expects($this->never())->method('search');
        $llm = $this->createMock(LlmChatService::class);
        $llm->expects($this->never())->method('generateReply');

        $result = $this->service($search, $llm)->reply('Quelle est la recette de la tarte aux pommes ?');

        $this->assertFalse($result['used_llm']);
        $this->assertSame([], $result['citations']);
        $this->assertStringContainsString('ne figure pas', $result['reply']);
        $this->assertStringContainsString('obligations des titulaires', $result['reply']);
        $this->assertStringContainsString('coopératives minières', $result['reply']);
        $this->assertStringContainsString('exploration', $result['reply']);
        $this->assertStringNotContainsString('Voici les passages', $result['reply']);
    }

    public function test_weak_retrieval_does_not_list_random_excerpts(): void
    {
        $excerpt = 'Extrait aléatoire sur un article sans lien avec la question posée.';
        $search = $this->createMock(DocumentSearchService::class);
        $search->method('search')->willReturn([
            $this->hit($excerpt, 0.22),
            $this->hit('Deuxième extrait tout aussi éloigné du sujet demandé.', 0.18),
        ]);
        $llm = $this->createMock(LlmChatService::class);
        $llm->expects($this->never())->method('generateReply');

        $result = $this->service($search, $llm)->reply('Quelle est la capitale de la France ?');

        $this->assertSame([], $result['citations']);
        $this->assertStringNotContainsString($excerpt, $result['reply']);
        $this->assertStringContainsString('1. Quelles sont les obligations', $result['reply']);
    }

    public function test_grounded_reply_keeps_at_most_three_citation_objects(): void
    {
        $search = $this->createMock(DocumentSearchService::class);
        $search->method('search')->willReturn([
            $this->hit('Le titulaire doit déposer un plan.', 1.4, 'Code minier'),
            $this->hit('La coopérative tient un registre.', 1.1, 'Règlement'),
            $this->hit('L’exploration est autorisée par permis.', 0.9, 'Procédure'),
            $this->hit('Extrait faible à ne pas empiler.', 0.7, 'Annexe'),
        ]);

        $result = $this->service($search, $this->llm(false))->reply(
            'Quelles sont les obligations des titulaires de permis miniers ?',
        );

        $this->assertFalse($result['used_llm']);
        $this->assertCount(3, $result['citations']);
        $this->assertSame(
            ['title', 'filename', 'excerpt', 'score'],
            array_keys($result['citations'][0]),
        );
        $this->assertEquals(140, $result['citations'][0]['score']);
        $this->assertStringContainsString('Le titulaire doit déposer un plan.', $result['reply']);
        $this->assertStringNotContainsString('Extrait faible à ne pas empiler.', $result['reply']);
    }

    public function test_llm_reply_keeps_citation_shape(): void
    {
        $search = $this->createMock(DocumentSearchService::class);
        $search->method('search')->willReturn([
            $this->hit('Article 12 : le titulaire dépose une étude.', 1.5),
        ]);
        $llm = $this->llm(true);
        $llm->expects($this->once())
            ->method('generateReply')
            ->willReturn("Le titulaire dépose une étude.\n\nBase légale :\n1. Code minier, article 12.");

        $result = $this->service($search, $llm)->reply('Quelles sont les obligations des titulaires ?');

        $this->assertTrue($result['used_llm']);
        $this->assertStringContainsString('Base légale', $result['reply']);
        $this->assertSame(['title', 'filename', 'excerpt', 'score'], array_keys($result['citations'][0]));
        $this->assertSame('code-minier.pdf', $result['citations'][0]['filename']);
    }

    public function test_accidental_match_without_mining_vocabulary_is_not_dumped(): void
    {
        $excerpt = 'Le mot France apparaît une fois dans une annexe administrative.';
        $search = $this->createMock(DocumentSearchService::class);
        $search->method('search')->willReturn([
            $this->hit($excerpt, 1.1),
        ]);

        $result = $this->service($search, $this->llm(false))->reply('Quelle est la capitale de la France ?');

        $this->assertSame([], $result['citations']);
        $this->assertStringNotContainsString($excerpt, $result['reply']);
        $this->assertStringContainsString('ne figure pas', $result['reply']);
    }

    public function test_conversational_probe_suggests_questions_without_citations(): void
    {
        $search = $this->createMock(DocumentSearchService::class);
        $search->expects($this->never())->method('search');

        $result = $this->service($search, $this->llm(false))->reply('Bonjour');

        $this->assertSame([], $result['citations']);
        $this->assertStringContainsString('coopératives minières', $result['reply']);
    }

    public function test_greeting_with_a_real_question_still_searches(): void
    {
        $search = $this->createMock(DocumentSearchService::class);
        $search->expects($this->once())->method('search')->willReturn([
            $this->hit('Obligation de réhabilitation du site.', 1.6),
        ]);

        $result = $this->service($search, $this->llm(false))->reply(
            'Bonjour, quelles sont les obligations des titulaires ?',
        );

        $this->assertNotSame([], $result['citations']);
        $this->assertStringContainsString('réhabilitation', $result['reply']);
    }

    private function service(DocumentSearchService $search, LlmChatService $llm): ChatReplyService
    {
        return new ChatReplyService($search, $llm);
    }

    private function llm(bool $configured): LlmChatService
    {
        $llm = $this->createMock(LlmChatService::class);
        $llm->method('isConfigured')->willReturn($configured);

        return $llm;
    }

    /**
     * @return array{document_id: int, title: string, filename: string, excerpt: string, score: float}
     */
    private function hit(string $excerpt, float $score, string $title = 'Code minier'): array
    {
        return [
            'document_id' => 1,
            'title' => $title,
            'filename' => 'code-minier.pdf',
            'excerpt' => $excerpt,
            'score' => $score,
        ];
    }
}
