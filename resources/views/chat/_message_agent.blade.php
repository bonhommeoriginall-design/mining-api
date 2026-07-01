<div class="agent-msg {{ $message['is_user'] ? 'agent-msg-user' : 'agent-msg-ai' }}">
    <div class="agent-msg-content">{!! nl2br(e($message['text'])) !!}</div>
    @if (! $message['is_user'] && ! empty($message['citations']))
        <div class="agent-citations">
            @foreach($message['citations'] as $citation)
                <details class="agent-cite-item">
                    <summary>{{ $citation['filename'] }} · ~{{ $citation['score'] }}%</summary>
                    <p>{{ $citation['excerpt'] }}</p>
                </details>
            @endforeach
        </div>
    @endif
</div>
