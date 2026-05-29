-- Renombra roles de permisos al esquema en espanol sin romper relaciones existentes
-- Ejecutar una sola vez en la base de datos actual.

SET NAMES utf8mb4;
START TRANSACTION;

INSERT IGNORE INTO ws_roles (name, is_active) VALUES
('ADMINISTRADOR', 1),
('ADMIN_MODULO', 1),
('OPERADOR_ENTREGAS', 1),
('PLANIFICADOR', 1),
('GESTOR_BENEFICIARIOS', 1),
('VISOR_REPORTES', 1),
('AUDITOR', 1);

UPDATE ws_roles r
SET r.name = 'ADMINISTRADOR'
WHERE r.name = 'ADMIN'
  AND NOT EXISTS (SELECT 1 FROM ws_roles x WHERE x.name = 'ADMINISTRADOR');

UPDATE ws_roles r
SET r.name = 'ADMIN_MODULO'
WHERE r.name = 'MODULE_ADMIN'
  AND NOT EXISTS (SELECT 1 FROM ws_roles x WHERE x.name = 'ADMIN_MODULO');

UPDATE ws_roles r
SET r.name = 'OPERADOR_ENTREGAS'
WHERE r.name = 'DELIVERY_OPERATOR'
  AND NOT EXISTS (SELECT 1 FROM ws_roles x WHERE x.name = 'OPERADOR_ENTREGAS');

UPDATE ws_roles r
SET r.name = 'PLANIFICADOR'
WHERE r.name = 'PLANNER'
  AND NOT EXISTS (SELECT 1 FROM ws_roles x WHERE x.name = 'PLANIFICADOR');

UPDATE ws_roles r
SET r.name = 'GESTOR_BENEFICIARIOS'
WHERE r.name = 'BENEFICIARY_MANAGER'
  AND NOT EXISTS (SELECT 1 FROM ws_roles x WHERE x.name = 'GESTOR_BENEFICIARIOS');

UPDATE ws_roles r
SET r.name = 'VISOR_REPORTES'
WHERE r.name = 'REPORT_VIEWER'
  AND NOT EXISTS (SELECT 1 FROM ws_roles x WHERE x.name = 'VISOR_REPORTES');

COMMIT;

SELECT id, name, is_active
FROM ws_roles
ORDER BY id;
