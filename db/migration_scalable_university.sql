-- Migracion Well Snack a modelo escalable por organigrama
-- Ejecutar en ambiente de pruebas primero.
-- Respaldo recomendado antes de correr: mysqldump -u <user> -p db_project > backup_pre_migration.sql

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1) Nuevas tablas base de organizacion y perfiles
-- =====================================================

CREATE TABLE IF NOT EXISTS ws_organizational_unit_types (
  id BIGINT NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uk_ws_org_unit_types_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ws_organizational_units (
  id BIGINT NOT NULL AUTO_INCREMENT,
  parent_id BIGINT DEFAULT NULL,
  organizational_unit_type_id BIGINT NOT NULL,
  name VARCHAR(150) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_ws_org_units_parent_id (parent_id),
  KEY idx_ws_org_units_type_id (organizational_unit_type_id),
  KEY idx_ws_org_units_name (name),
  CONSTRAINT fk_ws_org_units_parent
    FOREIGN KEY (parent_id) REFERENCES ws_organizational_units (id),
  CONSTRAINT fk_ws_org_units_type
    FOREIGN KEY (organizational_unit_type_id) REFERENCES ws_organizational_unit_types (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ws_profile_types (
  id BIGINT NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uk_ws_profile_types_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ws_person_profiles (
  id BIGINT NOT NULL AUTO_INCREMENT,
  person_id BIGINT NOT NULL,
  profile_type_id BIGINT NOT NULL,
  organizational_unit_id BIGINT DEFAULT NULL,
  code VARCHAR(30) DEFAULT NULL,
  semester INT DEFAULT NULL,
  photo_path VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_ws_person_profiles_person_id (person_id),
  KEY idx_ws_person_profiles_profile_type_id (profile_type_id),
  KEY idx_ws_person_profiles_org_unit_id (organizational_unit_id),
  UNIQUE KEY uk_ws_person_profile_scope (person_id, profile_type_id, organizational_unit_id),
  CONSTRAINT fk_ws_person_profiles_person
    FOREIGN KEY (person_id) REFERENCES ws_persons (id),
  CONSTRAINT fk_ws_person_profiles_profile_type
    FOREIGN KEY (profile_type_id) REFERENCES ws_profile_types (id),
  CONSTRAINT fk_ws_person_profiles_org_unit
    FOREIGN KEY (organizational_unit_id) REFERENCES ws_organizational_units (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ws_user_roles (
  user_id BIGINT NOT NULL,
  role_id BIGINT NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  assigned_by BIGINT DEFAULT NULL,
  PRIMARY KEY (user_id, role_id),
  KEY idx_ws_user_roles_role_id (role_id),
  KEY idx_ws_user_roles_assigned_by (assigned_by),
  CONSTRAINT fk_ws_user_roles_user
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT fk_ws_user_roles_role
    FOREIGN KEY (role_id) REFERENCES ws_roles (id),
  CONSTRAINT fk_ws_user_roles_assigned_by
    FOREIGN KEY (assigned_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 2) Catalogos iniciales
-- =====================================================

INSERT IGNORE INTO ws_organizational_unit_types (name, description)
VALUES
('RECTORY', 'Rectoria'),
('VICERRECTORY', 'Vicerrectoria'),
('FACULTY', 'Facultad'),
('PROGRAM', 'Programa academico'),
('DEPARTMENT', 'Departamento'),
('AREA', 'Area');

INSERT IGNORE INTO ws_profile_types (name, description)
VALUES
('STUDENT', 'Perfil estudiantil'),
('TEACHER', 'Perfil docente'),
('ADMINISTRATIVE', 'Perfil administrativo'),
('GRADUATE', 'Perfil egresado'),
('CONTRACTOR', 'Perfil contratista');

-- =====================================================
-- 3) Migrar programas academicos a unidades organizacionales
-- =====================================================

INSERT INTO ws_organizational_units (name, organizational_unit_type_id, is_active)
SELECT ap.name,
       outt.id,
       ap.is_active
FROM ws_academic_programs ap
INNER JOIN ws_organizational_unit_types outt
  ON outt.name = 'PROGRAM'
LEFT JOIN ws_organizational_units ou
  ON ou.name = ap.name
 AND ou.organizational_unit_type_id = outt.id
WHERE ou.id IS NULL;

-- =====================================================
-- 4) Migrar estudiantes a perfiles de persona
-- =====================================================

INSERT INTO ws_person_profiles (
  person_id,
  profile_type_id,
  organizational_unit_id,
  semester,
  photo_path,
  is_active
)
SELECT
  s.person_id,
  pt.id,
  ou.id,
  s.semester,
  s.photo_path,
  s.is_active
FROM ws_students s
INNER JOIN ws_profile_types pt
  ON pt.name = 'STUDENT'
INNER JOIN ws_academic_programs ap
  ON ap.id = s.academic_program_id
INNER JOIN ws_organizational_units ou
  ON ou.name = ap.name
INNER JOIN ws_organizational_unit_types outt
  ON outt.id = ou.organizational_unit_type_id
 AND outt.name = 'PROGRAM'
LEFT JOIN ws_person_profiles pp
  ON pp.person_id = s.person_id
 AND pp.profile_type_id = pt.id
 AND ((pp.organizational_unit_id = ou.id) OR (pp.organizational_unit_id IS NULL AND ou.id IS NULL))
WHERE pp.id IS NULL;

-- =====================================================
-- 5) Migrar rol actual de users hacia tabla pivote ws_user_roles
-- =====================================================

INSERT IGNORE INTO ws_user_roles (user_id, role_id, is_active)
SELECT id, role_id, 1
FROM users
WHERE role_id IS NOT NULL;

-- =====================================================
-- 6) Escalar planificacion por unidad organizacional
-- =====================================================

ALTER TABLE ws_delivery_scheduling
  ADD COLUMN IF NOT EXISTS organizational_unit_id BIGINT NULL AFTER created_by;

ALTER TABLE ws_delivery_scheduling
  ADD KEY idx_ws_delivery_scheduling_org_unit_id (organizational_unit_id);

ALTER TABLE ws_delivery_scheduling
  ADD CONSTRAINT fk_ws_delivery_scheduling_org_unit
  FOREIGN KEY (organizational_unit_id) REFERENCES ws_organizational_units(id);

-- Reemplaza unicidad global por fecha para soportar varias areas el mismo dia
ALTER TABLE ws_delivery_scheduling
  DROP INDEX uk_ws_delivery_scheduling_delivery_day;

ALTER TABLE ws_delivery_scheduling
  ADD UNIQUE KEY uk_ws_delivery_scheduling_org_day (organizational_unit_id, delivery_day);

-- =====================================================
-- 7) Cambiar entregas de student_id a person_profile_id
-- =====================================================

ALTER TABLE ws_deliveries
  ADD COLUMN IF NOT EXISTS person_profile_id BIGINT NULL AFTER student_id;

UPDATE ws_deliveries d
INNER JOIN ws_students s
  ON s.id = d.student_id
INNER JOIN ws_profile_types pt
  ON pt.name = 'STUDENT'
INNER JOIN ws_person_profiles pp
  ON pp.person_id = s.person_id
 AND pp.profile_type_id = pt.id
SET d.person_profile_id = pp.id
WHERE d.person_profile_id IS NULL;

ALTER TABLE ws_deliveries
  ADD CONSTRAINT fk_ws_deliveries_person_profile
  FOREIGN KEY (person_profile_id) REFERENCES ws_person_profiles(id);

ALTER TABLE ws_deliveries
  ADD UNIQUE KEY uk_ws_deliveries_profile_schedule (person_profile_id, delivery_scheduling_id);

-- =====================================================
-- 8) Validaciones recomendadas antes de limpieza final
-- =====================================================
-- SELECT COUNT(*) FROM ws_deliveries WHERE person_profile_id IS NULL;
-- SELECT COUNT(*) FROM ws_user_roles;
-- SELECT COUNT(*) FROM ws_person_profiles WHERE profile_type_id = (SELECT id FROM ws_profile_types WHERE name='STUDENT');

-- =====================================================
-- 9) Limpieza final (ejecutar SOLO cuando el codigo ya este adaptado)
-- =====================================================

-- ALTER TABLE users DROP FOREIGN KEY fk_users_role;
-- ALTER TABLE users DROP COLUMN role_id;

-- ALTER TABLE ws_deliveries DROP FOREIGN KEY fk_ws_deliveries_student;
-- ALTER TABLE ws_deliveries DROP INDEX uk_ws_deliveries_student_schedule;
-- ALTER TABLE ws_deliveries DROP COLUMN student_id;
-- ALTER TABLE ws_deliveries MODIFY person_profile_id BIGINT NOT NULL;

-- DROP TABLE ws_students;
-- DROP TABLE ws_academic_programs;

SET FOREIGN_KEY_CHECKS = 1;
