-- MySQL dump 8.22
--
-- Host: localhost    Database: fraggle
---------------------------------------------------------
-- Server version	3.23.55

--
-- Table structure for table 'commenttable'
--

CREATE TABLE commenttable (
  comment_id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL default '0',
  from_id int(11) NOT NULL default '0',
  text varchar(200) default NULL,
  readstatus enum('y','n') default NULL,
  PRIMARY KEY  (comment_id),
  KEY messageuserindex (user_id),
  KEY user_idreadstatus (user_id,readstatus)
) TYPE=MyISAM;

--
-- Table structure for table 'messagetable'
--

CREATE TABLE messagetable (
  message_id int(11) NOT NULL auto_increment,
  message_text mediumtext,
  topic_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  message_title varchar(50) default NULL,
  message_time timestamp(14) NOT NULL,
  message_popname varchar(70) default NULL,
  PRIMARY KEY  (message_id),
  KEY message_idAndtopic_id (message_id,topic_id),
  KEY messagetable_topic_id (topic_id),
  KEY messagetable_message_time (message_time)
) TYPE=MyISAM;

--
-- Table structure for table 'nexusmessagetable'
--

CREATE TABLE nexusmessagetable (
  nexusmessage_id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL default '0',
  from_id int(11) NOT NULL default '0',
  text varchar(200) default NULL,
  readstatus enum('y','n') default NULL,
  PRIMARY KEY  (nexusmessage_id),
  KEY messageuserindex (user_id),
  KEY user_idreadstatus (user_id,readstatus)
) TYPE=MyISAM;

--
-- Table structure for table 'sectiontable'
--

CREATE TABLE sectiontable (
  section_id int(11) NOT NULL auto_increment,
  section_title varchar(50) default NULL,
  user_id int(11) default NULL,
  parent_id int(11) default NULL,
  section_weight int(11) NOT NULL default '0',
  section_intro varchar(100) default '',
  PRIMARY KEY  (section_id),
  KEY index_parent_id (parent_id),
  KEY id_weight (section_id,section_weight)
) TYPE=MyISAM;

--
-- Table structure for table 'topictable'
--

CREATE TABLE topictable (
  topic_id int(11) NOT NULL auto_increment,
  topic_title varchar(50) default NULL,
  section_id int(11) default NULL,
  topic_desctiption mediumtext,
  topic_annon enum('y','n') default 'n',
  topic_readonly enum('y','n') default 'n',
  topic_weight tinyint(4) default '10',
  PRIMARY KEY  (topic_id),
  KEY topictable_section_id (section_id)
) TYPE=MyISAM;

--
-- Table structure for table 'topicview'
--

CREATE TABLE topicview (
  topicview_id int(11) NOT NULL auto_increment,
  user_id int(11) default NULL,
  topic_id int(11) default NULL,
  msg_date char(14) default NULL,
  unsubscribe tinyint(1) default '0',
  PRIMARY KEY  (topicview_id),
  KEY user_id_msg_date (user_id,topic_id),
  KEY topicview_msg_date (msg_date)
) TYPE=MyISAM;

--
-- Table structure for table 'usertable'
--

CREATE TABLE usertable (
  user_id int(11) NOT NULL auto_increment,
  user_name varchar(50) default 'user',
  user_email varchar(50) default 'no-one@nowhere.com',
  user_comment mediumtext,
  user_popname varchar(70) default 'new user',
  user_password varchar(50) default 'changeme',
  user_realname varchar(50) default 'firstname lastname',
  user_priv tinyint(3) unsigned default '100',
  user_display tinyint(3) unsigned default '20',
  user_backwards enum('y','n') default 'n',
  user_sysop enum('y','n') default 'n',
  user_location varchar(200) default NULL,
  user_theme varchar(50) NOT NULL default 'xp',
  user_totaledits int(11) NOT NULL default '0',
  user_totalvisits int(11) default '0',
  user_status enum('Online','Offline','Invalid') NOT NULL default 'Invalid',
  user_banned tinyint(1) default '0',
  user_film varchar(50) default '',
  user_band varchar(50) default '',
  user_sex enum('male','female','unknown') NOT NULL default 'male',
  user_town varchar(50) default '',
  user_age varchar(11) default '13',
  user_ipaddress varchar(100) default '127.0.0.1',
  user_no_pictures enum('y','n') default 'n',
  PRIMARY KEY  (user_id)
) TYPE=MyISAM;

--
-- Table structure for table 'whoison'
--

CREATE TABLE whoison (
  whoison_id int(11) NOT NULL auto_increment,
  user_id int(11) default NULL,
  timeon timestamp(14) NOT NULL,
  PRIMARY KEY  (whoison_id)
) TYPE=MyISAM;

