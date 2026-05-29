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
-- 3) Crear arbol institucional base (real)
-- =====================================================

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  NULL,
  outt.id,
  'Rectoria',
  1
FROM ws_organizational_unit_types outt
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Rectoria'
WHERE outt.name = 'RECTORY'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  rectoria.id,
  outt.id,
  'Vicerrectoria Academica',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units rectoria
  ON rectoria.name = 'Rectoria'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Vicerrectoria Academica'
WHERE outt.name = 'VICERRECTORY'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  rectoria.id,
  outt.id,
  'Vicerrectoria Administrativa y Financiera',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units rectoria
  ON rectoria.name = 'Rectoria'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Vicerrectoria Administrativa y Financiera'
WHERE outt.name = 'VICERRECTORY'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  rectoria.id,
  outt.id,
  'Vicerrectoria de Investigacion y Extension',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units rectoria
  ON rectoria.name = 'Rectoria'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Vicerrectoria de Investigacion y Extension'
WHERE outt.name = 'VICERRECTORY'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  rectoria.id,
  outt.id,
  'Secretaria General',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units rectoria
  ON rectoria.name = 'Rectoria'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Secretaria General'
WHERE outt.name = 'DEPARTMENT'
  AND ou.id IS NULL;

-- Unidades de apoyo directo a Rectoria
INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  rectoria.id,
  outt.id,
  'Planeacion',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units rectoria
  ON rectoria.name = 'Rectoria'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Planeacion'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  rectoria.id,
  outt.id,
  'Comunicaciones',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units rectoria
  ON rectoria.name = 'Rectoria'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Comunicaciones'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  rectoria.id,
  outt.id,
  'Oficina de Control Interno',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units rectoria
  ON rectoria.name = 'Rectoria'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Oficina de Control Interno'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  rectoria.id,
  outt.id,
  'Consejo Academico',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units rectoria
  ON rectoria.name = 'Rectoria'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Consejo Academico'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

-- Hijos de Secretaria General
INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  sg.id,
  outt.id,
  'Oficina Asesora Juridica',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units sg
  ON sg.name = 'Secretaria General'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Oficina Asesora Juridica'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  sg.id,
  outt.id,
  'Gestion Documental',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units sg
  ON sg.name = 'Secretaria General'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Gestion Documental'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  sg.id,
  outt.id,
  'Atencion al Ciudadano',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units sg
  ON sg.name = 'Secretaria General'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Atencion al Ciudadano'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

-- Hijos de Vicerrectoria de Investigacion y Extension
INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  vie.id,
  outt.id,
  'Investigacion',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units vie
  ON vie.name = 'Vicerrectoria de Investigacion y Extension'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Investigacion'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  vie.id,
  outt.id,
  'Extension y Proyeccion Social',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units vie
  ON vie.name = 'Vicerrectoria de Investigacion y Extension'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Extension y Proyeccion Social'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  vie.id,
  outt.id,
  'Internacionalizacion',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units vie
  ON vie.name = 'Vicerrectoria de Investigacion y Extension'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Internacionalizacion'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

-- Hijos de Vicerrectoria Academica
INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  va.id,
  outt.id,
  'Facultad de Ingenieria',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units va
  ON va.name = 'Vicerrectoria Academica'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Facultad de Ingenieria'
WHERE outt.name = 'FACULTY'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  va.id,
  outt.id,
  'Facultad de CAEC',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units va
  ON va.name = 'Vicerrectoria Academica'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Facultad de CAEC'
WHERE outt.name = 'FACULTY'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  va.id,
  outt.id,
  'Facultad de Humanidades',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units va
  ON va.name = 'Vicerrectoria Academica'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Facultad de Humanidades'
WHERE outt.name = 'FACULTY'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  va.id,
  outt.id,
  'Admisiones y Registro',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units va
  ON va.name = 'Vicerrectoria Academica'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Admisiones y Registro'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  va.id,
  outt.id,
  'Bienestar Institucional',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units va
  ON va.name = 'Vicerrectoria Academica'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Bienestar Institucional'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  va.id,
  outt.id,
  'Biblioteca',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units va
  ON va.name = 'Vicerrectoria Academica'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Biblioteca'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  va.id,
  outt.id,
  'Aseguramiento de los Sistemas',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units va
  ON va.name = 'Vicerrectoria Academica'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Aseguramiento de los Sistemas'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

-- Hijos de Vicerrectoria Administrativa y Financiera
INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  vaf.id,
  outt.id,
  'Gestion Financiera',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units vaf
  ON vaf.name = 'Vicerrectoria Administrativa y Financiera'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Gestion Financiera'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  vaf.id,
  outt.id,
  'Gestion de Bienes y Servicio',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units vaf
  ON vaf.name = 'Vicerrectoria Administrativa y Financiera'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Gestion de Bienes y Servicio'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  vaf.id,
  outt.id,
  'Gestion Humana',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units vaf
  ON vaf.name = 'Vicerrectoria Administrativa y Financiera'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Gestion Humana'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  vaf.id,
  outt.id,
  'Coordinador de Sede',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units vaf
  ON vaf.name = 'Vicerrectoria Administrativa y Financiera'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Coordinador de Sede'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

