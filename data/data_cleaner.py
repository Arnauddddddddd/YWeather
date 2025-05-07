import pandas as pd

data = pd.read_csv('data/meteo-0025.csv', sep=';')

column_mapping = {
    'Forecast timestamp': 'forecast_timestamp',
    'Position': 'position',
    'Forecast base': 'forecast_base',
    '2 metre temperature': 'temperature_2m',
    'Minimum temperature at 2 metres': 'min_temperature_2m',
    'Maximum temperature at 2 metres': 'max_temperature_2m',
    '2 metre relative humidity': 'humidity_2m',
    'Total precipitation': 'total_precipitation',
    '10m wind speed': 'wind_speed_10m',
    'Surface net solar radiation': 'surface_net_solar_radiation',
    'Surface net thermal radiation': 'surface_net_thermal_radiation',
    'Surface solar radiation downwards': 'surface_solar_radiation_downwards',
    'Surface latent heat flux': 'surface_latent_heat_flux',
    'Surface sensible heat flux': 'surface_sensible_heat_flux',
    'Commune': 'commune',
    'code_commune': 'code_commune'
}
data = data.rename(columns=column_mapping)

columns_to_drop = [
    'surface_net_solar_radiation',
    'surface_net_thermal_radiation',
    'surface_solar_radiation_downwards',
    'surface_latent_heat_flux',
    'surface_sensible_heat_flux',
    'forecast_base',
    'code_commune',
    'min_temperature_2m',
    'max_temperature_2m'
]
data = data.drop(columns=columns_to_drop, errors='ignore')

data = data.dropna(subset=['commune'])

print(data.columns)
print(data.head())

print(data.shape)

data = data.dropna(subset=['forecast_timestamp', 'position', 'temperature_2m', 'humidity_2m', 'total_precipitation', 'wind_speed_10m'])
print(data.shape)

# Convert forecast_timestamp to datetime and extract features
data['forecast_timestamp'] = pd.to_datetime(data['forecast_timestamp'])
data['date'] = data['forecast_timestamp'].dt.date
data['hour'] = data['forecast_timestamp'].dt.hour
data['month'] = data['forecast_timestamp'].dt.month
data['day_of_week'] = data['forecast_timestamp'].dt.dayofweek

# Extract latitude and longitude from position
data[['latitude', 'longitude']] = data['position'].str.split(',', expand=True).astype(float)

# Save processed data before model training
data.to_csv('data/processed_data.csv', index=False)
