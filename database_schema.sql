-- SQL Database Schema for uklondoninternationalawardboard LMS
-- Matches the database architecture specifications.
-- You can import this file directly into phpMyAdmin.

CREATE TABLE IF NOT EXISTS `faculties` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `dob` DATE NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `whatsapp_number` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('student', 'admin') NOT NULL DEFAULT 'student',
  `faculty_id` INT DEFAULT NULL,
  `rep_code` VARCHAR(50) DEFAULT NULL,
  `account_status` ENUM('active', 'pending_manual_unlock', 'locked') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `modules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `faculty_id` INT DEFAULT NULL, -- Nullable for universal modules 1 & 2
  `module_number` INT NOT NULL,
  `phase` VARCHAR(10) NOT NULL DEFAULT 'I',
  `title` VARCHAR(255) NOT NULL,
  `content_type` ENUM('text', 'video') DEFAULT 'text',
  `content_path` TEXT NOT NULL,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `assignments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `module_id` INT NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_size` VARCHAR(50) NOT NULL,
  `status` ENUM('pending', 'reviewed') DEFAULT 'pending',
  `grade` VARCHAR(50) DEFAULT NULL,
  `feedback` TEXT,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `exams` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `faculty_id` INT NOT NULL,
  `duration_minutes` INT DEFAULT 120,
  `pass_threshold` INT DEFAULT 50, -- 50% passing grade
  `total_questions` INT DEFAULT 10,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `exam_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `exam_id` INT NOT NULL,
  `start_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `end_time` TIMESTAMP NULL DEFAULT NULL,
  `score` DECIMAL(5,2) DEFAULT NULL,
  `status` ENUM('in_progress', 'completed', 'force_submitted_violation') DEFAULT 'in_progress',
  `violation_count` INT DEFAULT 0,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `certificates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `course_id` INT DEFAULT NULL,
  `exam_attempt_id` INT DEFAULT NULL,
  `certificate_uid` VARCHAR(100) UNIQUE NOT NULL,
  `issue_date` DATE NOT NULL,
  `pdf_path` VARCHAR(255) NOT NULL,
  `verification_status` ENUM('pending', 'approved', 'revoked') DEFAULT 'approved',
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `faculties` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`exam_attempt_id`) REFERENCES `exam_attempts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `type` ENUM('tuition', 'verification_lookup') DEFAULT 'tuition',
  `method` ENUM('stripe', 'western_union', 'ria', 'worldremit') NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `installment_number` INT DEFAULT NULL,
  `status` ENUM('paid', 'pending_manual_unlock', 'failed') DEFAULT 'pending_manual_unlock',
  `transaction_ref` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `affiliates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `rep_code` VARCHAR(50) UNIQUE NOT NULL,
  `contact_info` TEXT NOT NULL,
  `application_status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `linked_students_count` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default Faculties
INSERT INTO `faculties` (`id`, `name`) VALUES
(1, 'Business'),
(2, 'Health'),
(3, 'Nutrition')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- Seed default Modules
-- Universal Modules 1 & 2 (faculty_id = NULL)
INSERT INTO `modules` (`id`, `faculty_id`, `module_number`, `phase`, `title`, `content_type`, `content_path`) VALUES
(1, NULL, 1, 'I', 'Academic English', 'text', 'Foundational guidelines for academic English communication, research reporting, and structural grammar.'),
(2, NULL, 2, 'I', 'Advanced Mathematics', 'text', 'Advanced mathematical concepts, statistical analysis, and data modeling for academic research.')
ON DUPLICATE KEY UPDATE `title`=`title`;

-- Conditional Modules 3 & 4 (Faculty specific)
INSERT INTO `modules` (`id`, `faculty_id`, `module_number`, `phase`, `title`, `content_type`, `content_path`) VALUES
(3, 1, 3, 'I', 'Essential Sciences', 'text', 'Fundamental scientific principles, empirical observation, and methodological inquiry.'),
(4, 1, 4, 'II', 'Global Supply Chain Management', 'text', 'Logistics coordination, procurement protocols, and international supply chain ethics.'),
(5, 2, 3, 'I', 'Essential Sciences', 'text', 'Fundamental scientific principles, empirical observation, and methodological inquiry.'),
(6, 2, 4, 'II', 'Public Health Administration', 'text', 'Analyzing community health policy, regional clinical distribution, and pandemic protocol audits.'),
(7, 3, 3, 'I', 'Essential Sciences', 'text', 'Fundamental scientific principles, empirical observation, and methodological inquiry.'),
(8, 3, 4, 'II', 'Dietary Therapy Planning', 'text', 'Designing target menu plans for clinical diabetes, cardiovascular risk reduction, and specialized diets.')
ON DUPLICATE KEY UPDATE `title`=`title`;

-- Seed default Exams
INSERT INTO `exams` (`id`, `faculty_id`, `duration_minutes`, `pass_threshold`, `total_questions`) VALUES
(1, 1, 120, 50, 10),
(2, 2, 120, 50, 10),
(3, 3, 120, 50, 10)
ON DUPLICATE KEY UPDATE `duration_minutes`=`duration_minutes`;

-- Seed default Admin and Student accounts for testing
-- Admin: admin@mail.com / password: 1234567890 (Hashed)
-- Student: student@mail.com / password: 1234567890 (Hashed)
INSERT INTO `users` (`id`, `full_name`, `dob`, `email`, `whatsapp_number`, `password_hash`, `role`, `faculty_id`, `rep_code`, `account_status`) VALUES
(1, 'LMS Admin Assessor', '1985-01-01', 'admin@mail.com', '+447000000000', '$2y$10$sX8lWkRoxhV9D9sV1r92OOfcSwvB59Eeh96aTpepBwN9Z42gYf20K', 'admin', NULL, NULL, 'active'),
(2, 'Test Student Profile', '2000-05-15', 'student@mail.com', '+447111111111', '$2y$10$sX8lWkRoxhV9D9sV1r92OOfcSwvB59Eeh96aTpepBwN9Z42gYf20K', 'student', 1, 'REP-DEMO-01', 'active')
ON DUPLICATE KEY UPDATE `email`=`email`;

-- Seed default Affiliate Representative
INSERT INTO `affiliates` (`id`, `name`, `rep_code`, `contact_info`, `application_status`, `linked_students_count`) VALUES
(1, 'Global Education Consultant Co', 'REP-DEMO-01', 'Email: regional@consultancy.org, Whatsapp: +447222222222', 'approved', 1)
ON DUPLICATE KEY UPDATE `name`=`name`;
