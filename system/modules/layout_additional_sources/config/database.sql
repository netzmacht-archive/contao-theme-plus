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
  `css_file` blob NULL,
  `css_url` blob NULL,
  `cc` varchar(32) NOT NULL default '',
  `media` varchar(255) NOT NULL default '',
  `restrictLayout` char(1) NOT NULL default '',
  `layout` blob NULL,
  `compress_yui` char(1) NOT NULL default '',
  `compress_gz` char(1) NOT NULL default '',
  `compress_outdir` blob NULL,
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

