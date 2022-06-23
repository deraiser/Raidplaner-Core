DROP TABLE IF EXISTS rp1_classification;
CREATE TABLE rp1_classification (
    classificationID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    packageID INT(10) NOT NULL,
    gameID INT(10) NOT NULL,
    identifier VARCHAR(191) NOT NULL,
    icon VARCHAR(255) NOT NULL DEFAULT '',
    isDisabled TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY identifier (identifier, gameID)
);

DROP TABLE IF EXISTS rp1_classification_to_faction;
CREATE TABLE rp1_classification_to_faction (
    classificationID INT(10) NOT NULL,
    factionID INT(10) NOT NULL,
    UNIQUE KEY (classificationID, factionID)
);


DROP TABLE IF EXISTS rp1_classification_to_race;
CREATE TABLE rp1_classification_to_race (
    classificationID INT(10) NOT NULL,
    raceID INT(10) NOT NULL,
    UNIQUE KEY (classificationID, raceID)
);

DROP TABLE IF EXISTS rp1_classification_to_role;
CREATE TABLE rp1_classification_to_role (
    classificationID INT(10),
    roleID INT(10),
    UNIQUE KEY (classificationID, roleID)
);

DROP TABLE IF EXISTS rp1_faction;
CREATE TABLE rp1_faction (
    factionID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    packageID INT(10) NOT NULL,
    gameID INT(10) NOT NULL,
    identifier VARCHAR(191) NOT NULL,
    icon VARCHAR(255) NOT NULL DEFAULT '',
    isDisabled TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY identifier (identifier, gameID)
);

DROP TABLE IF EXISTS rp1_game;
CREATE TABLE rp1_game (
    gameID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    packageID INT(10) NOT NULL,
    identifier VARCHAR(191) NOT NULL,
    maxLevel INT(10) NOT NULL DEFAULT 0,
    maxClass INT(10) NOT NULL DEFAULT 0,
    icon VARCHAR(255) NOT NULL DEFAULT '',
    UNIQUE KEY identifier (identifier)
);

DROP TABLE IF EXISTS rp1_member;
-- Alternative for character
CREATE TABLE rp1_member (
    characterID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    characterName VARCHAR(191) NOT NULL DEFAULT '',
    userID INT(10),
    gameID INT(10) NOT NULL,
    created INT(10) NOT NULL DEFAULT 0,
    lastUpdateTime INT(10) NOT NULL DEFAULT 0,
    notes MEDIUMTEXT,
    additionalData TEXT,
    guildName VARCHAR(255) NOT NULL DEFAULT '',
    isPrimary TINYINT(1) NOT NULL DEFAULT 0,
    isDisabled TINYINT(1) NOT NULL DEFAULT 0
);

DROP TABLE IF EXISTS rp1_race;
CREATE TABLE rp1_race (
    raceID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    packageID  INT(10) NOT NULL,
    gameID INT(10) NOT NULL,
    identifier VARCHAR(191) NOT NULL,
    icon VARCHAR(255) NOT NULL DEFAULT '',
    isDisabled TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY identifier (identifier, gameID)
);

DROP TABLE IF EXISTS rp1_race_to_faction;
CREATE TABLE rp1_race_to_faction (
    raceID INT(10) NOT NULL,
    factionID INT(10) NOT NULL,
    UNIQUE KEY(raceID, factionID)
);

DROP TABLE IF EXISTS rp1_role;
CREATE TABLE rp1_role (
    roleID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    packageID INT(10) NOT NULL,
    gameID INT(10) NOT NULL,
    identifier VARCHAR(191) NOT NULL,
    icon VARCHAR(255) NOT NULL DEFAULT '',
    isDisabled TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY identifier (identifier, gameID)
);

DROP TABLE IF EXISTS rp1_server;
CREATE TABLE rp1_server (
    serverID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    packageID INT(10) NOT NULL,
    gameID INT(10) NOT NULL,
    identifier VARCHAR(191) NOT NULL,
    type VARCHAR(10) NOT NULL DEFAULT '',
    serverGroup VARCHAR(255) NOT NULL DEFAULT '',
    UNIQUE KEY identifier (identifier, gameID)
);

/* SQL_PARSER_OFFSET */

-- foreign keys
ALTER TABLE rp1_classification ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_classification ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;
ALTER TABLE rp1_classification_to_faction ADD FOREIGN KEY (classificationID) REFERENCES rp1_classification (classificationID) ON DELETE CASCADE;
ALTER TABLE rp1_classification_to_faction ADD FOREIGN KEY (factionID) REFERENCES rp1_faction (factionID) ON DELETE CASCADE;
ALTER TABLE rp1_classification_to_race ADD FOREIGN KEY (classificationID) REFERENCES rp1_classification (classificationID) ON DELETE CASCADE;
ALTER TABLE rp1_classification_to_race ADD FOREIGN KEY (raceID) REFERENCES rp1_race (raceID) ON DELETE CASCADE;
ALTER TABLE rp1_classification_to_role ADD FOREIGN KEY (classificationID) REFERENCES rp1_classification (classificationID) ON DELETE CASCADE;
ALTER TABLE rp1_classification_to_role ADD FOREIGN KEY (roleID) REFERENCES rp1_role (roleID) ON DELETE CASCADE;

ALTER TABLE rp1_faction ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_faction ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;

ALTER TABLE rp1_game ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;

ALTER TABLE rp1_member ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_member ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;

ALTER TABLE rp1_race ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_race ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;
ALTER TABLE rp1_race_to_faction ADD FOREIGN KEY (raceID) REFERENCES rp1_race (raceID) ON DELETE CASCADE;
ALTER TABLE rp1_race_to_faction ADD FOREIGN KEY (factionID) REFERENCES rp1_faction (factionID) ON DELETE CASCADE;

ALTER TABLE rp1_role ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_role ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;

ALTER TABLE rp1_server ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_server ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;