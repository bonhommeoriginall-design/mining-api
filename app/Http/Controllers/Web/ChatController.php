<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ChatReplyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatReplyService $chat,
    ) {}

    public function index(): View
    {
        $history = session('chat_history', []);
        $data = [
            'messages' => $history,
            'initialMessages' => $history,
            'llmConfigured' => app(\App\Services\LlmChatService::class)->isConfigured(),
        ];

        if (auth()->user()?->isUtilisateur()) {
            return view('chat.agent', $data);
        }

        return view('chat.index', $data);
    }

    public function ask(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $history = session('chat_history', []);
        $priorForLlm = collect($history)
            ->map(static fn (array $m) => [
                'role' => $m['is_user'] ? 'user' : 'assistant',
                'content' => $m['text'],
            ])
            ->all();

        $result = $this->chat->reply($data['message'], $priorForLlm);

        $history[] = [
            'id' => uniqid('u_', true),
            'text' => $data['message'],
            'is_user' => true,
            'citations' => [],
            'at' => now()->toIso8601String(),
        ];
        $history[] = [
            'id' => uniqid('a_', true),
            'text' => $result['reply'],
            'is_user' => false,
            'citations' => $result['citations'],
            'used_llm' => $result['used_llm'],
            'at' => now()->toIso8601String(),
        ];

        if (count($history) > 40) {
            $history = array_slice($history, -40);
        }

        session(['chat_history' => $history]);

        return response()->json([
            'reply' => $result['reply'],
            'citations' => $result['citations'],
            'used_llm' => $result['used_llm'],
        ]);
    }

    public function clear(): JsonResponse
    {
        session()->forget(['chat_history', 'chat_active_conversation_id']);

        return response()->json(['ok' => true]);
    }

    public function restore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'conversation_id' => ['nullable', 'string', 'max:80'],
            'messages' => ['required', 'array', 'max:40'],
            'messages.*.id' => ['required', 'string', 'max:80'],
            'messages.*.text' => ['required', 'string', 'max:5000'],
            'messages.*.is_user' => ['required', 'boolean'],
            'messages.*.citations' => ['nullable', 'array'],
            'messages.*.used_llm' => ['nullable', 'boolean'],
            'messages.*.at' => ['nullable', 'string', 'max:40'],
        ]);

        $messages = collect($data['messages'])
            ->map(static fn (array $m) => [
                'id' => $m['id'],
                'text' => $m['text'],
                'is_user' => (bool) $m['is_user'],
                'citations' => $m['citations'] ?? [],
                'used_llm' => (bool) ($m['used_llm'] ?? false),
                'at' => $m['at'] ?? now()->toIso8601String(),
            ])
            ->values()
            ->all();

        session([
            'chat_history' => $messages,
            'chat_active_conversation_id' => $data['conversation_id'] ?? null,
        ]);

        return response()->json(['ok' => true]);
    }
}
