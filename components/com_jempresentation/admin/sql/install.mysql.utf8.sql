CREATE TABLE IF NOT EXISTS `#__jempresentation_assignments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `context_type` varchar(32) NOT NULL DEFAULT 'event',
  `context_id` int unsigned NOT NULL,
  `profile` varchar(64) NOT NULL DEFAULT '',
  `layout` varchar(64) NOT NULL DEFAULT '',
  `params` text NULL,
  `created` datetime NULL,
  `modified` datetime NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_context` (`context_type`,`context_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
