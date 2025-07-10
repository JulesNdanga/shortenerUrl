# Raccourcisseur d'URL Laravel

## Présentation
Ce projet est un service de raccourcissement d'URL (type bit.ly/tinyurl) développé avec Laravel. Il permet de générer des liens courts, de rediriger vers l'URL d'origine et de suivre les statistiques de clics.

## Installation

### Prérequis
- PHP >= 8.1
- Composer
- MySQL
- (Optionnel) Postman ou curl pour tester l'API

### Étapes
1. Clonez le dépôt et placez-vous dans le dossier :
   ```bash
   git clone <repo_url>
   cd url-shortener
   ```
2. Installez les dépendances :
   ```bash
   composer install
   ```
3. Copiez le fichier `.env.example` en `.env` et configurez la connexion MySQL :
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=url_shortener
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Générez la clé d'application :
   ```bash
   php artisan key:generate
   ```
5. Créez la base de données MySQL (via phpMyAdmin ou ligne de commande).
6. Lancez les migrations :
   ```bash
   php artisan migrate
   ```
7. Démarrez le serveur :
   ```bash
   php artisan serve
   ```

## Interface utilisateur web (accès et navigation)

- **Accueil (`/`)** : page d’introduction et navigation vers toutes les fonctionnalités.
- **Raccourcir une URL (`/shorten`)** : formulaire pour raccourcir une URL unique, avec code personnalisé et date d’expiration.
- **Raccourcissement en masse (`/batch`)** : formulaire pour coller plusieurs URLs, options avancées par ligne, résultats en tableau.
- **Statistiques (`/stats`)** : formulaire pour consulter les statistiques d’un code court.
- **Historique (`/history`)** : tableau listant tous les liens générés (code, URL d’origine, URL courte, date, clics, expiration).
- Toutes ces pages sont intégrées à Laravel (Blade, navigation commune).

## Tests automatisés (Feature/API)

Des tests automatisés couvrent l'API (authentification, création de liens, historique, etc.) dans `tests/Feature`.

- **Lancer tous les tests :**
  ```bash
  php artisan test --testsuite=Feature
  ```
- Les tests utilisent une base SQLite en mémoire et réinitialisent la base à chaque exécution.
- Pour permettre les tests API avec Sanctum, le middleware `EnsureFrontendRequestsAreStateful` est commenté dans le groupe `api` du Kernel. **Il faut le réactiver pour la production.**

## Déploiement et développement avec Docker

Le projet est prêt à être lancé dans des conteneurs Docker avec MySQL :

- Lancez simplement :
  ```bash
  docker-compose up --build
  ```
- L'application Laravel sera accessible sur le port 9000 (modifiable dans docker-compose.yml).
- La base de données MySQL est fournie par le service `db` et persiste grâce à un volume Docker.

