@extends('layouts.app')



@section('title', 'Ajouter un document — MINING IA')

@section('page-title', 'Nouveau document')



@section('content')

<div class="card form-card">

    <div class="card-header">

        <h1>Ajouter un document</h1>

    </div>



    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">

        @csrf



        <div class="form-group">

            <label for="title">Titre du document</label>

            <input id="title" name="title" value="{{ old('title') }}" required

                   placeholder="Ex. Code minier — version 2024">

            @error('title')<div class="error">{{ $message }}</div>@enderror

        </div>



        <div class="form-group">

            <label for="file">Fichier source</label>

            <div class="file-drop">

                <p class="file-drop-hint">Formats acceptés : PDF ou DOCX (max. selon configuration serveur)</p>

                <label class="file-drop-btn" for="file">

                    <span data-file-label data-file-default="Choisir un fichier">Choisir un fichier</span>

                </label>

                <input id="file" type="file" name="file" accept=".pdf,.docx" required data-file-input class="file-drop-input">

            </div>

            @error('file')<div class="error">{{ $message }}</div>@enderror

        </div>



        <div class="checkbox-row">

            <input id="publish" type="checkbox" name="publish" value="1"

                   {{ old('publish') ? 'checked' : '' }}>

            <label for="publish">Publier immédiatement pour les utilisateurs</label>

        </div>



        <div class="btn-group">

            <button class="btn" type="submit">Enregistrer</button>

            <a class="btn btn-secondary" href="{{ route('documents.index') }}">Annuler</a>

        </div>

    </form>

</div>

@endsection

