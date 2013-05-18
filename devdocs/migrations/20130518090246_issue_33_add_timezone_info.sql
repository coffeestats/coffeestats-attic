-- issue 33 add timezone info
-- Migration SQL that makes the change goes here.

ALTER TABLE cs_users ADD COLUMN utimezone VARCHAR(40);

CREATE INDEX cs_users_utimezone_index ON cs_users(utimezone);

-- @UNDO
-- SQL to undo the change goes here.

ALTER TABLE cs_users DROP COLUMN utimezone;
