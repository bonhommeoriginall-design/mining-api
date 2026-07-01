<div id="chat-history-panel" class="chat-history-panel" hidden aria-hidden="true">
    <div class="chat-history-backdrop" id="chat-history-backdrop" tabindex="-1"></div>
    <aside class="chat-history-drawer" role="dialog" aria-labelledby="chat-history-title">
        <header class="chat-history-header">
            <h2 id="chat-history-title">Historique des conversations</h2>
            <button type="button" class="chat-history-close" id="chat-history-close" aria-label="Fermer">&times;</button>
        </header>
        <div class="chat-history-actions">
            <button type="button" class="btn btn-secondary btn-sm" id="chat-history-new">Nouvelle conversation</button>
            <button type="button" class="chat-history-clear-all" id="chat-history-clear-all">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M10 11v6M14 11v6"/></svg>
                <span>Tout effacer</span>
            </button>
        </div>
        <div class="chat-history-list-wrap">
            <p class="chat-history-empty" id="chat-history-empty" hidden>
                Aucune conversation enregistrée.<br>
                Vos échanges seront sauvegardés automatiquement.
            </p>
            <ul class="chat-history-list" id="chat-history-list"></ul>
        </div>
    </aside>
</div>
