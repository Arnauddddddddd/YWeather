CREATE TABLE `Place` (
  `place_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50),
  `latitude` FLOAT,
  `longitude` FLOAT,
  PRIMARY KEY (`place_id`)
);

CREATE TABLE `Time` (
  `time_id` INT NOT NULL AUTO_INCREMENT,
  `day` DATE,
  `hour` TIME,
  PRIMARY KEY (`time_id`)
);

CREATE TABLE `Weather` (
  `weather_id` INT NOT NULL AUTO_INCREMENT,
  `temperature` FLOAT,
  `precipitation` FLOAT,
  `state` VARCHAR(50),
  `wind` FLOAT,
  `humidity` FLOAT,
  PRIMARY KEY (`weather_id`)
);

CREATE TABLE `Place_Time_Weather` (
  `place_time_weather_id` INT NOT NULL AUTO_INCREMENT,
  `time_id` INT,
  `place_id` INT,
  `weather_id` INT,
  PRIMARY KEY (`place_time_weather_id`),
  FOREIGN KEY (`time_id`) REFERENCES `Time`(`time_id`) ON DELETE CASCADE,
  FOREIGN KEY (`place_id`) REFERENCES `Place`(`place_id`) ON DELETE CASCADE,
  FOREIGN KEY (`weather_id`) REFERENCES `Weather`(`weather_id`) ON DELETE CASCADE
);
