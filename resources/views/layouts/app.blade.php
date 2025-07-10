<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Raccourcisseur d’URL')</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 0; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; padding: 2em; }
        h1 { text-align: center; color: #2d3e50; }
        .btn { background: #1976d2; color: #fff; border: none; border-radius: 4px; padding: 0.7em 1.5em; font-size: 1em; cursor: pointer; }
        .btn:hover { background: #1257a6; }
        nav { background: #1976d2; color: #fff; padding: 1em; text-align: center; }
        nav a { color: #fff; margin: 0 1.2em; text-decoration: none; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
    </style>
    @yield('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">URL Shortener</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/shorten">Raccourcir</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/batch">Batch</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/stats">Stats</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/history">Historique</a>
                </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
                @auth
                    <li class="nav-item">
                        <span class="navbar-text me-2">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link" style="display:inline;">Déconnexion</button>
                        </form>
                    </li>
                @endauth
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Inscription</a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
@yield('content')
@yield('scripts')
</body>
</html>
