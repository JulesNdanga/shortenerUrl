@extends('layouts.app')
@section('title', 'Accueil - Raccourcisseur d’URL')
@section('content')
<div class="container">
    <h1>Bienvenue sur le raccourcisseur d’URL</h1>
    <p>Créez, gérez et suivez vos liens courts facilement.</p>
    <ul>
        <li><a href="{{ url('/shorten') }}">Raccourcir une URL</a></li>
        <li><a href="{{ url('/batch') }}">Raccourcissement en masse</a></li>
        <li><a href="{{ url('/stats') }}">Consulter les statistiques</a></li>
    </ul>
</div>
@endsection
