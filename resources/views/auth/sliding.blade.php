@extends('layouts.app')

@section('title', ($panel === 'register' ? 'Créer un compte' : 'Connexion') . ' — MINING IA')

@section('content')
@php
    $activePanel = $panel ?? 'login';
    if ($errors->has('name') || old('name')) {
        $activePanel = 'register';
    }
    $isRegisterContext = $activePanel === 'register';
@endphp

<div class="auth-sliding-page">
    <div class="auth-sliding-container {{ $activePanel === 'register' ? 'right-panel-active' : '' }}" id="auth-sliding">
        {{-- Connexion --}}
        <div class="auth-form-panel auth-sign-in">
            <div class="auth-form-inner">
                <img src="{{ asset('images/mininglogo.png') }}" alt="MINING IA" class="auth-form-logo">
                <h2>Connexion</h2>
                <p class="auth-form-subtitle">Accédez à votre espace MINING IA</p>

                <form method="POST" action="{{ route('login') }}" id="login-form" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label for="login-email">Adresse e-mail</label>
                        <input id="login-email" type="email" name="email"
                               value="{{ old('email') }}" required
                               placeholder="vous@mining.local" autocomplete="email"
                               class="login-input">
                        @error('email')
                            @unless($isRegisterContext)
                                <div class="error login-error-shake">{{ $message }}</div>
                            @endunless
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="login-password">Mot de passe</label>
                        <div class="password-field">
                            <input id="login-password" type="password" name="password" required
                                   placeholder="••••••••" autocomplete="current-password"
                                   class="login-input">
                            <button type="button" class="password-toggle" data-password-toggle
                                    aria-label="Afficher le mot de passe" aria-pressed="false">👁</button>
                        </div>
                        @error('password')
                            @unless($isRegisterContext)
                                <div class="error login-error-shake">{{ $message }}</div>
                            @endunless
                        @enderror
                    </div>

                    <div class="checkbox-row">
                        <input id="remember" type="checkbox" name="remember" value="1">
                        <label for="remember">Se souvenir de moi</label>
                    </div>

                    <button class="btn login-submit auth-submit" type="submit">
                        <span class="login-submit-text">Se connecter</span>
                        <span class="login-submit-spinner" aria-hidden="true"></span>
                    </button>
                </form>

                <p class="auth-form-switch auth-form-switch-desktop">
                    Pas encore de compte ?
                    <button type="button" data-auth-panel="register">Créer un compte</button>
                </p>
            </div>
        </div>

        {{-- Inscription --}}
        <div class="auth-form-panel auth-sign-up">
            <div class="auth-form-inner auth-form-compact">
                <img src="{{ asset('images/mininglogo.png') }}" alt="MINING IA" class="auth-form-logo auth-form-logo-compact">
                <h2>Créer un compte</h2>

                <form method="POST" action="{{ route('register') }}" id="register-form" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input id="name" type="text" name="name"
                               value="{{ old('name') }}" required
                               placeholder="Prénom et nom" autocomplete="name"
                               class="login-input">
                        @error('name')<div class="error login-error-shake">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="register-email">E-mail</label>
                        <input id="register-email" type="email" name="email"
                               value="{{ old('email') }}" required
                               placeholder="vous@mining.local" autocomplete="email"
                               class="login-input">
                        @error('email')
                            @if($isRegisterContext)
                                <div class="error login-error-shake">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    <div class="auth-form-row">
                        <div class="form-group">
                            <label for="register-password">Mot de passe</label>
                            <div class="password-field">
                                <input id="register-password" type="password" name="password" required
                                       placeholder="8 car. min." autocomplete="new-password"
                                       class="login-input">
                                <button type="button" class="password-toggle" data-password-toggle
                                        aria-label="Afficher le mot de passe" aria-pressed="false">👁</button>
                            </div>
                            @error('password')<div class="error login-error-shake">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirmer</label>
                            <div class="password-field">
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                       placeholder="Retaper" autocomplete="new-password"
                                       class="login-input">
                                <button type="button" class="password-toggle" data-password-toggle
                                        aria-label="Afficher le mot de passe" aria-pressed="false">👁</button>
                            </div>
                        </div>
                    </div>

                    <button class="btn login-submit auth-submit auth-submit-compact" type="submit">
                        <span class="login-submit-text">Créer mon compte</span>
                        <span class="login-submit-spinner" aria-hidden="true"></span>
                    </button>
                </form>

                <p class="auth-form-switch auth-form-switch-desktop">
                    Déjà un compte ?
                    <button type="button" data-auth-panel="login">Se connecter</button>
                </p>
            </div>
        </div>

        {{-- Panneau coulissant --}}
        <div class="auth-overlay-wrap">
            <div class="auth-overlay">
                <div class="auth-overlay-panel auth-overlay-left">
                    <img src="{{ asset('images/mininglogo.png') }}" alt="" class="auth-overlay-logo" aria-hidden="true">
                    <h2>Bon retour !</h2>
                    <p>Connectez-vous pour accéder au nouveau chat et à vos documents.</p>
                    <button type="button" class="auth-ghost-btn" id="sign-in-btn">Se connecter</button>
                </div>
                <div class="auth-overlay-panel auth-overlay-right">
                    <img src="{{ asset('images/mininglogo.png') }}" alt="" class="auth-overlay-logo" aria-hidden="true">
                    <h2>Bienvenue</h2>
                    <p>Créez un compte pour interroger la réglementation minière avec MINING IA.</p>
                    <button type="button" class="auth-ghost-btn" id="sign-up-btn">Créer un compte</button>
                </div>
            </div>
        </div>
    </div>

    <p class="auth-mobile-switch">
        <span id="auth-mobile-login-label" {{ $activePanel === 'register' ? 'hidden' : '' }}>
            Pas encore de compte ?
            <button type="button" class="auth-mobile-link" data-auth-panel="register">Créer un compte</button>
        </span>
        <span id="auth-mobile-register-label" {{ $activePanel === 'register' ? '' : 'hidden' }}>
            Déjà un compte ?
            <button type="button" class="auth-mobile-link" data-auth-panel="login">Se connecter</button>
        </span>
    </p>
</div>

<script>
(() => {
    const container = document.getElementById('auth-sliding');
    const signUpBtn = document.getElementById('sign-up-btn');
    const signInBtn = document.getElementById('sign-in-btn');
    const mobileLoginLabel = document.getElementById('auth-mobile-login-label');
    const mobileRegisterLabel = document.getElementById('auth-mobile-register-label');

    function setPanel(panel) {
        const isRegister = panel === 'register';
        container?.classList.toggle('right-panel-active', isRegister);
        mobileLoginLabel?.toggleAttribute('hidden', isRegister);
        mobileRegisterLabel?.toggleAttribute('hidden', !isRegister);
        history.replaceState(null, '', isRegister ? '{{ route('register') }}' : '{{ route('login') }}');
    }

    signUpBtn?.addEventListener('click', () => setPanel('register'));
    signInBtn?.addEventListener('click', () => setPanel('login'));

    document.querySelectorAll('[data-auth-panel]').forEach((btn) => {
        btn.addEventListener('click', () => setPanel(btn.dataset.authPanel));
    });

    ['login-form', 'register-form'].forEach((id) => {
        document.getElementById(id)?.addEventListener('submit', function () {
            this.querySelector('.login-submit')?.classList.add('is-loading');
        });
    });
})();
</script>
@endsection
