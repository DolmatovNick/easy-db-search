CREATE TABLE site_feedback (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(63) NOT NULL,
  `email_local` varchar(64) NOT NULL,
  `email_domain` varchar(255) NOT NULL,
  `text` varchar(1024)  NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE INDEX site_feedback_email_local on site_feedback(email_local(16));

-- TEST STRING