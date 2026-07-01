@php
    $user = auth()->user();
@endphp

<div class="agent-account-wrap">
    <div class="agent-account-menu" id="agent-account-menu" role="menu" aria-labelledby="agent-account-trigger" hidden>
        <button type="button" class="agent-account-menu-head" data-agent-profile-open role="menuitem">
            @include('partials.user-avatar', ['user' => $user, 'class' => 'agent-sidebar-avatar agent-account-menu-avatar'])
            <span class="agent-account-menu-head-text">
                <strong>{{ $user->name }}</strong>
                <small>{{ $user->role->label() }}</small>
            </span>
            <svg class="agent-account-menu-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
        </button>

        <div class="agent-account-menu-divider" role="separator"></div>

        <button type="button" class="agent-account-menu-item" data-agent-profile-open role="menuitem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Profil</span>
        </button>
        <button type="button" class="agent-account-menu-item" data-agent-settings-open data-settings-tab="general" role="menuitem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span>Paramètres</span>
        </button>

        <div class="agent-account-menu-divider" role="separator"></div>

        <button type="button" class="agent-account-menu-item" id="agent-account-help" role="menuitem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span>Aide</span>
            <svg class="agent-account-menu-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
        </button>
        <button type="button" class="agent-account-menu-item agent-account-menu-item-danger" data-logout-trigger role="menuitem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Se déconnecter</span>
        </button>
    </div>

    <button type="button"
            class="agent-sidebar-user"
            id="agent-account-trigger"
            aria-haspopup="menu"
            aria-expanded="false"
            aria-controls="agent-account-menu">
        @include('partials.user-avatar', ['user' => $user])
        <span class="agent-sidebar-user-text">
            <strong>{{ $user->name }}</strong>
            <small>{{ $user->role->label() }}</small>
        </span>
        <svg class="agent-sidebar-user-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 15l-6-6-6 6"/></svg>
    </button>
</div>

<div class="agent-help-modal" id="agent-help-modal" role="dialog" aria-modal="true" aria-labelledby="agent-help-title" hidden>
    <div class="agent-help-backdrop" data-agent-help-close tabindex="-1"></div>
    <div class="agent-help-card">
        <h2 id="agent-help-title">Aide — MINING IA</h2>
        <ul class="agent-help-list">
            <li>Posez vos questions en langage naturel sur la réglementation minière.</li>
            <li>Consultez les <strong>sources citées</strong> dans chaque réponse.</li>
            <li>Utilisez <strong>Historique</strong> pour reprendre une conversation passée.</li>
            <li>MINING IA peut se tromper : vérifiez toujours les textes officiels.</li>
        </ul>
        <button type="button" class="btn btn-secondary" data-agent-help-close>Fermer</button>
    </div>
</div>
