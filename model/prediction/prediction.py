import pandas as pd
import numpy as np
from sklearn.preprocessing import LabelEncoder
import joblib
from datetime import datetime

# Charger le modèle et les données
import os
script_dir = os.path.dirname(os.path.abspath(__file__))
model_dir = os.path.dirname(script_dir)

try:
    model = joblib.load(os.path.join(model_dir, 'weather_model.pkl'))
    villes_connues = joblib.load(os.path.join(model_dir, 'villes_connues.pkl'))
except FileNotFoundError as e:
    print(f"Erreur: Fichier non trouve - {e}")
    print("Assurez-vous d'avoir execute model.py d'abord pour creer les fichiers.")
    exit()
except Exception as e:
    print(f"Erreur lors du chargement du modele: {e}")
    exit()


df = pd.read_csv(os.path.join(model_dir, 'data', 'processed_data.csv'))
df.columns = ['forecast_timestamp', 'position', 'temperature_2m', 'humidity_2m', 
              'total_precipitation', 'wind_speed_10m', 'commune', 'date', 'hour', 
              'month', 'day_of_week', 'latitude', 'longitude']

# Preprocessing
df['forecast_timestamp'] = pd.to_datetime(df['forecast_timestamp'])
df = df.sort_values(['commune', 'forecast_timestamp'])


# Fonction de prédiction basée sur l'historique de la ville
def predict_weather(date_str, hour, ville, temperature_possible):
    
    if ville not in villes_connues:
        print(f"Ville '{ville}' non trouvée dans les données d'entraînement")
        print(f"Villes disponibles: {list(villes_connues)}")
        return None
    
    # Récupérer les 5 dernières observations de cette ville
    ville_data = df[df['commune'] == ville].sort_values('forecast_timestamp').tail(5)
    
    if len(ville_data) < 5:
        print(f"Pas assez de données historiques pour {ville}")
        return None
    
    # Parser la date
    date = datetime.strptime(date_str, '%Y-%m-%d')
    month = date.month
    day_of_week = date.weekday()
    
    # Créer les features d'entrée (dernières 5 observations + info temporelle)
    seq_features = ville_data[['temperature_2m', 'humidity_2m', 'total_precipitation', 
                              'wind_speed_10m', 'hour', 'month', 'day_of_week']].values
    
    # Remplacer la dernière température par celle fournie
    seq_features[-1, 0] = temperature_possible
    # Mettre à jour les informations temporelles pour la prédiction
    seq_features[-1, 4] = hour
    seq_features[-1, 5] = month
    seq_features[-1, 6] = day_of_week
    
    input_features = seq_features.flatten().reshape(1, -1)
    
    # Faire la prédiction
    prediction = model.predict(input_features)[0]
    
    # Retourner les résultats
    result = {
        'temperature_2m': prediction[0],
        'humidity_2m': prediction[1], 
        'total_precipitation': prediction[2],
        'wind_speed_10m': prediction[3]
    }
    
    return result

