-- পুরানো সব টেবিল মুছে ফেলার জন্য
DROP TABLE IF EXISTS `user_data`, `submissions`, `form_fields`, `settings`, `admin_users`, `invitation_codes`;

-- ১. অ্যাডমিন ব্যবহারকারীদের জন্য টেবিল
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('owner','admin') NOT NULL DEFAULT 'admin',
  `security_codes` text DEFAULT NULL, -- শুধুমাত্র owner-এর জন্য
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ২. নতুন অ্যাডমিনদের জন্য ইনভাইটেশন কোড টেবিল
CREATE TABLE `invitation_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL UNIQUE,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ৩. সাইটের সেটিংসের জন্য টেবিল
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(100) NOT NULL UNIQUE,
  `setting_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `settings` (`setting_name`, `setting_value`) VALUES ('form_title', 'Registration Form');

-- ৪. ফর্মের ফিল্ডের জন্য টেবিল
CREATE TABLE `form_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_name` varchar(100) NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_type` varchar(50) NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `placeholder` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `form_fields` (`field_name`, `field_label`, `field_type`, `is_required`, `placeholder`, `display_order`) VALUES
('full_name', 'Full Name', 'text', 1, 'Enter your full name', 10),
('phone_number', 'Phone Number', 'tel', 1, 'Enter your phone number', 20),
('email_address', 'Email Address', 'email', 1, 'Enter your email address', 30);

-- ৫. সাবমিশনের জন্য টেবিল
CREATE TABLE `submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ৬. ব্যবহারকারীর ডেটার জন্য টেবিল
CREATE TABLE `user_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;