@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur — Paramètres')
@section('page-title', 'Paramètres')

@section('content')
<div class="card" style="max-width:560px;">
    <div class="card-header">
        <h1>{{ $user->name }}</h1>
    </div>

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nom complet</label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="role">Rôle</label>
            @if($user->id === auth()->id())
                <input type="text" value="{{ $user->role->label() }}" disabled>
            @else
                <select id="role" name="role" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" @selected(old('role', $user->role->value) === $role->value)>
                            {{ $role->label() }}
                        </option>
                    @endforeach
                </select>
            @endif
            @error('role')<div class="error">{{ $message }}</div>@enderror
        </div>

        @if($user->id !== auth()->id())
            <div class="checkbox-row">
                <input id="is_active" type="checkbox" name="is_active" value="1"
                       @checked(old('is_active', $user->is_active))>
                <label for="is_active">Compte actif</label>
            </div>
        @endif

        <div class="form-group">
            <label for="password">Nouveau mot de passe</label>
            <input id="password" type="password" name="password" autocomplete="new-password" minlength="8"
                   placeholder="Laisser vide pour ne pas changer">
            @error('password')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="8">
        </div>

        <div class="btn-group">
            <button class="btn" type="submit">Enregistrer</button>
            <a class="btn btn-secondary" href="{{ route('settings.index') }}#utilisateurs">Retour aux paramètres</a>
        </div>
    </form>
</div>
@endsection
