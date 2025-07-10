@extends('layouts.app')
@section('title', 'Raccourcir une URL')
@section('content')
<div class="container">
    <h1>Raccourcir une URL</h1>
    <form id="shortenForm">
        <label>URL à raccourcir :</label>
        <input type="url" name="url" id="url" required placeholder="https://..." style="width:100%"><br><br>
        <label>Code personnalisé (optionnel) :</label>
        <input type="text" name="custom_code" id="custom_code" placeholder="ex: promo2025" style="width:100%"><br><br>
        <label>Date d'expiration (optionnelle) :</label>
        <input type="datetime-local" name="expires_at" id="expires_at" style="width:100%"><br><br>
        <button type="submit" class="btn">Raccourcir</button>
    </form>
    <div id="result"></div>
</div>
@endsection
@section('scripts')
<script>
document.getElementById('shortenForm').onsubmit = async function(e) {
    e.preventDefault();
    const url = document.getElementById('url').value.trim();
    const custom_code = document.getElementById('custom_code').value.trim();
    const expires_at = document.getElementById('expires_at').value;
    const payload = { url };
    if (custom_code) payload.custom_code = custom_code;
    if (expires_at) payload.expires_at = expires_at.replace('T', ' ');
    const res = await fetch('/api/shorten', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    });
    const data = await res.json();
    let html = '';
    if (data.short_url) {
        html += `<p><b>URL courte :</b> <a href="${data.short_url}" target="_blank">${data.short_url}</a></p>`;
        html += `<p><b>Code court :</b> ${data.short_code}</p>`;
        html += `<p><b>URL d'origine :</b> ${data.original_url}</p>`;
    } else if (data.message) {
        html += `<p style='color:orange'>${data.message}</p>`;
        if (data.short_url) {
            html += `<p><b>URL courte existante :</b> <a href="${data.short_url}" target="_blank">${data.short_url}</a></p>`;
        }
    } else if (data.error) {
        html += `<p style='color:red'>${data.error}</p>`;
    }
    document.getElementById('result').innerHTML = html;
};
</script>
@endsection
