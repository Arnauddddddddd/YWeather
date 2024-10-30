CREATE TABLE Place (
  id text,
  name varchar(50),
  latitude float,
  longitude float
);

CREATE TABLE Place_Time (
  id text,
  time_id text,
  place_id text
);

CREATE TABLE Time (
  id text,
  day timestamp,
  hour timestamp,
  weather_id text
);

CREATE TABLE Weather (
  id text,
  temperature float,
  precipitation float,
  state varchar(50),
  wind float,
  humidity float
);

ALTER TABLE Time ADD FOREIGN KEY (weather_id) REFERENCES Weather (id);

ALTER TABLE Place_Time ADD FOREIGN KEY (time_id) REFERENCES Time (id);

ALTER TABLE Place_Time ADD FOREIGN KEY (place_id) REFERENCES Place (id);
