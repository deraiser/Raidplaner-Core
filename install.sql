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

DROP TABLE IF EXISTS rp1_event;
CREATE TABLE rp1_event (
    eventID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    objectTypeID INT(10) NOT NULL,
    title VARCHAR(191) NOT NULL DEFAULT '',
    userID INT(10),
    username VARCHAR(255) NOT NULL DEFAULT '',
    created INT(10) NOT NULL DEFAULT 0,
    startTime INT(10) NOT NULL DEFAULT 0,
    endTime INT(10) NOT NULL DEFAULT 0,
    isFullDay TINYINT(1) NOT NULL DEFAULT 0,
    notes MEDIUMTEXT,
    views MEDIUMINT(7) NOT NULL DEFAULT 0,
    enableComments TINYINT(1) NOT NULL DEFAULT 0,
    comments SMALLINT(5) NOT NULL DEFAULT 0,
	cumulativeLikes MEDIUMINT(7) NOT NULL DEFAULT 0,
    hasEmbeddedObjects TINYINT(1) NOT NULL DEFAULT 0,
	deleteTime INT(10) NOT NULL DEFAULT 0,
	isDeleted TINYINT(1) NOT NULL DEFAULT 0,
    isCanceled TINYINT(1) NOT NULL DEFAULT 0,
    isDisabled TINYINT(1) NOT NULL DEFAULT 0,
    additionalData TEXT
);

DROP TABLE IF EXISTS rp1_event_raid_attendee;
CREATE TABLE rp1_event_raid_attendee (
    attendeeID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    eventID INT(10) NOT NULL,
    characterID INT(10),
    characterName VARCHAR(255) NOT NULL DEFAULT '',
    email VARCHAR(191) NOT NULL DEFAULT '',
    classificationID INT(10),
    roleID INT(10),
    notes VARCHAR(255) NOT NULL DEFAULT '',
    created INT(10) NOT NULL DEFAULT 0,
    addByLeader TINYINT(1) NOT NULL DEFAULT 0,
    status TINYINT(1) NOT NULL DEFAULT 0
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
    UNIQUE KEY identifier (identifier)
);

DROP TABLE IF EXISTS rp1_member;
-- Alternative for character
CREATE TABLE rp1_member (
    characterID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    characterName VARCHAR(191) NOT NULL DEFAULT '',
    userID INT(10),
    gameID INT(10) NOT NULL,
    rankID INT(10),
    avatarID INT(10),
    created INT(10) NOT NULL DEFAULT 0,
    lastUpdateTime INT(10) NOT NULL DEFAULT 0,
    notes MEDIUMTEXT,
    notesHasEmbeddedObjects TINYINT(1) DEFAULT 0,
    additionalData TEXT,
    guildName VARCHAR(255) NOT NULL DEFAULT '',
	profileHits INT(10) NOT NULL DEFAULT 0,
    isPrimary TINYINT(1) NOT NULL DEFAULT 0,
    isDisabled TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY characterName (characterName, gameID)
);

DROP TABLE IF EXISTS rp1_member_avatar;
CREATE TABLE rp1_member_avatar (
    avatarID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    avatarName VARCHAR(255) NOT NULL DEFAULT '',
    avatarExtension VARCHAR(7) NOT NULL DEFAULT '',
    width SMALLINT(5) NOT NULL DEFAULT 0,
    height SMALLINT(5) NOT NULL DEFAULT 0,
    characterID INT(10) NOT NULL,
    fileHash VARCHAR(40) NOT NULL DEFAULT '',
    hasWebP TINYINT(1) NOT NULL DEFAULT 0
);

DROP TABLE IF EXISTS rp1_member_profile_menu_item;
CREATE TABLE rp1_member_profile_menu_item (
    menuItemID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    packageID INT(10) NOT NULL,
    menuItem VARCHAR(191) NOT NULL DEFAULT '',
    showOrder INT(10) NOT NULL DEFAULT 0,
    permissions TEXT,
    options TEXT,
    className VARCHAR(255) NOT NULL DEFAULT '',
    UNIQUE KEY (packageID, menuItem)
);

