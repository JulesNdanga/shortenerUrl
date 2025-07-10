@extends('layouts.app')
@section('title', "Raccourcissement d'URL en masse")
@section('content')
<div class="container">
    <h1>Raccourcissement d'URL en masse</h1>
    <form id="batchForm">
        <label for="urls">Collez vos URLs (une par ligne) :</label>
        <textarea id="urls" placeholder="https://ex1.com\nhttps://ex2.com\n..."></textarea>
        <div id="options"></div>
        <button type="button" class="btn" onclick="prepareRows()">Préparer les options</button>
        <div id="rowsContainer"></div>
        <button type="submit" class="btn">Raccourcir toutes les URLs</button>
    </form>
    <div id="results"></div>
</div>
@endsection
@section('styles')
<style>
body { background: #f7f7f7; }
.container { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; padding: 2em; }
h1 { text-align: center; color: #2d3e50; }
textarea { width: 100%; min-height: 120px; font-size: 1em; margin-bottom: 1em; border-radius: 4px; border: 1px solid #ccc; padding: 0.5em; }
table { width: 100%; border-collapse: collapse; margin-top: 2em; }
th, td { border: 1px solid #eee; padding: 0.5em; text-align: left; }
th { background: #f0f0f0; }
.row-options { display: flex; gap: 0.5em; }
input[type="text"], input[type="datetime-local"] { width: 100%; box-sizing: border-box; }
.btn { background: #1976d2; color: #fff; border: none; border-radius: 4px; padding: 0.7em 1.5em; font-size: 1em; cursor: pointer; }
.btn:hover { background: #1257a6; }
.results-table td.status-created { color: #388e3c; }
.results-table td.status-exists { color: #fbc02d; }
.results-table td.status-error { color: #d32f2f; }
</style>
@endsection
@section('scripts')
<script>
function prepareRows() {
    const urlsText = document.getElementById('urls').value.trim();
    const rowsContainer = document.getElementById('rowsContainer');
    rowsContainer.innerHTML = '';
    if (!urlsText) return;
    const lines = urlsText.split(/\r?\n/).filter(l => l.trim() !== '');
    let table = '<table><thead><tr><th>URL</th><th>Code personnalisé (optionnel)</th><th>Date d\'expiration (optionnel)</th></tr></thead><tbody>';
    lines.forEach((url, idx) => {
        table += `<tr>
            <td><input type="text" name="url_${idx}" value="${url.replace(/&/g, '&amp;').replace(/</g, '&lt;')}" required></td>
            <td><input type="text" name="custom_${idx}" placeholder="ex: promo2025"></td>
            <td><input type="datetime-local" name="exp_${idx}"></td>
        </tr>`;
    });
    table += '</tbody></table>';
    rowsContainer.innerHTML = table;
}

document.getElementById('batchForm').onsubmit = async function(e) {
    e.preventDefault();
    const rows = document.querySelectorAll('#rowsContainer table tbody tr');
    if (!rows.length) { alert('Veuillez d\'abord préparer les options.'); return; }
    const items = [];
    rows.forEach((row, idx) => {
        const url = row.querySelector(`input[name="url_${idx}"]`).value.trim();
        const custom = row.querySelector(`input[name="custom_${idx}"]`).value.trim();
        const exp = row.querySelector(`input[name="exp_${idx}"]`).value;
        if (url) {
            const item = { url };
            if (custom) item.custom_code = custom;
            if (exp) item.expires_at = exp.replace('T', ' ');
            items.push(item);
        }
    });
    if (!items.length) { alert('Aucune URL valide.'); return; }
    const res = await fetch('/api/shorten/batch', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ items })
    });
    const data = await res.json();
    displayResults(data.results);
};

function displayResults(results) {
    if (!results || !results.length) {
        document.getElementById('results').innerHTML = '<p>Aucun résultat.</p>';
        return;
    }
    let html = '<table class="results-table"><thead><tr><th>URL d\'origine</th><th>Code court</th><th>URL courte</th><th>Statut</th><th>Message</th></tr></thead><tbody>';
    results.forEach(r => {
        html += `<tr>
            <td>${r.original_url ? r.original_url : ''}</td>
            <td>${r.short_code ? r.short_code : ''}</td>
            <td>${r.short_url ? `<a href="${r.short_url}" target="_blank">${r.short_url}</a>` : ''}</td>
            <td class="status-${r.status}">${r.status}</td>
            <td>${r.message ? r.message : ''}</td>
        </tr>`;
    });
    html += '</tbody></table>';
    document.getElementById('results').innerHTML = html;
}
</script>
@endsection
