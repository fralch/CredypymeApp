-- Desactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM solucion_master.bancos
WHERE id=1;
DELETE FROM solucion_master.bancos
WHERE id=2;
DELETE FROM solucion_master.bancos
WHERE id=3;
DELETE FROM solucion_master.bancos
WHERE id=9;
DELETE FROM solucion_master.bancos
WHERE id=10;
DELETE FROM solucion_master.bancos
WHERE id=11;
DELETE FROM solucion_master.bancos
WHERE id=12;
DELETE FROM solucion_master.bancos
WHERE id=14;
DELETE FROM solucion_master.bancos
WHERE id=15;
DELETE FROM solucion_master.bancos
WHERE id=16;
DELETE FROM solucion_master.bancos
WHERE id=17;

-- Reactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;