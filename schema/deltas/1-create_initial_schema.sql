--//

CREATE TABLE content_posts (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  class     VARCHAR(255) NOT NULL ,
  slug      VARCHAR(255) NULL ,
  status    INT UNSIGNED NOT NULL ,
  published DATETIME     NOT NULL ,
  UNIQUE(slug)
) ENGINE = InnoDB ;

CREATE TABLE content_revisions (
  id         INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  post       INT UNSIGNED     NOT NULL ,
  active     TINYINT UNSIGNED NOT NULL ,
  title      VARCHAR(255)     NOT NULL ,
  body       LONGTEXT         NULL ,
  bodyFilter VARCHAR(50)      NULL ,
  created    DATETIME         NOT NULL ,
  FOREIGN KEY (post) REFERENCES content_posts(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB ;

--//@UNDO

DROP TABLE content_revisions ;
DROP TABLE content_posts ;

--//
