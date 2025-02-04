# TaskLinker

## Description

TaskLinker est une plateforme web permettant de suivre et gérer les projets d'une entreprise. Cette version simplifiée ne comprend pas de gestion d'utilisateurs et se concentre sur la gestion des employés, des projets et des tâches. Le site permet de gérer l’équipe de l’entreprise, de créer des projets, d’ajouter des tâches et de suivre leur progression.

## Installation du projet

1. **Cloner le projet** :
   ```bash
   git clone https://github.com/QuentinDidelot/TaskLinker.git
1. Modifier le fichier _.env_ et renseigner vos informations de connexion à la base de données
2. Créer la base de données avec `php bin/console doctrine:database:create`
3. Appliquer les migrations avec `php bin/console doctirne:migrations:migrate`
4. Insérer les fixtures avec `php bin/console doctrine:fixtures:load`
5. Lancer le serveur

## Fonctionnalités 

1. ✅ Gestion de l’équipe : Liste des employés avec avatar, prénom, nom, e-mail et statut. Possibilité de modifier ou supprimer un employé. Lors de la suppression, l’employé est retiré des projets et tâches associés.
2. ✅ Gestion des projets : Liste des projets non archivés avec possibilité de création, modification et archivage. Lors de la création, on définit un titre et on invite des membres. Lors de la modification, le titre et les membres peuvent être ajustés.
3. ✅ Fiche d’un projet : Détail du projet avec la liste des employés associés et des tâches (To Do, Doing, Done). Possibilité de modifier ou supprimer des tâches et d'ajouter de nouvelles tâches.
4. ✅ Gestion des tâches : Ajout de tâches avec titre, description, date, statut et membre associé. Possibilité de modifier et supprimer des tâches depuis leur fiche.

## Technologies utilisées
1. HTML5
2. CSS3
3. PHP 8.3
4. MySQL
5. Symfony
   
✨ Projet réalisé dans le cadre du parcours OpenClassrooms.
