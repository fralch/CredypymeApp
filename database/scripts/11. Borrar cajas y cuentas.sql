-- Desactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 0;



delete from caja_registros where dni not in (99999996,
99999994,
99999991,
11111111) ;

delete from cuenta_usuarios where dni not in (99999996,
99999994,
99999991,
11111111) ;


delete from transaccion_registros ;

delete from caja_billeteos ;

delete from caja_envios_pagos ;

delete from caja_recepcion_pagos ;

delete from caja_transferencias ;

delete from cuenta_envios ;

delete from cuenta_movimientos ;

delete from cuenta_transferencias  ;

delete from inversion_productos_meta where id not in (218,
217,
216) ;

delete from inversion_meta_registros ;

delete from inversion_meta_movimientos ;


delete from solucion_records_5.cuenta_usuarios_records where cuenta_id in (select id from cuenta_usuarios where dni not in (99999996,
99999994,
99999991,
11111111)) ;


-- Reactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;
