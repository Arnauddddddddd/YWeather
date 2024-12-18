CREATE TABLE `Place` (
  `id` text,
  `name` varchar(50),
  `latitude` float,
  `longitude` float
);

CREATE TABLE `Place_Time_Weather` (
  `id` text,
  `time_id` text,
  `place_id` text,
  `weather_id` text
);

CREATE TABLE `Time` (
  `id` text,
  `day` timestamp,
  `hour` timestamp
);

CREATE TABLE `Weather` (
  `id` text,
  `temperature` float,
  `precipitation` float,
  `state` varchar(50),
  `wind` float,
  `humidity` float
);

ALTER TABLE `Place_Time_Weather` ADD FOREIGN KEY (`weather_id`) REFERENCES `Weather` (`id`);

ALTER TABLE `Place_Time_Weather` ADD FOREIGN KEY (`time_id`) REFERENCES `Time` (`id`);

ALTER TABLE `Place_Time_Weather` ADD FOREIGN KEY (`place_id`) REFERENCES `Place` (`id`);
