-- Desactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 0;

delete from evaluacion_financiera_activo_corriente where evaluacion_id in (select id from evaluacion_financiera_registros efr  where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332));
delete from evaluacion_financiera_activo_no_corriente  where evaluacion_id in (select id from evaluacion_financiera_registros efr  where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332));
delete from evaluacion_financiera_comentarios  where evaluacion_id in (select id from evaluacion_financiera_registros efr  where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332));
delete from evaluacion_financiera_convenio  where evaluacion_id in (select id from evaluacion_financiera_registros efr  where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332));
delete from evaluacion_financiera_flujo_caja  where evaluacion_id in (select id from evaluacion_financiera_registros efr  where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332));
delete from evaluacion_financiera_pasivo_corriente  where evaluacion_id in (select id from evaluacion_financiera_registros efr  where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332));
delete from evaluacion_financiera_registros where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332);

delete from cliente_negocios  where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332);
delete from cliente_parientes where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332);
delete from cliente_avales where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332);
delete from cliente_comentarios where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332);
delete from cliente_album_fotos where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332);

delete from credito_aprobaciones where propuesta_id in ( select id from credito_propuestas cp where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332) );
delete from credito_propuestas where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332);

delete from credito_cuotas where credito_id in ( select id from credito_registros cr where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332) );
delete from credito_compromisos where credito_id in ( select id from credito_registros cr where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332) );
delete from caja_pago_cuotas  where credito_id in ( select id from credito_registros cr where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332) );
delete from caja_pago_vouchers  where credito_id in ( select id from credito_registros cr where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332) );
delete from caja_pago_moras  where credito_id in ( select id from credito_registros cr where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332) );
delete from caja_pago_notificaciones where notificacion_id in (select id from credito_notificaciones cn  where credito_id in ( select id from credito_registros cr where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332) ));
delete from credito_notificaciones  where credito_id in ( select id from credito_registros cr where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332) );


delete from caja_comisiones_pagos  where desembolso_id in (select id from caja_desembolsos where credito_id in ( select id from credito_registros cr where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332) ));
delete from caja_desembolsos  where credito_id in ( select id from credito_registros cr where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332) );

delete from solucion_records_5.credito_registros_records where credito_id in ( select id from solucion_master_5.credito_registros cr where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332) );

delete from credito_registros where cliente_id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332);

delete from cliente_registros where id not in (14, 32, 35, 243, 256, 266, 274, 279, 288, 331, 332);



-- Reactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;
