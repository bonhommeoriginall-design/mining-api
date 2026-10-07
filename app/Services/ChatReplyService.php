<?php

namespace App\Services;

class ChatReplyService
{
    private const WEAK_SCORE = 0.45;

    private const MAX_CITATIONS = 3;

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

        if ($this->isClearlyOffTopic($trimmed)) {
            return $this->offTopicResult();
        }

        $hits = $this->usableHits($trimmed, $this->search->search($trimmed, 5));
        if ($hits === []) {
            return $this->offTopicResult();
        }

        $hits = array_slice($hits, 0, self::MAX_CITATIONS);
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

        if (! preg_match(
            '/^(bonjour|bonsoir|salut|hello|hey|coucou|merci|ok|d\'?accord|aide|help|qui es-tu|qui es tu|ça va|ca va|test)\b/u',
            $q,
        )) {
            return false;
        }

        if ($this->hasDomainSignal($query) && (str_contains($query, '?') || mb_strlen($query) >= 40)) {
            return false;
        }

        return mb_strlen($query) < 80;
    }

    private function conversationalReply(string $query): string
    {
        $q = mb_strtolower($query);
        if (preg_match('/^(merci|ok|d\'?accord)\b/u', $q)) {
            return "Avec plaisir. Posez une question précise sur les documents publiés.\n\n"
                .$this->suggestionBlock();
        }
        if (preg_match('/^(aide|help|qui es-tu|qui es tu)\b/u', $q)) {
            return "Je suis MINING IA, assistant sur les documents réglementaires publiés.\n\n"
                .$this->suggestionBlock();
        }

        return "Bonjour. Je suis MINING IA, assistant sur les documents miniers publiés.\n\n"
            .$this->suggestionBlock();
    }

    private function isClearlyOffTopic(string $query): bool
    {
        if ($this->hasDomainSignal($query)) {
            return false;
        }

        return (bool) preg_match(
            '/\b(m[ée]t[ée]o|recette|cuisine|cuisiner|football|basket|cin[ée]ma|blague|horoscope|pok[ée]mon|s[ée]rie t[ée]l[ée]|jeu vid[ée]o)\b/u',
            mb_strtolower($query),
        );
    }

    private function hasDomainSignal(string $query): bool
    {
        return preg_match(
            '/minier|mini[eè]re|\bmines?\b|permis|explor|exploit|coop[eé]rat|environnement|concession|carri[eè]re|redevance|cadastre|substance|diamant|cobalt|cuivre|orpaill|prospection|titulaire|r[eè]glement minier|code minier|obligation|proc[eé]dure/u',
            mb_strtolower($query),
        ) === 1;
    }

    /**
     * @param  list<array{document_id: int, title: string, filename: string, excerpt: string, score: float}>  $hits
     * @return list<array{document_id: int, title: string, filename: string, excerpt: string, score: float}>
     */
    private function usableHits(string $query, array $hits): array
    {
        if ($hits === []) {
            return [];
        }

        $top = (float) ($hits[0]['score'] ?? 0);
        if ($top < self::WEAK_SCORE) {
            return [];
        }

        // Un mot isolé, sans vocabulaire minier, ne suffit pas à justifier un extrait.
        if (! $this->hasDomainSignal($query) && $top < 1.25) {
            return [];
        }

        return array_values(array_filter(
            $hits,
            static fn (array $hit): bool => (float) ($hit['score'] ?? 0) >= self::WEAK_SCORE,
        ));
    }

    /**
     * @return array{reply: string, citations: list<empty>, used_llm: false}
     */
    private function offTopicResult(): array
    {
        return [
            'reply' => "Ce point ne figure pas dans les documents miniers publiés.\n\n"
                .$this->suggestionBlock(),
            'citations' => [],
            'used_llm' => false,
        ];
    }

    private function suggestionBlock(): string
    {
        return "Vous pouvez par exemple demander :\n"
            ."1. Quelles sont les obligations des titulaires de permis miniers ?\n"
            ."2. Que dit le texte sur les coopératives minières ?\n"
            ."3. Quelles procédures pour l'exploration ?";
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
            return $this->offTopicResult()['reply'];
        }

        $lines = [];
        if ($llmError) {
            $lines[] = "Réponse intelligente indisponible ({$llmError}). Voici les extraits les plus proches :\n";
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
