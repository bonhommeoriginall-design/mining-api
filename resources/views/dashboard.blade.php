@extends('layouts.app')



@section('title', 'Tableau de bord — MINING IA')

@section('page-title', 'Tableau de bord')



@section('content')

<div class="welcome-banner">

    <h1>Bonjour, {{ auth()->user()->name }} 👋</h1>

    <p>

        Vous êtes connecté en tant que

        <strong>{{ auth()->user()->role->label() }}</strong>.

        Gérez les documents réglementaires et le nouveau chat pour les équipes sur le terrain.

    </p>

</div>



<div class="stats-grid">

    <a class="stat-card stat-card-accent stat-card-link" href="{{ route('documents.index', ['status' => 'published']) }}">
        <div class="stat-icon" aria-hidden="true">📗</div>
        <div class="stat-label">Documents publiés</div>
        <div class="stat-value">{{ $publishedCount }}</div>
        <p class="stat-hint">Visibles sur l’app mobile</p>
    </a>

    <a class="stat-card stat-card-link" href="{{ route('documents.index', ['status' => 'draft']) }}">
        <div class="stat-icon" aria-hidden="true">📝</div>
        <div class="stat-label">Brouillons</div>
        <div class="stat-value">{{ $draftCount }}</div>
        <p class="stat-hint">En attente de publication</p>
    </a>

    <a class="stat-card stat-card-link" href="{{ route('chat.index') }}">
        <div class="stat-icon" aria-hidden="true">💬</div>
        <div class="stat-label">Nouveau chat</div>
        <div class="stat-value stat-value-text">Actif</div>
        <p class="stat-hint">Réponses sur docs publiés</p>
    </a>

</div>



<div class="quick-actions-grid">

    <a class="quick-action-card quick-action-primary" href="{{ route('chat.index') }}">

        <span class="quick-action-icon" aria-hidden="true">💬</span>

        <strong>Nouveau chat</strong>

        <span>Poser une question sur la réglementation</span>

    </a>

    @if($canManageDocuments)

        <a class="quick-action-card" href="{{ route('documents.create') }}">

            <span class="quick-action-icon" aria-hidden="true">➕</span>

            <strong>Ajouter un document</strong>

            <span>PDF ou DOCX — publication immédiate possible</span>

        </a>

    @endif

    <a class="quick-action-card" href="{{ route('documents.index') }}">

        <span class="quick-action-icon" aria-hidden="true">📚</span>

        <strong>Bibliothèque</strong>

        <span>Consulter, publier ou télécharger</span>

    </a>

    <button type="button" class="quick-action-card" data-agent-settings-open data-settings-tab="general">

        <span class="quick-action-icon" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
        </span>

        <strong>Paramètres</strong>

        <span>Compte, mot de passe{{ $canManageUsers ? ', utilisateurs' : '' }}</span>
    </button>

</div>

@endsection

