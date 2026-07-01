# MINING IA API (Laravel)

Backend Phase 1 — Option A (hybride) : auth, documents, API sync pour l'app Flutter.

## Prérequis

- PHP 8.3+ avec extension `pdo_mysql` activée
- Composer
- **WampServer** (MySQL) — ou MySQL / MariaDB équivalent

## Installation avec WampServer

1. Démarrez WampServer (icône verte dans la barre des tâches).

2. Créez la base `mining_ia` :
   - Ouvrez **phpMyAdmin** : http://localhost/phpmyadmin
   - Onglet **Bases de données** → nom : `mining_ia` → interclassement `utf8mb4_unicode_ci` → **Créer**

   Ou en SQL :

```sql
CREATE DATABASE mining_ia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. Configurez `.env` (copiez depuis `.env.example` si besoin) :

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mining_ia
DB_USERNAME=root
DB_PASSWORD=
```

> Mot de passe Wamp : souvent **vide** par défaut. Si vous en avez défini un, mettez-le dans `DB_PASSWORD`.

4. Générez la clé et migrez :

```powershell
cd "D:\projet flutter\mining-api"
php artisan key:generate
php artisan migrate
php artisan db:seed
```

5. Vérifiez la connexion :

```powershell
php artisan db:show
```

6. Lancez le serveur :

```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

Site : http://127.0.0.1:8000  
API : http://127.0.0.1:8000/api

## Comptes de démonstration

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@mining.local | Mining2026! |
| Éditeur | editeur@mining.local | Mining2026! |
| Utilisateur | utilisateur@mining.local | Mining2026! |

## Nouveau chat (portail web)

Menu **Nouveau chat** (`/chat`) — posez des questions sur les documents **publiés**.

Sans clé API : extraits pertinents avec sources. Avec clé API : réponses rédigées par IA.

Dans `.env` :

```
MINING_LLM_API_KEY=votre_cle_openai_ou_groq
MINING_LLM_BASE_URL=https://api.openai.com/v1
MINING_LLM_MODEL=gpt-4o-mini
```

Pour Groq : `MINING_LLM_BASE_URL=https://api.groq.com/openai/v1` et un modèle compatible.

## API mobile (Sanctum)

### Login
`POST /api/login`

### Register
`POST /api/register` — Body : `name`, `email`, `password`, `password_confirmation`

### Sync documents
`GET /api/documents/sync` — Header : `Authorization: Bearer {token}`

### Téléchargement
`GET /api/documents/{id}/download` — Header : `Authorization: Bearer {token}`

## Téléphone physique

Utilisez l'IP locale du PC (ex. `http://192.168.1.10:8000/api`) dans l'app Flutter.
