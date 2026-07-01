@php
    $user = auth()->user();
    $username = old('username', $user->username ?? \Illuminate\Support\Str::before($user->email, '@'));
    $avatarUrl = $user->avatarUrl();
@endphp

<div class="agent-profile-modal" id="agent-profile-modal" role="dialog" aria-modal="true" aria-labelledby="agent-profile-title" hidden>
    <div class="agent-profile-backdrop" data-agent-profile-close tabindex="-1"></div>
    <div class="agent-profile-dialog">
        <h2 id="agent-profile-title">Modifier le profil</h2>

        <div class="agent-profile-avatar-wrap">
            <div class="agent-profile-avatar" id="agent-profile-avatar-preview" style="background-color: {{ $user->avatarColor() }}">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="" class="agent-profile-avatar-img" id="agent-profile-avatar-img">
                    <span class="agent-profile-avatar-initials" id="agent-profile-avatar-initials" hidden>{{ $user->initials() }}</span>
                @else
                    <img src="" alt="" class="agent-profile-avatar-img" id="agent-profile-avatar-img" hidden>
                    <span class="agent-profile-avatar-initials" id="agent-profile-avatar-initials">{{ $user->initials() }}</span>
                @endif
            </div>
            <button type="button" class="agent-profile-avatar-edit" id="agent-profile-avatar-btn" title="Importer une photo" aria-label="Importer une photo de profil">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </button>
        </div>

        @if ($avatarUrl)
            <button type="button" class="agent-profile-remove-photo" id="agent-profile-remove-photo">Supprimer la photo</button>
        @else
            <button type="button" class="agent-profile-remove-photo" id="agent-profile-remove-photo" hidden>Supprimer la photo</button>
        @endif

        <form method="POST" action="{{ route('settings.profile.update') }}" class="agent-profile-form" id="agent-profile-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="remove_avatar" id="agent-profile-remove-avatar" value="0">
            <input type="file" name="avatar" id="agent-profile-avatar-input" class="agent-profile-avatar-input" accept="image/jpeg,image/png,image/webp,image/gif" hidden>

            <label class="agent-profile-field">
                <span class="agent-profile-field-label">Nom d’affichage</span>
                <input type="text" name="name" id="agent-profile-name" value="{{ old('name', $user->name) }}" maxlength="255" required autocomplete="name">
                @error('name')<span class="error">{{ $message }}</span>@enderror
            </label>

            <label class="agent-profile-field">
                <span class="agent-profile-field-label">Nom d’utilisateur</span>
                <input type="text" name="username" id="agent-profile-username" value="{{ $username }}" maxlength="50" minlength="3" pattern="[a-zA-Z0-9_]+" required autocomplete="username">
                @error('username')<span class="error">{{ $message }}</span>@enderror
            </label>

            @error('avatar')<span class="error agent-profile-avatar-error">{{ $message }}</span>@enderror

            <p class="agent-profile-hint">JPG, PNG, WEBP ou GIF — 2 Mo max. Votre photo apparaît dans la barre latérale.</p>

            <div class="agent-profile-actions">
                <button type="button" class="agent-profile-btn agent-profile-btn-cancel" data-agent-profile-close>Annuler</button>
                <button type="submit" class="agent-profile-btn agent-profile-btn-save">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
