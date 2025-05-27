import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split
import joblib
from datetime import datetime
import time

# Charger les données depuis le fichier fourni
df = pd.read_csv('data/processed_data.csv')
df.columns = ['forecast_timestamp', 'position', 'temperature_2m', 'humidity_2m', 
              'total_precipitation', 'wind_speed_10m', 'commune', 'date', 'hour', 
              'month', 'day_of_week', 'latitude', 'longitude']

# Preprocessing
df['forecast_timestamp'] = pd.to_datetime(df['forecast_timestamp'])
df = df.sort_values(['commune', 'forecast_timestamp'])

# Encoder la commune
commune_encoder = LabelEncoder()
df['commune_encoded'] = commune_encoder.fit_transform(df['commune'])

# Version ultra-rapide avec pandas groupby et apply
def create_all_sequences_fast(df, seq_length=5):
    """Créer toutes les séquences en une seule opération vectorisée"""
    print("Utilisation de la méthode rapide...")
    
    # Trier une seule fois pour toutes les villes
    df_sorted = df.sort_values(['commune', 'forecast_timestamp']).reset_index(drop=True)
    
    features_cols = ['temperature_2m', 'humidity_2m', 'total_precipitation', 'wind_speed_10m', 'hour', 'month', 'day_of_week']
    target_cols = ['temperature_2m', 'humidity_2m', 'total_precipitation', 'wind_speed_10m']
    
    X_all = []
    y_all = []
    villes_valides = []
    
    # Grouper par ville une seule fois
    grouped = df_sorted.groupby('commune')
    total_villes = len(grouped)
    
    for i, (ville, ville_data) in enumerate(grouped):
        if len(ville_data) >= seq_length + 1:
            # Convertir en numpy pour les opérations rapides
            features_array = ville_data[features_cols].values
            target_array = ville_data[target_cols].values
            
            n_samples = len(ville_data) - seq_length
            
            if n_samples > 0:
                # Création vectorisée des séquences
                X_ville = np.array([features_array[j:j+seq_length].flatten() 
                                   for j in range(n_samples)])
                y_ville = target_array[seq_length:]
                
                X_all.append(X_ville)
                y_all.append(y_ville)
                villes_valides.append(ville)
        
        # Progression moins fréquente pour éviter les ralentissements
        if i % 10 == 0 or i == total_villes - 1:
            progress = (i + 1) / total_villes * 100
            print(f"\rTraitement des villes: {progress:.1f}% ({i+1}/{total_villes})", end="")
    
    return X_all, y_all, villes_valides

# Préparer les données d'entraînement pour toutes les villes (version optimisée)
print("Préparation des données d'entraînement...")
start_time = time.time()

# Utiliser la méthode rapide
X_all, y_all, villes_valides = create_all_sequences_fast(df)

prep_time = time.time() - start_time
print(f"\n✓ Préparation terminée en {prep_time:.1f} secondes!")
print(f"Villes traitées: {len(villes_valides)}")

if X_all:
    X_train = np.vstack(X_all)
    y_train = np.vstack(y_all)
    villes = np.array(villes_valides)
    
    print(f"Forme des données: X_train={X_train.shape}, y_train={y_train.shape}")
    
    # Diviser les données
    X_train_split, X_test, y_train_split, y_test = train_test_split(X_train, y_train, test_size=0.2, random_state=42)
else:
    print("Pas assez de données pour créer des séquences")
    exit()

# Créer et entraîner le modèle avec indicateur de progression
class ProgressRandomForest(RandomForestRegressor):
    def fit(self, X, y):
        print(f"Démarrage de l'entraînement avec {self.n_estimators} arbres...")
        start_time = time.time()
        
        # Entraîner arbre par arbre pour afficher la progression
        self.estimators_ = []
        self.n_features_ = X.shape[1]
        self.n_outputs_ = y.shape[1] if y.ndim > 1 else 1
        
        for i in range(self.n_estimators):
            tree_start = time.time()
            
            # Créer un arbre temporaire
            temp_forest = RandomForestRegressor(
                n_estimators=1, 
                random_state=self.random_state + i if self.random_state else None,
                n_jobs=1,
                max_depth=self.max_depth
            )
            temp_forest.fit(X, y)
            
            # Ajouter l'arbre à notre modèle
            self.estimators_.extend(temp_forest.estimators_)
            
            tree_time = time.time() - tree_start
            elapsed_time = time.time() - start_time
            
            # Calculer le temps restant estimé
            avg_time_per_tree = elapsed_time / (i + 1)
            remaining_trees = self.n_estimators - (i + 1)
            estimated_remaining = avg_time_per_tree * remaining_trees
            
            # Afficher la progression
            progress = (i + 1) / self.n_estimators * 100
            print(f"\rArbre {i+1}/{self.n_estimators} ({progress:.1f}%) - "
                  f"Temps écoulé: {elapsed_time:.1f}s - "
                  f"Temps restant estimé: {estimated_remaining:.1f}s", end="")
        
        total_time = time.time() - start_time
        print(f"\n✓ Entraînement terminé en {total_time:.1f} secondes!")
        
        return self

model = ProgressRandomForest(n_estimators=200, random_state=42, n_jobs=-1, max_depth=10)
model.fit(X_train_split, y_train_split)

# Convertir en RandomForestRegressor standard pour la sauvegarde
standard_model = RandomForestRegressor(n_estimators=20, random_state=42, n_jobs=-1, max_depth=10)
standard_model.estimators_ = model.estimators_
standard_model.n_features_ = model.n_features_
standard_model.n_outputs_ = model.n_outputs_

# Sauvegarder le modèle standard, l'encodeur et les villes connues
print("Sauvegarde des modèles...")
joblib.dump(standard_model, 'weather_model.pkl')
joblib.dump(commune_encoder, 'commune_encoder.pkl')
joblib.dump(villes, 'villes_connues.pkl')

print("✓ Modèle entraîné et sauvegardé avec succès!")
print(f"Score sur les données de test: {standard_model.score(X_test, y_test):.3f}")
print(f"Villes dans le dataset: {list(villes)}")

