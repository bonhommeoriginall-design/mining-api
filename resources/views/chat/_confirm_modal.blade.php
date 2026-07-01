<div class="logout-modal chat-confirm-modal" id="chat-confirm-modal" role="dialog" aria-modal="true"
     aria-labelledby="chat-confirm-title" hidden>
    <div class="logout-modal-backdrop" data-chat-confirm-close tabindex="-1"></div>
    <div class="logout-modal-card chat-confirm-card">
        <div class="logout-modal-icon chat-confirm-icon chat-confirm-icon-new" id="chat-confirm-icon" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                <line x1="12" y1="11" x2="12" y2="11"/>
                <line x1="8" y1="11" x2="8" y2="11"/>
                <line x1="16" y1="11" x2="16" y2="11"/>
            </svg>
        </div>
        <h2 class="logout-modal-title" id="chat-confirm-title">Nouvelle conversation ?</h2>
        <p class="logout-modal-text" id="chat-confirm-text"></p>
        <p class="chat-confirm-preview" id="chat-confirm-preview" hidden></p>
        <p class="chat-confirm-hint" id="chat-confirm-hint"></p>
        <div class="logout-modal-actions">
            <button type="button" class="logout-modal-btn logout-modal-btn-cancel" data-chat-confirm-close>
                Annuler
            </button>
            <button type="button" class="logout-modal-btn chat-confirm-btn-primary" id="chat-confirm-ok">
                Nouvelle conversation
            </button>
        </div>
    </div>
</div>
