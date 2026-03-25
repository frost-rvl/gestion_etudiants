# Gestion des Étudiants

Application web PHP de gestion de fiches étudiantes avec authentification, stockage CSV et interface Tailwind CSS.

## Fonctionnalités

- Authentification avec sessions PHP et mots de passe hachés (bcrypt)
- Liste des étudiants avec photo, nom, téléphone et email
- Ajout, modification et suppression de fiches étudiantes
- Upload de photo par drag & drop avec mise à jour AJAX
- Import groupé depuis un fichier CSV avec photos
- Export de la liste en CSV
- Gestion des utilisateurs réservée aux administrateurs
- Suppression multiple par cases à cocher

## Structure

```
├── index.php
├── auth/
│   ├── login.php
│   └── logout.php
├── main/
│   ├── dashboard.php
│   ├── add.php
│   ├── detail.php
│   ├── delete.php
│   ├── users.php
│   └── components/
│       ├── navbar.php
│       └── importModal.php
├── data/
│   ├── etudiants.csv
│   └── users.csv
├── photos/
├── utils/
│   └── upload_photo.php
└── styles/
    └── output.css
```

## Installation

### Avec Docker

```bash
docker-compose up -d
```

L'application est accessible sur http://localhost:8080.

### Sans Docker

Prérequis : PHP 8.x, Apache ou Nginx.

```bash
npm install
npm run build
php -S localhost:8000
```

## Connexion par défaut

| Utilisateur | Mot de passe | Rôle  |
|-------------|--------------|-------|
| admin       | 123456       | Admin |

Changer le mot de passe dès le premier déploiement via la page de gestion des utilisateurs.

## Format CSV étudiant

```
id,nom,prenom,telephone,email,photo
```

## Technologies

- Backend : PHP 8 natif
- Stockage : fichiers CSV
- Frontend : Tailwind CSS
- Conteneur : Docker / Apache
