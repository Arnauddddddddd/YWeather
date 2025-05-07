import pandas as pd
import time
import numpy as np
from datetime import datetime
import joblib
import os

def load_model():
    try:
        # Display model file size for debugging
        model_path = 'data/model/weather_model.pkl'
        encoder_path = 'data/model/encoder_commune.pkl'
            
        model = joblib.load(model_path)
        encoder = joblib.load(encoder_path)
        return model, encoder
    except Exception as e:
        print(f"Error loading model: {e}")
        return None, None

def predict_weather(commune=None, lat=None, lon=None, date_str=None, input_temperature=None):
    """
    Predict weather using temperature and location information
    """
    start_time = time.time()
    
    # Load model
    model, encoder = load_model()
    if model is None or encoder is None:
        return {"error": "Failed to load prediction model"}
    
    # Handle input validation
    if date_str is None:
        date_str = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    
    # Determine which commune to use
    if commune is None:
        if lat is not None and lon is not None:
            # In a real implementation, we would find the nearest commune
            commune = "Paris"  # Default if coordinates provided but no lookup implemented
        else:
            return {"error": "No commune specified"}
    
    # Make sure we have a temperature
    if input_temperature is None:
        return {"error": "Temperature value is required for prediction"}
    
    try:
        temperature = float(input_temperature)
        
        # Parse date
        date_obj = pd.to_datetime(date_str)
        hour = date_obj.hour
        day = date_obj.day
        month = date_obj.month
        weekday = date_obj.weekday()
        
        # Check if commune is in encoder
        if commune not in encoder.classes_:
            return {"error": f"Unknown commune: {commune}"}
            
        # Encode commune
        commune_encoded = encoder.transform([commune])[0]
        
        # Prepare input data
        input_data = pd.DataFrame([{
            'commune_encoded': commune_encoded,
            'hour': hour,
            'day': day,
            'month': month,
            'weekday': weekday,
            'temperature_2m': temperature
        }])
        
        # Make predictions with the model
        predictions_array = model.predict(input_data)[0]
        
        # Create prediction result
        result = {
            "commune": commune,
            "date": date_obj.strftime('%Y-%m-%d'),
            "time": date_obj.strftime('%H:%M'),
            "latitude": lat if lat is not None else 0,
            "longitude": lon if lon is not None else 0,
            "weather": {
                "temperature_2m": temperature,
                "humidity_2m": round(predictions_array[0], 2),
                "total_precipitation": round(predictions_array[1], 2),
                "wind_speed_10m": round(predictions_array[2], 2)
            }
        }
        
        prediction_time = time.time() - start_time
        result["prediction_time"] = f"{prediction_time:.4f} seconds"
        
        return result
        
    except Exception as e:
        return {"error": f"Prediction error: {str(e)}"}

def display_weather(weather_data):
    if "error" in weather_data:
        print(f"Error: {weather_data['error']}")
        return
        
    print("\n===== Weather Prediction =====")
    print(f"Location: {weather_data['commune']} ({weather_data['latitude']:.4f}, {weather_data['longitude']:.4f})")
    print(f"Date: {weather_data['date']} at {weather_data['time']}")
    print("\nWeather conditions:")
    
    # Display each weather parameter with nice formatting
    weather = weather_data['weather']
    if 'temperature_2m' in weather:
        print(f"Temperature: {weather['temperature_2m']}°C")
    if 'humidity_2m' in weather:
        print(f"Humidity: {weather['humidity_2m']}%")
    if 'total_precipitation' in weather:
        print(f"Precipitation: {weather['total_precipitation']} mm")
    if 'wind_speed_10m' in weather:
        print(f"Wind speed: {weather['wind_speed_10m']} m/s")
        
    print(f"\nPrediction generated in {weather_data['prediction_time']}")

# Example usage
if __name__ == "__main__":
    # Predict with known temperature
    print("\nPrediction with known temperature:")
    weather1 = predict_weather(commune="Paris", input_temperature=25.0)
    display_weather(weather1)
    
    # Predict for current time and different location
    print("\nPrediction for different location:")
    weather2 = predict_weather(commune="Paris", date_str="2027-07-15 14:00:00", input_temperature=35.0)
    display_weather(weather2)
    
    # Predict with coordinates
    print("\nPrediction with coordinates:")
    weather3 = predict_weather(commune="Spycker", lat=43.2965, lon=5.3698, input_temperature=28.5)
    display_weather(weather3)

