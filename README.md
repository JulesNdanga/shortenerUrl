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

## Endpoints API

- **POST `/api/shorten`**
  - Corps : `{ "url": "https://example.com/very/long/url" }`
  - Réponse : `{ "short_url": "http://localhost:8000/abc123", "original_url": "...", "short_code": "abc123" }`

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

- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
