@extends('layouts.app')

@section('title', 'Paramètres — MINING IA')
@section('page-title', 'Paramètres avancés')

@section('content')
<div class="settings-page settings-page-wide">
    <p class="settings-hint settings-full-intro">
        Gestion avancée du compte et des utilisateurs.
        <button type="button" class="settings-inline-link" data-agent-settings-open data-settings-tab="general">Ouvrir les paramètres rapides</button>
    </p>
    <div class="card settings-section" id="mon-compte">
        <div class="card-header">
            <h1>Mon compte</h1>
        </div>

        <dl class="settings-info">
            <div>
                <dt>Nom</dt>
                <dd>{{ $user->name }}</dd>
            </div>
            <div>
                <dt>E-mail</dt>
                <dd>{{ $user->email }}</dd>
            </div>
            <div>
                <dt>Nom d'utilisateur</dt>
                <dd>{{ $user->username ?? \Illuminate\Support\Str::before($user->email, '@') }}</dd>
            </div>
            @unless($user->isUtilisateur())
                <div>
                    <dt>Rôle</dt>
                    <dd>{{ $user->role->label() }}</dd>
                </div>
            @endunless
        </dl>
    </div>

    <div class="card settings-section">
        <div class="card-header">
            <h1>Mot de passe</h1>
        </div>

        <p class="settings-hint">
            Choisissez un mot de passe d’au moins 8 caractères.
        </p>

        <form method="POST" action="{{ route('settings.password.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="current_password">Mot de passe actuel</label>
                <div class="password-field">
                    <input id="current_password" type="password" name="current_password" required autocomplete="current-password">
                    <button type="button" class="password-toggle" data-password-toggle
                            aria-label="Afficher le mot de passe" aria-pressed="false">👁</button>
                </div>
                @error('current_password')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <div class="password-field">
                    <input id="password" type="password" name="password" required autocomplete="new-password" minlength="8">
                    <button type="button" class="password-toggle" data-password-toggle
                            aria-label="Afficher le mot de passe" aria-pressed="false">👁</button>
                </div>
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
                <div class="password-field">
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" minlength="8">
                    <button type="button" class="password-toggle" data-password-toggle
                            aria-label="Afficher le mot de passe" aria-pressed="false">👁</button>
                </div>
            </div>

            <button class="btn" type="submit">Mettre à jour le mot de passe</button>
        </form>
    </div>

    @if($users)
        <div class="card settings-section" id="utilisateurs">
            <div class="card-header">
                <h1>Utilisateurs</h1>
                <a class="btn" href="{{ route('users.create') }}">➕ Créer</a>
            </div>

            <p class="settings-hint">
                Gérez les comptes utilisateurs, éditeurs et administrateurs.
            </p>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>E-mail</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $account)
                            <tr>
                                <td><strong>{{ $account->name }}</strong></td>
                                <td>{{ $account->email }}</td>
                                <td>
                                    @php
                                        $roleClass = match($account->role->value) {
                                            'admin' => 'badge-role-admin',
                                            'editor' => 'badge-role-editor',
                                            'utilisateur' => 'badge-role-utilisateur',
                                            default => 'badge-role-utilisateur',
                                        };
                                    @endphp
                                    <span class="badge {{ $roleClass }}">{{ $account->role->label() }}</span>
                                </td>
                                <td>
                                    @if($account->is_active)
                                        <span class="badge badge-green">Actif</span>
                                    @else
                                        <span class="badge badge-gray">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-secondary" href="{{ route('users.edit', $account) }}">
                                        Modifier
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->withQueryString()->fragment('utilisateurs')->links() }}
        </div>
    @endif
</div>
@endsection
