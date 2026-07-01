@extends('layouts.app')

@section('title', 'Créer un utilisateur — Paramètres')
@section('page-title', 'Paramètres')

@section('content')
<div class="card" style="max-width:560px;">
    <div class="card-header">
        <h1>Créer un utilisateur</h1>
    </div>

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="form-group">
            <label for="name">Nom complet</label>
            <input id="name" name="name" value="{{ old('name') }}" required
                   placeholder="Prénom et nom">
            @error('name')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   placeholder="utilisateur@mining.local">
            @error('email')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="password" required
                   placeholder="Minimum 8 caractères">
            @error('password')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="role">Rôle</label>
            <select id="role" name="role" required>
                @foreach($roles as $role)
                    <option value="{{ $role->value }}" @selected(old('role') === $role->value)>
                        {{ $role->label() }}
                    </option>
                @endforeach
            </select>
            @error('role')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="btn-group">
            <button class="btn" type="submit">Créer le compte</button>
            <a class="btn btn-secondary" href="{{ route('settings.index') }}#utilisateurs">Retour aux paramètres</a>
        </div>
    </form>
</div>
@endsection
