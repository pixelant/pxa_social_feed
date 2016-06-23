#
# Table structure for table 'tx_pxasocialfeed_domain_model_feed'
#
CREATE TABLE tx_pxasocialfeed_domain_model_feed (

  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  post_date int(11) DEFAULT '0' NOT NULL,
  post_url varchar(255) DEFAULT '' NOT NULL,
  message text NOT NULL,
  image text NOT NULL,
  title varchar(255) DEFAULT '' NOT NULL,
  external_identifier varchar(255) DEFAULT '' NOT NULL,
  update_date int(11) unsigned DEFAULT '0' NOT NULL,
  configuration int(11) unsigned DEFAULT '0',

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)

);

#
# Table structure for table 'tx_pxasocialfeed_domain_model_token'
#
CREATE TABLE tx_pxasocialfeed_domain_model_token (

  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  serialized_credentials blob,
  social_type int(11) DEFAULT '1' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)

);

#
# Table structure for table 'tx_pxasocialfeed_domain_model_config'
#
CREATE TABLE tx_pxasocialfeed_domain_model_configuration (

  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  name varchar(255) DEFAULT '' NOT NULL,
  social_id varchar(255) DEFAULT '' NOT NULL,
  token int(11) unsigned DEFAULT '0',
  feeds_limit int(11) unsigned DEFAULT '0',

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)

);