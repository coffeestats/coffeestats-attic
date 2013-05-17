-- refactor caffeine tables
-- Migration SQL that makes the change goes here.

CREATE TABLE cs_caffeine (
    cid INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ctype INTEGER NOT NULL,
    cuid INTEGER NOT NULL REFERENCES cs_users(uid),
    cdate DATETIME NOT NULL,
    centrytime DATETIME NOT NULL
);

INSERT INTO cs_caffeine
  (ctype, cuid, cdate, centrytime)
SELECT 0, cuid, cdate, cdate FROM cs_coffees;

INSERT INTO cs_caffeine
  (ctype, cuid, cdate, centrytime)
SELECT 1, cuid, mdate, mdate FROM cs_mate;

CREATE INDEX cs_caffeine_ctype_idx ON cs_caffeine(ctype);
CREATE INDEX cs_caffeine_cuid_idx ON cs_caffeine(cuid);
CREATE INDEX cs_caffeine_cdate_idx ON cs_caffeine(cdate);
CREATE INDEX cs_caffeine_centrytime_idx ON cs_caffeine(centrytime);

DROP TABLE cs_coffees;
DROP TABLE cs_mate;

-- @UNDO
-- SQL to undo the change goes here.

CREATE TABLE cs_mate (
    mid INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cuid INTEGER NOT NULL REFERENCES cs_users(uid),
    mdate DATETIME NOT NULL
);

CREATE TABLE cs_coffees (
    cid INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cuid INTEGER NOT NULL REFERENCES cs_users(uid),
    cdate DATETIME NOT NULL
);

INSERT INTO cs_coffees
    (cuid, cdate)
SELECT cuid, cdate FROM cs_caffeine WHERE ctype=0;

INSERT INTO cs_mate
    (cuid, mdate)
SELECT cuid, cdate FROM cs_caffeine WHERE ctype=1;

CREATE INDEX cs_mate_cuid_idx ON cs_mate(cuid);
CREATE INDEX cs_mate_mdate_idx ON cs_mate(mdate);

CREATE INDEX cs_coffees_cuid_idx ON cs_coffees(cuid);
CREATE INDEX cs_coffees_cdate_idx ON cs_coffees(cdate);

DROP TABLE cs_caffeine;
