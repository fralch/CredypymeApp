
-- CUOTAS

UPDATE caja_pago_cuotas
SET caja_id = CASE agencia_caja
                WHEN 1 THEN 1804
                WHEN 4 THEN 1805
                WHEN 6 THEN 1806
                ELSE caja_id
              end
  
WHERE agencia_caja != 2;

UPDATE caja_pago_cuotas
SET agencia_caja = 5
WHERE agencia_caja != 2;

-- MORAS

UPDATE caja_pago_moras
SET caja_id = CASE agencia_caja
                WHEN 1 THEN 1804
                WHEN 4 THEN 1805
                WHEN 6 THEN 1806
                ELSE caja_id
              end
  
WHERE agencia_caja != 2;

UPDATE caja_pago_moras
SET agencia_caja = 5
WHERE agencia_caja != 2;

-- NOTIFICACIONES

UPDATE caja_pago_notificaciones 
SET caja_id = CASE agencia_caja
                WHEN 1 THEN 1804
                WHEN 4 THEN 1805
                WHEN 6 THEN 1806
                ELSE caja_id
              end
  
WHERE agencia_caja != 2;

UPDATE caja_pago_notificaciones
SET agencia_caja = 5
WHERE agencia_caja != 2;

-- CRÉDITO REGISTROS

UPDATE credito_registros 
SET caja_cancelado_id = CASE agencia_caja_cancelado
                WHEN 1 THEN 1804
                WHEN 4 THEN 1805
                WHEN 6 THEN 1806
                ELSE caja_cancelado_id
              end
  
WHERE agencia_caja_cancelado != 2;

UPDATE credito_registros
SET agencia_caja_cancelado = 5
WHERE agencia_caja_cancelado != 2;

-- CAJA DESEMBOLSOS

UPDATE caja_desembolsos  
SET caja_id = CASE agencia_caja
                WHEN 1 THEN 1804
                WHEN 4 THEN 1805
                WHEN 6 THEN 1806
                ELSE caja_id
              end
  
WHERE agencia_caja != 2;

UPDATE caja_desembolsos
SET agencia_caja = 5
WHERE agencia_caja != 2;

-- CAJA COMISIONES PAGOS

UPDATE caja_comisiones_pagos  
SET caja_id = CASE agencia_caja
                WHEN 1 THEN 1804
                WHEN 4 THEN 1805
                WHEN 6 THEN 1806
                ELSE caja_id
              end
  
WHERE agencia_caja != 2;

UPDATE caja_comisiones_pagos
SET agencia_caja = 5
WHERE agencia_caja != 2;

-- INVERSION META REGISTROS 

UPDATE inversion_meta_registros  
SET caja_apertura = CASE agencia_caja_apertura
                WHEN 1 THEN 1804
                WHEN 4 THEN 1805
                WHEN 6 THEN 1806
                ELSE caja_apertura
              end
  
WHERE agencia_caja_apertura != 2;

UPDATE inversion_meta_registros
SET agencia_caja_apertura = 5
WHERE agencia_caja_apertura != 2;

UPDATE inversion_meta_registros  
SET caja_cierre = CASE agencia_caja_cierre
                WHEN 1 THEN 1804
                WHEN 4 THEN 1805
                WHEN 6 THEN 1806
                ELSE caja_cierre
              end
  
WHERE agencia_caja_cierre != 2;

UPDATE inversion_meta_registros
SET agencia_caja_cierre = 5
WHERE agencia_caja_cierre != 2;

-- INVERSION META MOVIMIENTOS 

UPDATE inversion_meta_movimientos 
SET caja_id = CASE agencia_caja
                WHEN 1 THEN 1804
                WHEN 4 THEN 1805
                WHEN 6 THEN 1806
                ELSE caja_id
              end
  
WHERE agencia_caja != 2;

UPDATE inversion_meta_movimientos
SET agencia_caja = 5
WHERE agencia_caja != 2;