DROP TABLE IF EXISTS rp1_point_account;
CREATE TABLE rp1_point_account (
    pointAccountID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    pointAccountName VARCHAR(255) NOT NULL DEFAULT '',
    description VARCHAR(255) NOT NULL DEFAULT '',
    gameID INT(10) NOT NULL,
    showOrder INT(10) NOT NULL DEFAULT 0
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

DROP TABLE IF EXISTS rp1_raid_event;
CREATE TABLE rp1_raid_event (
    eventID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    eventName VARCHAR(255) NOT NULL DEFAULT '',
    pointAccountID INT(10),
    gameID INT(10) NOT NULL,
    defaultPoints FLOAT(11,2) NOT NULL DEFAULT 0,
    icon VARCHAR(255) NOT NULL DEFAULT '',
    showProfile TINYINT(1) NOT NULL DEFAULT 0
);

DROP TABLE IF EXISTS rp1_rank;
CREATE TABLE rp1_rank (
    rankID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    rankName VARCHAR(100) NOT NULL DEFAULT '',
    gameID INT(10) NOT NULL,
    prefix VARCHAR(25) NOT NULL DEFAULT '',
    suffix VARCHAR(25) NOT NULL DEFAULT '',
    showOrder INT(10) NOT NULL DEFAULT 0,
    isDefault TINYINT(1) NOT NULL DEFAULT 0
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

ALTER TABLE rp1_event ADD FOREIGN KEY (objectTypeID) REFERENCES wcf1_object_type (objectTypeID) ON DELETE CASCADE;
ALTER TABLE rp1_event ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE rp1_event_raid_attendee ADD FOREIGN KEY (characterID) REFERENCES rp1_member (characterID) ON DELETE SET NULL;
ALTER TABLE rp1_event_raid_attendee ADD FOREIGN KEY (classificationID) REFERENCES rp1_classification (classificationID) ON DELETE SET NULL;
ALTER TABLE rp1_event_raid_attendee ADD FOREIGN KEY (eventID) REFERENCES rp1_event (eventID) ON DELETE CASCADE;
ALTER TABLE rp1_event_raid_attendee ADD FOREIGN KEY (roleID) REFERENCES rp1_role (roleID) ON DELETE SET NULL;

ALTER TABLE rp1_faction ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_faction ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;

ALTER TABLE rp1_game ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;

ALTER TABLE rp1_member ADD FOREIGN KEY (avatarID) REFERENCES rp1_member_avatar (avatarID) ON DELETE SET NULL;
ALTER TABLE rp1_member ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_member ADD FOREIGN KEY (rankID) REFERENCES rp1_rank (rankID) ON DELETE SET NULL;
ALTER TABLE rp1_member ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE rp1_member_avatar ADD FOREIGN KEY (characterID) REFERENCES rp1_member (characterID) ON DELETE CASCADE;
ALTER TABLE rp1_member_profile_menu_item ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;

ALTER TABLE rp1_point_account ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;

ALTER TABLE rp1_race ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_race ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;
ALTER TABLE rp1_race_to_faction ADD FOREIGN KEY (raceID) REFERENCES rp1_race (raceID) ON DELETE CASCADE;
ALTER TABLE rp1_race_to_faction ADD FOREIGN KEY (factionID) REFERENCES rp1_faction (factionID) ON DELETE CASCADE;

ALTER TABLE rp1_raid_event ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_raid_event ADD FOREIGN KEY (pointAccountID) REFERENCES rp1_point_account (pointAccountID) ON DELETE SET NULL;

ALTER TABLE rp1_rank ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;

ALTER TABLE rp1_role ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_role ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;

ALTER TABLE rp1_server ADD FOREIGN KEY (gameID) REFERENCES rp1_game (gameID) ON DELETE CASCADE;
ALTER TABLE rp1_server ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;