-- Adminer 5.4.2 MySQL 8.0.45-0ubuntu0.24.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `db_project` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_project`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `person_id` bigint NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(300) NOT NULL,
  `role_id` bigint NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_person_id` (`person_id`),
  UNIQUE KEY `uk_users_username` (`username`),
  KEY `idx_users_role_id` (`role_id`),
  CONSTRAINT `fk_users_person` FOREIGN KEY (`person_id`) REFERENCES `ws_persons` (`id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `ws_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`, `person_id`, `username`, `password`, `role_id`, `is_active`) VALUES
(1,	1,	'mperez',	'e4f6dc3dedc813c5ed58a0041f7d41da116a6ee1',	1,	1);

DROP TABLE IF EXISTS `ws_academic_programs`;
CREATE TABLE `ws_academic_programs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ws_academic_programs_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `ws_academic_programs` (`id`, `name`, `is_active`, `created_at`) VALUES
(1,	'Ingeniería de Sistemas',	1,	'2026-04-17 09:54:32'),
(2,	'Ingeniería Industrial',	1,	'2026-04-17 09:54:32'),
(3,	'Administración de Empresas',	1,	'2026-04-17 09:54:32'),
(4,	'Ingeniería Electronica',	1,	'2026-04-17 11:26:39');

DROP TABLE IF EXISTS `ws_deliveries`;
CREATE TABLE `ws_deliveries` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `student_id` bigint NOT NULL,
  `delivery_scheduling_id` bigint NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ws_deliveries_student_schedule` (`student_id`,`delivery_scheduling_id`),
  KEY `idx_ws_deliveries_delivery_scheduling_id` (`delivery_scheduling_id`),
  KEY `idx_ws_deliveries_created_by` (`created_by`),
  CONSTRAINT `fk_ws_deliveries_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_ws_deliveries_delivery_scheduling` FOREIGN KEY (`delivery_scheduling_id`) REFERENCES `ws_delivery_scheduling` (`id`),
  CONSTRAINT `fk_ws_deliveries_student` FOREIGN KEY (`student_id`) REFERENCES `ws_students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `ws_delivery_scheduling`;
CREATE TABLE `ws_delivery_scheduling` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `delivery_day` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ws_delivery_scheduling_delivery_day` (`delivery_day`),
  KEY `idx_ws_delivery_scheduling_created_by` (`created_by`),
  CONSTRAINT `fk_ws_delivery_scheduling_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `ws_persons`;
CREATE TABLE `ws_persons` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `document_number` varchar(20) NOT NULL,
  `first_name` varchar(60) NOT NULL,
  `last_name` varchar(60) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ws_persons_document_number` (`document_number`),
  UNIQUE KEY `uk_ws_persons_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `ws_roles`;
CREATE TABLE `ws_roles` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ws_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `ws_roles` (`id`, `name`, `is_active`) VALUES
(1,	'ADMIN',	1),
(2,	'COORDINADOR',	1),
(3,	'AUXILIAR',	1),
(4,	'SECRETARIA',	1);

DROP TABLE IF EXISTS `ws_students`;
CREATE TABLE `ws_students` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `person_id` bigint NOT NULL,
  `academic_program_id` bigint NOT NULL,
  `semester` int NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ws_students_person_id` (`person_id`),
  KEY `idx_ws_students_academic_program_id` (`academic_program_id`),
  CONSTRAINT `fk_ws_students_academic_program` FOREIGN KEY (`academic_program_id`) REFERENCES `ws_academic_programs` (`id`),
  CONSTRAINT `fk_ws_students_person` FOREIGN KEY (`person_id`) REFERENCES `ws_persons` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- 2026-05-08 23:26:31 UTC
