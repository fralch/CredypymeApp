
-- Desactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 0;

DELETE from sesiones;

-- Reactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;