@extends('layouts.app')

@section('title', 'Nouveau chat — MINING IA')

@section('body-class', 'chat-focus-mode')

@push('head')
<script>
(function () {
    if (localStorage.getItem('mining-staff-chat-theme') === 'dark') {
        document.documentElement.classList.add('staff-chat-dark-boot');
    }
})();
</script>
@endpush

@section('content')
<div class="chat-page chat-page-staff" id="chat-page-staff">
    @unless($llmConfigured)
        @if(auth()->user()->isAdmin())
            <div class="chat-notice chat-staff-notice">
                <strong>Mode extraits</strong> — Ajoutez <code>MINING_LLM_API_KEY</code> dans le fichier <code>.env</code>
                pour activer les réponses rédigées par intelligence artificielle.
            </div>
        @endif
    @endunless

    <div class="chat-staff" id="chat-staff">
        <header class="chat-staff-toolbar">
            <div class="chat-staff-toolbar-brand">
                <img src="{{ asset('images/mininglogo.png') }}" alt="" class="chat-staff-toolbar-logo" aria-hidden="true">
                <div>
                    <strong>MINING IA</strong>
                    <span>Réponses basées sur les documents publiés</span>
                </div>
            </div>
            <div class="chat-staff-toolbar-actions">
                <button type="button" class="chat-staff-tool-btn chat-staff-tool-btn-icon" id="chat-theme-toggle"
                        title="Thème sombre" aria-label="Basculer le thème" aria-pressed="false">
                    <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                    </svg>
                    <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" hidden>
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    <span class="chat-theme-label">Thème</span>
                </button>
                <button type="button" class="chat-staff-tool-btn" id="chat-history-open" title="Historique">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>Historique</span>
                </button>
                <button type="button" class="chat-staff-tool-btn chat-staff-tool-btn-primary" id="chat-clear" title="Nouveau chat">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Nouveau chat</span>
                </button>
            </div>
        </header>

        <div class="chat-staff-messages" id="chat-messages" aria-live="polite">
            @if (empty($messages))
                <div class="chat-staff-welcome" id="chat-empty">
                    <div class="chat-staff-hero">
                        <img src="{{ asset('images/mininglogo.png') }}" alt="" class="chat-staff-hero-logo" aria-hidden="true">
                        <h2>Par quoi commençons-nous ?</h2>
                        <p>Interrogez la réglementation minière avec des réponses documentées et des sources citées.</p>
                    </div>
                    <div class="chat-suggestions chat-staff-suggestions">
                        <button type="button" class="chat-suggestion" data-example-question="Quelles sont les obligations des titulaires de permis miniers ?">
                            Obligations des titulaires
                        </button>
                        <button type="button" class="chat-suggestion" data-example-question="Que dit le texte sur les coopératives minières ?">
                            Coopératives minières
                        </button>
                        <button type="button" class="chat-suggestion" data-example-question="Quelles procédures pour l'exploration ?">
                            Procédures d'exploration
                        </button>
                    </div>
                </div>
            @else
                <div class="chat-thread">
                    @foreach($messages as $message)
                        @include('chat._message', ['message' => $message])
                    @endforeach
                </div>
            @endif
        </div>

        <footer class="chat-staff-footer">
            <form class="chat-staff-input-bar" id="chat-form">
                @csrf
                <textarea id="chat-input" name="message" rows="1"
                          placeholder="Posez une question sur vos documents…"
                          required maxlength="2000" autocomplete="off"></textarea>
                <button type="submit" class="chat-staff-send" id="chat-submit" aria-label="Envoyer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.4 20.4l17.45-7.48a1 1 0 0 0 0-1.84L3.4 3.6a1 1 0 0 0-1.57.87v5.2a1 1 0 0 0 .76.97l7.55 1.74-7.55 1.74a1 1 0 0 0-.76.97v5.2a1 1 0 0 0 1.57.87z"/></svg>
                </button>
            </form>
            <p class="chat-staff-disclaimer">MINING IA peut se tromper. Vérifiez les sources citées.</p>
        </footer>
    </div>
</div>

@include('chat._history_panel')
@include('chat._confirm_modal')
@include('chat._script', ['dark' => false])
@endsection
