<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'MINING IA')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/mining.css') }}">

    <link rel="icon" type="image/png" href="{{ asset('images/mininglogo.png') }}">

</head>

<body class="agent-shell">

    @include('partials.agent-sidebar')



    <div class="agent-layout">

        <header class="agent-content-header">

            <button type="button" class="agent-sidebar-toggle" id="agent-sidebar-toggle" aria-label="Ouvrir le menu">

                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 12h18M3 6h18M3 18h18"/></svg>

            </button>

            <h1 class="agent-content-title">@yield('agent-title', 'MINING IA')</h1>

            <div class="agent-content-header-spacer" aria-hidden="true"></div>

        </header>



        @if (session('status'))

            <div class="agent-toast" role="status">{{ session('status') }}</div>

        @endif



        <main class="agent-main">

            @yield('content')

        </main>

    </div>



    @include('partials.account-portal-modals')

    <script src="{{ asset('js/mining-ui.js') }}" defer></script>

    <script>

    (() => {

        const sidebar = document.getElementById('agent-sidebar');

        const backdrop = document.getElementById('agent-sidebar-backdrop');

        const openBtn = document.getElementById('agent-sidebar-toggle');

        const closeBtn = document.getElementById('agent-sidebar-close');



        function openSidebar() {

            sidebar?.classList.add('is-open');

            backdrop?.removeAttribute('hidden');

            document.body.classList.add('agent-sidebar-open');

        }



        function closeSidebar() {

            sidebar?.classList.remove('is-open');

            backdrop?.setAttribute('hidden', '');

            document.body.classList.remove('agent-sidebar-open');

        }



        openBtn?.addEventListener('click', openSidebar);

        closeBtn?.addEventListener('click', closeSidebar);

        backdrop?.addEventListener('click', closeSidebar);



        window.matchMedia('(min-width: 900px)').addEventListener('change', (e) => {

            if (e.matches) closeSidebar();

        });

    })();

    </script>

    @include('partials.account-portal-script')

</body>

</html>
