import pandas as pd
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.multioutput import MultiOutputRegressor
from sklearn.metrics import r2_score
import joblib

# --- --- --- --- --- --- --- --- --- --- --- #
# Load and prepare data
# --- --- --- --- --- --- --- --- --- --- --- #

print("Loading and preparing data...")
data = pd.read_csv('data/processed_data.csv')
data['forecast_timestamp'] = pd.to_datetime(data['forecast_timestamp'])

# Extract time features
data['hour'] = data['forecast_timestamp'].dt.hour
data['day'] = data['forecast_timestamp'].dt.day
data['month'] = data['forecast_timestamp'].dt.month
data['weekday'] = data['forecast_timestamp'].dt.weekday

# Encode commune names
encoder = LabelEncoder()
data['commune_encoded'] = encoder.fit_transform(data['commune'])

# --- --- --- --- --- --- --- --- --- --- --- #
# WEATHER MODEL: Single model that predicts all weather parameters 
# --- --- --- --- --- --- --- --- --- --- --- #

print("\nTraining weather prediction model...")

# Features for weather model
features = ['commune_encoded', 'hour', 'day', 'month', 'weekday', 'temperature_2m']

# Prepare training data - all weather parameters except temperature
targets_to_predict = ['humidity_2m', 'total_precipitation', 'wind_speed_10m']

# Create X and y for weather model
X = data[features]
y = data[targets_to_predict]

# Split into train/test sets
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
print(f"Training data shape: {X_train.shape}, {y_train.shape}")
# Train a multi-output random forest model
weather_model = MultiOutputRegressor(RandomForestRegressor(
    n_estimators=50, 
    max_depth=10,
    min_samples_leaf=5,
    n_jobs=-1,
    random_state=42
))
print("Fitting the model...")
weather_model.fit(X_train, y_train)
print("Model training completed.")
# Evaluate the model
y_pred = weather_model.predict(X_test)

# Calculate R² for each target
print("Model performance:")
r2_scores = {}
for i, target in enumerate(targets_to_predict):
    r2_scores[target] = r2_score(y_test.iloc[:, i], y_pred[:, i])
    print(f"- {target} R² score: {r2_scores[target]:.4f}")

# Save the weather model
joblib.dump(weather_model, 'data/model/weather_model.pkl')
joblib.dump(encoder, 'data/model/encoder_commune.pkl')

print("\nWeather model saved successfully as 'weather_model.pkl'")
