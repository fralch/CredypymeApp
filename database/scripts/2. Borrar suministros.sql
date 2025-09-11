
-- Desactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 0;

delete from suministro_devoluciones where id in (select devolucion_id from suministro_devoluciones_detalles where asignacion_detalle_id in (select id from suministro_asignaciones_detalles where suministro_id in (select id from suministro_almacen where agencia_id in (1,4,5,6))));

delete from suministro_devoluciones_detalles where asignacion_detalle_id in (select id from suministro_asignaciones_detalles where suministro_id in (select id from suministro_almacen where agencia_id in (1,4,5,6)));

delete from suministro_asignaciones where id in (select asignacion_id from suministro_asignaciones_detalles where suministro_id in (select id from suministro_almacen where agencia_id in (1,4,5,6)));

delete from suministro_asignaciones_detalles where suministro_id in (select id from suministro_almacen where agencia_id in (1,4,5,6));

delete from suministro_compras where id in (select compra_id from suministro_compras_detalles where suministro_id in (select id from suministro_almacen where agencia_id in (1,4,5,6)));

delete from suministro_compras_detalles where suministro_id in (select id from suministro_almacen where agencia_id in (1,4,5,6));

delete from suministro_envios where id in (select envio_id from suministro_envios_detalles where suministro_id in (select id from suministro_almacen where agencia_id in (1,4,5,6)));

delete from suministro_envios_detalles where suministro_id in (select id from suministro_almacen where agencia_id in (1,4,5,6));

delete from suministro_ventas where id in (select venta_id from suministro_ventas_detalles where suministro_id in (select id from suministro_almacen where agencia_id in (1,4,5,6)));

delete from suministro_ventas_detalles where suministro_id in (select id from suministro_almacen where agencia_id in (1,4,5,6));

delete from suministro_almacen where agencia_id in (1,4,5,6);

delete from logistica_responsables  where agencia_id in (1,4,5,6);

delete from solucion_records.suministro_almacen_records  where agencia_id in (1,4,5,6);

-- Reactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;
