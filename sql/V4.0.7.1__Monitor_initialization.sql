
CREATE TABLE `monitor_metric_monitor_tag_assoc` (
  `monitor_tag_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `monitor_metric_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`monitor_tag_id`,`monitor_metric_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `monitor_metrics` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `value` decimal(32,8) DEFAULT 0.00000000,
  `unit` varchar(100) DEFAULT '',
  `function` varchar(100) DEFAULT '',
  `interval` int(11) DEFAULT 0,
  `cacheable` tinyint(1) DEFAULT 1,
  `modif_dtime` datetime DEFAULT '0000-00-00 00:00:00',
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique_idx` (`tenant`,`name`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `monitor_tags` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `creation_dtime` datetime DEFAULT '0000-00-00 00:00:00',
  `modif_dtime` datetime DEFAULT '0000-00-00 00:00:00',
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique_idx` (`tenant`,`name`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
