# YWeather 🌤️
<br>
Description
YWeather est une application web intelligente de prévision météorologique qui combine l'analyse de données historiques avec des algorithmes de machine learning pour fournir des prédictions précises et personnalisées par ville.
<br>

![Afficher l'image](https://img.shields.io/badge/Status-En%20d%C3%A9veloppement-yellow)
![Afficher l'image](https://img.shields.io/badge/PHP-7.4+-blue)
![Afficher l'image](https://img.shields.io/badge/Python-3.8+-green)
![Afficher l'image](https://img.shields.io/badge/License-MIT-red)
<br>
🚀 Fonctionnalités

Prévisions météo intelligentes : Utilise un modèle Random Forest pour des prédictions précises
Interface immersive : Effets visuels avec Vanta.js
Recherche intuitive : Autocomplétion en temps réel des noms de villes
Prévisions multi-échelles : Vue journalière et hebdomadaire
Données enrichies : Température, humidité, précipitations, vitesse du vent
API RESTful : Architecture modulaire et extensible

<br>
📋 Prérequis

XAMPP 8.0+ (Apache, MySQL, PHP)
PHP 7.4 ou supérieur
Python 3.8 ou supérieur
Navigateur moderne (Chrome, Firefox, Safari, Edge)

<br>
🛠️ Installation
1. Cloner le repository
bashgit clone https://github.com/yourusername/yweather.git
cd yweather
<br>
2. Configuration de la base de données

Démarrer XAMPP et activer Apache + MySQL
Accéder à phpMyAdmin : http://localhost/phpmyadmin
Créer une base de données nommée yweather
Importer le fichier SQL :
sql-- Utiliser le fichier yweatherDb.sql fourni


<br>
3. Configuration Python
bash# Créer un environnement virtuel
python -m venv venv

# Activer l'environnement (Windows)
venv\Scripts\activate

# Activer l'environnement (Linux/Mac)
source venv/bin/activate

# Installer les dépendances
pip install -r requirements.txt
<br>
4. Import des données
Importer le fichier SQL dans model/data
<br>
5. Préparation des données
bash# Nettoyer les données brutes
python model/data/data_cleaner.py

# Importer les données dans MySQL
php model/data/import_data.php

# Entraîner le modèle ML
python model/model.py
<br>
⚙️ Configuration
Configuration de la base de données
Éditer le fichier src/db/db.php :
php<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "yweather";
<br>
Configuration Apache (.htaccess)
Le fichier .htaccess est déjà configuré pour :

Activer la réécriture d'URL
Bloquer l'accès aux fichiers sensibles
Configurer CORS

<br>
🚀 Utilisation
Démarrage de l'application

S'assurer que XAMPP est lancé (Apache + MySQL)
Placer le projet dans C:\xampp\htdocs\YWeather\
Accéder à l'application : http://localhost/YWeather/

<br>
Interface utilisateur

Page d'accueil : Rechercher une ville
Autocomplétion : Suggestions en temps réel
Page météo : Affichage des prévisions avec animations 3D

<br>
## 📁 Structure du projet

```text
YWeather/
├── API/
│   ├── city.php            # Page principale météo
│   ├── crud.php            # API CRUD pour les villes
│   ├── crudPlace.php       # Gestion des lieux
│   ├── predictions.php     # Interface PHP-Python
│   └── result.js           # JavaScript pour l'UI
│
├── assets/
│   ├── css/                # Feuilles de style
│   └── images/             # Images et icônes
│
├── model/
│   ├── data/
│   │   ├── data_cleaner.py     # Nettoyage des données
│   │   └── import_data.php     # Import en base
│   ├── prediction/
│   │   └── prediction.py       # Script de prédiction
│   └── model.py               # Entraînement du modèle
│
├── src/
│   └── db/
│       └── db.php           # Configuration base de données
│
├── .htaccess                # Configuration Apache
├── index.php                # Point d'entrée
└── README.md                # Ce fichier
```
<br>
🔌 API Documentation
Endpoints disponibles
MéthodeEndpointDescriptionGET/YWeather/{city}Récupère les infos d'une villeGET/YWeather/suggest/{query}Autocomplétion des villesGET/YWeather/city/{name}Page météo complètePOST/YWeather/Ajoute une nouvelle villePUT/YWeather/Met à jour une villeDELETE/YWeather/Supprime une ville
<br>
Exemple de réponse API
json{
    "status": "success",
    "value": [
        {
            "place_id": 1,
            "name": "Montpellier",
            "latitude": 43.6108,
            "longitude": 3.8767
        }
    ]
}
<br>
🤖 Module d'Intelligence Artificielle
Modèle utilisé

Algorithme : Random Forest Regressor
Bibliothèque : scikit-learn
Features : 35 dimensions (5 observations × 7 caractéristiques)
Cibles : Température, Humidité, Précipitations, Vitesse du vent

<br>
Utilisation du modèle
python# Prédiction simple
python model/prediction/prediction.py predict 2025-05-27 14 Montpellier 20.5

<br>
YWeather - Prévisions météo intelligentes 🌦️
<br>
