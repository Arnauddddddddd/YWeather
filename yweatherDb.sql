CREATE TABLE IF NOT EXISTS `Place` (
  `place_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(50),
  `latitude` float,
  `longitude` float
);

CREATE TABLE IF NOT EXISTS `Time` (
  `time_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `day` timestamp,
  `hour` timestamp
);

CREATE TABLE IF NOT EXISTS `Weather` (
  `weather_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `temperature` float,
  `precipitation` float,
  `state` varchar(50),
  `wind` float,
  `humidity` float
);

CREATE TABLE IF NOT EXISTS `Place_Time_Weather` (
  `place_time_weather_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `time_id` text,
  `place_id` text,
  `weather_id` text
);

