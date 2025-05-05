# 🍽️ Projet Web : Site de gestion multilingue de recettes

Ce projet est un site web dynamique permettant aux utilisateurs de créer, consulter, modifier, traduire et gérer des recettes en français et en anglais. Il inclut une gestion de rôles avancée (Chef, Traducteur, Admin).

## 🌐 Fonctionnalités principales

- 🔐 Authentification / Inscription
- 👨‍🍳 Création, modification, suppression des recettes (Chef)
- 🌍 Traduction bilingue des recettes (Traducteur ou Admin)
- 🧾 Interface détaillée par recette
- 🧑‍💼 Gestion des utilisateurs et des rôles (Admin)
- ❤️ Commentaires, likes, et filtres diététiques
- 🖼️ Ajout / suppression d’images de recette
- 📊 Statistiques de l'application (Admin)

---

## 📁 Structure du projet

```
projet/
│
├── back/                     # Backend PHP (API)
│   ├── controllers/          # Contrôleurs PHP
│   │   ├── AuthController.php      # Connexion / inscription
│   │   ├── RecipeController.php    # Gestion des recettes
│   │   └── AdminController.php     # Rôles, validation
│   ├── data/                 # Données persistées (JSON)
│   └── index.php            # Routeur principal (API)
│
├── front/                   # Interface utilisateur (Frontend)
│   ├── assets/
│   │   ├── css/             # Feuilles de style
│   │   └── js/              # Scripts JS (optionnel)
│   ├── auth/                # Pages de connexion / inscription
│   ├── admin/               # Espace admin (gestion utilisateurs, recettes)
│   ├── chef/                # Pages spécifiques au rôle Chef
│   ├── dashboard.html       # Tableau de bord multirôle
│   ├── index.html           # Page d’accueil
│   ├── recipes.html         # Liste de toutes les recettes
│   ├── recipe_details.html  # Détail d’une recette
│   └── translation.html     # Interface spéciale de traduction à 2 colonnes
```



##👥 Gestion des rôles

| Rôle         | Permissions                                                                                                                                     |
|--------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| `Cuisinier`  | Accès public uniquement                                                                                                                         |
| `Chef`       | Ajouter / modifier / supprimer ses recettes                                                                                                     |
| `Traducteur` | Traduire uniquement les champs vides des recettes validées, sauf s’il est aussi Chef (alors tout modifier dans ses propres recettes)           |
| `Admin`      | Gérer les utilisateurs, valider les recettes, traduire, modifier toutes les recettes, supprimer n’importe quelle recette, voir les statistiques |

---

## ⚙️ Lancer le projet localement

1. Cloner le dépôt
2. Lancer un serveur PHP :
   ```bash
   php -S localhost:8000
Accéder à :


http://localhost:8000/front/auth/login.html
🧪 Accès test rapide
Dans le fichier back/data/users.json :

Nom d'utilisateur	Mot de passe	Rôles
admin	admin	["Admin", "Traducteur"]
chef1	test	["Chef"]
trad1	test	["Traducteur"]

📌 Notes techniques
Toutes les données sont enregistrées dans des fichiers .json

Les appels frontend vers le backend utilisent fetch ou $.ajax (jQuery)

Chaque fonctionnalité respecte les permissions selon les rôles

L’interface de traduction est responsive et conforme aux consignes d'affichage bilingue

📸 Capture d'écran
(Ajoutez ici des captures si nécessaire)

👨‍💻 Réalisé par
Projet académique réalisé par Huiying et Massyl, dans le cadre du cours Programmation Web - L3 MIAGE.

### 🚀 Démo en ligne

🖥️ [https://drive.google.com/file/d/1iIHh9cugGrvGLhaTnvb0t5ot2MFq25fD/view?usp=drive_link)


