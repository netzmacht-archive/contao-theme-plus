-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- 
-- Table `tl_additional_source`
-- 

CREATE TABLE `tl_additional_source` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `type` varchar(32) NOT NULL default '',
  `js_file` blob NULL,
  `js_url` blob NULL,
  `js_url_real_path` blob NULL,
  `css_file` blob NULL,
  `css_url` blob NULL,
  `css_url_real_path` blob NULL,
  `cc` varchar(32) NOT NULL default '',
  `media` varchar(255) NOT NULL default '',
  `editor_integration` blob NULL,
  `force_editor_integration` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_layout`
-- 

CREATE TABLE `tl_layout` (
  `additional_source` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_page`
-- 

CREATE TABLE `tl_page` (
  `additional_source` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
