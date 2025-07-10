@extends('layouts.app')
@section('title', 'Historique des liens générés')
@section('content')
<div class="container">
    <h1>Historique des liens générés</h1>
    <table id="historyTable" class="results-table">
        <thead>
            <tr>
                <th>Code court</th>
                <th>URL d'origine</th>
                <th>URL courte</th>
                <th>Date de création</th>
                <th>Nombre de clics</th>
                <th>Expiration</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection
@section('scripts')
<script>
async function fetchHistory() {
    const res = await fetch('/api/history', {headers: {'Accept': 'application/json'}});
    const data = await res.json();
    let html = '';
    if (data && data.length) {
        data.forEach(row => {
            html += `<tr>
                <td>${row.short_code}</td>
                <td><a href="${row.original_url}" target="_blank">${row.original_url}</a></td>
                <td><a href="/${row.short_code}" target="_blank">/${row.short_code}</a></td>
                <td>${row.created_at}</td>
                <td>${row.click_count}</td>
                <td>${row.expires_at ? row.expires_at : ''}</td>
            </tr>`;
        });
    } else {
        html = '<tr><td colspan="6">Aucun lien généré pour le moment.</td></tr>';
    }
    document.querySelector('#historyTable tbody').innerHTML = html;
}
fetchHistory();
</script>
@endsection
