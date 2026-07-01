<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Cache;

class DocumentSearchService
{
    private const CHUNK_SIZE = 900;

    private const CHUNK_OVERLAP = 120;

    public function __construct(
        private readonly DocumentTextExtractor $extractor,
    ) {}

    /**
     * @return list<array{document_id: int, title: string, filename: string, excerpt: string, score: float}>
     */
    public function search(string $query, int $limit = 5): array
    {
        $terms = $this->queryTerms($query);
        if ($terms === []) {
            return [];
        }

        $hits = [];
        $documents = Document::query()
            ->where('status', Document::STATUS_PUBLISHED)
            ->orderBy('title')
            ->get();

        foreach ($documents as $document) {
            $chunks = $this->chunksForDocument($document);
            foreach ($chunks as $chunk) {
                $score = $this->scoreChunk($chunk, $terms);
                if ($score < 0.15) {
                    continue;
                }
                $hits[] = [
                    'document_id' => $document->id,
                    'title' => $document->title,
                    'filename' => $document->original_filename,
                    'excerpt' => $this->trimExcerpt($chunk),
                    'score' => $score,
                ];
            }
        }

        usort($hits, static fn (array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice($hits, 0, $limit);
    }

    /** @return list<string> */
    private function chunksForDocument(Document $document): array
    {
        $cacheKey = 'doc_chunks_'.$document->id.'_'.$document->checksum;

        return Cache::remember($cacheKey, now()->addDay(), function () use ($document) {
            $text = $this->extractor->extract($document);
            if ($text === '') {
                return [];
            }

            return $this->splitIntoChunks($text);
        });
    }

    /** @return list<string> */
    private function splitIntoChunks(string $text): array
    {
        $chunks = [];
        $length = mb_strlen($text);
        $start = 0;

        while ($start < $length) {
            $piece = mb_substr($text, $start, self::CHUNK_SIZE);
            $chunks[] = trim($piece);
            if ($start + self::CHUNK_SIZE >= $length) {
                break;
            }
            $start += self::CHUNK_SIZE - self::CHUNK_OVERLAP;
        }

        return array_values(array_filter($chunks, static fn (string $c) => mb_strlen($c) > 80));
    }

    /** @param list<string> $terms */
    private function scoreChunk(string $chunk, array $terms): float
    {
        $lower = mb_strtolower($chunk);
        $matched = 0;
        $weight = 0.0;

        foreach ($terms as $term) {
            $count = mb_substr_count($lower, $term);
            if ($count > 0) {
                $matched++;
                $weight += min(3, $count) * (mb_strlen($term) >= 5 ? 1.2 : 1.0);
            }
        }

        if ($matched === 0) {
            return 0.0;
        }

        return $weight / count($terms);
    }

    /** @return list<string> */
    private function queryTerms(string $query): array
    {
        $stopwords = [
            'le', 'la', 'les', 'un', 'une', 'des', 'du', 'de', 'et', 'ou', 'en', 'au', 'aux',
            'sur', 'par', 'pour', 'dans', 'avec', 'que', 'qui', 'quoi', 'comment', 'est', 'sont',
            'ce', 'cette', 'ces', 'mon', 'ma', 'mes', 'ton', 'ta', 'tes', 'son', 'sa', 'ses',
            'je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles', 'ne', 'pas', 'plus',
        ];

        $normalized = mb_strtolower(DocumentTextNormalizer::normalize($query));
        $parts = preg_split('/[^\p{L}\p{N}\']+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return collect($parts)
            ->filter(static fn (string $w) => mb_strlen($w) >= 3 && ! in_array($w, $stopwords, true))
            ->unique()
            ->values()
            ->all();
    }

    private function trimExcerpt(string $chunk): string
    {
        $excerpt = trim(preg_replace('/\s+/u', ' ', $chunk) ?? $chunk);
        if (mb_strlen($excerpt) <= 320) {
            return $excerpt;
        }

        return mb_substr($excerpt, 0, 317).'…';
    }

    public function forgetDocumentCache(Document $document): void
    {
        Cache::forget('doc_chunks_'.$document->id.'_'.$document->checksum);
    }
}
