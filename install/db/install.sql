CREATE TABLE IF NOT EXISTS b_citfact_uservars (
  ID int(11) unsigned NOT NULL AUTO_INCREMENT,
  GROUP_ID int(11) unsigned NOT NULL,
  NAME varchar(100) NOT NULL,
  CODE varchar(100) NOT NULL,
  VALUE varchar(255) NOT NULL,
  DESCRIPTION text,
  PRIMARY KEY (ID)
);

CREATE TABLE IF NOT EXISTS b_citfact_uservars_group (
  ID int(11) unsigned NOT NULL AUTO_INCREMENT,
  NAME varchar(100) NOT NULL,
  CODE varchar(100) NOT NULL,
  PRIMARY KEY (ID)
);