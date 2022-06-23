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

/* SQL_PARSER_OFFSET */

-- foreign keys
ALTER TABLE rp1_faction ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_faction ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;

ALTER TABLE rp1_game ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;