# ITAM — Système de Gestion de Parc Informatique

Application web de gestion du parc informatique développée avec Laravel 13.
Projet de fin de formation — ISTA Med Elfassi — Avril 2026.

**Étudiant:** Abdelouafi Louardi  
**Encadrant:** Mr Anwar  
**Formation:** Développement Web & Logiciel

---

## Technologies utilisées

| Composant       | Technologie             |
| --------------- | ----------------------- |
| Backend         | PHP 8.4 + Laravel 13    |
| Base de données | MySQL 8                 |
| Frontend        | Bootstrap 5 + Chart.js  |
| Serveur local   | Laravel Herd (Nginx)    |
| Export PDF      | barryvdh/laravel-dompdf |
| Email local     | Mailpit                 |

---

## Fonctionnalités

### Authentification (RF-01 à RF-05)

- Inscription, connexion, déconnexion sécurisée
- Réinitialisation du mot de passe par email
- Limitation à 5 tentatives de connexion
- Expiration de session après inactivité
- Protection CSRF sur tous les formulaires

### Gestion des équipements (RF-06 à RF-11)

- CRUD complet pour les catégories et produits
- Différenciation Hardware / Software
- Gestion du stock par quantité
- Historique complet des affectations

### Affectations (RF-12 à RF-16)

- Affectation d'équipements aux employés
- Vérification de disponibilité avant affectation
- Workflow de retour avec mise à jour du stock
- Vue personnalisée par rôle

### Licences logicielles (RF-17 à RF-20)

- Suivi des sièges utilisés / disponibles
- Alertes automatiques à 30 jours d'expiration
- Notification email automatique quotidienne

### Maintenance (RF-21 à RF-23)

- Enregistrement des interventions
- Suivi des coûts de maintenance
- Retrait définitif d'équipement (irréversible)

### Reporting (RF-24 à RF-28)

- Tableau de bord avec statistiques en temps réel
- Rapport d'inventaire des actifs (filtrable)
- Rapport de conformité des licences
- Rapport des coûts de maintenance
- Export PDF et CSV pour chaque rapport

---

## Rôles et permissions

| Module               | Administrateur | Technicien | Manager | Employé |
| -------------------- | :------------: | :--------: | :-----: | :-----: |
| Tableau de bord      |       ✅       |     ✅     |   ✅    |   ✅    |
| Gestion utilisateurs |       ✅       |     ❌     |   ❌    |   ❌    |
| Équipements          |       ✅       |     ✅     |   👁    |   👁    |
| Affectations         |       ✅       |     ✅     |   👁    |  👁\*   |
| Licences             |       ✅       |     ✅     |   👁    |   ❌    |
| Maintenance          |       ✅       |     ✅     |   👁    |   ❌    |
| Rapports             |       ✅       |     ❌     |   ✅    |   ❌    |
| Configuration        |       ✅       |     ❌     |   ❌    |   ❌    |

\*L'employé voit uniquement ses propres affectations

---

## Installation

### Prérequis

- PHP 8.3 ou supérieur
- Composer
- MySQL 8
- Node.js 18+
- Laravel Herd (recommandé) ou tout serveur Nginx/Apache

### Étapes d'installation

**1 — Cloner le dépôt**

```bash
git clone https://github.com/Abdelouafi-L/itam.git
cd itam
```

**2 — Installer les dépendances PHP**

```bash
composer install
```

**3 — Installer les dépendances JavaScript**

```bash
npm install
npm run build
```

**4 — Configurer l'environnement**

```bash
cp .env.example .env
php artisan key:generate
```

Ouvrir `.env` et configurer la base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=itam
DB_USERNAME=root
DB_PASSWORD=
```

Configurer l'email (Mailpit pour le développement) :

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@techcorp.ma"
MAIL_FROM_NAME="ITAM TechCorp"
```

**5 — Créer la base de données**

```bash
mysql -u root -e "CREATE DATABASE itam CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**6 — Exécuter les migrations et les seeders**

```bash
php artisan migrate --seed
```

**7 — Démarrer le serveur**

Avec Laravel Herd — le site est automatiquement disponible à : http://itam.test

Sans Herd :

```bash
php artisan serve
```

Puis ouvrir `http://localhost:8000`

---

## Comptes de démonstration

| Rôle           | Email               | Mot de passe |
| -------------- | ------------------- | ------------ |
| Administrateur | admin@techcorp.ma   | password123  |
| Technicien     | tech@techcorp.ma    | password123  |
| Manager        | manager@techcorp.ma | password123  |
| Employé        | employe@techcorp.ma | password123  |

---

## Notification automatique des licences

La commande suivante vérifie les licences expirant dans 30 jours
et envoie une notification email aux administrateurs :

```bash
php artisan itam:check-expiring-licenses
```

En production, ajouter au crontab : cd /path/to/itam && php artisan schedule:run >> /dev/null 2>&1

---

## Structure du projet

app/
├── Console/Commands/ # Commandes artisan personnalisées
├── Http/
│ ├── Controllers/ # Contrôleurs MVC
│ └── Middleware/ # Middleware d'authentification et rôles
├── Models/ # Modèles Eloquent (14 tables)
├── Notifications/ # Notifications email
database/
├── migrations/ # 18 migrations
├── seeders/ # 8 seeders avec données réalistes
resources/views/
├── assignments/ # Vues affectations
├── auth/ # Vues authentification
├── categories/ # Vues catégories
├── layouts/ # Layout principal
├── licenses/ # Vues licences
├── maintenances/ # Vues maintenance
├── products/ # Vues produits
├── reports/ # Vues rapports (HTML + PDF)
routes/
├── web.php # Routes web avec middleware
└── console.php # Tâches planifiées

---

## Diagrammes UML

- Diagramme de classes — 14 tables
- Diagramme de cas d'utilisation — 4 acteurs
- Diagramme de séquence — workflow d'affectation

---

_Développé dans le cadre de la formation Développement Web & Logiciel_  
_ISTA Med Elfassi — Avril 2026_
