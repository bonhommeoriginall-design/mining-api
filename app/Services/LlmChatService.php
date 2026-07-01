<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LlmChatService
{
    private const SYSTEM_PROMPT = <<<'PROMPT'
Tu es MINING IA, assistant expert en réglementation minière et documents techniques, en français.

Règles :
- Base-toi uniquement sur les extraits fournis et l'historique de la conversation.
- Les extraits peuvent contenir des espaces manquants (mots collés) : reformule-les en français correct et lisible.
- Réponds de façon claire, structurée et professionnelle (résumé court, puis points clés numérotés si utile).
- Texte brut uniquement : n'utilise pas de Markdown (pas de **, pas de #, pas de backticks).
- Ne recopie pas les extraits bruts : synthétise et explique avec tes propres mots.
- Cite brièvement la source (nom du document) quand c'est utile.
- N'invente pas de faits, chiffres ou articles absents des extraits.
- Si l'information manque vraiment, précise ce que les documents couvrent à la place.
PROMPT;

    public function isConfigured(): bool
    {
        $config = config('mining.llm');

        return ($config['enabled'] ?? false) && filled($config['api_key'] ?? null);
    }

    /**
     * @param  list<array{role: string, content: string}>  $history
     */
    public function generateReply(string $userQuery, string $ragContext, array $history = []): string
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Clé API LLM non configurée sur le serveur.');
        }

        $config = config('mining.llm');
        $baseUrl = rtrim($config['base_url'], '/');
        $context = trim($ragContext);

        $userContent = $context === ''
            ? "Question : {$userQuery}\n\n(Aucun extrait documentaire disponible.)"
            : "Extraits des documents publiés :\n\n{$context}\n\n---\n\nQuestion : {$userQuery}";

        $messages = [
            ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
        ];

        $recent = array_slice($history, -6);
        foreach ($recent as $message) {
            $messages[] = [
                'role' => $message['role'],
                'content' => $message['content'],
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => mb_substr($userContent, 0, 12000),
        ];

        $request = Http::timeout(60)
            ->withToken($config['api_key'])
            ->acceptJson();

        if (app()->environment('local')) {
            $request = $request->withOptions(['verify' => false]);
        }

        $response = $request->post("{$baseUrl}/chat/completions", [
                'model' => $config['model'],
                'temperature' => 0.35,
                'max_tokens' => 1000,
                'messages' => $messages,
            ]);

        if (! $response->successful()) {
            $detail = $response->json('error.message') ?? $response->body();
            throw new \RuntimeException('Erreur API LLM : '.$detail);
        }

        $content = $response->json('choices.0.message.content');

        return is_string($content) ? trim($content) : 'Réponse vide du modèle.';
    }
}
