<aside class="agent-sidebar" id="agent-sidebar" aria-label="Navigation">
    <div class="agent-sidebar-inner">
        <div class="agent-sidebar-head">
            <a href="{{ route('chat.index') }}" class="agent-sidebar-brand">
                <img src="{{ asset('images/mininglogo.png') }}" alt="" class="agent-sidebar-logo" aria-hidden="true">
                <span>MINING IA</span>
            </a>
            <button type="button" class="agent-sidebar-close" id="agent-sidebar-close" aria-label="Fermer le menu">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <button type="button" class="agent-sidebar-new" id="agent-new-chat">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            <span>Nouveau chat</span>
        </button>

        <nav class="agent-sidebar-nav">
            <button type="button" class="agent-sidebar-link" id="agent-chat-history">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span>Historique</span>
            </button>
            <a href="{{ route('chat.index') }}" class="agent-sidebar-link {{ request()->routeIs('chat.*') ? 'is-active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                <span>Chat</span>
            </a>
        </nav>

        <div class="agent-sidebar-spacer"></div>

        <div class="agent-sidebar-footer">
            @include('partials.agent-account-menu')
        </div>
    </div>
</aside>
<div class="agent-sidebar-backdrop" id="agent-sidebar-backdrop" hidden></div>
