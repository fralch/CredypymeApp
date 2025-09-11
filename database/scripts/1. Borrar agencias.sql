-- Desactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 0;

-- Borrar filas de la tabla (ejemplo con condición)
DELETE FROM agencias
WHERE id_agencia in (1,4,6);

-- Reactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;