@extends('layouts.agent')



@section('title', 'MINING IA')

@section('agent-title', 'MINING IA')



@section('content')

<div class="agent-chat" id="agent-chat">

    <div class="agent-chat-body" id="chat-messages" aria-live="polite">

        @if (empty($messages))

            <div class="agent-chat-empty" id="chat-empty">

                <div class="agent-chat-hero">

                    <img src="{{ asset('images/mininglogo.png') }}" alt="" class="agent-chat-hero-logo" aria-hidden="true">

                    <h1>Par quoi commençons-nous ?</h1>

                    <p class="agent-chat-hero-sub">Interrogez la réglementation minière avec des réponses documentées.</p>

                </div>

                <div class="chat-suggestions chat-suggestions-dark agent-chat-suggestions">

                    <button type="button" class="chat-suggestion" data-example-question="Quelles sont les obligations des titulaires de permis miniers ?">

                        Obligations des titulaires

                    </button>

                    <button type="button" class="chat-suggestion" data-example-question="Que dit le texte sur les coopératives minières ?">

                        Coopératives minières

                    </button>

                    <button type="button" class="chat-suggestion" data-example-question="Quelles procédures pour l'exploration ?">

                        Procédures d'exploration

                    </button>

                </div>

            </div>

        @else

            <div class="agent-chat-thread">

                @foreach($messages as $message)

                    @include('chat._message_agent', ['message' => $message])

                @endforeach

            </div>

        @endif

    </div>



    <div class="agent-chat-footer">

        <div class="agent-input-wrap">

            <form class="agent-input-bar" id="chat-form">

                @csrf

                <input type="text" id="chat-input" name="message"

                       placeholder="Posez une question sur vos documents…"

                       autocomplete="off"

                       maxlength="2000" required>

                <button type="submit" class="agent-input-send" id="chat-submit" aria-label="Envoyer">

                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.4 20.4l17.45-7.48a1 1 0 0 0 0-1.84L3.4 3.6a1 1 0 0 0-1.57.87v5.2a1 1 0 0 0 .76.97l7.55 1.74-7.55 1.74a1 1 0 0 0-.76.97v5.2a1 1 0 0 0 1.57.87z"/></svg>

                </button>

            </form>

            <p class="agent-chat-disclaimer">MINING IA peut se tromper. Vérifiez les sources citées.</p>

        </div>

    </div>

</div>



@include('chat._history_panel')

@include('chat._confirm_modal')

@include('chat._script', ['dark' => true])

@endsection

