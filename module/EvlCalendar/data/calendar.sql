CREATE TABLE events (
  id INT AUTO_INCREMENT NOT NULL,
  name VARCHAR(63) NOT NULL,
  started_at DATETIME NOT NULL,
  ended_at DATETIME NOT NULL,
  all_day TINYINT(1) NOT NULL,

  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

INSERT INTO events (name, started_at, ended_at, all_day) VALUES
('Spotkanie', '2013-08-19 11:00:00', '2013-08-19 15:00:00', 0),
('Lunch', '2013-08-19 12:00:00', '2013-08-19 13:00:00', 0),
('Święto Wojska Polskiego', '2013-08-15 00:00:00', '2013-08-15 23:59:59', 1),
('Delegacja', '2013-08-26 00:00:00', '2013-08-28 23:59:59', 1);

CREATE TABLE holidays (
  id INT AUTO_INCREMENT NOT NULL,
  name VARCHAR(63) NOT NULL,
  dated_at DATE NOT NULL,
  year SMALLINT NOT NULL,
  weekday SMALLINT DEFAULT NULL,
  type SMALLINT DEFAULT '1' NOT NULL,
  constant TINYINT(1) NOT NULL,

  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


INSERT INTO holidays (name, dated_at, year, weekday, type, constant) VALUES
('Nowy Rok', '2012-01-01', 2012, NULL, 1, 1),
('Trzech Króli', '2012-01-06', 2012, NULL, 1, 1),
('Wielkanoc, Wielka Niedziela', '2012-04-08', 2012, 64, 1, 0),
('Wielkanoc, Poniedziałek wielkanocny', '2012-04-09', 2012, 1, 1, 0),
('Święto pracy', '2012-05-01', 2012, NULL, 1, 1),
('Trzeciego maja', '2012-05-03', 2012, NULL, 1, 1),
('Niedziela Zesłania Ducha św. (Zielone Świątki)', '2012-05-27', 2012, 64, 1, 0),
('Boże Ciało', '2012-06-07', 2012, 8, 1, 0),
('Wniebowzięcie Najświętszej Maryi Panny', '2012-08-15', 2012, NULL, 1, 1),
('Wszystkich Świętych', '2012-11-01', 2012, NULL, 1, 1),
('Święto Niepodległości', '2012-11-11', 2012, NULL, 1, 1),
('Boże Narodzenie', '2012-12-25', 2012, NULL, 1, 1),
('Boże Narodzenie, św. Szczepana', '2012-12-26', 2012, NULL, 1, 1),
('Nowy Rok', '2013-01-01', 2013, NULL, 1, 1),
('Trzech Króli', '2013-01-06', 2013, NULL, 1, 1),
('Wielkanoc, Wielka Niedziela', '2013-03-31', 2013, 64, 1, 0),
('Wielkanoc, Poniedziałek wielkanocny', '2013-04-01', 2013, 1, 1, 0),
('Święto pracy', '2013-05-01', 2013, NULL, 1, 1),
('Trzeciego maja', '2013-05-03', 2013, NULL, 1, 1),
('Niedziela Zesłania Ducha św. (Zielone Świątki)', '2013-05-19', 2013, 64, 1, 0),
('Boże Ciało', '2013-05-30', 2013, 8, 1, 0),
('Wniebowzięcie Najświętszej Maryi Panny', '2013-08-15', 2013, NULL, 1, 1),
('Wszystkich Świętych', '2013-11-01', 2013, NULL, 1, 1),
('Święto Niepodległości', '2013-11-11', 2013, NULL, 1, 1),
('Boże Narodzenie', '2013-12-25', 2013, NULL, 1, 1),
('Boże Narodzenie, św. Szczepana', '2013-12-26', 2013, NULL, 1, 1),
('Nowy Rok', '2014-01-01', 2014, NULL, 1, 1),
('Trzech Króli', '2014-01-06', 2014, NULL, 1, 1),
('Wielkanoc, Wielka Niedziela', '2014-04-20', 2014, 64, 1, 0),
('Wielkanoc, Poniedziałek wielkanocny', '2014-04-21', 2014, 1, 1, 0),
('Święto pracy', '2014-05-01', 2014, NULL, 1, 1),
('Trzeciego maja', '2014-05-03', 2014, NULL, 1, 1),
('Niedziela Zesłania Ducha św. (Zielone Świątki)', '2014-06-08', 2014, 64, 1, 0),
('Boże Ciało', '2014-06-19', 2014, 8, 1, 0),
('Wniebowzięcie Najświętszej Maryi Panny', '2014-08-15', 2014, NULL, 1, 1),
('Wszystkich Świętych', '2014-11-01', 2014, NULL, 1, 1),
('Święto Niepodległości', '2014-11-11', 2014, NULL, 1, 1),
('Boże Narodzenie', '2014-12-25', 2014, NULL, 1, 1),
('Boże Narodzenie, św. Szczepana', '2014-12-26', 2014, NULL, 1, 1);
