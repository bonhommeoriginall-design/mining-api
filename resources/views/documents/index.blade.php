@extends('layouts.app')

@section('title', 'Documents — MINING IA')
@section('page-title', 'Documents')

@section('content')
<div class="card">
    <div class="card-header">
        <h1>Bibliothèque documentaire</h1>
        @if($canManage)
            <a class="btn" href="{{ route('documents.create') }}">➕ Ajouter</a>
        @endif
    </div>

    @if($canManage)
        <div class="doc-filter-tabs" role="tablist" aria-label="Filtrer par statut">
            <a href="{{ route('documents.index') }}"
               class="doc-filter-tab {{ empty($statusFilter) ? 'is-active' : '' }}">Tous</a>
            <a href="{{ route('documents.index', ['status' => 'published']) }}"
               class="doc-filter-tab {{ ($statusFilter ?? null) === 'published' ? 'is-active' : '' }}">Publiés</a>
            <a href="{{ route('documents.index', ['status' => 'draft']) }}"
               class="doc-filter-tab {{ ($statusFilter ?? null) === 'draft' ? 'is-active' : '' }}">Brouillons</a>
        </div>
    @endif

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Fichier</th>
                    <th>Version</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $document)
                    <tr>
                        <td><strong>{{ $document->title }}</strong></td>
                        <td>{{ $document->original_filename }}</td>
                        <td>
                            <span class="badge badge-gray">v{{ $document->version }}</span>
                        </td>
                        <td>
                            @if($document->status === 'published')
                                <span class="badge badge-green">Publié</span>
                            @else
                                <span class="badge badge-amber">Brouillon</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-sm btn-secondary"
                                   href="{{ route('documents.download', $document) }}">
                                    Télécharger
                                </a>
                                @if($canManage && $document->status !== 'published')
                                    <form action="{{ route('documents.publish', $document) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-success" type="submit">
                                            Publier
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-state-icon" aria-hidden="true">📄</div>
                                <h3>Aucun document pour l'instant</h3>
                                <p>
                                    Les documents publiés alimentent le nouveau chat et l'application mobile.
                                </p>
                                @if($canManage)
                                    <a class="btn" href="{{ route('documents.create') }}">Ajouter le premier document</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $documents->links() }}
</div>
@endsection
