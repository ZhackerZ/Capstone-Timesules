-- -----------------------------------------------------
-- Bug Reports
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bugs`;
CREATE TABLE IF NOT EXISTS `bugs` (
  `bug_id` int(10) NOT NULL AUTO_INCREMENT,
  `bug_page` varchar(50) NOT NULL,
  `bug_msg` text NOT NULL,
  `bug_user` int(10) NOT NULL,
  `bug_date` int(10) NOT NULL,
  PRIMARY KEY (`bug_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
-- -----------------------------------------------------
-- User comments
-- -----------------------------------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `com_id` int(10) NOT NULL AUTO_INCREMENT,
  `com_type` int(1) NOT NULL,
  `com_user` int(10) NOT NULL,
  `com_post` int(10) NOT NULL,
  `com_date` int(10) NOT NULL,
  `com_comment` varchar(500) NOT NULL,
  PRIMARY KEY (`com_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
-- -----------------------------------------------------
-- Bug Tracker
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bug_tracker`;
CREATE TABLE IF NOT EXISTS `debug_tracker` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user` int(10) NOT NULL,
  `page` varchar(50) NOT NULL,
  `get_data` text NOT NULL,
  `post_data` text NOT NULL,
  `queries` text NOT NULL,
  `user_data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- -----------------------------------------------------
-- TODO: Groups
-- -----------------------------------------------------
DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` int(10) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(100) NOT NULL,
  `group_admin` int(10) NOT NULL,
  `group_users` text NOT NULL,
  `group_avatar` varchar(32) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- -----------------------------------------------------
-- TODO: Group Posts
-- -----------------------------------------------------
DROP TABLE IF EXISTS `group_posts`;
CREATE TABLE IF NOT EXISTS `group_posts` (
  `gpo_id` int(10) NOT NULL AUTO_INCREMENT,
  `gpo_pid` int(10) NOT NULL,
  `gpo_uid` int(10) NOT NULL,
  `gpo_msg` text NOT NULL,
  `gpo_attachments` text NOT NULL,
  `gpo_date` int(10) NOT NULL,
  PRIMARY KEY (`gpo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- -----------------------------------------------------
-- TODO: Group Capsules
-- -----------------------------------------------------
DROP TABLE IF EXISTS `group_prompts`;
CREATE TABLE IF NOT EXISTS `group_prompts` (
  `gpr_id` int(10) NOT NULL AUTO_INCREMENT,
  `gpr_gid` int(10) NOT NULL,
  `gpr_prompt` varchar(200) NOT NULL,
  `gpr_des` varchar(200) NOT NULL,
  `gpr_lock` int(10) NOT NULL,
  `gpr_release` int(10) NOT NULL,
  `gpr_vis` tinyint(1) NOT NULL,
  PRIMARY KEY (`gpr_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- -----------------------------------------------------
-- TODO: User Capsules
-- -----------------------------------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `post_id` int(10) NOT NULL AUTO_INCREMENT,
  `post_user` int(10) NOT NULL,
  `post_to` text NOT NULL,
  `post_prompt` varchar(200) NOT NULL,
  `post_msg` text NOT NULL,
  `post_attachments` text NOT NULL,
  `post_lock` int(10) NOT NULL,
  `post_release` int(10) NOT NULL,
  `post_vis` tinyint(1) NOT NULL DEFAULT '1',
  `post_draft` tinyint(1) NOT NULL DEFAULT '0',
  `post_hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


-- -----------------------------------------------------
-- Front Page Capsule
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sendacapsule`;
CREATE TABLE IF NOT EXISTS `sendacapsule` (
  `cap_id` int(10) NOT NULL AUTO_INCREMENT,
  `cap_email` varchar(100) NOT NULL,
  `cap_subj` varchar(100) NOT NULL,
  `cap_msg` varchar(500) NOT NULL,
  `cap_time` int(10) NOT NULL,
  PRIMARY KEY (`cap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- -----------------------------------------------------
-- Bug Tickets
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `ticket_id` int(10) NOT NULL AUTO_INCREMENT,
  `ticket_name` varchar(200) NOT NULL,
  `ticket_email` varchar(200) NOT NULL,
  `ticket_area` varchar(50) NOT NULL,
  `ticket_msg` text NOT NULL,
  `ticket_user` int(10) NOT NULL,
  `ticket_date` int(10) NOT NULL,
  PRIMARY KEY (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- -----------------------------------------------------
-- TODO: Users
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(50) NOT NULL,
  `user_first` varchar(50) NOT NULL,
  `user_middle` varchar(50) NOT NULL,
  `user_last` varchar(50) NOT NULL,
  `user_age` date NOT NULL,
  `user_gender` tinyint(1) NOT NULL,
  `user_avatar` varchar(32) NOT NULL,
  `user_ip` varchar(50) NOT NULL,
  `user_ban` tinyint(1) NOT NULL DEFAULT '0',
  `user_conf` varchar(50) NOT NULL,
  `user_contacts` text NOT NULL,
  `user_groups` text NOT NULL,
  `user_notifications` text NOT NULL,
  `user_prefs` bit(5) NOT NULL DEFAULT b'11111',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
