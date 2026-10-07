<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LlmChatService
{
    private const SYSTEM_PROMPT = <<<'PROMPT'
Tu es MINING IA, assistant expert en réglementation minière et documents techniques, en français.

Règles générales :
- Base-toi uniquement sur les extraits fournis et l'historique de la conversation.
- Les extraits peuvent contenir des espaces manquants (mots collés) : reformule-les en français correct et lisible.
- Texte brut uniquement : n'utilise pas de Markdown (pas de **, pas de #, pas de backticks, pas de puces *).
- Ne recopie pas les extraits bruts : synthétise et explique avec tes propres mots.
- N'invente pas de faits, chiffres, articles ou pages absents des extraits.
- N'empile pas une liste d'extraits. Cite au plus les 1 à 3 sources les plus solides.

Si la question est hors sujet, si c'est une simple formule de politesse, ou si les extraits ne permettent pas de répondre :
- Ne dresse pas d'inventaire d'extraits.
- Dis en une phrase courte que ce point ne figure pas dans les documents publiés.
- Propose ensuite deux ou trois questions sur la réglementation minière, par exemple les obligations des titulaires de permis, les coopératives minières, ou les procédures d'exploration.

Quand les extraits permettent de répondre, suis exactement cette structure en texte brut :
1. Une réponse directe de 2 à 4 phrases.
2. Des points clés numérotés (1. 2. 3.) pour les obligations ou les étapes, seulement si c'est utile. Sinon omets cette partie.
3. Une partie intitulée « Base légale » avec au plus 1 à 3 citations les plus solides, chacune sous la forme document, article, page lorsque ces éléments sont présents dans les extraits.
4. Une partie intitulée « Limites » seulement si la couverture des extraits est incomplète. Sinon omets cette partie.
5. Une seule question de clarification si la demande de l'utilisateur est vague. Sinon n'ajoute pas de question.
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
