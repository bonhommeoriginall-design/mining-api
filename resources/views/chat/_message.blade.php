<div class="chat-bubble {{ $message['is_user'] ? 'chat-bubble-user' : 'chat-bubble-ai' }}">
    <div class="chat-bubble-text">{!! nl2br(e($message['text'])) !!}</div>
    @if (! $message['is_user'] && ! empty($message['citations']))
        <div class="chat-citations-staff">
            @foreach($message['citations'] as $citation)
                <details class="chat-cite-card">
                    <summary>
                        <span class="chat-cite-name">{{ $citation['filename'] }}</span>
                        <span class="chat-cite-score">~{{ $citation['score'] }}%</span>
                    </summary>
                    <p>{{ $citation['excerpt'] }}</p>
                </details>
            @endforeach
        </div>
    @endif
    @if (! $message['is_user'] && ! empty($message['used_llm']))
        <div class="chat-llm-tag">Réponse IA</div>
    @endif
</div>