**Exemple de variables d'environnement (.env) pour Docker :**
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=urlshortener
DB_USERNAME=user
DB_PASSWORD=password
```

Adaptez le reste de votre `.env` selon vos besoins (clé, mail, etc.).

## Prévisualisation et sécurité des URL

Chaque URL courte dispose d'une page de prévisualisation accessible via `/preview/{short_code}` :
- Affiche l'URL d'origine et le code court.
- Vérifie si l'URL cible est en HTTPS, si elle contient des mots-clés suspects (blacklist), et le statut HTTP de la cible.
- Affiche un bouton "Continuer vers le site" désactivé si l'URL est blacklistée.
- Permet à l'utilisateur de vérifier la sécurité avant de visiter la destination.

## Documentation API (Swagger)

Une documentation interactive de l'API est intégrée au projet !

- Accède à la doc Swagger sur : [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation) après avoir démarré le serveur Laravel (`php artisan serve`).
- Tu peux explorer, tester tous les endpoints, consulter les schémas, et récupérer les exemples de requêtes/réponses.
- La doc est générée automatiquement à partir des annotations présentes dans les contrôleurs API.
- Pour régénérer la doc manuellement :
  ```bash
  vendor/bin/openapi --output storage/api-docs/api-docs.json app/Http/Controllers/Api
  ```
- Plus de fichier openapi.yaml : tout est centralisé et interactif via Swagger UI.

## Pages protégées et Administration

Certaines pages (raccourcissement, batch, historique) sont accessibles uniquement aux utilisateurs connectés.

Une interface d'administration est disponible :
- Accès via `/admin/login` (login par défaut : admin@admin.com / admin123, à créer dans la base).
- Dashboard admin pour gérer les utilisateurs et liens (évolutif).

Les formulaires d'authentification affichent des messages de succès/erreur UX-friendly.

## Authentification & Propriété des URLs

Le service propose une authentification API (Laravel Sanctum) :
- Enregistrement (`POST /api/register`) et connexion (`POST /api/login`) : chaque utilisateur obtient un token d’accès.
- Les endpoints de création d’URL courte, de batch et d’historique sont réservés aux utilisateurs authentifiés (via le token).
- Chaque URL créée est associée à l’utilisateur connecté (champ `user_id`).
- L’historique (`/api/history`) ne retourne que les liens de l’utilisateur connecté.
- Un endpoint de déconnexion (`POST /api/logout`) permet de révoquer le token.

Cela garantit la confidentialité, la sécurité et la propriété des liens générés.

## Limitation du débit (Rate Limiting)

Les endpoints critiques de l’API sont protégés contre les abus :
- **POST `/api/shorten`** et **POST `/api/shorten/batch`** : maximum 10 requêtes par minute par IP.
- En cas de dépassement, le serveur retourne une réponse **HTTP 429 Too Many Requests** avec un message d’erreur standard Laravel.
- Cette limitation protège le service contre les usages excessifs ou malveillants.

## Endpoints API

- **POST `/api/shorten`**

- **POST `/api/shorten/batch`** (raccourcissement en masse)
  - Corps : `{ "items": [ { "url": "https://ex1.com", "custom_code": "promo1", "expires_at": "2025-07-15 23:59:59" }, { "url": "https://ex2.com" } ] }`
  - Chaque objet peut contenir `url` (obligatoire), `custom_code` (optionnel), `expires_at` (optionnel).
  - Réponse :
    ```json
    {
      "results": [
        { "status": "created", "short_url": "http://localhost:8000/promo1", "original_url": "https://ex1.com", "short_code": "promo1" },
        { "status": "created", "short_url": "http://localhost:8000/abc123", "original_url": "https://ex2.com", "short_code": "abc123" }
      ]
    }
    ```
  - Si un code existe déjà :
    ```json
    { "status": "exists", "message": "Ce code court personnalisé existe déjà.", ... }
    ```
  - Si erreur sur une entrée :
    ```json
    { "status": "error", "original_url": "...", "message": "..." }
    ```
  - Corps : `{ "url": "https://example.com/very/long/url", "custom_code": "monalias2025" }`
    - `custom_code` est optionnel (alphanumérique, 4 à 32 caractères). Si fourni et unique, il sera utilisé comme code court.
    - `expires_at` est optionnel (format ISO8601 ou `YYYY-MM-DD HH:MM:SS`). Si fourni, le lien expirera à cette date et ne sera plus accessible après.
    - Si le code personnalisé existe déjà, l'API retourne une réponse 200 avec le mapping existant et un message explicite (pas d'erreur).
  - Exemple de réponse si le code existe déjà :
    ```json
    {
      "message": "Ce code court personnalisé existe déjà.",
      "short_url": "http://localhost:8000/monalias2025",
      "original_url": "https://example.com/long/url",
      "short_code": "monalias2025"
    }
    ```
  - Réponse standard (création réussie) : `{ "short_url": "http://localhost:8000/monalias2025", "original_url": "...", "short_code": "monalias2025" }`

- **GET `/api/stats/{short_code}`**
  - Réponse : `{ "original_url": "...", "short_code": "...", "click_count": 5, "created_at": "..." }`

- **GET `/{short_code}`**
  - Redirige vers l'URL d'origine et incrémente le compteur de clics

## Utilisation rapide
1. Créez une URL courte :
   ```bash
   curl -X POST http://localhost:8000/api/shorten -H "Content-Type: application/json" -d '{"url":"https://example.com"}'
   ```
2. Visitez `http://localhost:8000/abc123` dans le navigateur pour être redirigé.
3. Consultez les stats :
   ```bash
   curl http://localhost:8000/api/stats/abc123
   ```

## Hypothèses et choix techniques
- Génération de codes courts aléatoires (6 caractères)
- Stockage du mapping et des clics en base MySQL
- Gestion des erreurs et validation des entrées
- Code commenté en français pour l'entretien
- Structure Laravel standard (modèles, migrations, contrôleurs, routes)

## Structure du projet
- `app/Models/ShortUrl.php` : modèle principal
- `app/Models/Click.php` : suivi des clics
- `app/Http/Controllers/Api/ShortUrlController.php` : API
- `app/Http/Controllers/RedirectController.php` : redirection
- `routes/api.php` : endpoints API
- `routes/web.php` : redirection
- `database/migrations/` : schéma de la base

## Contact
Pour toute question, contactez l'auteur du projet.

- **[Jules NDANGA](julesndanga7@gail.com)**




