CREATE TABLE processo (
  id varchar(255) NOT NULL default '',
  code varchar(255) NOT NULL default '',
  classe varchar(255) NOT NULL default '',
  assunto varchar(255) NOT NULL default '',
  magistrado varchar(255) NOT NULL default '',
  comarca varchar(255) NOT NULL default '',
  foro varchar(255) NOT NULL default '',
  vara varchar(255) NOT NULL default '',
  data int(11) NOT NULL default '0',
  sumario longtext default NULL,
  file varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) ENGINE=InnoDB;
