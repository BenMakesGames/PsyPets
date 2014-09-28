-- phpMyAdmin SQL Dump
-- version 3.3.10.4
-- http://www.phpmyadmin.net
--
-- Host: db.psypets.net
-- Generation Time: Feb 14, 2013 at 12:43 PM
-- Server version: 5.1.56
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `psypets`
--

-- --------------------------------------------------------

--
-- Table structure for table `bay_spam`
--

CREATE TABLE `bay_spam` (
  `spamid` int(11) NOT NULL AUTO_INCREMENT,
  `token` text NOT NULL,
  `spamcount` int(11) NOT NULL DEFAULT '0',
  `hamcount` int(11) NOT NULL DEFAULT '0',
  `spamrating` double NOT NULL DEFAULT '0.4',
  PRIMARY KEY (`spamid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bay_totals`
--

CREATE TABLE `bay_totals` (
  `totalsid` int(11) NOT NULL AUTO_INCREMENT,
  `totalspam` int(11) NOT NULL DEFAULT '0',
  `totalham` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`totalsid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_admins`
--

CREATE TABLE `monster_admins` (
  `user` varchar(16) NOT NULL DEFAULT '',
  `createpolls` enum('yes','no') NOT NULL DEFAULT 'no',
  `viewpolls` enum('yes','no') NOT NULL DEFAULT 'no',
  `admintools` enum('yes','no') NOT NULL DEFAULT 'no',
  `admintag` enum('yes','no') NOT NULL DEFAULT 'no',
  `mailpsypets` enum('yes','no') NOT NULL DEFAULT 'no',
  `proxypost` enum('yes','no') NOT NULL DEFAULT 'no',
  `forcemail` enum('yes','no') NOT NULL DEFAULT 'no',
  `seestats` enum('yes','no') NOT NULL DEFAULT 'no',
  `seedebug` enum('yes','no') NOT NULL DEFAULT 'no',
  `clairvoyant` enum('yes','no') NOT NULL DEFAULT 'no',
  `massgift` enum('yes','no') NOT NULL DEFAULT 'no',
  `manageitems` enum('yes','no') NOT NULL DEFAULT 'no',
  `manageevents` enum('yes','no') NOT NULL DEFAULT 'no',
  `manageplaza` enum('yes','no') NOT NULL DEFAULT 'no',
  `pruneorphans` enum('yes','no') NOT NULL DEFAULT 'no',
  `manageaccounts` enum('yes','no') NOT NULL DEFAULT 'no',
  `possessaccounts` enum('yes','no') NOT NULL DEFAULT 'no',
  `managewatchers` enum('yes','no') NOT NULL DEFAULT 'no',
  `uploadpetgraphics` enum('yes','no') NOT NULL DEFAULT 'no',
  `managewishlist` enum('yes','no') NOT NULL DEFAULT 'no',
  `abusewatcher` enum('yes','no') NOT NULL DEFAULT 'no',
  `deletespam` enum('yes','no') NOT NULL DEFAULT 'no',
  `seeserversettings` enum('yes','no') NOT NULL DEFAULT 'no',
  `coder` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_auctions`
--

CREATE TABLE `monster_auctions` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ownerid` int(10) unsigned NOT NULL DEFAULT '0',
  `itemid` int(10) unsigned NOT NULL DEFAULT '0',
  `itemname` varchar(64) NOT NULL DEFAULT '',
  `ldesc` text NOT NULL,
  `highbidder` int(10) unsigned NOT NULL DEFAULT '0',
  `bidvalue` int(10) unsigned NOT NULL DEFAULT '0',
  `bidtime` int(10) unsigned NOT NULL DEFAULT '0',
  `claimed` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `bidtime` (`bidtime`),
  KEY `claimed` (`claimed`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_events`
--

CREATE TABLE `monster_events` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `descript` text NOT NULL,
  `fee` int(10) unsigned NOT NULL DEFAULT '0',
  `prizes` text NOT NULL,
  `prizedescript` text NOT NULL,
  `trophies` tinyint(4) NOT NULL DEFAULT '0',
  `minlevel` int(10) unsigned NOT NULL DEFAULT '0',
  `maxlevel` int(10) unsigned NOT NULL DEFAULT '0',
  `minparticipant` int(10) unsigned NOT NULL DEFAULT '0',
  `event` varchar(20) NOT NULL DEFAULT '',
  `participants` text NOT NULL,
  `report` text NOT NULL,
  `prereport` text NOT NULL,
  `postreport` text NOT NULL,
  `finished` enum('no','yes') NOT NULL DEFAULT 'no',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `host` varchar(32) NOT NULL DEFAULT '',
  `graphic` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`idnum`),
  KEY `finished` (`finished`),
  KEY `event` (`event`),
  KEY `minlevel` (`minlevel`,`maxlevel`),
  KEY `host` (`host`),
  KEY `fee` (`fee`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_globals`
--

CREATE TABLE `monster_globals` (
  `name` varchar(32) NOT NULL DEFAULT '',
  `type` enum('string','number','list','yorn') NOT NULL DEFAULT 'string',
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_gods`
--

CREATE TABLE `monster_gods` (
  `id` enum('rigzivizgi','gijubi','kirikashu') NOT NULL DEFAULT 'gijubi',
  `name` varchar(18) NOT NULL DEFAULT '',
  `title` varchar(24) NOT NULL,
  `attitude` tinyint(4) NOT NULL DEFAULT '0',
  `contributions` int(10) unsigned NOT NULL DEFAULT '0',
  `currentvalue` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_graphics`
--

CREATE TABLE `monster_graphics` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL DEFAULT '',
  `graphic` varchar(64) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `names` text NOT NULL,
  `year` smallint(5) unsigned NOT NULL DEFAULT '2006',
  `rights` varchar(16) NOT NULL DEFAULT '',
  `source` varchar(200) NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `rights` (`rights`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_graphicslibrary`
--

CREATE TABLE `monster_graphicslibrary` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uploader` int(10) unsigned NOT NULL DEFAULT '0',
  `recipient` int(10) unsigned NOT NULL DEFAULT '0',
  `url` varchar(64) NOT NULL DEFAULT '',
  `w` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `h` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `author` varchar(64) NOT NULL DEFAULT '',
  `rights` enum('reserved','pd_found','pd_released','unlimited') NOT NULL DEFAULT 'reserved',
  `source` varchar(200) NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `uploader` (`uploader`),
  KEY `recipient` (`recipient`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_houses`
--

CREATE TABLE `monster_houses` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `locid` int(10) unsigned NOT NULL DEFAULT '1',
  `lasthour` int(10) unsigned NOT NULL DEFAULT '0',
  `hoursearned` int(10) unsigned NOT NULL DEFAULT '0',
  `curbulk` int(10) unsigned NOT NULL DEFAULT '0',
  `maxbulk` int(10) unsigned NOT NULL DEFAULT '0',
  `curbasement` smallint(5) unsigned NOT NULL DEFAULT '0',
  `maxbasement` smallint(5) unsigned NOT NULL DEFAULT '100',
  `addons` text NOT NULL,
  `rooms` text NOT NULL,
  `nopet_rooms` text NOT NULL,
  `wallpapers` text NOT NULL,
  `curroom` varchar(12) NOT NULL DEFAULT '',
  `sort` varchar(16) NOT NULL DEFAULT 'idnum',
  `view` enum('icons','details') NOT NULL DEFAULT 'icons',
  `rats` enum('no','yes') NOT NULL DEFAULT 'no',
  `worn_indicator` enum('color','text','none') NOT NULL DEFAULT 'color',
  `construction_number` int(10) unsigned NOT NULL DEFAULT '1',
  `max_rooms_shown` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `max_addons_shown` tinyint(3) unsigned NOT NULL DEFAULT '255',
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`,`locid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_inventory`
--

CREATE TABLE `monster_inventory` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(16) NOT NULL DEFAULT '',
  `original_owner` varchar(16) NOT NULL,
  `creator` varchar(16) NOT NULL DEFAULT '',
  `esteembonus` enum('yes','no') NOT NULL DEFAULT 'yes',
  `itemname` varchar(64) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `message2` text NOT NULL,
  `data` text NOT NULL,
  `health` smallint(5) unsigned NOT NULL DEFAULT '0',
  `location` varchar(16) NOT NULL DEFAULT 'incoming',
  `roomid` int(10) unsigned NOT NULL DEFAULT '0',
  `locid` int(10) unsigned NOT NULL DEFAULT '1',
  `forsale` int(10) unsigned NOT NULL DEFAULT '0',
  `changed` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `user_index` (`user`(15)),
  KEY `location_index` (`location`),
  KEY `forsale` (`forsale`),
  KEY `itemname` (`itemname`(12)),
  KEY `changed` (`changed`),
  KEY `user_location` (`user`,`location`),
  KEY `message` (`message`(10))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_items`
--

CREATE TABLE `monster_items` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `implementationtime` int(10) unsigned NOT NULL DEFAULT '0',
  `itemname` varchar(64) NOT NULL DEFAULT '',
  `itemtype` varchar(32) NOT NULL DEFAULT '',
  `anagramname` varchar(64) NOT NULL DEFAULT '',
  `bigname` enum('no','yes') NOT NULL DEFAULT 'no',
  `custom` enum('no','yes','limited','secret') NOT NULL DEFAULT 'no',
  `can_pawn_with` enum('no','yes') NOT NULL DEFAULT 'no',
  `can_pawn_for` enum('no','yes') NOT NULL DEFAULT 'no',
  `playdesc` varchar(48) NOT NULL DEFAULT '',
  `bulk` smallint(5) unsigned NOT NULL DEFAULT '0',
  `weight` smallint(5) unsigned NOT NULL DEFAULT '0',
  `flammability` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `graphictype` enum('bitmap','flash') NOT NULL DEFAULT 'bitmap',
  `graphic` varchar(32) NOT NULL DEFAULT '',
  `recycle_for` text NOT NULL,
  `can_recycle` enum('no','yes') NOT NULL DEFAULT 'no',
  `recycle_fraction` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `additional_flavors` text NOT NULL,
  `durability` smallint(5) unsigned NOT NULL DEFAULT '0',
  `value` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `key_id` int(10) unsigned NOT NULL DEFAULT '0',
  `is_equipment` enum('no','yes') NOT NULL DEFAULT 'no',
  `equipeffect` varchar(64) NOT NULL DEFAULT '',
  `equipreqs` varchar(64) NOT NULL DEFAULT '',
  `equip_is_revised` enum('no','yes') NOT NULL DEFAULT 'yes',
  `req_str` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `req_dex` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `req_athletics` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `req_sta` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `req_int` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `req_per` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `req_wit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `equip_open` tinyint(4) NOT NULL DEFAULT '0',
  `equip_extraverted` tinyint(4) NOT NULL DEFAULT '0',
  `equip_conscientious` tinyint(4) NOT NULL DEFAULT '0',
  `equip_playful` tinyint(4) NOT NULL DEFAULT '0',
  `equip_independent` tinyint(4) NOT NULL DEFAULT '0',
  `equip_str` tinyint(4) NOT NULL DEFAULT '0',
  `equip_dex` tinyint(4) NOT NULL DEFAULT '0',
  `equip_sta` tinyint(4) NOT NULL DEFAULT '0',
  `equip_int` tinyint(4) NOT NULL DEFAULT '0',
  `equip_per` tinyint(4) NOT NULL DEFAULT '0',
  `equip_wit` tinyint(4) NOT NULL DEFAULT '0',
  `equip_mining` tinyint(4) NOT NULL DEFAULT '0',
  `equip_lumberjacking` tinyint(4) NOT NULL DEFAULT '0',
  `equip_fishing` tinyint(4) NOT NULL DEFAULT '0',
  `equip_painting` tinyint(4) NOT NULL DEFAULT '0',
  `equip_sculpting` tinyint(4) NOT NULL DEFAULT '0',
  `equip_carpentry` tinyint(4) NOT NULL DEFAULT '0',
  `equip_jeweling` tinyint(4) NOT NULL DEFAULT '0',
  `equip_electronics` tinyint(4) NOT NULL DEFAULT '0',
  `equip_mechanics` tinyint(4) NOT NULL DEFAULT '0',
  `equip_adventuring` tinyint(4) NOT NULL DEFAULT '0',
  `equip_hunting` tinyint(4) NOT NULL DEFAULT '0',
  `equip_gathering` tinyint(4) NOT NULL DEFAULT '0',
  `equip_smithing` tinyint(4) NOT NULL DEFAULT '0',
  `equip_tailoring` tinyint(4) NOT NULL DEFAULT '0',
  `equip_leather` tinyint(4) NOT NULL DEFAULT '0',
  `equip_crafting` tinyint(4) NOT NULL DEFAULT '0',
  `equip_binding` tinyint(4) NOT NULL DEFAULT '0',
  `equip_chemistry` tinyint(4) NOT NULL DEFAULT '0',
  `equip_piloting` tinyint(4) NOT NULL DEFAULT '0',
  `equip_gardening` tinyint(4) NOT NULL DEFAULT '0',
  `equip_stealth` tinyint(4) NOT NULL DEFAULT '0',
  `equip_athletics` tinyint(4) NOT NULL DEFAULT '0',
  `equip_fertility` tinyint(4) NOT NULL DEFAULT '0',
  `equip_goldenmushroom` enum('no','yes') NOT NULL DEFAULT 'no',
  `equip_vampire_slayer` enum('no','yes') NOT NULL DEFAULT 'no',
  `equip_berry_craft` enum('no','yes') NOT NULL DEFAULT 'no',
  `equip_were_killer` enum('no','yes') NOT NULL DEFAULT 'no',
  `equip_pressurized` enum('no','yes') NOT NULL DEFAULT 'no',
  `equip_flight` enum('no','yes') NOT NULL DEFAULT 'no',
  `equip_fire_immunity` enum('no','yes') NOT NULL DEFAULT 'no',
  `equip_chill_touch` enum('no','yes') NOT NULL DEFAULT 'no',
  `equip_healing` enum('no','yes') NOT NULL DEFAULT 'no',
  `equip_more_dreams` enum('no','yes') NOT NULL DEFAULT 'no',
  `equipreincarnateonly` enum('no','yes') NOT NULL DEFAULT 'no',
  `equipl33tonly` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_grocery` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_edible` enum('no','yes') NOT NULL DEFAULT 'no',
  `max_conscientiousness` tinyint(4) NOT NULL DEFAULT '0',
  `ediblehealing` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ediblecaffeine` tinyint(4) NOT NULL DEFAULT '0',
  `edibleenergy` tinyint(4) NOT NULL DEFAULT '0',
  `ediblefood` tinyint(4) NOT NULL DEFAULT '0',
  `ediblesafety` tinyint(4) NOT NULL DEFAULT '0',
  `ediblelove` tinyint(4) NOT NULL DEFAULT '0',
  `edibleesteem` tinyint(4) NOT NULL DEFAULT '0',
  `playbed` enum('no','yes') NOT NULL DEFAULT 'no',
  `playfood` tinyint(4) NOT NULL DEFAULT '0',
  `playsafety` tinyint(4) NOT NULL DEFAULT '0',
  `playlove` tinyint(4) NOT NULL DEFAULT '0',
  `playesteem` tinyint(4) NOT NULL DEFAULT '0',
  `playstat` varchar(10) NOT NULL DEFAULT '',
  `hourlyfood` tinyint(4) NOT NULL DEFAULT '0',
  `hourlysafety` tinyint(4) NOT NULL DEFAULT '0',
  `hourlylove` tinyint(4) NOT NULL DEFAULT '0',
  `hourlyesteem` tinyint(4) NOT NULL DEFAULT '0',
  `hourlystat` varchar(10) NOT NULL DEFAULT '',
  `book_text` longtext NOT NULL,
  `action` text NOT NULL,
  `enc_entry` text NOT NULL,
  `treasurevalue` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rare` enum('yes','no') NOT NULL DEFAULT 'no',
  `treasure` enum('yes','no') NOT NULL DEFAULT 'no',
  `nomarket` enum('yes','no') NOT NULL DEFAULT 'no',
  `noexchange` enum('yes','no') NOT NULL DEFAULT 'no',
  `nosellback` enum('yes','no') NOT NULL DEFAULT 'no',
  `cursed` enum('yes','no') NOT NULL DEFAULT 'no',
  `cancombine` enum('no','yes') NOT NULL DEFAULT 'no',
  `norepair` enum('yes','no') NOT NULL DEFAULT 'no',
  `questitem` enum('no','yes') NOT NULL DEFAULT 'no',
  `admin_notes` text NOT NULL,
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `itemname` (`itemname`),
  KEY `cursed` (`cursed`),
  KEY `rare` (`rare`),
  KEY `ediblefood` (`ediblefood`),
  KEY `itemtype` (`itemtype`),
  KEY `value` (`value`),
  KEY `custom` (`custom`),
  KEY `bulk` (`bulk`),
  KEY `can_pawn_for` (`can_pawn_for`),
  KEY `is_equipment` (`is_equipment`),
  KEY `is_edible` (`is_edible`),
  KEY `can_recycle` (`can_recycle`),
  KEY `can_pawn_with` (`can_pawn_with`),
  KEY `treasure` (`treasure`),
  KEY `nosellback` (`nosellback`),
  KEY `key_id` (`key_id`),
  KEY `anagramname` (`anagramname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Table structure for table `monster_loginhistory`
--

CREATE TABLE `monster_loginhistory` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `ipaddress` varchar(15) NOT NULL DEFAULT '',
  KEY `userid` (`userid`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_mail`
--

CREATE TABLE `monster_mail` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(16) NOT NULL DEFAULT '',
  `to` varchar(16) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` tinytext NOT NULL,
  `message` mediumtext NOT NULL,
  `starred` enum('no','yes') NOT NULL DEFAULT 'no',
  `new` enum('yes','no') NOT NULL DEFAULT 'yes',
  `replied` enum('yes','no') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `attachments` int(10) unsigned NOT NULL DEFAULT '0',
  `location` varchar(32) NOT NULL DEFAULT 'post',
  `updated` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `to_index` (`to`(15)),
  KEY `date_index` (`date`),
  KEY `from` (`from`),
  KEY `new` (`new`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_monsters`
--

CREATE TABLE `monster_monsters` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `lycanthrope` varchar(56) NOT NULL DEFAULT '',
  `name` varchar(56) NOT NULL DEFAULT '',
  `type` varchar(32) NOT NULL DEFAULT '',
  `graphic` varchar(64) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `prizes` text NOT NULL,
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `needs_key` int(10) unsigned NOT NULL DEFAULT '0',
  `min_stealth` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_stamina` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_athletics` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_wits` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_vampire` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_berries` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_flying` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_burny` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_sensitive_to_cold` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_in_space` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_deep_sea` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `name` (`name`),
  KEY `rarity` (`level`),
  KEY `min_month` (`min_month`,`max_month`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_passreset`
--

CREATE TABLE `monster_passreset` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `resetid` int(10) unsigned NOT NULL DEFAULT '0',
  `newpass` varchar(32) NOT NULL DEFAULT '',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `userid_2` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_petlogs`
--

CREATE TABLE `monster_petlogs` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `petid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('hourly','realtime') NOT NULL DEFAULT 'hourly',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `hour` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `energy` tinyint(4) NOT NULL DEFAULT '0',
  `food` tinyint(4) NOT NULL DEFAULT '0',
  `safety` tinyint(4) NOT NULL DEFAULT '0',
  `love` tinyint(4) NOT NULL DEFAULT '0',
  `esteem` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`,`timestamp`),
  KEY `petid` (`petid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_pets`
--

CREATE TABLE `monster_pets` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locid` int(10) unsigned NOT NULL DEFAULT '1',
  `location` enum('home','shelter') NOT NULL DEFAULT 'home',
  `orderid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `protected` enum('yes','no') NOT NULL DEFAULT 'yes',
  `birthedtouser` int(10) unsigned NOT NULL DEFAULT '0',
  `user` varchar(16) NOT NULL DEFAULT '',
  `ownerid` int(10) unsigned NOT NULL,
  `original` enum('no','yes') NOT NULL DEFAULT 'yes',
  `petname` varchar(32) NOT NULL DEFAULT '',
  `mininote` varchar(255) NOT NULL DEFAULT '',
  `free_rename` enum('no','yes') NOT NULL DEFAULT 'no',
  `generation` smallint(5) unsigned NOT NULL DEFAULT '1',
  `motherid` int(10) unsigned NOT NULL DEFAULT '0',
  `fatherid` int(10) unsigned NOT NULL DEFAULT '0',
  `birthday` int(10) unsigned NOT NULL DEFAULT '0',
  `gender` enum('male','female') NOT NULL DEFAULT 'male',
  `attraction_to_males` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `attraction_to_females` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `bloodtype` varchar(4) NOT NULL DEFAULT '',
  `bloodtype_revealed` enum('no','yes') NOT NULL DEFAULT 'no',
  `prolific` enum('yes','no') NOT NULL DEFAULT 'yes',
  `graphic` varchar(32) NOT NULL DEFAULT '',
  `graphic_flip` enum('no','yes') NOT NULL DEFAULT 'no',
  `graphic_size` tinyint(3) unsigned NOT NULL DEFAULT '48',
  `park_event_hours` tinyint(3) unsigned NOT NULL DEFAULT '24',
  `sleeping` enum('yes','no') NOT NULL DEFAULT 'no',
  `extraverted` tinyint(3) unsigned NOT NULL DEFAULT '5',
  `open` tinyint(3) unsigned NOT NULL DEFAULT '5',
  `conscientious` tinyint(3) unsigned NOT NULL DEFAULT '5',
  `playful` tinyint(3) unsigned NOT NULL DEFAULT '5',
  `independent` tinyint(3) unsigned NOT NULL DEFAULT '5',
  `asleep_time` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `energy` smallint(6) NOT NULL DEFAULT '10',
  `food` smallint(6) NOT NULL DEFAULT '0',
  `safety` smallint(6) NOT NULL DEFAULT '0',
  `love` smallint(6) NOT NULL DEFAULT '0',
  `esteem` smallint(6) NOT NULL DEFAULT '0',
  `caffeinated` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `inspired` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `actions_since_last_level` int(10) unsigned NOT NULL DEFAULT '0',
  `love_exp` int(10) unsigned NOT NULL DEFAULT '0',
  `love_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `str` smallint(5) unsigned NOT NULL DEFAULT '0',
  `dex` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sta` smallint(5) unsigned NOT NULL DEFAULT '0',
  `per` smallint(5) unsigned NOT NULL DEFAULT '0',
  `int` smallint(5) unsigned NOT NULL DEFAULT '0',
  `wit` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bra` smallint(5) unsigned NOT NULL DEFAULT '0',
  `athletics` smallint(5) unsigned NOT NULL DEFAULT '0',
  `stealth` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sur` smallint(5) unsigned NOT NULL DEFAULT '0',
  `gathering` smallint(5) unsigned NOT NULL DEFAULT '0',
  `fishing` smallint(5) unsigned NOT NULL DEFAULT '0',
  `mining` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cra` smallint(5) unsigned NOT NULL DEFAULT '0',
  `painting` smallint(5) unsigned NOT NULL DEFAULT '0',
  `carpentry` smallint(5) unsigned NOT NULL DEFAULT '0',
  `jeweling` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sculpting` smallint(5) unsigned NOT NULL DEFAULT '0',
  `eng` smallint(5) unsigned NOT NULL DEFAULT '0',
  `mechanics` smallint(5) unsigned NOT NULL DEFAULT '0',
  `chemistry` smallint(5) unsigned NOT NULL DEFAULT '0',
  `smi` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tai` smallint(5) unsigned NOT NULL DEFAULT '0',
  `leather` smallint(5) unsigned NOT NULL DEFAULT '0',
  `binding` smallint(5) unsigned NOT NULL DEFAULT '0',
  `pil` smallint(5) unsigned NOT NULL DEFAULT '0',
  `astronomy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `str_count` int(10) unsigned NOT NULL DEFAULT '0',
  `dex_count` int(10) unsigned NOT NULL DEFAULT '0',
  `sta_count` int(10) unsigned NOT NULL DEFAULT '0',
  `per_count` int(10) unsigned NOT NULL DEFAULT '0',
  `int_count` int(10) unsigned NOT NULL DEFAULT '0',
  `wit_count` int(10) unsigned NOT NULL DEFAULT '0',
  `bra_count` int(10) unsigned NOT NULL DEFAULT '0',
  `athletics_count` int(10) unsigned NOT NULL DEFAULT '0',
  `stealth_count` int(10) unsigned NOT NULL DEFAULT '0',
  `sur_count` int(10) unsigned NOT NULL DEFAULT '0',
  `gathering_count` int(10) unsigned NOT NULL DEFAULT '0',
  `fishing_count` int(10) unsigned NOT NULL DEFAULT '0',
  `mining_count` int(10) unsigned NOT NULL DEFAULT '0',
  `cra_count` int(10) unsigned NOT NULL DEFAULT '0',
  `painting_count` int(10) unsigned NOT NULL DEFAULT '0',
  `carpentry_count` int(10) unsigned NOT NULL DEFAULT '0',
  `jeweling_count` int(10) unsigned NOT NULL DEFAULT '0',
  `sculpting_count` int(10) unsigned NOT NULL DEFAULT '0',
  `eng_count` int(10) unsigned NOT NULL DEFAULT '0',
  `mechanics_count` int(10) unsigned NOT NULL DEFAULT '0',
  `chemistry_count` int(10) unsigned NOT NULL DEFAULT '0',
  `smi_count` int(10) unsigned NOT NULL DEFAULT '0',
  `tai_count` int(10) unsigned NOT NULL DEFAULT '0',
  `leather_count` int(10) unsigned NOT NULL DEFAULT '0',
  `binding_count` int(10) unsigned NOT NULL DEFAULT '0',
  `pil_count` int(10) unsigned NOT NULL DEFAULT '0',
  `astronomy_count` int(10) unsigned NOT NULL DEFAULT '0',
  `music_count` int(10) unsigned NOT NULL DEFAULT '0',
  `merit_steady_hands` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_light_sleeper` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_acute_senses` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_catlike_balance` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_tough_hide` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_lightning_calculator` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_silver_tongue` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_lucky` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_medium` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_berserker` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_predicts_earthquakes` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_ravenous` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_careful_with_equipment` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_transparent` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_pruriency` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_sleep_walker` enum('no','yes') NOT NULL DEFAULT 'no',
  `merit_moonkin` enum('no','yes') NOT NULL DEFAULT 'no',
  `knack_mechanics` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_electronics` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_hunting` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_gathering` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_smithing` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_tailoring` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_leather` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_adventuring` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_crafting` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_painting` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_carpentry` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_sculpting` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_jeweling` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_mining` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_lumberjacking` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_fishing` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_binding` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_chemistry` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_gardening` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `knack_videogames` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `special_firebreathing` enum('no','yes') NOT NULL DEFAULT 'no',
  `special_chameleon` enum('no','yes') NOT NULL DEFAULT 'no',
  `special_love` enum('no','yes') NOT NULL DEFAULT 'no',
  `special_sparkles` enum('no','yes') NOT NULL DEFAULT 'no',
  `special_digital` enum('no','yes') NOT NULL DEFAULT 'no',
  `special_lightning` enum('no','yes') NOT NULL DEFAULT 'no',
  `incarnation` int(10) unsigned NOT NULL DEFAULT '1',
  `ascend` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_adventurer` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_hunter` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_inventor` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_artist` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_gatherer` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_smith` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_tailor` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_leather` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_fisher` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_lumberjack` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_miner` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_carpenter` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_jeweler` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_painter` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_sculptor` enum('no','yes') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'no',
  `ascend_mechanic` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_binder` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_chemist` enum('no','yes') NOT NULL DEFAULT 'no',
  `ascend_vhagst` enum('no','yes') NOT NULL DEFAULT 'no',
  `toolid` int(10) unsigned NOT NULL DEFAULT '0',
  `keyid` int(10) unsigned NOT NULL DEFAULT '0',
  `costumed` enum('no','yes') NOT NULL DEFAULT 'no',
  `lastlevelquestion` int(10) unsigned NOT NULL DEFAULT '0',
  `levelquestion` int(10) unsigned NOT NULL DEFAULT '0',
  `last_check` int(10) unsigned NOT NULL DEFAULT '0',
  `last_love` int(10) unsigned NOT NULL DEFAULT '0',
  `last_love_action` int(11) NOT NULL,
  `last_love_by` int(10) unsigned NOT NULL DEFAULT '0',
  `pregnant_asof` int(10) unsigned NOT NULL DEFAULT '0',
  `pregnant_by` varchar(64) NOT NULL DEFAULT '',
  `last_log_check` int(10) unsigned NOT NULL DEFAULT '0',
  `dead` enum('no','starved','magical','bonestaff') NOT NULL DEFAULT 'no',
  `history` text NOT NULL,
  `size` smallint(5) unsigned NOT NULL DEFAULT '0',
  `nasty_wound` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `healing` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sneezing` enum('no','yes') NOT NULL DEFAULT 'no',
  `lycanthrope` enum('no','yes') NOT NULL DEFAULT 'no',
  `changed` enum('no','yes') NOT NULL DEFAULT 'no',
  `eggplant` enum('no','yes') NOT NULL DEFAULT 'no',
  `zombie` enum('no','yes') NOT NULL DEFAULT 'no',
  `sleepuntil` int(10) unsigned NOT NULL DEFAULT '0',
  `trickedaliens` enum('no','yes') NOT NULL DEFAULT 'no',
  `jousting` enum('no','yes') NOT NULL DEFAULT 'no',
  `free_respec` enum('no','yes') NOT NULL DEFAULT 'no',
  `revealed_skills` enum('no','yes') NOT NULL DEFAULT 'no',
  `revealed_relationship_preferences` enum('no','yes') NOT NULL DEFAULT 'no',
  `revealed_preferences` enum('no','yes') NOT NULL DEFAULT 'no',
  `likes_flavor` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `dislikes_flavor` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `likes_color` enum('none','red','orange','yellow','green','blue','purple','pink','white','black') NOT NULL DEFAULT 'none',
  PRIMARY KEY (`idnum`),
  KEY `user` (`user`),
  KEY `orderid` (`orderid`),
  KEY `locid` (`locid`),
  KEY `location` (`location`),
  KEY `petname` (`petname`),
  KEY `motherid` (`motherid`),
  KEY `costumed` (`costumed`),
  KEY `fatherid` (`fatherid`),
  KEY `gender` (`gender`),
  KEY `prolific` (`prolific`),
  KEY `zombie` (`zombie`),
  KEY `ownerid` (`ownerid`),
  KEY `graphic` (`graphic`),
  KEY `toolid` (`toolid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_plaza`
--

CREATE TABLE `monster_plaza` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `guidelines` varchar(255) NOT NULL DEFAULT '',
  `graphic` varchar(32) NOT NULL DEFAULT '',
  `show_thumbs` enum('no','yes') NOT NULL DEFAULT 'no',
  `agereq` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `updatedate` int(10) unsigned NOT NULL DEFAULT '0',
  `replies` int(10) unsigned NOT NULL DEFAULT '0',
  `admins` text NOT NULL,
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `locked` enum('no','yes') NOT NULL DEFAULT 'no',
  `newthreadlock` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `groupid` (`groupid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_posts`
--

CREATE TABLE `monster_posts` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `threadid` int(10) unsigned NOT NULL DEFAULT '0',
  `forkedthreadid` int(10) unsigned NOT NULL DEFAULT '0',
  `locked` enum('no','yes') NOT NULL DEFAULT 'no',
  `says` varchar(20) NOT NULL DEFAULT 'says',
  `title` text NOT NULL,
  `action` varchar(160) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `creationdate` int(10) unsigned NOT NULL DEFAULT '0',
  `updatedate` int(10) unsigned NOT NULL DEFAULT '0',
  `createdby` int(10) unsigned NOT NULL DEFAULT '0',
  `egg` enum('none','taken','red','blue','yellow','rainbow','silver','gold') NOT NULL DEFAULT 'none',
  `goldstars` smallint(5) unsigned NOT NULL DEFAULT '0',
  `background` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `troll_flag` enum('no','yes') NOT NULL DEFAULT 'no',
  `voted_on` enum('no','yes') NOT NULL DEFAULT 'no',
  `updated` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `threadid_index` (`threadid`),
  KEY `creationdate` (`creationdate`),
  KEY `goldstars` (`goldstars`),
  KEY `createdby` (`createdby`),
  KEY `updated` (`updated`),
  FULLTEXT KEY `body_index` (`body`),
  FULLTEXT KEY `title_index` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_prey`
--

CREATE TABLE `monster_prey` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity` enum('hunt','fish') NOT NULL DEFAULT 'hunt',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(56) NOT NULL DEFAULT '',
  `type` varchar(32) NOT NULL DEFAULT '',
  `graphic` varchar(64) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `prizes` text NOT NULL,
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `needs_key` int(10) unsigned NOT NULL DEFAULT '0',
  `min_stealth` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_stamina` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_athletics` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_wits` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_vampire` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_berries` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_flying` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_burny` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_sensitive_to_cold` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_in_space` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_deep_sea` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `name` (`name`),
  KEY `level` (`level`),
  KEY `min_month` (`min_month`,`max_month`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_profiles`
--

CREATE TABLE `monster_profiles` (
  `idnum` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(24) NOT NULL DEFAULT '',
  `pronunciation` varchar(30) NOT NULL,
  `enabled` enum('yes','no') NOT NULL DEFAULT 'no',
  `aim` varchar(64) NOT NULL DEFAULT '',
  `yahoo` varchar(64) NOT NULL DEFAULT '',
  `msn` varchar(64) NOT NULL DEFAULT '',
  `skype` varchar(64) NOT NULL DEFAULT '',
  `url` varchar(128) NOT NULL DEFAULT '',
  `facebook` varchar(32) NOT NULL DEFAULT '',
  `myspace` varchar(32) NOT NULL DEFAULT '',
  `show_age` enum('no','yes') NOT NULL DEFAULT 'yes',
  `gender` enum('none','male','female') NOT NULL DEFAULT 'none',
  `location` varchar(64) NOT NULL DEFAULT '',
  `locationsearch` enum('no','yes') NOT NULL DEFAULT 'no',
  `zip` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `latitude` float NOT NULL DEFAULT '0',
  `longitude` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `enabled` (`enabled`),
  KEY `latitude` (`latitude`,`longitude`),
  KEY `locationsearch` (`locationsearch`),
  KEY `name` (`name`(8))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_projects`
--

CREATE TABLE `monster_projects` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `complete` enum('no','yes') NOT NULL DEFAULT 'no',
  `completetime` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `locid` int(10) unsigned NOT NULL DEFAULT '1',
  `priority` enum('no','yes') NOT NULL DEFAULT 'no',
  `itemid` int(10) unsigned NOT NULL DEFAULT '0',
  `creator` varchar(16) NOT NULL DEFAULT '',
  `projectid` int(10) unsigned NOT NULL DEFAULT '0',
  `progress` int(10) unsigned NOT NULL DEFAULT '0',
  `notes` text NOT NULL,
  `destination` varchar(16) NOT NULL DEFAULT 'home',
  PRIMARY KEY (`idnum`),
  KEY `location` (`locid`),
  KEY `complete` (`complete`),
  KEY `userid` (`userid`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_recipes`
--

CREATE TABLE `monster_recipes` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ingredients` varchar(128) NOT NULL DEFAULT '',
  `makes` varchar(160) NOT NULL,
  `availability` enum('standard','limited','monthly','recurring') NOT NULL DEFAULT 'standard',
  `machine_only` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `ingredients` (`ingredients`),
  KEY `machine_only` (`machine_only`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_reports`
--

CREATE TABLE `monster_reports` (
  `plazaid` int(10) unsigned NOT NULL DEFAULT '0',
  `threadid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reports` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`threadid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_smith`
--

CREATE TABLE `monster_smith` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `secret` enum('yes','no') NOT NULL DEFAULT 'no',
  `type` enum('smith','tailor','alchemy') NOT NULL DEFAULT 'smith',
  `supplies` text NOT NULL,
  `makes` text NOT NULL,
  `cost` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_statistics`
--

CREATE TABLE `monster_statistics` (
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(64) NOT NULL DEFAULT '',
  `numusers` int(10) unsigned NOT NULL DEFAULT '0',
  `numactiveusers` int(10) unsigned NOT NULL DEFAULT '0',
  `nummonthlyusers` int(10) unsigned NOT NULL DEFAULT '0',
  `numweeklyusers` int(10) unsigned NOT NULL DEFAULT '0',
  `numpets` int(10) unsigned NOT NULL DEFAULT '0',
  `numactivepets` int(10) unsigned NOT NULL DEFAULT '0',
  `cash` bigint(20) unsigned NOT NULL DEFAULT '0',
  `savings` bigint(20) unsigned NOT NULL DEFAULT '0',
  `objects` int(10) unsigned NOT NULL DEFAULT '0',
  `totallevels` int(10) unsigned NOT NULL DEFAULT '0',
  `maxlevel` int(10) unsigned NOT NULL DEFAULT '0',
  `malepets` int(10) unsigned NOT NULL DEFAULT '0',
  `deadpets` int(10) unsigned NOT NULL DEFAULT '0',
  `pregnantpets` int(10) unsigned NOT NULL DEFAULT '0',
  `adson` int(10) unsigned NOT NULL DEFAULT '0',
  `activeadson` int(10) unsigned NOT NULL DEFAULT '0',
  `numlurkers` int(10) unsigned NOT NULL DEFAULT '0',
  `numposts` int(10) unsigned NOT NULL DEFAULT '0',
  `numposters` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_threads`
--

CREATE TABLE `monster_threads` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `plaza` int(10) unsigned NOT NULL DEFAULT '0',
  `opening_post_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` text NOT NULL,
  `highlight` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `rating` enum('unrated','adult') NOT NULL DEFAULT 'unrated',
  `sticky` enum('no','yes') NOT NULL DEFAULT 'no',
  `locked` enum('no','yes') NOT NULL DEFAULT 'no',
  `creationdate` int(10) unsigned NOT NULL DEFAULT '0',
  `updatedate` int(10) unsigned NOT NULL DEFAULT '0',
  `createdby` int(10) unsigned NOT NULL DEFAULT '0',
  `updateby` int(10) unsigned NOT NULL DEFAULT '0',
  `replies` int(10) unsigned NOT NULL DEFAULT '0',
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `plaza` (`plaza`),
  KEY `creationdate` (`creationdate`),
  KEY `updatedate` (`updatedate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_trades`
--

CREATE TABLE `monster_trades` (
  `tradeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid1` int(10) unsigned NOT NULL DEFAULT '0',
  `userid2` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `anonymous` enum('no','yes') NOT NULL DEFAULT 'no',
  `gift` enum('no','yes') NOT NULL DEFAULT 'no',
  `step` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `dialog` varchar(40) NOT NULL DEFAULT '',
  `items1` longtext NOT NULL,
  `itemsdesc1` longtext NOT NULL,
  `money1` int(10) unsigned NOT NULL DEFAULT '0',
  `items2` longtext NOT NULL,
  `itemsdesc2` longtext NOT NULL,
  `money2` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tradeid`),
  KEY `timestamp` (`timestamp`),
  KEY `userid1` (`userid1`,`userid2`),
  KEY `step` (`step`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_transactions`
--

CREATE TABLE `monster_transactions` (
  `user` varchar(16) NOT NULL DEFAULT '',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `description` varchar(120) NOT NULL DEFAULT '',
  `details` text NOT NULL,
  `amount` mediumint(9) NOT NULL DEFAULT '0',
  KEY `user` (`user`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_users`
--

CREATE TABLE `monster_users` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_artist` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_npc` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_admin` enum('no','yes') NOT NULL DEFAULT 'no',
  `user` varchar(16) NOT NULL DEFAULT '',
  `pass` varchar(32) NOT NULL DEFAULT '',
  `display` varchar(24) NOT NULL DEFAULT '',
  `display_normalized` varchar(24) NOT NULL,
  `title` varchar(56) NOT NULL DEFAULT 'Resident',
  `email` varchar(48) NOT NULL DEFAULT '',
  `newemail` varchar(48) NOT NULL DEFAULT '',
  `passworddate` int(10) unsigned NOT NULL DEFAULT '0',
  `birthday` date NOT NULL DEFAULT '0000-00-00',
  `readtos` enum('no','yes') NOT NULL DEFAULT 'no',
  `locid` int(10) unsigned NOT NULL DEFAULT '1',
  `alt_group` int(10) unsigned NOT NULL DEFAULT '0',
  `graphic` varchar(48) NOT NULL DEFAULT 'crazyface.png',
  `is_a_whale` enum('no','yes') NOT NULL DEFAULT 'no',
  `color` varchar(6) NOT NULL DEFAULT '77aadd',
  `multi_login` enum('no','yes') NOT NULL DEFAULT 'no',
  `login_persist` int(10) unsigned NOT NULL DEFAULT '86400',
  `flower_receipt` enum('no','yes') NOT NULL DEFAULT 'yes',
  `childlockout` enum('no','yes') NOT NULL DEFAULT 'no',
  `parentalaccess` enum('no','yes') NOT NULL DEFAULT 'no',
  `parentalpassword` varchar(32) NOT NULL DEFAULT '',
  `parentalemail` varchar(64) NOT NULL DEFAULT '',
  `profile_wall` varchar(50) NOT NULL,
  `profile_wall_repeat` enum('no','yes','horizontal','vertical') NOT NULL DEFAULT 'no',
  `cornergraphic` varchar(16) NOT NULL DEFAULT '',
  `meteor` enum('no','yes') NOT NULL DEFAULT 'no',
  `emote` enum('no','yes') NOT NULL DEFAULT 'yes',
  `defaultstyle` varchar(50) NOT NULL DEFAULT '',
  `last_ip_address` varchar(16) NOT NULL DEFAULT '',
  `bindip` enum('no','yes') NOT NULL DEFAULT 'no',
  `can_edit_wiki` enum('yes','no') NOT NULL DEFAULT 'yes',
  `is_a_bad_person` enum('yes','no') NOT NULL DEFAULT 'no',
  `warned` enum('yes','no') NOT NULL DEFAULT 'no',
  `easter_warning` enum('yes','no') NOT NULL DEFAULT 'no',
  `disabled` enum('yes','no') NOT NULL DEFAULT 'no',
  `activated` enum('yes','no') NOT NULL DEFAULT 'no',
  `signupdate` int(10) unsigned NOT NULL DEFAULT '0',
  `activateid` int(10) unsigned NOT NULL DEFAULT '0',
  `sessionid` int(10) unsigned NOT NULL DEFAULT '0',
  `allowance` enum('standard','food','resources','rizivizi','gizubi','kaera') NOT NULL DEFAULT 'food',
  `lastchat` int(10) unsigned NOT NULL DEFAULT '0',
  `lastactivity` int(10) unsigned NOT NULL DEFAULT '0',
  `logintime` int(10) unsigned NOT NULL DEFAULT '0',
  `nextreward` int(10) unsigned NOT NULL DEFAULT '0',
  `auto_spend_hours` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `confirm_skip` enum('no','yes') NOT NULL DEFAULT 'no',
  `menu_popup_setting` enum('click','mouseover','mouseenter') NOT NULL DEFAULT 'mouseover',
  `menu_floating` enum('no','yes') NOT NULL DEFAULT 'yes',
  `encyclopedia_popup` enum('no','yes') NOT NULL DEFAULT 'yes',
  `inventory_context_menu` enum('no','yes') NOT NULL DEFAULT 'yes',
  `defaultlove` varchar(64) NOT NULL DEFAULT '',
  `defaultto` varchar(24) NOT NULL DEFAULT 'storage',
  `storagesort` enum('idnum','bulk','itemname','itemtype','ediblefood','message') NOT NULL DEFAULT 'itemtype',
  `incomingsort` enum('itemname ASC','changed DESC') NOT NULL DEFAULT 'itemname ASC',
  `storageview` enum('icons','details','stacked') NOT NULL DEFAULT 'icons',
  `postsize` smallint(5) unsigned NOT NULL DEFAULT '100',
  `license` enum('yes','no') NOT NULL DEFAULT 'no',
  `breeder` enum('no','yes') NOT NULL DEFAULT 'no',
  `openstore` enum('yes','no') NOT NULL DEFAULT 'no',
  `storeclosed` enum('no','yes') NOT NULL DEFAULT 'no',
  `savings_pay_storage` enum('no','yes') NOT NULL DEFAULT 'no',
  `money` int(11) NOT NULL DEFAULT '0',
  `savings` varchar(32) NOT NULL DEFAULT '',
  `rupees` smallint(6) NOT NULL DEFAULT '0',
  `karma` int(11) NOT NULL DEFAULT '0',
  `pixels` int(11) NOT NULL DEFAULT '0',
  `greenhouse_points` int(11) NOT NULL DEFAULT '0',
  `fireworks` text NOT NULL,
  `stickers_to_give` int(11) NOT NULL DEFAULT '0',
  `stickers_given` int(11) NOT NULL DEFAULT '0',
  `groups` text NOT NULL,
  `publicfriends` enum('yes','no') NOT NULL DEFAULT 'yes',
  `profilecomments` enum('all','friends','none') NOT NULL DEFAULT 'all',
  `showmimic` enum('no','yes') NOT NULL DEFAULT 'yes',
  `incomingto` enum('storage/incoming','storage') NOT NULL DEFAULT 'storage/incoming',
  `newevent` int(10) unsigned NOT NULL DEFAULT '0',
  `newcomment` enum('yes','no') NOT NULL DEFAULT 'no',
  `postofficesort` enum('datea','dated','froma','fromd','subjecta','subjectd') NOT NULL DEFAULT 'dated',
  `newcityhallpost` enum('no','yes') NOT NULL DEFAULT 'yes',
  `wishlistupdate` enum('no','yes') NOT NULL DEFAULT 'no',
  `newpoll` enum('no','yes') NOT NULL DEFAULT 'no',
  `newtrade` enum('no','yes') NOT NULL DEFAULT 'no',
  `newmail` enum('no','yes') NOT NULL DEFAULT 'no',
  `newincoming` enum('no','yes') NOT NULL DEFAULT 'no',
  `newgroupinvite` enum('no','yes') NOT NULL DEFAULT 'no',
  `newchangelogentries` enum('no','yes') NOT NULL DEFAULT 'no',
  `notifynewchangelogentries` enum('small','medium','large','never') NOT NULL DEFAULT 'medium',
  `new_bid` enum('no','yes') NOT NULL DEFAULT 'no',
  `bankflag` enum('no','yes') NOT NULL DEFAULT 'no',
  `lastbankvisit` int(10) unsigned NOT NULL DEFAULT '0',
  `totalsells` int(10) unsigned NOT NULL DEFAULT '0',
  `totalvalue` int(10) unsigned NOT NULL DEFAULT '0',
  `event_step` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_graphic` varchar(32) NOT NULL DEFAULT '',
  `event_name` varchar(32) NOT NULL DEFAULT '',
  `event_type` varchar(32) NOT NULL DEFAULT '',
  `event_minlevel` smallint(5) unsigned NOT NULL DEFAULT '0',
  `event_maxlevel` smallint(5) unsigned NOT NULL DEFAULT '0',
  `event_size` smallint(5) unsigned NOT NULL DEFAULT '0',
  `event_fee` smallint(5) unsigned NOT NULL DEFAULT '0',
  `event_descript` text NOT NULL,
  `event_prereport` text NOT NULL,
  `event_postreport` text NOT NULL,
  `timezone` float NOT NULL DEFAULT '-5',
  `daylightsavings` enum('no','yes') NOT NULL DEFAULT 'yes',
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `inventorylink` enum('no','yes') NOT NULL DEFAULT 'yes',
  `iconhoverbox` enum('no','yes') NOT NULL DEFAULT 'yes',
  `storename` varchar(48) NOT NULL DEFAULT 'The Unnamed Market',
  `stack_mystore_items` enum('no','yes') NOT NULL DEFAULT 'no',
  `mailboxes` text NOT NULL,
  `pagehits` int(10) unsigned NOT NULL DEFAULT '0',
  `threadtrack` smallint(5) unsigned NOT NULL DEFAULT '12',
  `style_layout` varchar(16) NOT NULL DEFAULT 'default',
  `style_color` varchar(16) NOT NULL DEFAULT 'telkoth',
  `style_background` varchar(32) NOT NULL DEFAULT 'stars.png',
  `recycling_view` enum('grouped','ungrouped') NOT NULL DEFAULT 'grouped',
  `levelupbar` enum('graphic','numeric','both') NOT NULL DEFAULT 'both',
  `backlightnew` enum('yes','no') NOT NULL DEFAULT 'yes',
  `backlighttime` smallint(5) unsigned NOT NULL DEFAULT '120',
  `badges` varchar(64) NOT NULL DEFAULT '',
  `mazeloc` int(10) unsigned NOT NULL DEFAULT '0',
  `mazemp` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `pattern_movement_history` varchar(6) NOT NULL,
  `pattern_item_room` varchar(16) NOT NULL DEFAULT 'storage',
  `tot` int(10) unsigned NOT NULL DEFAULT '0',
  `tot_done` int(10) unsigned NOT NULL DEFAULT '0',
  `tot_time` int(10) unsigned NOT NULL DEFAULT '0',
  `ghosts_caught` int(10) unsigned NOT NULL DEFAULT '0',
  `got_he_map` enum('yes','no') NOT NULL DEFAULT 'no',
  `newgoldstar` enum('yes','no') NOT NULL DEFAULT 'no',
  `adoptedtoday` enum('no','yes') NOT NULL DEFAULT 'no',
  `last_daycare_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `show_totemgardern` enum('no','yes') NOT NULL DEFAULT 'no',
  `show_pattern` enum('no','yes') NOT NULL DEFAULT 'no',
  `show_temple` enum('no','yes') NOT NULL DEFAULT 'no',
  `show_mysteriousshop` enum('no','yes') NOT NULL DEFAULT 'no',
  `show_aerosoc` enum('no','yes') NOT NULL DEFAULT 'no',
  `show_universe` enum('no','yes') NOT NULL DEFAULT 'no',
  `show_volcano` enum('no','yes') NOT NULL DEFAULT 'no',
  `show_park` enum('no','yes') NOT NULL DEFAULT 'no',
  `show_florist` enum('no','yes') NOT NULL DEFAULT 'no',
  `show_smithery` enum('no','yes') NOT NULL DEFAULT 'no',
  `show_alchemist` enum('no','yes') NOT NULL,
  `show_ark` enum('no','yes') NOT NULL DEFAULT 'no',
  `show_roleplay` enum('no','yes') NOT NULL DEFAULT 'no',
  `equip_and_home` enum('no','yes') NOT NULL DEFAULT 'yes',
  `receive_giving_tree_gifts` enum('no','yes') NOT NULL DEFAULT 'yes',
  `tips_enabled` enum('no','yes') NOT NULL DEFAULT 'yes',
  `show_tip` enum('no','yes') NOT NULL DEFAULT 'no',
  `tip_number` int(10) unsigned NOT NULL DEFAULT '0',
  `lastclickcheck` int(10) unsigned NOT NULL DEFAULT '0',
  `clickcount` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lastclient` text NOT NULL,
  `lastcountry` varchar(2) NOT NULL DEFAULT 'XX',
  `take_survey_please` enum('no','yes') NOT NULL DEFAULT 'no',
  `newfriend` enum('no','yes') NOT NULL DEFAULT 'no',
  `daily_threadviews` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `daily_posts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `museumcount` int(10) unsigned NOT NULL DEFAULT '0',
  `arkcount` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `comicvoteban` enum('no','yes') NOT NULL DEFAULT 'no',
  `pvp_message` enum('no','yes') NOT NULL DEFAULT 'no',
  `email_personal` enum('no','yes') NOT NULL DEFAULT 'no',
  `email_game` enum('no','yes') NOT NULL DEFAULT 'no',
  `toasty` enum('no','yes') NOT NULL DEFAULT 'no',
  `no_hours_fool` enum('no','yes') NOT NULL DEFAULT 'no',
  `autosorterrecording` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `user` (`user`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `display_2` (`display`),
  KEY `lastactivity` (`lastactivity`),
  KEY `disabled` (`disabled`,`activated`),
  KEY `openstore` (`openstore`),
  KEY `museumcount` (`museumcount`),
  KEY `arkcount` (`arkcount`),
  KEY `is_admin` (`is_admin`),
  KEY `mazeloc` (`mazeloc`),
  KEY `notifynewchangelogentries` (`notifynewchangelogentries`),
  KEY `alt_group` (`alt_group`),
  KEY `lastcountry` (`lastcountry`),
  KEY `receive_giving_tree_gifts` (`receive_giving_tree_gifts`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_watchermove`
--

CREATE TABLE `monster_watchermove` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `watcher` int(10) unsigned NOT NULL DEFAULT '0',
  `threadid` int(10) unsigned NOT NULL DEFAULT '0',
  `destination` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `monster_watching`
--

CREATE TABLE `monster_watching` (
  `user` varchar(32) NOT NULL DEFAULT '',
  `threadid` int(11) NOT NULL DEFAULT '0',
  `lastread` int(10) unsigned NOT NULL DEFAULT '0',
  `reported` enum('no','yes') NOT NULL DEFAULT 'no',
  `voted` enum('no','yes') NOT NULL DEFAULT 'no',
  KEY `user_index` (`user`(15)),
  KEY `threadid` (`threadid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypetsrp_characters`
--

CREATE TABLE `psypetsrp_characters` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `storyid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `experience` smallint(5) unsigned NOT NULL DEFAULT '0',
  `training_points` int(10) unsigned NOT NULL DEFAULT '3',
  `gender` varchar(10) NOT NULL DEFAULT '',
  `age` smallint(5) unsigned NOT NULL,
  `description` text NOT NULL,
  `history` text NOT NULL,
  `max_hp` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `cur_hp` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `max_sp` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `cur_sp` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `max_fate` tinyint(4) NOT NULL DEFAULT '3',
  `cur_fate` tinyint(4) NOT NULL DEFAULT '3',
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`),
  KEY `storyid` (`storyid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypetsrp_character_skills`
--

CREATE TABLE `psypetsrp_character_skills` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `characterid` int(10) unsigned NOT NULL,
  `skillid` int(10) unsigned NOT NULL,
  `type` varchar(40) NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `characterid` (`characterid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypetsrp_items`
--

CREATE TABLE `psypetsrp_items` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypetsrp_schedule`
--

CREATE TABLE `psypetsrp_schedule` (
  `idnum` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `hour` int(11) NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypetsrp_skills`
--

CREATE TABLE `psypetsrp_skills` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `level` tinyint(3) unsigned NOT NULL,
  `type` varchar(40) NOT NULL,
  `role` set('Defense','Damage','Control','Utility','Support') NOT NULL,
  `cost_to_use` varchar(40) NOT NULL,
  `duration` varchar(120) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `name` (`name`),
  KEY `level` (`level`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypetsrp_stories`
--

CREATE TABLE `psypetsrp_stories` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `storytellerid` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `summary` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `storytellerid` (`storytellerid`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_404_log`
--

CREATE TABLE `psypets_404_log` (
  `url` varchar(200) NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `lastlog` int(10) unsigned NOT NULL,
  KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_abusereports`
--

CREATE TABLE `psypets_abusereports` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('thread','mail','post') NOT NULL DEFAULT 'thread',
  `threadid` int(10) unsigned NOT NULL DEFAULT '0',
  `page` smallint(5) unsigned NOT NULL DEFAULT '0',
  `reporter` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `original_text` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `threadid` (`threadid`,`reporter`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_adventure`
--

CREATE TABLE `psypets_adventure` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `level` smallint(5) unsigned NOT NULL,
  `next_adventure` int(10) unsigned NOT NULL,
  `progress` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `difficulty` mediumint(8) unsigned NOT NULL,
  `stats` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `prize` varchar(100) NOT NULL,
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_advertising`
--

CREATE TABLE `psypets_advertising` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `permanent` enum('yes','no') NOT NULL DEFAULT 'no',
  `expirytime` int(10) unsigned NOT NULL DEFAULT '0',
  `ad` text NOT NULL,
  `voters` text NOT NULL,
  `vote` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `expiry` (`expirytime`,`permanent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_airships`
--

CREATE TABLE `psypets_airships` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ownerid` int(10) unsigned NOT NULL DEFAULT '0',
  `disabled` enum('no','yes') NOT NULL DEFAULT 'no',
  `name` varchar(32) NOT NULL DEFAULT '',
  `power` mediumint(9) NOT NULL DEFAULT '0',
  `mana` mediumint(9) NOT NULL DEFAULT '0',
  `seats` mediumint(9) NOT NULL DEFAULT '0',
  `attack` mediumint(9) NOT NULL DEFAULT '0',
  `defense` mediumint(9) NOT NULL DEFAULT '0',
  `special` mediumint(9) NOT NULL DEFAULT '0',
  `weight` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `bulk` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `maxbulk` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `propulsion` mediumint(9) NOT NULL DEFAULT '0',
  `chassis` varchar(64) NOT NULL DEFAULT '',
  `parts` text NOT NULL,
  `crewids` text NOT NULL,
  `returntime` int(10) unsigned NOT NULL DEFAULT '0',
  `wins` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `losses` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `ownerid` (`ownerid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_alchemy`
--

CREATE TABLE `psypets_alchemy` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('special','zodiac','material','potion') NOT NULL DEFAULT 'zodiac',
  `month` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `items_in` text NOT NULL,
  `item_out` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`idnum`),
  KEY `item_out` (`item_out`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_apiaries`
--

CREATE TABLE `psypets_apiaries` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `experience` smallint(5) unsigned NOT NULL DEFAULT '0',
  `nextfeeding` int(10) unsigned NOT NULL DEFAULT '0',
  `progress_sugar` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `progress_wax` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `progress_pansy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `progress_blueprint` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `progress_wand` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `progress_royaljelly` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `progress_honeycomb` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_aquariums`
--

CREATE TABLE `psypets_aquariums` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `king_name` varchar(32) NOT NULL,
  `trouble_time` int(10) unsigned NOT NULL DEFAULT '0',
  `item_needed` varchar(64) NOT NULL,
  `next_reward` varchar(64) NOT NULL,
  `happy` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_arcadegames`
--

CREATE TABLE `psypets_arcadegames` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(56) NOT NULL DEFAULT '',
  `type` varchar(32) NOT NULL DEFAULT '',
  `graphic` varchar(24) NOT NULL DEFAULT '',
  `prizes` text NOT NULL,
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `name` (`name`),
  KEY `level` (`level`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_ark`
--

CREATE TABLE `psypets_ark` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `graphic` varchar(32) NOT NULL DEFAULT '',
  `gender` enum('male','female') NOT NULL DEFAULT 'male',
  `petid` int(10) unsigned NOT NULL,
  KEY `userid` (`userid`),
  KEY `pet_type` (`graphic`(12),`gender`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_auctions`
--

CREATE TABLE `psypets_auctions` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ownerid` int(10) unsigned NOT NULL,
  `inventoryids` text NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `itemid` int(10) unsigned NOT NULL,
  `minimumbid` int(10) unsigned NOT NULL DEFAULT '1',
  `posttime` int(10) unsigned NOT NULL,
  `expirationtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `ownerid` (`ownerid`,`expirationtime`),
  KEY `posttime` (`posttime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_auction_bids`
--

CREATE TABLE `psypets_auction_bids` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `auctionid` int(10) unsigned NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`,`auctionid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_autosort`
--

CREATE TABLE `psypets_autosort` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `itemname` varchar(64) NOT NULL,
  `room` varchar(16) NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`),
  KEY `room` (`room`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_badges`
--

CREATE TABLE `psypets_badges` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `secure` enum('no','yes') NOT NULL DEFAULT 'no',
  `paidaccount` enum('no','yes') NOT NULL DEFAULT 'no',
  `ltc` enum('no','yes') NOT NULL DEFAULT 'no',
  `ltb` enum('no','yes') NOT NULL DEFAULT 'no',
  `ridiculous` enum('no','yes') NOT NULL DEFAULT 'no',
  `thousandaire` enum('no','yes') NOT NULL DEFAULT 'no',
  `millionaire` enum('no','yes') NOT NULL DEFAULT 'no',
  `artist` enum('no','yes') NOT NULL DEFAULT 'no',
  `mansion` enum('no','yes') NOT NULL DEFAULT 'no',
  `castle` enum('no','yes') NOT NULL DEFAULT 'no',
  `island` enum('no','yes') NOT NULL DEFAULT 'no',
  `islandplus` enum('no','yes') NOT NULL DEFAULT 'no',
  `worstideaever` enum('no','yes') NOT NULL DEFAULT 'no',
  `giver` enum('no','yes') NOT NULL DEFAULT 'no',
  `giverplus` enum('no','yes') NOT NULL DEFAULT 'no',
  `recycler` enum('no','yes') NOT NULL DEFAULT 'no',
  `yaynature` enum('no','yes') NOT NULL DEFAULT 'no',
  `gamesell` enum('no','yes') NOT NULL DEFAULT 'no',
  `gamesellmore` enum('no','yes') NOT NULL DEFAULT 'no',
  `spender` enum('no','yes') NOT NULL DEFAULT 'no',
  `egyptian` enum('no','yes') NOT NULL DEFAULT 'no',
  `koboldkiller` enum('no','yes') NOT NULL DEFAULT 'no',
  `sirenslayer` enum('no','yes') NOT NULL DEFAULT 'no',
  `manticoremenace` enum('no','yes') NOT NULL DEFAULT 'no',
  `museum_wing` enum('no','yes') NOT NULL DEFAULT 'no',
  `museum_plus` enum('no','yes') NOT NULL DEFAULT 'no',
  `dna` enum('no','yes') NOT NULL DEFAULT 'no',
  `pantheon` enum('no','yes') NOT NULL DEFAULT 'no',
  `pantheon_ii` enum('no','yes') NOT NULL DEFAULT 'no',
  `ranger` enum('no','yes') NOT NULL DEFAULT 'no',
  `goldstar` enum('no','yes') NOT NULL DEFAULT 'no',
  `starhoarder` enum('no','yes') NOT NULL DEFAULT 'no',
  `adopter` enum('no','yes') NOT NULL DEFAULT 'no',
  `goatherder` enum('no','yes') NOT NULL DEFAULT 'no',
  `over9000` enum('no','yes') NOT NULL DEFAULT 'no',
  `hangman` enum('no','yes') NOT NULL DEFAULT 'no',
  `execute` enum('no','yes') NOT NULL DEFAULT 'no',
  `brickhouse` enum('no','yes') NOT NULL DEFAULT 'no',
  `maze-1` enum('no','yes') NOT NULL DEFAULT 'no',
  `maze-2` enum('no','yes') NOT NULL DEFAULT 'no',
  `maze-final` enum('no','yes') NOT NULL DEFAULT 'no',
  `rollem` enum('no','yes') NOT NULL DEFAULT 'no',
  `graffiti` enum('no','yes') NOT NULL DEFAULT 'no',
  `ringbearer` enum('no','yes') NOT NULL DEFAULT 'no',
  `fairyfriend` enum('no','yes') NOT NULL DEFAULT 'no',
  `pyrophiliac` enum('no','yes') NOT NULL DEFAULT 'no',
  `trireme_burner` enum('no','yes') NOT NULL DEFAULT 'no',
  `beekeeper` enum('no','yes') NOT NULL DEFAULT 'no',
  `oranges` enum('no','yes') NOT NULL DEFAULT 'no',
  `cardplayer` enum('no','yes') NOT NULL DEFAULT 'no',
  `shrinky` enum('no','yes') NOT NULL DEFAULT 'no',
  `bathtime` enum('no','yes') NOT NULL DEFAULT 'no',
  `rush` enum('no','yes') NOT NULL DEFAULT 'no',
  `jellier` enum('no','yes') NOT NULL DEFAULT 'no',
  `toasty` enum('no','yes') NOT NULL DEFAULT 'no',
  `zombielord` enum('no','yes') NOT NULL DEFAULT 'no',
  `wizardhat` enum('no','yes') NOT NULL DEFAULT 'no',
  `fisher` enum('no','yes') NOT NULL DEFAULT 'no',
  `fish-in-barrel` enum('no','yes') NOT NULL DEFAULT 'no',
  `royal` enum('no','yes') NOT NULL DEFAULT 'no',
  `materia` enum('no','yes') NOT NULL DEFAULT 'no',
  `sealearth` enum('no','yes') NOT NULL DEFAULT 'no',
  `toadstool` enum('no','yes') NOT NULL DEFAULT 'no',
  `horseshoe` enum('no','yes') NOT NULL DEFAULT 'no',
  `demolition` enum('no','yes') NOT NULL DEFAULT 'no',
  `cordonbleu` enum('no','yes') NOT NULL DEFAULT 'no',
  `archaeologist` enum('no','yes') NOT NULL DEFAULT 'no',
  `cryptographer` enum('no','yes') NOT NULL DEFAULT 'no',
  `seeker` enum('no','yes') NOT NULL DEFAULT 'no',
  `plushycollector` enum('no','yes') NOT NULL DEFAULT 'no',
  `hamletcollector` enum('no','yes') NOT NULL DEFAULT 'no',
  `gemstonecollector` enum('no','yes') NOT NULL DEFAULT 'no',
  `gamer` enum('no','yes') NOT NULL DEFAULT 'no',
  `colossus` enum('no','yes') NOT NULL DEFAULT 'no',
  `defenderofearth` enum('no','yes') NOT NULL DEFAULT 'no',
  `ihavealife` enum('no','yes') NOT NULL DEFAULT 'no',
  `leonids` enum('no','yes') NOT NULL DEFAULT 'no',
  `stpatricks` enum('no','yes') NOT NULL DEFAULT 'no',
  `stpatricks2` enum('no','yes') NOT NULL DEFAULT 'no',
  `stpatricks3` enum('no','yes') NOT NULL DEFAULT 'no',
  `str_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `dex_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `sta_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `int_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `wit_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `per_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `athletics_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `stealth_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `bra_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `sur_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `gathering_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `fishing_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `mining_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `cra_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `painting_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `carpentry_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `jeweling_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `sculpting_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `eng_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `mechanics_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `chemistry_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `smi_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `tai_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `binding_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `pil_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `astronomy_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `music_trainer` enum('no','yes') NOT NULL DEFAULT 'no',
  `trained_20` enum('no','yes') NOT NULL DEFAULT 'no',
  `level20` enum('no','yes') NOT NULL DEFAULT 'no',
  `level50` enum('no','yes') NOT NULL DEFAULT 'no',
  `level100` enum('no','yes') NOT NULL DEFAULT 'no',
  `reincarnate20` enum('no','yes') NOT NULL DEFAULT 'no',
  `reincarnate30` enum('no','yes') NOT NULL DEFAULT 'no',
  `reincarnate40` enum('no','yes') NOT NULL DEFAULT 'no',
  `reincarnate50` enum('no','yes') NOT NULL DEFAULT 'no',
  `sixmonthaccount` enum('no','yes') NOT NULL DEFAULT 'no',
  `oneyearaccount` enum('no','yes') NOT NULL DEFAULT 'no',
  `twoyearaccount` enum('no','yes') NOT NULL DEFAULT 'no',
  `threeyearaccount` enum('no','yes') NOT NULL DEFAULT 'no',
  `fouryearaccount` enum('no','yes') NOT NULL DEFAULT 'no',
  `fiveyearaccount` enum('no','yes') NOT NULL DEFAULT 'no',
  `10badges` enum('no','yes') NOT NULL DEFAULT 'no',
  `20badges` enum('no','yes') NOT NULL DEFAULT 'no',
  `30badges` enum('no','yes') NOT NULL DEFAULT 'no',
  `40badges` enum('no','yes') NOT NULL DEFAULT 'no',
  `50badges` enum('no','yes') NOT NULL DEFAULT 'no',
  `60badges` enum('no','yes') NOT NULL DEFAULT 'no',
  `70badges` enum('no','yes') NOT NULL DEFAULT 'no',
  `80badges` enum('no','yes') NOT NULL DEFAULT 'no',
  `90badges` enum('no','yes') NOT NULL DEFAULT 'no',
  `100badges` enum('no','yes') NOT NULL DEFAULT 'no',
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_basement`
--

CREATE TABLE `psypets_basement` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `locid` int(10) unsigned NOT NULL DEFAULT '0',
  `itemname` varchar(64) NOT NULL DEFAULT '',
  `quantity` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`,`itemname`),
  KEY `locid` (`locid`),
  KEY `itemname` (`itemname`(25))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_bindings`
--

CREATE TABLE `psypets_bindings` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '120',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `addon` enum('no','yes') NOT NULL DEFAULT 'no',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_berries` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_burny` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_sensitive_to_cold` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_botreport`
--

CREATE TABLE `psypets_botreport` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `clicks` smallint(5) unsigned NOT NULL DEFAULT '0',
  `useragent` text NOT NULL,
  KEY `userid` (`userid`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_broadcasting_topics`
--

CREATE TABLE `psypets_broadcasting_topics` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `residentid` int(10) unsigned NOT NULL,
  `vote` varchar(80) NOT NULL,
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `residentid` (`residentid`),
  KEY `vote` (`vote`(10))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_cardgame`
--

CREATE TABLE `psypets_cardgame` (
  `userid` int(10) unsigned NOT NULL,
  `cards` varchar(42) NOT NULL,
  `mask` varchar(42) NOT NULL DEFAULT '??????????????????????????????????????????',
  `tries` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `flipped` enum('0','1') NOT NULL DEFAULT '0',
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_carpentry`
--

CREATE TABLE `psypets_carpentry` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '127',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `addon` enum('no','yes') NOT NULL DEFAULT 'no',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_berries` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_changelog`
--

CREATE TABLE `psypets_changelog` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL,
  `category` enum('bugfix','newpet','newitem','renameditem','newfeature','uichange','removedfeature','mechanicschange','newtext','textcorrection','admintool','performance') NOT NULL,
  `impact` enum('small','medium','large') NOT NULL,
  `summary` varchar(160) NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `category` (`category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_chemistry`
--

CREATE TABLE `psypets_chemistry` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '120',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `addon` enum('no','yes') NOT NULL DEFAULT 'no',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_berries` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_burny` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `mazeable` (`mazeable`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_civilizations`
--

CREATE TABLE `psypets_civilizations` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `universeid` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `homeworldid` int(10) unsigned NOT NULL,
  `num_planets` smallint(5) unsigned NOT NULL DEFAULT '1',
  `total_population` int(10) unsigned NOT NULL,
  `tech_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `wealth` smallint(6) NOT NULL DEFAULT '0',
  `philosophy` enum('god','clockmaker','athiest','matrix') NOT NULL DEFAULT 'god',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `homeworldid` (`homeworldid`),
  KEY `universeid` (`universeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_crafts`
--

CREATE TABLE `psypets_crafts` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '127',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `addon` enum('no','yes') NOT NULL DEFAULT 'no',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_berries` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_burny` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_dailychallenge`
--

CREATE TABLE `psypets_dailychallenge` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastchallenge` varchar(8) NOT NULL DEFAULT '',
  `difficulty` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bonus_room` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `puzzle` text NOT NULL,
  `step` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `failed` enum('no','yes') NOT NULL DEFAULT 'no',
  `plastic` smallint(5) unsigned NOT NULL DEFAULT '0',
  `copper` smallint(5) unsigned NOT NULL DEFAULT '0',
  `silver` smallint(5) unsigned NOT NULL DEFAULT '0',
  `gold` smallint(5) unsigned NOT NULL DEFAULT '0',
  `platinum` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_daily_report_stats`
--

CREATE TABLE `psypets_daily_report_stats` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `name` varchar(200) NOT NULL,
  `value` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `date` (`date`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_dreidel_logs`
--

CREATE TABLE `psypets_dreidel_logs` (
  `timestamp` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `result` enum('Shin','Nun','Gimel','Hay','(joined game)') NOT NULL,
  `potchange` int(11) NOT NULL,
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_dungeons`
--

CREATE TABLE `psypets_dungeons` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `locid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastadventure` int(10) unsigned NOT NULL DEFAULT '0',
  `monsters` text NOT NULL,
  `sortby` enum('level DESC','level ASC') NOT NULL DEFAULT 'level DESC',
  KEY `userid` (`userid`,`locid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_failedlogins`
--

CREATE TABLE `psypets_failedlogins` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `user_exists` enum('no','yes') NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `username` (`username`,`ip`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_farms`
--

CREATE TABLE `psypets_farms` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `field_active` enum('no','yes') NOT NULL DEFAULT 'no',
  `field_crop` enum('Wheat','Rye','Rice','Wild Oats','Fluff','Barley','Hops') NOT NULL DEFAULT 'Wheat',
  `silo_quantity` tinyint(4) NOT NULL DEFAULT '0',
  `coop_has_timer` enum('no','yes') NOT NULL DEFAULT 'no',
  `coop_feed_time` int(10) unsigned NOT NULL DEFAULT '0',
  `coop_egg` smallint(6) NOT NULL DEFAULT '0',
  `coop_speckled_egg` smallint(6) NOT NULL DEFAULT '0',
  `coop_blue_egg` smallint(6) NOT NULL DEFAULT '0',
  `coop_gargantuan_egg` smallint(6) NOT NULL DEFAULT '0',
  `coop_feather` smallint(6) NOT NULL DEFAULT '0',
  `coop_phoenix_down` smallint(6) NOT NULL DEFAULT '0',
  `coop_rubber_chicken` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_feature_drive`
--

CREATE TABLE `psypets_feature_drive` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `residentid` int(10) unsigned NOT NULL,
  `vote` varchar(20) NOT NULL,
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `residentid` (`residentid`),
  KEY `vote` (`vote`(10))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_fiberoptic_link_titles`
--

CREATE TABLE `psypets_fiberoptic_link_titles` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `residentid` int(10) unsigned NOT NULL,
  `title` varchar(56) NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `residentid` (`residentid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_fireplaces`
--

CREATE TABLE `psypets_fireplaces` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `locid` int(10) unsigned NOT NULL DEFAULT '0',
  `fire` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fireduration` int(10) unsigned NOT NULL DEFAULT '0',
  `mantle` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`,`locid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_fireplace_log`
--

CREATE TABLE `psypets_fireplace_log` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `event` varchar(60) NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_flash_messages`
--

CREATE TABLE `psypets_flash_messages` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL,
  `new` enum('no','yes') NOT NULL DEFAULT 'yes',
  `userid` int(10) unsigned NOT NULL,
  `category` tinyint(3) unsigned NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_friendreport`
--

CREATE TABLE `psypets_friendreport` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `friendedby` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_galactic_objects`
--

CREATE TABLE `psypets_galactic_objects` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `universeid` int(10) unsigned NOT NULL,
  `type` enum('spiral','spiral_agn','elliptical','elliptical_agn','cloud') NOT NULL,
  `image` varchar(30) NOT NULL,
  `x` smallint(5) unsigned NOT NULL,
  `y` smallint(5) unsigned NOT NULL,
  `name` varchar(60) NOT NULL,
  `creationdate` int(10) unsigned NOT NULL,
  `stardust` int(10) unsigned NOT NULL DEFAULT '0',
  `solar_system_count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `universeid` (`universeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_gamesold`
--

CREATE TABLE `psypets_gamesold` (
  `itemname` varchar(64) NOT NULL,
  `transaction` enum('sold','greenhoused','recycled','pawned','exchanged','tossed') NOT NULL DEFAULT 'sold',
  `quantity` int(10) unsigned NOT NULL,
  KEY `transaction` (`transaction`),
  KEY `quantity` (`quantity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_game_rooms`
--

CREATE TABLE `psypets_game_rooms` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `money` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_game_room_games`
--

CREATE TABLE `psypets_game_room_games` (
  `userid` int(10) unsigned NOT NULL,
  `gameid` int(10) unsigned NOT NULL,
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_gardening`
--

CREATE TABLE `psypets_gardening` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_graveyard`
--

CREATE TABLE `psypets_graveyard` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locid` int(10) unsigned NOT NULL DEFAULT '0',
  `ownerid` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `tombstone` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `petname` varchar(32) NOT NULL DEFAULT '',
  `petid` int(10) unsigned NOT NULL DEFAULT '0',
  `epitaph` varchar(64) NOT NULL DEFAULT '',
  `ghost` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `ownerid` (`ownerid`),
  KEY `locid` (`locid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_groupboxlogs`
--

CREATE TABLE `psypets_groupboxlogs` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('add','remove') NOT NULL DEFAULT 'add',
  `details` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `type` (`type`),
  KEY `groupid` (`groupid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_groups`
--

CREATE TABLE `psypets_groups` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `systemgroup` enum('no','yes') NOT NULL DEFAULT 'no',
  `name` varchar(64) NOT NULL DEFAULT '',
  `graphic` varchar(32) NOT NULL DEFAULT '',
  `leaderid` int(10) unsigned NOT NULL DEFAULT '0',
  `members` text NOT NULL,
  `member_count` int(10) unsigned NOT NULL DEFAULT '0',
  `towntiles` int(10) unsigned NOT NULL DEFAULT '0',
  `birthdate` int(10) unsigned NOT NULL DEFAULT '0',
  `profile` text NOT NULL,
  `forumid` int(10) unsigned NOT NULL DEFAULT '0',
  `badge-year-1` enum('no','yes') NOT NULL DEFAULT 'no',
  `badge-year-2` enum('no','yes') NOT NULL DEFAULT 'no',
  `badge-year-3` enum('no','yes') NOT NULL DEFAULT 'no',
  `badge-year-4` enum('no','yes') NOT NULL DEFAULT 'no',
  `badge-year-plus` enum('no','yes') NOT NULL DEFAULT 'no',
  `badge-company` enum('no','yes') NOT NULL DEFAULT 'no',
  `badge-crowd` enum('no','yes') NOT NULL DEFAULT 'no',
  `badge-village` enum('no','yes') NOT NULL DEFAULT 'no',
  `badge-town` enum('no','yes') NOT NULL DEFAULT 'no',
  `badge-city` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `name` (`name`),
  KEY `birthdate` (`birthdate`),
  KEY `member_count` (`member_count`),
  KEY `towntiles` (`towntiles`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_group_currencies`
--

CREATE TABLE `psypets_group_currencies` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` int(10) unsigned NOT NULL,
  `type` enum('pet','resident') NOT NULL DEFAULT 'resident',
  `name` varchar(20) NOT NULL,
  `symbol` varchar(3) NOT NULL,
  `is_money` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `groupid` (`groupid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_group_invites`
--

CREATE TABLE `psypets_group_invites` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `residentid` int(10) unsigned NOT NULL DEFAULT '0',
  `message` varchar(160) NOT NULL DEFAULT '',
  PRIMARY KEY (`idnum`),
  KEY `guildid` (`groupid`,`residentid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_group_pet_currencies`
--

CREATE TABLE `psypets_group_pet_currencies` (
  `currencyid` int(10) unsigned NOT NULL,
  `petid` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  KEY `currencyid` (`currencyid`,`petid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_group_player_currencies`
--

CREATE TABLE `psypets_group_player_currencies` (
  `userid` int(10) unsigned NOT NULL,
  `currencyid` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  `hidden` enum('no','yes') NOT NULL DEFAULT 'no',
  KEY `userid` (`userid`,`currencyid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_group_ranks`
--

CREATE TABLE `psypets_group_ranks` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL DEFAULT '',
  `power` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rights` text NOT NULL,
  KEY `idnum` (`idnum`,`groupid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_homeimprovement`
--

CREATE TABLE `psypets_homeimprovement` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `requirement` smallint(5) unsigned NOT NULL DEFAULT '0',
  `craft_reqs` text NOT NULL,
  PRIMARY KEY (`idnum`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_ideachart`
--

CREATE TABLE `psypets_ideachart` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `postdate` int(10) unsigned NOT NULL DEFAULT '0',
  `sdesc` varchar(120) NOT NULL DEFAULT '',
  `ldesc` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `authorid` int(10) unsigned NOT NULL DEFAULT '0',
  `threadid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `postdate` (`postdate`),
  KEY `pillar` (`category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_ideachart_complete`
--

CREATE TABLE `psypets_ideachart_complete` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `postdate` int(10) unsigned NOT NULL DEFAULT '0',
  `completedate` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(40) NOT NULL DEFAULT 'implemented',
  `sdesc` varchar(120) NOT NULL DEFAULT '',
  `ldesc` text NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT '',
  `authorid` int(10) unsigned NOT NULL DEFAULT '0',
  `moverid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `completedate` (`completedate`,`status`,`postdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_ideachart_tags`
--

CREATE TABLE `psypets_ideachart_tags` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ideaid` int(10) unsigned NOT NULL,
  `tag` varchar(30) NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `ideaid` (`ideaid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_ideavotes`
--

CREATE TABLE `psypets_ideavotes` (
  `ideaid` int(10) unsigned NOT NULL DEFAULT '0',
  `residentid` int(10) unsigned NOT NULL DEFAULT '0',
  `votes` smallint(6) NOT NULL DEFAULT '0',
  KEY `ideaid` (`ideaid`,`residentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_inventions`
--

CREATE TABLE `psypets_inventions` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '127',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `addon` enum('no','yes') NOT NULL DEFAULT 'no',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `mazeable` (`mazeable`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_item_sales`
--

CREATE TABLE `psypets_item_sales` (
  `date` date NOT NULL,
  `seller` int(10) unsigned NOT NULL,
  `buyer` int(10) unsigned NOT NULL,
  `itemid` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `batch_price` int(10) unsigned NOT NULL,
  `individual_price` decimal(10,2) unsigned NOT NULL,
  KEY `date` (`date`,`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_jewelry`
--

CREATE TABLE `psypets_jewelry` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '120',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `addon` enum('no','yes') NOT NULL DEFAULT 'no',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_berries` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_burny` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_known_recipes`
--

CREATE TABLE `psypets_known_recipes` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `recipeid` int(10) unsigned NOT NULL,
  `learned_on` int(10) unsigned NOT NULL,
  `times_prepared` int(10) unsigned NOT NULL DEFAULT '1',
  `favorite` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`),
  KEY `times_prepared` (`times_prepared`,`favorite`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_lakes`
--

CREATE TABLE `psypets_lakes` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `duckies` int(10) unsigned NOT NULL DEFAULT '0',
  `boats` text NOT NULL,
  `fountain` enum('no','yes') NOT NULL DEFAULT 'no',
  `waterfall` enum('no','yes') NOT NULL DEFAULT 'no',
  `tunnel` enum('no','yes') NOT NULL DEFAULT 'no',
  `monster` varchar(100) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_leatherworks`
--

CREATE TABLE `psypets_leatherworks` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '120',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_berries` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_burny` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `min_month` (`min_month`,`max_month`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_libraries`
--

CREATE TABLE `psypets_libraries` (
  `userid` int(10) unsigned NOT NULL,
  `itemid` int(10) unsigned NOT NULL,
  `quantity` mediumint(8) unsigned NOT NULL,
  KEY `userid` (`userid`,`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_locations`
--

CREATE TABLE `psypets_locations` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` enum('gather','mine','lumberjack') NOT NULL DEFAULT 'gather',
  `name` varchar(56) NOT NULL DEFAULT '',
  `prizes` text NOT NULL,
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `needs_key` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `name` (`name`),
  KEY `level` (`level`),
  KEY `min_month` (`min_month`,`max_month`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_maprooms`
--

CREATE TABLE `psypets_maprooms` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `locations` text NOT NULL,
  `sortby` enum('level DESC','level ASC') NOT NULL DEFAULT 'level DESC',
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_maze`
--

CREATE TABLE `psypets_maze` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `x` int(11) NOT NULL DEFAULT '0',
  `y` int(11) NOT NULL DEFAULT '0',
  `z` int(11) NOT NULL DEFAULT '0',
  `tile` varchar(4) NOT NULL DEFAULT '',
  `treasure` varchar(64) NOT NULL DEFAULT '',
  `obstacle` varchar(64) NOT NULL DEFAULT 'none',
  `feature` enum('none','gate','shop','weird','ladder_up','ladder_down') NOT NULL DEFAULT 'none',
  `players` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `coords` (`x`,`y`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_maze_gates`
--

CREATE TABLE `psypets_maze_gates` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `z` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `coordinates` (`x`,`y`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_maze_messages`
--

CREATE TABLE `psypets_maze_messages` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mazeloc` int(10) unsigned NOT NULL,
  `authorid` int(10) unsigned NOT NULL,
  `message` varchar(40) NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `mazeloc` (`mazeloc`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_mechanics`
--

CREATE TABLE `psypets_mechanics` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '127',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `addon` enum('no','yes') NOT NULL DEFAULT 'no',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `mazeable` (`mazeable`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_monkeylog`
--

CREATE TABLE `psypets_monkeylog` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `monkeyname` varchar(32) NOT NULL DEFAULT '',
  `food` varchar(64) NOT NULL DEFAULT '',
  `prize` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_museum`
--

CREATE TABLE `psypets_museum` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `itemid` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `itemid` (`itemid`,`userid`),
  KEY `userid` (`userid`),
  KEY `userid_itemid` (`userid`,`itemid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_museum_displays`
--

CREATE TABLE `psypets_museum_displays` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  `num_items` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `items` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_news`
--

CREATE TABLE `psypets_news` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `author` int(10) unsigned NOT NULL DEFAULT '0',
  `category` enum('routine','event','ramble','broadcast','comic','important','severe') NOT NULL DEFAULT 'routine',
  `subject` varchar(120) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `threadid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `date` (`date`,`author`,`category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_notes`
--

CREATE TABLE `psypets_notes` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `modifiedon` int(10) unsigned NOT NULL,
  `icon` varchar(20) NOT NULL,
  `category` varchar(16) NOT NULL,
  `title` varchar(80) NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `modifiedon` (`modifiedon`),
  KEY `title` (`title`(4)),
  KEY `userid` (`userid`),
  KEY `category` (`category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_nuclear_power_plants`
--

CREATE TABLE `psypets_nuclear_power_plants` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `power` mediumint(9) NOT NULL DEFAULT '0',
  `max_power` mediumint(9) NOT NULL DEFAULT '10000',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_overbuy_report`
--

CREATE TABLE `psypets_overbuy_report` (
  `buytype` enum('over','under') NOT NULL DEFAULT 'over',
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `sellerid` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `itemname` varchar(64) NOT NULL DEFAULT '',
  `price` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity` tinyint(3) unsigned NOT NULL DEFAULT '0',
  KEY `userid` (`userid`,`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_paintings`
--

CREATE TABLE `psypets_paintings` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '120',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `addon` enum('no','yes') NOT NULL DEFAULT 'no',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `solo` enum('no','yes') NOT NULL DEFAULT 'no',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_park_event_results`
--

CREATE TABLE `psypets_park_event_results` (
  `petid` int(10) unsigned NOT NULL,
  `eventid` int(10) unsigned NOT NULL,
  `eventtype` varchar(16) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `size` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `placement` tinyint(3) unsigned NOT NULL,
  `result` text NOT NULL,
  `prizeid` text NOT NULL,
  `prizename` varchar(64) NOT NULL,
  KEY `petid` (`petid`,`eventid`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_pawned_for`
--

CREATE TABLE `psypets_pawned_for` (
  `itemname` varchar(64) NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  KEY `quantity` (`quantity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_payment_records`
--

CREATE TABLE `psypets_payment_records` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `paypalid` varchar(20) NOT NULL DEFAULT '',
  `anonymous` enum('yes','no') NOT NULL DEFAULT 'no',
  `name` varchar(64) NOT NULL DEFAULT '',
  `userid` int(10) unsigned NOT NULL,
  `email` varchar(64) NOT NULL DEFAULT '',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `fee` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `user` (`userid`),
  KEY `paypalid` (`paypalid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_paypalipn`
--

CREATE TABLE `psypets_paypalipn` (
  `itemnumber` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `initiatedby` varchar(16) NOT NULL DEFAULT '',
  `user` varchar(16) NOT NULL DEFAULT '',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `anonymous` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`itemnumber`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_petbadges`
--

CREATE TABLE `psypets_petbadges` (
  `petid` int(10) unsigned NOT NULL DEFAULT '0',
  `level20` enum('no','yes') NOT NULL DEFAULT 'no',
  `level50` enum('no','yes') NOT NULL DEFAULT 'no',
  `level100` enum('no','yes') NOT NULL DEFAULT 'no',
  `oneyearold` enum('no','yes') NOT NULL DEFAULT 'no',
  `masteradventure` enum('no','yes') NOT NULL DEFAULT 'no',
  `mastercraft` enum('no','yes') NOT NULL DEFAULT 'no',
  `mastertailor` enum('no','yes') NOT NULL DEFAULT 'no',
  `masterinvention` enum('no','yes') NOT NULL DEFAULT 'no',
  `mastermechanics` enum('no','yes') NOT NULL DEFAULT 'no',
  `mastersmith` enum('no','yes') NOT NULL DEFAULT 'no',
  `mastergather` enum('no','yes') NOT NULL DEFAULT 'no',
  `masterhunt` enum('no','yes') NOT NULL DEFAULT 'no',
  `masterfish` enum('no','yes') NOT NULL DEFAULT 'no',
  `masterlumberjack` enum('no','yes') NOT NULL DEFAULT 'no',
  `masterpainter` enum('no','yes') NOT NULL DEFAULT 'no',
  `mastercarpenter` enum('no','yes') NOT NULL DEFAULT 'no',
  `masterjeweler` enum('no','yes') NOT NULL DEFAULT 'no',
  `mastersculptor` enum('no','yes') NOT NULL DEFAULT 'no',
  `masterbinder` enum('no','yes') NOT NULL DEFAULT 'no',
  `masterminer` enum('no','yes') NOT NULL DEFAULT 'no',
  `masterchemist` enum('no','yes') NOT NULL DEFAULT 'no',
  `mastervhagst` enum('no','yes') NOT NULL DEFAULT 'no',
  `mother` enum('no','yes') NOT NULL DEFAULT 'no',
  `genderswitcher` enum('no','yes') NOT NULL DEFAULT 'no',
  `potter` enum('no','yes') NOT NULL DEFAULT 'no',
  `illuminator` enum('no','yes') NOT NULL DEFAULT 'no',
  `diver` enum('no','yes') NOT NULL DEFAULT 'no',
  `dragonslayer` enum('no','yes') NOT NULL DEFAULT 'no',
  `demonslayer` enum('no','yes') NOT NULL DEFAULT 'no',
  `paranormal` enum('no','yes') NOT NULL DEFAULT 'no',
  `ctf` enum('no','yes') NOT NULL DEFAULT 'no',
  `runner` enum('no','yes') NOT NULL DEFAULT 'no',
  `archer` enum('no','yes') NOT NULL DEFAULT 'no',
  `roborenaist` enum('no','yes') NOT NULL DEFAULT 'no',
  `peppers` enum('no','yes') NOT NULL DEFAULT 'no',
  `masterspy` enum('no','yes') NOT NULL DEFAULT 'no',
  `defenderofearth` enum('no','yes') NOT NULL DEFAULT 'no',
  `10badges` enum('no','yes') NOT NULL DEFAULT 'no',
  `20badges` enum('no','yes') NOT NULL DEFAULT 'no',
  `30badges` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`petid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_petlives`
--

CREATE TABLE `psypets_petlives` (
  `petid` int(10) unsigned NOT NULL DEFAULT '0',
  `life` smallint(5) unsigned NOT NULL DEFAULT '0',
  `birthdate` int(10) unsigned NOT NULL DEFAULT '0',
  `deathdate` int(10) unsigned NOT NULL DEFAULT '0',
  `graphic` varchar(32) NOT NULL,
  `level` smallint(5) unsigned NOT NULL DEFAULT '0',
  `mastery` text NOT NULL,
  KEY `petid` (`petid`,`life`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_petstats`
--

CREATE TABLE `psypets_petstats` (
  `petid` int(10) unsigned NOT NULL,
  `sleep` int(10) unsigned NOT NULL DEFAULT '0',
  `restup` int(10) unsigned NOT NULL DEFAULT '0',
  `eat` int(10) unsigned NOT NULL DEFAULT '0',
  `eat_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `safety` int(10) unsigned NOT NULL DEFAULT '0',
  `safety_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `love` int(10) unsigned NOT NULL DEFAULT '0',
  `love_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `esteem` int(10) unsigned NOT NULL DEFAULT '0',
  `esteem_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `hangout` int(10) unsigned NOT NULL DEFAULT '0',
  `hangout_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `beg` int(10) unsigned NOT NULL DEFAULT '0',
  `lycanthrope` int(10) unsigned NOT NULL DEFAULT '0',
  `birth` int(10) unsigned NOT NULL DEFAULT '0',
  `construction_success` int(10) unsigned NOT NULL DEFAULT '0',
  `construction_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `construction_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `craft_success` int(10) unsigned NOT NULL DEFAULT '0',
  `craft_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `craft_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `carpenter_success` int(10) unsigned NOT NULL DEFAULT '0',
  `carpenter_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `carpenter_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `sculpture_success` int(10) unsigned NOT NULL DEFAULT '0',
  `sculpture_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `sculpture_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `jewel_success` int(10) unsigned NOT NULL DEFAULT '0',
  `jewel_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `jewel_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `paint_success` int(10) unsigned NOT NULL DEFAULT '0',
  `paint_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `paint_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `engineer_success` int(10) unsigned NOT NULL DEFAULT '0',
  `engineer_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `engineer_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `mechanical_success` int(10) unsigned NOT NULL DEFAULT '0',
  `mechanical_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `mechanical_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `smith_success` int(10) unsigned NOT NULL DEFAULT '0',
  `smith_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `smith_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `tailor_success` int(10) unsigned NOT NULL DEFAULT '0',
  `tailor_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `tailor_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `leatherwork_success` int(10) unsigned NOT NULL DEFAULT '0',
  `leatherwork_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `leatherwork_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `chemistry_success` int(10) unsigned NOT NULL DEFAULT '0',
  `chemistry_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `chemistry_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `binding_success` int(10) unsigned NOT NULL DEFAULT '0',
  `binding_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `binding_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `farming_success` int(10) unsigned NOT NULL DEFAULT '0',
  `farming_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `farming_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `gather_success` int(10) unsigned NOT NULL DEFAULT '0',
  `gather_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `gardening_success` int(10) unsigned NOT NULL DEFAULT '0',
  `gardening_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `mine_success` int(10) unsigned NOT NULL DEFAULT '0',
  `mine_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `lumberjack_success` int(10) unsigned NOT NULL DEFAULT '0',
  `lumberjack_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `adventure_success` int(10) unsigned NOT NULL DEFAULT '0',
  `adventure_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `hunt_success` int(10) unsigned NOT NULL DEFAULT '0',
  `hunt_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `fish_success` int(10) unsigned NOT NULL DEFAULT '0',
  `fish_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `game_room_success` int(10) unsigned NOT NULL DEFAULT '0',
  `game_room_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `online_success` int(10) unsigned NOT NULL DEFAULT '0',
  `online_failure` int(10) unsigned NOT NULL DEFAULT '0',
  `online_unable` int(10) unsigned NOT NULL DEFAULT '0',
  `aliens_defeat` int(10) unsigned NOT NULL DEFAULT '0',
  `aliens_steal` int(10) unsigned NOT NULL DEFAULT '0',
  `aliens_sabotage` int(10) unsigned NOT NULL DEFAULT '0',
  `aliens_failure` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `petid` (`petid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_pet_extra_stats`
--

CREATE TABLE `psypets_pet_extra_stats` (
  `petid` int(10) unsigned NOT NULL,
  `stat` varchar(50) NOT NULL,
  `value` int(10) unsigned NOT NULL,
  `lastupdate` int(10) unsigned NOT NULL,
  KEY `petid` (`petid`),
  KEY `stat` (`stat`),
  KEY `lastupdate` (`lastupdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_pet_level_logs`
--

CREATE TABLE `psypets_pet_level_logs` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL,
  `petid` int(10) unsigned NOT NULL,
  `answer` varchar(200) NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `petid` (`petid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_pet_market`
--

CREATE TABLE `psypets_pet_market` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `expiration` int(10) unsigned NOT NULL DEFAULT '0',
  `petid` int(10) unsigned NOT NULL DEFAULT '0',
  `ownerid` int(10) unsigned NOT NULL DEFAULT '0',
  `price` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `ownerid` (`ownerid`,`price`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_pet_relationships`
--

CREATE TABLE `psypets_pet_relationships` (
  `petid` int(10) unsigned NOT NULL DEFAULT '0',
  `friendid` int(10) unsigned NOT NULL DEFAULT '0',
  `firstmet` int(10) unsigned NOT NULL,
  `rejected` enum('no','yes') NOT NULL DEFAULT 'no',
  `forbidden` enum('no','yes') NOT NULL DEFAULT 'no',
  `hangouts_to_ignore` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `intimacy` tinyint(4) NOT NULL DEFAULT '0',
  `passion` tinyint(4) NOT NULL DEFAULT '0',
  `commitment` tinyint(4) NOT NULL DEFAULT '0',
  KEY `petid` (`petid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_planets`
--

CREATE TABLE `psypets_planets` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `universeid` int(10) unsigned NOT NULL,
  `systemid` int(10) unsigned NOT NULL,
  `type` enum('planet','giant','comet','belt') NOT NULL DEFAULT 'planet',
  `class` enum('deadly','desolate','habitable','eden') NOT NULL,
  `size` mediumint(8) unsigned NOT NULL,
  `name` varchar(40) NOT NULL,
  `life` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `population` int(10) unsigned NOT NULL DEFAULT '0',
  `civilizationid` int(10) unsigned NOT NULL DEFAULT '0',
  `x` mediumint(8) unsigned NOT NULL,
  `y` mediumint(8) unsigned NOT NULL,
  `image` varchar(20) NOT NULL,
  `image_size` tinyint(3) unsigned NOT NULL DEFAULT '8',
  PRIMARY KEY (`idnum`),
  KEY `systemid` (`systemid`),
  KEY `civilizationid` (`civilizationid`),
  KEY `universeid` (`universeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_player_stats`
--

CREATE TABLE `psypets_player_stats` (
  `userid` int(10) unsigned NOT NULL,
  `stat` varchar(50) NOT NULL,
  `value` int(10) unsigned NOT NULL,
  `lastupdate` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `stat` (`stat`(10)),
  KEY `userid` (`userid`),
  KEY `lastupdate` (`lastupdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_polls`
--

CREATE TABLE `psypets_polls` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paidonly` enum('no','yes') NOT NULL DEFAULT 'no',
  `title` text NOT NULL,
  `description` text NOT NULL,
  `options` text NOT NULL,
  PRIMARY KEY (`idnum`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_poll_votes`
--

CREATE TABLE `psypets_poll_votes` (
  `pollid` int(10) unsigned NOT NULL DEFAULT '0',
  `residentid` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(16) NOT NULL,
  `vote` tinyint(3) unsigned NOT NULL DEFAULT '0',
  KEY `pollid` (`pollid`,`residentid`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_possible_trolling`
--

CREATE TABLE `psypets_possible_trolling` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL,
  `postid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`idnum`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_post_notification`
--

CREATE TABLE `psypets_post_notification` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `threadid` int(10) unsigned NOT NULL DEFAULT '0',
  `stars` enum('no','yes') NOT NULL DEFAULT 'no',
  `fireworks` enum('no','yes') NOT NULL DEFAULT 'no',
  KEY `userid` (`userid`,`threadid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_post_thumbs`
--

CREATE TABLE `psypets_post_thumbs` (
  `timestamp` int(10) unsigned NOT NULL,
  `postid` int(10) unsigned NOT NULL,
  `voterid` int(10) unsigned NOT NULL,
  `vote` tinyint(4) NOT NULL,
  KEY `postid` (`postid`,`voterid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_profilecomments`
--

CREATE TABLE `psypets_profilecomments` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `authorid` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`),
  KEY `authorid` (`authorid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_profile_pet`
--

CREATE TABLE `psypets_profile_pet` (
  `petid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastupdate` int(10) unsigned NOT NULL DEFAULT '0',
  `profile` text NOT NULL,
  UNIQUE KEY `petid` (`petid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_profile_text`
--

CREATE TABLE `psypets_profile_text` (
  `player_id` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `last_update` int(10) unsigned NOT NULL,
  PRIMARY KEY (`player_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_profile_treasures`
--

CREATE TABLE `psypets_profile_treasures` (
  `idnum` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `ranking` int(11) NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`,`itemid`,`ranking`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_profile_user`
--

CREATE TABLE `psypets_profile_user` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastupdate` int(10) unsigned NOT NULL DEFAULT '0',
  `profile` text NOT NULL,
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_publictrades`
--

CREATE TABLE `psypets_publictrades` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `askingitem` varchar(64) NOT NULL DEFAULT '',
  `askingquantity` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `givingitem` varchar(64) NOT NULL DEFAULT '',
  `givingquantity` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `askingitem` (`askingitem`,`givingitem`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_questvalues`
--

CREATE TABLE `psypets_questvalues` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`),
  KEY `name` (`name`(4))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_reversemarket`
--

CREATE TABLE `psypets_reversemarket` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `itemname` varchar(64) NOT NULL DEFAULT '',
  `buyer` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `bid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `awesome_index` (`itemname`,`quantity`,`bid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_sculptures`
--

CREATE TABLE `psypets_sculptures` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '127',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `addon` enum('no','yes') NOT NULL DEFAULT 'no',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_sellback_report`
--

CREATE TABLE `psypets_sellback_report` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `itemname` varchar(64) NOT NULL DEFAULT '',
  `quantity` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`,`timestamp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_shrines`
--

CREATE TABLE `psypets_shrines` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastcheck` int(10) unsigned NOT NULL DEFAULT '0',
  `candles` text NOT NULL,
  `spells` text NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_sidewalks`
--

CREATE TABLE `psypets_sidewalks` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `pigeons` int(10) unsigned NOT NULL DEFAULT '0',
  `progress_brokenglass` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `progress_dandelion` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `progress_emptycan` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `progress_clay` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `progress_skate` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `progress_brandy` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `progress_cellphone` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `progress_moneys` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_slots`
--

CREATE TABLE `psypets_slots` (
  `money` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_smiths`
--

CREATE TABLE `psypets_smiths` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '120',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `addon` enum('no','yes') NOT NULL DEFAULT 'no',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_berries` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_burny` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_starlog`
--

CREATE TABLE `psypets_starlog` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `postid` int(10) unsigned NOT NULL DEFAULT '0',
  `authorid` int(10) unsigned NOT NULL DEFAULT '0',
  `stars` int(10) unsigned NOT NULL DEFAULT '0',
  `new` enum('no','yes') NOT NULL DEFAULT 'yes',
  KEY `userid` (`userid`,`authorid`),
  KEY `postid` (`postid`),
  KEY `new` (`new`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_stars`
--

CREATE TABLE `psypets_stars` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `universeid` int(10) unsigned NOT NULL,
  `systemid` int(10) unsigned NOT NULL,
  `type` varchar(10) NOT NULL,
  `mass` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `creationdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `systemid` (`systemid`),
  KEY `universeid` (`universeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_stellar_objects`
--

CREATE TABLE `psypets_stellar_objects` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `universeid` int(10) unsigned NOT NULL,
  `galaxyid` int(10) unsigned NOT NULL,
  `image` varchar(20) NOT NULL,
  `star_count` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `object_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `creationdate` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `x` smallint(5) unsigned NOT NULL,
  `y` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `galaxyid` (`galaxyid`),
  KEY `universeid` (`universeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_store_portraits`
--

CREATE TABLE `psypets_store_portraits` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `use_for_store` enum('no','yes') NOT NULL DEFAULT 'no',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `datalength` int(10) unsigned NOT NULL,
  `data` mediumblob NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `timestamp` (`timestamp`),
  KEY `userid` (`userid`),
  KEY `use_for_store` (`use_for_store`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_stpatricks`
--

CREATE TABLE `psypets_stpatricks` (
  `npc` enum('totem','bank') NOT NULL DEFAULT 'totem',
  `year` smallint(5) unsigned NOT NULL DEFAULT '2009',
  `items` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `npc` (`npc`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_tailors`
--

CREATE TABLE `psypets_tailors` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `difficulty` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complexity` tinyint(3) unsigned NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '120',
  `ingredients` text NOT NULL,
  `makes` varchar(64) NOT NULL DEFAULT '',
  `mazeable` enum('no','yes') NOT NULL DEFAULT 'yes',
  `min_month` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `max_month` tinyint(3) unsigned NOT NULL DEFAULT '12',
  `min_openness` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_openness` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `min_music` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_astronomy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_playful` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_playful` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `is_secret` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_berries` enum('no','yes') NOT NULL DEFAULT 'no',
  `is_burny` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`idnum`),
  KEY `difficulty` (`difficulty`),
  KEY `min_month` (`min_month`,`max_month`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_threadpolls`
--

CREATE TABLE `psypets_threadpolls` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL DEFAULT '',
  `options` text NOT NULL,
  `votes1` smallint(5) unsigned NOT NULL DEFAULT '0',
  `votes2` smallint(5) unsigned NOT NULL DEFAULT '0',
  `votes3` smallint(5) unsigned NOT NULL DEFAULT '0',
  `votes4` smallint(5) unsigned NOT NULL DEFAULT '0',
  `votes5` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_thread_history`
--

CREATE TABLE `psypets_thread_history` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `threadid` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `gamenote` varchar(200) NOT NULL DEFAULT '',
  `usernote` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `threadid` (`threadid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_totempoles`
--

CREATE TABLE `psypets_totempoles` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `rating` smallint(5) unsigned NOT NULL DEFAULT '0',
  `totem` text NOT NULL,
  `last_add` int(10) unsigned NOT NULL DEFAULT '0',
  `remove_cost` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`idnum`),
  UNIQUE KEY `userid` (`userid`),
  KEY `rating` (`rating`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_towers`
--

CREATE TABLE `psypets_towers` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `monkeyname` varchar(64) NOT NULL,
  `monkeydesc` varchar(200) NOT NULL,
  `nextsearch` int(10) unsigned NOT NULL DEFAULT '0',
  `lastfood` varchar(64) NOT NULL DEFAULT '',
  `bell` enum('no','yes') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_towns`
--

CREATE TABLE `psypets_towns` (
  `groupid` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  UNIQUE KEY `groupid` (`groupid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_trading_house_bids`
--

CREATE TABLE `psypets_trading_house_bids` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `tradeid` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `itemids` text NOT NULL,
  `itemtext` text NOT NULL,
  `itemtable` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`,`tradeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_trading_house_requests`
--

CREATE TABLE `psypets_trading_house_requests` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `itemids` text NOT NULL,
  `itemtext` text NOT NULL,
  `itemtable` text NOT NULL,
  `sdesc` varchar(80) NOT NULL,
  `ldesc` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_universes`
--

CREATE TABLE `psypets_universes` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ownerid` int(10) unsigned NOT NULL,
  `stage` enum('inflation','recombination','formation','gameplay') NOT NULL DEFAULT 'inflation',
  `lastupdate` int(10) unsigned NOT NULL,
  `hydrogen` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `stars` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `clouds` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `galaxies` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `rocks` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `gasgiants` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `supernova` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `galactic_object_count` int(10) unsigned NOT NULL DEFAULT '0',
  `total_object_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `ownerid` (`ownerid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_universe_history`
--

CREATE TABLE `psypets_universe_history` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `universeid` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `event` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `universeid` (`universeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_user_enemies`
--

CREATE TABLE `psypets_user_enemies` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `enemyid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_user_friends`
--

CREATE TABLE `psypets_user_friends` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `friendid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`,`friendid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_user_word_associations`
--

CREATE TABLE `psypets_user_word_associations` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL,
  `word` varchar(100) NOT NULL,
  `associated_word` varchar(100) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_warninglog`
--

CREATE TABLE `psypets_warninglog` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `publicnote` text NOT NULL,
  `adminnote` text NOT NULL,
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_watchedthreads`
--

CREATE TABLE `psypets_watchedthreads` (
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `threadid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idnum`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_whatisbendoing`
--

CREATE TABLE `psypets_whatisbendoing` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastchange` int(10) unsigned NOT NULL DEFAULT '0',
  `gameplay` tinyint(4) NOT NULL DEFAULT '0',
  `community` tinyint(4) NOT NULL DEFAULT '0',
  `pets` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_wired`
--

CREATE TABLE `psypets_wired` (
  `petid` int(10) unsigned NOT NULL DEFAULT '0',
  `skill` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `lastplay` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`petid`),
  KEY `lastplay` (`lastplay`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psypets_zoos`
--

CREATE TABLE `psypets_zoos` (
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `locid` int(10) unsigned NOT NULL DEFAULT '0',
  `monsters` text NOT NULL,
  `sortby` enum('level DESC','level ASC') NOT NULL DEFAULT 'level DESC',
  KEY `userid` (`userid`,`locid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
