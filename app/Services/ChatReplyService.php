<?php

namespace App\Services;

class ChatReplyService
{
    public function __construct(
        private readonly DocumentSearchService $search,
        private readonly LlmChatService $llm,
    ) {}

    /**
     * @param  list<array{role: string, content: string}>  $history
     * @return array{reply: string, citations: list<array{title: string, filename: string, excerpt: string, score: float}>, used_llm: bool}
     */
    public function reply(string $query, array $history = []): array
    {
        $trimmed = trim($query);
        if ($trimmed === '') {
            return [
                'reply' => 'Posez une question sur les documents publiés (réglementation minière, procédures, etc.).',
                'citations' => [],
                'used_llm' => false,
            ];
        }

        if ($this->isConversational($trimmed)) {
            return [
                'reply' => $this->conversationalReply($trimmed),
                'citations' => [],
                'used_llm' => false,
            ];
        }

        $hits = $this->search->search($trimmed, 5);
        $context = $this->buildContext($hits);

        if ($this->llm->isConfigured()) {
            try {
                $reply = $this->llm->generateReply($trimmed, $context, $history);

                return [
                    'reply' => $reply,
                    'citations' => $this->mapCitations($hits),
                    'used_llm' => true,
                ];
            } catch (\Throwable $e) {
                return [
                    'reply' => $this->fallbackReply($hits, $e->getMessage()),
                    'citations' => $this->mapCitations($hits),
                    'used_llm' => false,
                ];
            }
        }

        return [
            'reply' => $this->fallbackReply($hits),
            'citations' => $this->mapCitations($hits),
            'used_llm' => false,
        ];
    }

    private function isConversational(string $query): bool
    {
        $q = mb_strtolower($query);

        return (bool) preg_match(
            '/^(bonjour|bonsoir|salut|merci|ok|d\'?accord|aide|help|qui es-tu)\b/u',
            $q,
        ) && mb_strlen($query) < 60;
    }

    private function conversationalReply(string $query): string
    {
        $q = mb_strtolower($query);
        if (preg_match('/^(merci|ok|d\'?accord)\b/u', $q)) {
            return 'Avec plaisir. Posez une question précise sur les documents publiés et je vous indiquerai les passages pertinents.';
        }
        if (preg_match('/^(aide|help|qui es-tu)\b/u', $q)) {
            return "Je suis MINING IA, assistant sur les documents réglementaires publiés.\n\n"
                ."Posez une question en langage naturel (ex. obligations environnementales, coopératives minières, procédures). "
                .'Je recherche les passages utiles dans les textes officiels indexés.';
        }

        return "Bonjour ! Je suis MINING IA.\n\n"
            .'Posez une question sur le Code minier, le règlement minier ou tout autre document publié.';
    }

    /**
     * @param  list<array{document_id: int, title: string, filename: string, excerpt: string, score: float}>  $hits
     */
    private function buildContext(array $hits): string
    {
        if ($hits === []) {
            return '';
        }

        $parts = [];
        foreach ($hits as $i => $hit) {
            $n = $i + 1;
            $parts[] = "[{$n}] {$hit['filename']} — {$hit['title']}\n{$hit['excerpt']}";
        }

        return implode("\n\n", $parts);
    }

    /**
     * @param  list<array{document_id: int, title: string, filename: string, excerpt: string, score: float}>  $hits
     * @return list<array{title: string, filename: string, excerpt: string, score: float}>
     */
    private function mapCitations(array $hits): array
    {
        return array_map(static fn (array $h) => [
            'title' => $h['title'],
            'filename' => $h['filename'],
            'excerpt' => $h['excerpt'],
            'score' => round($h['score'] * 100),
        ], $hits);
    }

    /**
     * @param  list<array{document_id: int, title: string, filename: string, excerpt: string, score: float}>  $hits
     */
    private function fallbackReply(array $hits, ?string $llmError = null): string
    {
        if ($hits === []) {
            $prefix = $llmError ? "Réponse intelligente indisponible ({$llmError}).\n\n" : '';

            return $prefix
                ."Je n'ai trouvé aucun passage pertinent dans les documents publiés.\n"
                .'Reformulez votre question avec des mots-clés plus précis, ou vérifiez qu’un document est bien publié.';
        }

        $lines = [];
        if ($llmError) {
            $lines[] = "Réponse intelligente indisponible ({$llmError}). Voici les extraits trouvés :\n";
        } else {
            $lines[] = "Voici les passages les plus pertinents dans les documents publiés :\n";
            $lines[] = '';
        }

        foreach ($hits as $i => $hit) {
            $n = $i + 1;
            $pct = (int) round($hit['score'] * 100);
            $lines[] = "{$n}. {$hit['title']} ({$hit['filename']}) — pertinence ~{$pct} %";
            $lines[] = '   « '.$hit['excerpt'].' »';
            $lines[] = '';
        }

        return trim(implode("\n", $lines));
    }
}
