-- initial schema
-- Migration SQL that makes the change goes here.

CREATE TABLE cs_users (
    uid INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ulogin VARCHAR(30) NOT NULL UNIQUE,
    uemail VARCHAR(128) NOT NULL UNIQUE,
    ufname VARCHAR(128) NOT NULL,
    uname VARCHAR(128) NOT NULL,
    ucryptsum VARCHAR(60) NOT NULL,      -- blowfish hash
    ujoined DATETIME NOT NULL,
    ulocation VARCHAR(128) NOT NULL,
    upublic TINYINT NOT NULL,            -- 1
    utoken VARCHAR(32) NOT NULL UNIQUE,  -- md5 hash
    uactive TINYINT NOT NULL
);

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

CREATE TABLE cs_actions (
    aid INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cuid INTEGER NOT NULL REFERENCES cs_users(uid),
    acode VARCHAR(32) NOT NULL UNIQUE,
    created DATETIME NOT NULL,
    validuntil DATETIME NOT NULL,
    atype INTEGER NOT NULL,
    adata TEXT
);

-- add some indexes to improve query performance
CREATE INDEX cs_users_upublic_idx ON cs_users(upublic);
CREATE INDEX cs_users_ujoined_idx ON cs_users(ujoined);
CREATE INDEX cs_users_uactive_idx ON cs_users(uactive);

CREATE INDEX cs_mate_cuid_idx ON cs_mate(cuid);
CREATE INDEX cs_mate_mdate_idx ON cs_mate(mdate);

CREATE INDEX cs_coffees_cuid_idx ON cs_coffees(cuid);
CREATE INDEX cs_coffees_cdate_idx ON cs_coffees(cdate);

CREATE INDEX cs_actions_cuid_idx ON cs_actions(cuid);
CREATE INDEX cs_actions_atype_idx ON cs_actions(atype);
CREATE INDEX cs_actions_validuntil_idx ON cs_actions(validuntil);

-- @UNDO
-- SQL to undo the change goes here.

DROP TABLE cs_actions;
DROP TABLE cs_coffees;
DROP TABLE cs_mate;
DROP TABLE cs_users;
