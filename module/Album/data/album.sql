CREATE TABLE album (
  id int(11) NOT NULL auto_increment,
  artist varchar(100) NOT NULL,
  title varchar(100) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

INSERT INTO album (artist, title)
    VALUES  ('The  Military  Wives',  'In  My  Dreams');
INSERT INTO album (artist, title)
    VALUES  ('Adele',  '21');
INSERT INTO album (artist, title)
    VALUES  ('Bruce  Springsteen',  'Wrecking Ball (Deluxe)');
INSERT INTO album (artist, title)
    VALUES  ('Lana  Del  Rey',  'Born  To  Die');
INSERT INTO album (artist, title)
    VALUES  ('Gotye',  'Making  Mirrors');

--  version 0.3.0
--  date: 2013-03-02
ALTER TABLE `album` ADD `discs` SMALLINT NOT NULL DEFAULT '1';

--  version 0.3.3
--  date: 2013-03-22
CREATE TABLE songs (
  id INT AUTO_INCREMENT NOT NULL,
  album_id INT NOT NULL,
  position INT NOT NULL,
  name VARCHAR(63) NOT NULL,
  duration TIME NOT NULL,
  disc INT NOT NULL,
  
  INDEX IDX_BAECB19B1137ABCF (album_id),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE songs ADD CONSTRAINT FK_BAECB19B1137ABCF FOREIGN KEY (album_id) REFERENCES album (id);