# Fonction de prédiction hebdomadaire cumulative
def weeklyPrediction(start_date_str, start_hour, ville, initial_temperature):
    """
    Prédit la météo pour 7 jours consécutifs
    Chaque jour utilise les 5 dernières données (historiques + prédictions précédentes)
    
    Args:
        start_date_str (str): Date de début au format 'YYYY-MM-DD'
        start_hour (int): Heure de début (0-23)
        ville (str): Nom de la commune
        initial_temperature (float): Température initiale pour le premier jour
    
    Returns:
        list: Liste des prédictions pour 7 jours
    """
    
    if ville not in villes_connues:
        print(f"Ville '{ville}' non trouvée dans les données d'entraînement")
        print(f"Villes disponibles: {list(villes_connues)}")
        return None
    
    # Récupérer les 5 dernières observations historiques de cette ville
    ville_data = df[df['commune'] == ville].sort_values('forecast_timestamp').tail(5)
    
    if len(ville_data) < 5:
        print(f"Pas assez de données historiques pour {ville}")
        return None
    
    # Convertir en liste pour faciliter les ajouts
    working_data = ville_data[['temperature_2m', 'humidity_2m', 'total_precipitation', 
                              'wind_speed_10m', 'hour', 'month', 'day_of_week']].values
    predictions = []
    current_date = datetime.strptime(start_date_str, '%Y-%m-%d')
    current_temp = initial_temperature
    
    # Prédire pour 7 jours à partir d'aujourd'hui
    for day in range(7):
        # Calculer les informations temporelles pour ce jour
        prediction_date = current_date + pd.Timedelta(days=day)
        month = prediction_date.month
        day_of_week = prediction_date.weekday()
        
        # Copier les données de travail et mettre à jour la dernière ligne
        current_data = working_data.copy()
        current_data[-1, 0] = current_temp    # temperature_2m
        current_data[-1, 4] = start_hour      # hour
        current_data[-1, 5] = month           # month
        current_data[-1, 6] = day_of_week     # day_of_week
        
        # Faire la prédiction
        input_features = current_data.flatten().reshape(1, -1)
        prediction = model.predict(input_features)[0]
        
        # Traduire les noms de jours en français
        day_names_fr = {
            'Monday': 'Lundi',
            'Tuesday': 'Mardi',
            'Wednesday': 'Mercredi',
            'Thursday': 'Jeudi',
            'Friday': 'Vendredi',
            'Saturday': 'Samedi',
            'Sunday': 'Dimanche'
        }
        
        # Stocker la prédiction
        day_prediction = {
            'date': prediction_date.strftime('%Y-%m-%d'),
            'day_name': day_names_fr[prediction_date.strftime('%A')],
            'temperature_2m': round(prediction[0], 2),
            'humidity_2m': round(prediction[1], 2),
            'total_precipitation': round(max(0, prediction[2]), 2),  # Pas de précipitations négatives
            'wind_speed_10m': round(max(0, prediction[3]), 2)  # Pas de vent négatif
        }
        
        predictions.append(day_prediction)
        
        # Mettre à jour les données pour le jour suivant
        new_row = np.array([prediction[0], prediction[1], prediction[2], prediction[3], 
                           start_hour, month, day_of_week])
        working_data = np.vstack([working_data[1:], new_row])
        current_temp = prediction[0]
    
    return predictions

# Interface en ligne de commande pour PHP
if __name__ == "__main__":
    import sys
    import json
    
    try:
        if len(sys.argv) < 2:
            print(json.dumps({"status": "error", "message": "Usage: python prediction.py <command> [args...]"}))
            sys.exit(1)
        
        command = sys.argv[1]
        
        if command == "predict":
            # Format: python prediction.py predict <date> <hour> <ville> <temperature>
            if len(sys.argv) != 6:
                print(json.dumps({"status": "error", "message": "Usage: python prediction.py predict <date> <hour> <ville> <temperature>"}))
                sys.exit(1)
            
            date_str = sys.argv[2]
            hour = int(sys.argv[3])
            ville = sys.argv[4]
            temperature = float(sys.argv[5])
            
            result = predict_weather(date_str, hour, ville, temperature)
            
            if result:
                print(json.dumps({"status": "success", "data": result}))
            else:
                print(json.dumps({"status": "error", "message": f"Prédiction impossible pour la ville '{ville}'"}))
        
        elif command == "weekly":
            # Format: python prediction.py weekly <date> <hour> <ville> <temperature>
            if len(sys.argv) != 6:
                print(json.dumps({"status": "error", "message": "Usage: python prediction.py weekly <date> <hour> <ville> <temperature>"}))
                sys.exit(1)
            
            date_str = sys.argv[2]
            hour = int(sys.argv[3])
            ville = sys.argv[4]
            temperature = float(sys.argv[5])
            
            result = weeklyPrediction(date_str, hour, ville, temperature)
            
            if result:
                print(json.dumps({"status": "success", "data": result}))
            else:
                print(json.dumps({"status": "error", "message": f"Prédiction hebdomadaire impossible pour la ville '{ville}'"}))
        
        else:
            print(json.dumps({"status": "error", "message": f"Commande inconnue: {command}. Utilisez 'predict' ou 'weekly'"}))
    
    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))
