@php
    $user = auth()->user();
@endphp

<div class="agent-settings-modal" id="agent-settings-modal" role="dialog" aria-modal="true" aria-labelledby="agent-settings-title" hidden>
    <div class="agent-settings-backdrop" data-agent-settings-close tabindex="-1"></div>
    <div class="agent-settings-dialog">
        <div class="agent-settings-layout">
            <nav class="agent-settings-nav" aria-label="Sections des paramètres">
                <button type="button" class="agent-settings-close" data-agent-settings-close aria-label="Fermer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>

                <button type="button" class="agent-settings-nav-item is-active" data-settings-tab="general">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span>Général</span>
                </button>
                <button type="button" class="agent-settings-nav-item" data-settings-tab="security">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span>Sécurité</span>
                </button>
                <button type="button" class="agent-settings-nav-item" data-settings-tab="data">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                    <span>Données</span>
                </button>
                <button type="button" class="agent-settings-nav-item" data-settings-tab="account">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Compte</span>
                </button>
            </nav>

            <div class="agent-settings-content">
                <section class="agent-settings-panel is-active" data-settings-panel="general" aria-labelledby="agent-settings-title">
                    <h2 id="agent-settings-title">Général</h2>

                    <div class="agent-settings-row">
                        <span class="agent-settings-row-label">Thème</span>
                        <span class="agent-settings-row-control agent-settings-badge">{{ $user->isUtilisateur() ? 'Sombre' : 'Clair' }}</span>
                    </div>
                    <div class="agent-settings-row">
                        <span class="agent-settings-row-label">Langue</span>
                        <span class="agent-settings-row-control agent-settings-badge">Français</span>
                    </div>
                    <div class="agent-settings-row">
                        <span class="agent-settings-row-label">Interface</span>
                        <span class="agent-settings-row-control agent-settings-muted">{{ $user->isUtilisateur() ? 'Chat documentaire' : 'Portail documentaire' }}</span>
                    </div>

                    <div class="agent-settings-info-card">
                        <div class="agent-settings-info-icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <strong>Sécurisez votre compte</strong>
                            <p>Utilisez un mot de passe unique d’au moins 8 caractères. Changez-le régulièrement dans l’onglet Sécurité.</p>
                            <button type="button" class="agent-settings-link-btn" data-settings-tab-jump="security">Modifier le mot de passe</button>
                        </div>
                    </div>
                </section>

                <section class="agent-settings-panel" data-settings-panel="security" aria-labelledby="agent-settings-security-title">
                    <h2 id="agent-settings-security-title">Sécurité</h2>
                    <p class="agent-settings-lead">Mettez à jour votre mot de passe pour protéger l’accès à MINING IA.</p>

                    <form method="POST" action="{{ route('settings.password.update') }}" class="agent-settings-form">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="agent-settings-current-password">Mot de passe actuel</label>
                            <div class="password-field">
                                <input id="agent-settings-current-password" type="password" name="current_password" required autocomplete="current-password" value="">
                                <button type="button" class="password-toggle" data-password-toggle
                                        aria-label="Afficher le mot de passe" aria-pressed="false">👁</button>
                            </div>
                            @error('current_password')<div class="error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="agent-settings-password">Nouveau mot de passe</label>
                            <div class="password-field">
                                <input id="agent-settings-password" type="password" name="password" required autocomplete="new-password" minlength="8">
                                <button type="button" class="password-toggle" data-password-toggle
                                        aria-label="Afficher le mot de passe" aria-pressed="false">👁</button>
                            </div>
                            @error('password')<div class="error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="agent-settings-password-confirmation">Confirmer le nouveau mot de passe</label>
                            <div class="password-field">
                                <input id="agent-settings-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password" minlength="8">
                                <button type="button" class="password-toggle" data-password-toggle
                                        aria-label="Afficher le mot de passe" aria-pressed="false">👁</button>
                            </div>
                        </div>

                        <button class="btn" type="submit">Mettre à jour le mot de passe</button>
                    </form>
                </section>

                <section class="agent-settings-panel" data-settings-panel="data" aria-labelledby="agent-settings-data-title">
                    <h2 id="agent-settings-data-title">Données</h2>
                    <p class="agent-settings-lead">Vos conversations sont enregistrées localement dans ce navigateur pour retrouver l’historique.</p>

                    <div class="agent-settings-row agent-settings-row-stack">
                        <div>
                            <span class="agent-settings-row-label">Historique des conversations</span>
                            <p class="agent-settings-row-desc">Stocké dans votre navigateur (localStorage). Non synchronisé entre appareils.</p>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" id="agent-settings-open-history">Ouvrir l’historique</button>
                    </div>

                    <div class="agent-settings-row agent-settings-row-stack">
                        <div>
                            <span class="agent-settings-row-label">Effacer l’historique local</span>
                            <p class="agent-settings-row-desc">Supprime toutes les conversations enregistrées sur cet appareil.</p>
                        </div>
                        <button type="button" class="chat-history-clear-all" id="agent-settings-clear-history">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M10 11v6M14 11v6"/></svg>
                            <span>Tout effacer</span>
                        </button>
                    </div>
                </section>

                <section class="agent-settings-panel" data-settings-panel="account" aria-labelledby="agent-settings-account-title">
                    <h2 id="agent-settings-account-title">Compte</h2>

                    <div class="agent-settings-row">
                        <span class="agent-settings-row-label">Nom</span>
                        <span class="agent-settings-row-control">{{ $user->name }}</span>
                    </div>
                    <div class="agent-settings-row">
                        <span class="agent-settings-row-label">E-mail</span>
                        <span class="agent-settings-row-control">{{ $user->email }}</span>
                    </div>
                    <div class="agent-settings-row">
                        <span class="agent-settings-row-label">Nom d’utilisateur</span>
                        <span class="agent-settings-row-control">{{ $user->username ?? \Illuminate\Support\Str::before($user->email, '@') }}</span>
                    </div>
                    <div class="agent-settings-row">
                        <span class="agent-settings-row-label">Rôle</span>
                        <span class="agent-settings-row-control agent-settings-badge">{{ $user->role->label() }}</span>
                    </div>
                    <div class="agent-settings-row">
                        <span class="agent-settings-row-label">Statut</span>
                        <span class="agent-settings-row-control agent-settings-badge agent-settings-badge-green">Actif</span>
                    </div>

                    <button type="button" class="agent-settings-link-btn" data-agent-profile-open style="margin-top: 16px;">Modifier le profil</button>

                    @if ($user->role->canManageUsers())
                        <a href="{{ route('settings.index', ['view' => 'full']) }}#utilisateurs" class="agent-settings-link-btn agent-settings-link-btn-secondary" style="margin-top: 10px; display: inline-flex; text-decoration: none;">
                            Gérer les utilisateurs
                        </a>
                    @endif
                </section>
            </div>
        </div>
    </div>
</div>
