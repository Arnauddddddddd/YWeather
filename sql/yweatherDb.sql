CREATE TABLE `Place` (
  `place_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(50),
  `latitude` float,
  `longitude` float
);

CREATE TABLE `Time` (
  `time_id` integer KEY NOT NULL AUTO_INCREMENT,
  `day` timestamp,
  `hour` timestamp
);

CREATE TABLE `Weather` (
  `weather_id` integer KEY NOT NULL AUTO_INCREMENT,
  `temperature` float,
  `precipitation` float,
  `state` varchar(50),
  `wind` float,
  `humidity` float
);

CREATE TABLE `Place_Time_Weather` (
  `place_time_weather_id` text KEY NOT NULL AUTO_INCREMENT,
  `time_id` text,
  `place_id` text,
  `weather_id` text,
  FOREIGN KEY (time_id) REFERENCES Time(time_id),
  FOREIGN KEY (place_id) REFERENCES Place(place_id),
  FOREIGN KEY (weather_id) REFERENCES Weather(weather_id)
);

