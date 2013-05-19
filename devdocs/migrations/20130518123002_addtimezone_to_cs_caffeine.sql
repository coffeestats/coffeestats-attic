-- addtimezone to cs caffeine
-- Migration SQL that makes the change goes here.

ALTER TABLE cs_caffeine ADD COLUMN ctimezone VARCHAR(32);

CREATE INDEX cs_caffeine_timezone_idx ON cs_caffeine(ctimezone);

-- @UNDO
-- SQL to undo the change goes here.

ALTER TABLE cs_caffeine DROP COLUMN ctimezone;
