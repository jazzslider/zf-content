--//

ALTER TABLE content_posts ADD COLUMN author VARCHAR(255) NULL AFTER status ;

--//@UNDO

ALTER TABLE content_posts DROP COLUMN author ;

--//
