@extends('layouts.app')
@section('title', 'Statistiques d\'URL')
@section('content')
<div class="container">
    <h1>Consulter les statistiques d'une URL courte</h1>
    <form id="statsForm">
        <label>Code court à analyser :</label>
        <input type="text" id="short_code" name="short_code" required placeholder="ex: promo2025" style="width:100%"><br><br>
        <button type="submit" class="btn">Voir les stats</button>
    </form>
    <div id="statsResult"></div>
</div>
@endsection
@section('scripts')
<script>
document.getElementById('statsForm').onsubmit = async function(e) {
    e.preventDefault();
    const code = document.getElementById('short_code').value.trim();
    if (!code) return;
    const res = await fetch(`/api/stats/${code}`, {
        headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    let html = '';
    if (data.original_url) {
        html += `<p><b>URL d'origine :</b> <a href="${data.original_url}" target="_blank">${data.original_url}</a></p>`;
        html += `<p><b>Code court :</b> ${data.short_code}</p>`;
        html += `<p><b>Nombre de clics :</b> ${data.click_count}</p>`;
        html += `<p><b>Créée le :</b> ${data.created_at}</p>`;
    } else if (data.error) {
        html += `<p style='color:red'>${data.error}</p>`;
    }
    document.getElementById('statsResult').innerHTML = html;
};
</script>
@endsection
