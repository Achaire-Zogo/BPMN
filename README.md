# BPMN Sales Process System

Un système de gestion des processus de vente basé sur BPMN, développé avec Laravel Par ZAZ.

## 🚀 Fonctionnalités

- **Modélisation BPMN**
  - Création et édition de diagrammes BPMN
  - Interface de modélisation intuitive avec bpmn-js
  - Sauvegarde automatique des diagrammes

- **Gestion des Processus de Vente**
  - Suivi en temps réel des processus
  - Gestion automatisée des stocks
  - Génération de reçus
  - Interface utilisateur moderne et responsive

- **Gestion des Données**
  - Gestion des clients
  - Gestion des produits
  - Historique des ventes
  - Suivi des instances de processus

## 📋 Prérequis

- PHP 8.x
- Composer
- MySQL
- Node.js et NPM (pour les assets front-end)

## 🛠 Installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/Achaire-Zogo/BPMN.git
   cd bpmn-sales
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configuration de l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configuration de la base de données**
   - Créer une base de données MySQL
   - Mettre à jour le fichier .env avec vos informations de connexion

5. **Migrations et données de test**
   ```bash
   php artisan migrate
   php artisan db:seed --class=TestDataSeeder
   ```

6. **Démarrer le serveur**
   ```bash
   php artisan serve
   ```

## 🏗 Structure du Projet

```
BPMN/
├── app/
│   ├── Http/Controllers/      # Contrôleurs
│   ├── Models/               # Modèles Eloquent
│   └── Services/            # Services métier
├── database/
│   ├── migrations/          # Migrations de base de données
│   └── seeders/            # Seeders de données
├── resources/
│   └── views/              # Vues Blade
└── routes/
    └── web.php             # Routes de l'application
```

## 📱 Utilisation

1. **Création d'un diagramme BPMN**
   - Accédez à la page des diagrammes
   - Cliquez sur "Nouveau Diagramme"
   - Utilisez l'éditeur BPMN pour modéliser votre processus

2. **Démarrage d'un processus de vente**
   - Sélectionnez un diagramme
   - Cliquez sur l'icône de panier
   - Remplissez les informations de vente
   - Suivez l'avancement du processus

## 🔒 Sécurité

- Protection CSRF sur tous les formulaires
- Validation des entrées utilisateur
- Gestion sécurisée des sessions
- Transactions base de données

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet
2. Créez une branche pour votre fonctionnalité
3. Committez vos changements
4. Poussez vers la branche
5. Ouvrez une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 👥 Auteurs

- [Votre Nom](https://github.com/votre-username)

## 🙏 Remerciements

- [Laravel](https://laravel.com) - Le framework PHP
- [bpmn-js](https://github.com/bpmn-io/bpmn-js) - La bibliothèque de modélisation BPMN
- [Tailwind CSS](https://tailwindcss.com) - Pour le style