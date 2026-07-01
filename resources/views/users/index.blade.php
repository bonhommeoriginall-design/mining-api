@extends('layouts.app')

@section('title', 'Utilisateurs — MINING IA')
@section('page-title', 'Utilisateurs')

@section('content')
<div class="card">
    <div class="card-header">
        <h1>Comptes utilisateurs</h1>
        <a class="btn" href="{{ route('users.create') }}">➕ Créer</a>
    </div>

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
                @foreach($users as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php
                                $roleClass = match($user->role->value) {
                                    'admin' => 'badge-role-admin',
                                    'editor' => 'badge-role-editor',
                                            'utilisateur' => 'badge-role-utilisateur',
                                            default => 'badge-role-utilisateur',
                                };
                            @endphp
                            <span class="badge {{ $roleClass }}">{{ $user->role->label() }}</span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge badge-green">Actif</span>
                            @else
                                <span class="badge badge-gray">Inactif</span>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-sm btn-secondary" href="{{ route('users.edit', $user) }}">
                                Modifier
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</div>
@endsection
