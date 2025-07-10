@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4>Prévisualisation de l'URL courte</h4>
        </div>
        <div class="card-body">
            <p><strong>Code court :</strong> {{ $short_code }}</p>
            <p><strong>URL d'origine :</strong> <a href="{{ $original_url }}" target="_blank" rel="noopener noreferrer">{{ $original_url }}</a></p>

            <h5 class="mt-4">Vérification sécurité :</h5>
            <ul>
                <li>
                    <strong>HTTPS :</strong>
                    @if($security['is_https'])
                        <span class="text-success">Oui</span>
                    @else
                        <span class="text-danger">Non</span>
                    @endif
                </li>
                <li>
                    <strong>Blacklist :</strong>
                    @if($security['is_blacklisted'])
                        <span class="text-danger">URL potentiellement dangereuse</span>
                    @else
                        <span class="text-success">Aucun mot-clé suspect détecté</span>
                    @endif
                </li>
                <li>
                    <strong>Statut HTTP :</strong>
                    @if($security['http_status'])
                        {{ $security['http_status'] }}
                    @elseif($security['error'])
                        <span class="text-warning">Erreur : {{ $security['error'] }}</span>
                    @else
                        <span class="text-muted">Non vérifié</span>
                    @endif
                </li>
            </ul>

            <div class="mt-4">
                <a href="{{ $original_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-success" @if($security['is_blacklisted']) disabled @endif>
                    Continuer vers le site
                </a>
                <a href="/" class="btn btn-secondary">Retour</a>
            </div>
        </div>
    </div>
</div>
@endsection
