-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- 
-- Table `tl_theme_plus_file`
-- 

CREATE TABLE `tl_theme_plus_file` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `type` varchar(32) NOT NULL default '',
  `js_file` blob NULL,
  `js_url` blob NULL,
  `css_file` blob NULL,
  `css_url` blob NULL,
  `media` blob NULL,
  `cc` blob NULL,
  `editor_integration` blob NULL,
  `force_editor_integration` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_theme_plus_variable`
-- 

CREATE TABLE `tl_theme_plus_variable` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `type` varchar(32) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `text` varchar(255) NOT NULL default '',
  `url` blob NULL,
  `file` blob NULL,
  `color` varchar(6) NOT NULL default '',
  `size` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_layout`
-- 

CREATE TABLE `tl_layout` (
  `theme_plus_exclude_contaocss` char(1) NOT NULL default '',
  `theme_plus_files` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_page`
-- 

CREATE TABLE `tl_page` (
  `theme_plus_include_files` char(1) NOT NULL default '',
  `theme_plus_files` blob NULL,
  `theme_plus_include_files_noinherit` char(1) NOT NULL default '',
  `theme_plus_files_noinherit` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_content`
-- 

CREATE TABLE `tl_content` (
  `script_source` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `script_source` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
