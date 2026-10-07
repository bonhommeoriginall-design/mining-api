<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MINING IA')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/mining.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/mininglogo.png') }}">
    @stack('head')
</head>
<body class="@auth app-auth @else guest-auth @endauth @yield('body-class')">
    @auth
        <div class="app-shell">
            <aside class="sidebar">
                <div class="sidebar-brand">
                    <a href="{{ auth()->user()->isUtilisateur() ? route('chat.index') : route('dashboard') }}" class="sidebar-brand-link">
                        <img src="{{ asset('images/mininglogo.png') }}" alt="MINING IA" class="brand-logo">
                        <div class="sidebar-brand-text">
                            <h1>MINING IA</h1>
                            <p>Portail documentaire</p>
                        </div>
                    </a>
                </div>

                <nav class="sidebar-nav">
                    @unless(auth()->user()->isUtilisateur())
                        <a href="{{ route('dashboard') }}"
                           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           title="Tableau de bord">
                            <span class="nav-icon">📊</span>
                            <span>Tableau de bord</span>
                        </a>
                    @endunless
                    <a href="{{ route('chat.index') }}"
                       class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"
                       id="sidebar-new-chat"
                       title="Nouveau chat">
                        <span class="nav-icon">💬</span>
                        <span>Nouveau chat</span>
                    </a>
                    @if(! auth()->user()->isUtilisateur())
                        <a href="{{ route('documents.index') }}"
                           class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"
                           title="Documents">
                            <span class="nav-icon">📚</span>
                            <span>Documents</span>
                        </a>
                    @endif
                    <button type="button"
                            class="nav-link {{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'active' : '' }}"
                            data-agent-settings-open
                            data-settings-tab="general"
                            title="Paramètres">
                        <span class="nav-icon nav-icon-svg" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                        </span>
                        <span>Paramètres</span>
                    </button>
                </nav>

                <div class="sidebar-footer">
                    @include('partials.agent-account-menu')
                </div>
            </aside>

            <div class="main-wrap">
                <header class="topbar">
                    <div class="topbar-brand">
                        <img src="{{ asset('images/mininglogo.png') }}" alt="" class="topbar-logo" aria-hidden="true">
                        <h2>@yield('page-title', 'MINING IA')</h2>
                    </div>
                </header>

                <main class="main-content">
                    @if (session('status'))
                        @include('partials.alert', ['message' => session('status')])
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        @yield('content')
    @endauth
    @auth
        @include('partials.account-portal-modals')
    @endauth
    <script src="{{ asset('js/mining-ui.js') }}" defer></script>
    @auth
        @include('partials.account-portal-script')
    @endauth
</body>
</html>