INSERT INTO ws_organizational_units (parent_id, organizational_unit_type_id, name, is_active)
SELECT
  vaf.id,
  outt.id,
  'Infraestructura Tecnologica',
  1
FROM ws_organizational_unit_types outt
INNER JOIN ws_organizational_units vaf
  ON vaf.name = 'Vicerrectoria Administrativa y Financiera'
LEFT JOIN ws_organizational_units ou
  ON ou.name = 'Infraestructura Tecnologica'
WHERE outt.name = 'AREA'
  AND ou.id IS NULL;

-- =====================================================
-- 4) Migrar programas academicos a unidades organizacionales
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
-- 5) Enlazar programas al arbol (parent_id)
-- =====================================================

UPDATE ws_organizational_units ou
INNER JOIN ws_organizational_unit_types outt
  ON outt.id = ou.organizational_unit_type_id
INNER JOIN ws_organizational_units fi
  ON fi.name = 'Facultad de Ingenieria'
SET ou.parent_id = fi.id
WHERE outt.name = 'PROGRAM'
  AND ou.parent_id IS NULL
  AND (
    ou.name LIKE 'Ingenieria%'
    OR ou.name LIKE '%Sistemas%'
    OR ou.name LIKE '%Industrial%'
    OR ou.name LIKE '%Electronica%'
  );

UPDATE ws_organizational_units ou
INNER JOIN ws_organizational_unit_types outt
  ON outt.id = ou.organizational_unit_type_id
SET ou.parent_id = (
  SELECT f.id
  FROM ws_organizational_units f
  WHERE f.name IN ('Facultad de CAEC', 'Facultad de Ciencias Economicas y Administrativas')
  ORDER BY FIELD(f.name, 'Facultad de CAEC', 'Facultad de Ciencias Economicas y Administrativas')
  LIMIT 1
)
WHERE outt.name = 'PROGRAM'
  AND ou.parent_id IS NULL
  AND (
    ou.name LIKE 'Administracion%'
    OR ou.name LIKE '%Contad%'
    OR ou.name LIKE '%Econom%'
    OR ou.name LIKE '%Finanz%'
  );

-- Fallback: si no hubo regla de mapeo, cuelga el programa de Vicerrectoria Academica.
UPDATE ws_organizational_units ou
INNER JOIN ws_organizational_unit_types outt
  ON outt.id = ou.organizational_unit_type_id
SET ou.parent_id = (
  SELECT va.id
  FROM ws_organizational_units va
  WHERE va.name = 'Vicerrectoria Academica'
  LIMIT 1
)
WHERE outt.name = 'PROGRAM'
  AND ou.parent_id IS NULL;

-- =====================================================
-- 6) Migrar estudiantes a perfiles de persona
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
-- 7) Sembrar roles orientados a permisos
-- =====================================================

INSERT IGNORE INTO ws_roles (name, is_active)
VALUES
('MODULE_ADMIN', 1),
('DELIVERY_OPERATOR', 1),
('PLANNER', 1),
('REPORT_VIEWER', 1);

-- =====================================================
-- 8) Migrar rol actual de users hacia tabla pivote ws_user_roles
-- =====================================================

INSERT IGNORE INTO ws_user_roles (user_id, role_id, is_active)
SELECT id, role_id, 1
FROM users
WHERE role_id IS NOT NULL;

-- =====================================================
-- 9) Escalar planificacion por unidad organizacional
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
-- 10) Cambiar entregas de student_id a person_profile_id
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
-- 11) Validaciones recomendadas antes de limpieza final
-- =====================================================
-- SELECT COUNT(*) FROM ws_deliveries WHERE person_profile_id IS NULL;
-- SELECT COUNT(*) FROM ws_user_roles;
-- SELECT COUNT(*) FROM ws_person_profiles WHERE profile_type_id = (SELECT id FROM ws_profile_types WHERE name='STUDENT');
-- SELECT outt.name AS unit_type, parent.name AS parent_name, child.name AS child_name
-- FROM ws_organizational_units child
-- LEFT JOIN ws_organizational_units parent ON parent.id = child.parent_id
-- INNER JOIN ws_organizational_unit_types outt ON outt.id = child.organizational_unit_type_id
-- ORDER BY outt.name, parent.name, child.name;
--
-- SELECT child.id, child.name, child.parent_id
-- FROM ws_organizational_units child
-- INNER JOIN ws_organizational_unit_types outt ON outt.id = child.organizational_unit_type_id
-- WHERE outt.name = 'PROGRAM' AND child.parent_id IS NULL;

-- =====================================================
-- 12) Limpieza final (ejecutar SOLO cuando el codigo ya este adaptado)
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
