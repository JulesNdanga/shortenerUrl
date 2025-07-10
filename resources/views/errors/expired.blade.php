<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>URL expirée</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff3f3; color: #b71c1c; text-align: center; margin-top: 10vh; }
        h1 { font-size: 2.5em; }
        p { font-size: 1.2em; }
    </style>
</head>
<body>
    <h1>Ce lien a expiré</h1>
    <p>Le lien court <b>{{ url('/' . $short_code) }}</b> n'est plus valide car il a dépassé sa date d'expiration.</p>
</body>
</html>
