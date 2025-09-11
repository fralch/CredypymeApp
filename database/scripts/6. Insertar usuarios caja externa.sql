-- USUARIOS ------------------------------------------------------

insert
	into
	solucion_master.usuarios
(dni,
	usuario,
	clave,
	nombres,
	apellido_paterno,
	apellido_materno,
	sexo,
	direccion,
	distrito_id,
	provincia_id,
	departamento_id,
	fecha_nacimiento,
	telefono,
	correo_corporativo,
	cargo_id,
	agencia_id,
	habilitado,
	actualizo_clave,
	usuario_real,
	datos_creacion,
	datos_actualizacion,
	created_at,
	updated_at)
values(99999991, 'caja_tambo', '$2y$10$Hvj1259OxC//IiWS6gydxeZlMxo5ScEpMlAVutn0OQtjdvxyLkW4O', 'CAJA_TAMBO', 'CAJA_TAMBO', 'CAJA_TAMBO', 'M', null, 1006, 103, 12, '1999-01-01', null, '-', 3, 5, 1, 0, 0, null, null, '2025-08-23 12:00:00', null);


insert
	into
	solucion_master.usuarios
(dni,
	usuario,
	clave,
	nombres,
	apellido_paterno,
	apellido_materno,
	sexo,
	direccion,
	distrito_id,
	provincia_id,
	departamento_id,
	fecha_nacimiento,
	telefono,
	correo_corporativo,
	cargo_id,
	agencia_id,
	habilitado,
	actualizo_clave,
	usuario_real,
	datos_creacion,
	datos_actualizacion,
	created_at,
	updated_at)
values(99999994, 'caja_hvca', '$2y$10$Hvj1259OxC//IiWS6gydxeZlMxo5ScEpMlAVutn0OQtjdvxyLkW4O', 'CAJA_HVCA', 'CAJA_HVCA', 'CAJA_HVCA', 'M', null, 1006, 103, 12, '1999-01-01', null, '-', 3, 5, 1, 0, 0, null, null, '2025-08-23 12:00:00', null);


insert
	into
	solucion_master.usuarios
(dni,
	usuario,
	clave,
	nombres,
	apellido_paterno,
	apellido_materno,
	sexo,
	direccion,
	distrito_id,
	provincia_id,
	departamento_id,
	fecha_nacimiento,
	telefono,
	correo_corporativo,
	cargo_id,
	agencia_id,
	habilitado,
	actualizo_clave,
	usuario_real,
	datos_creacion,
	datos_actualizacion,
	created_at,
	updated_at)
values(99999996, 'caja_chilca', '$2y$10$Hvj1259OxC//IiWS6gydxeZlMxo5ScEpMlAVutn0OQtjdvxyLkW4O', 'CAJA_CHILCA', 'CAJA_CHILCA', 'CAJA_CHILCA', 'M', null, 1006, 103, 12, '1999-01-01', null, '-', 3, 5, 1, 0, 0, null, null, '2025-08-23 12:00:00', null);

-- CAJAS ------------------------------------------------------

insert
	into
	solucion_master_5.caja_registros 
	(
	dni,
	agencia_id,
	comentario_apertura,
	comentario_cierre,
	monto_apertura,
	monto_cierre_ingresos,
	monto_cierre_egresos,
	created_at,
	updated_at)
values(99999991, 5, 'CAJA TAMBO', 'CAJA TAMBO', 0, 0, 0, '2025-08-23 12:00:00', '2025-08-23 12:00:00');

insert
	into
	solucion_master_5.caja_registros 
	(
	dni,
	agencia_id,
	comentario_apertura,
	comentario_cierre,
	monto_apertura,
	monto_cierre_ingresos,
	monto_cierre_egresos,
	created_at,
	updated_at)
values(99999994, 5, 'CAJA HVCA', 'CAJA HVCA', 0, 0, 0, '2025-08-23 12:00:00', '2025-08-23 12:00:00');

insert
	into
	solucion_master_5.caja_registros 
	(
	dni,
	agencia_id,
	comentario_apertura,
	comentario_cierre,
	monto_apertura,
	monto_cierre_ingresos,
	monto_cierre_egresos,
	created_at,
	updated_at)
values(99999996, 5, 'CAJA CHILCA', 'CAJA CHILCA', 0, 0, 0, '2025-08-23 12:00:00', '2025-08-23 12:00:00');

select id from solucion_master_5.caja_registros where dni in (99999991,99999994,99999996);

-- CUENTAS ------------------------------------------------------

insert
	into
	solucion_master_5.cuenta_usuarios (
	dni,
	con_cuenta,
	monto
	)
values( 99999991, 1, 0);

insert
	into
	solucion_master_5.cuenta_usuarios (
	dni,
	con_cuenta,
	monto
	)
values( 99999994, 1, 0);

insert
	into
	solucion_master_5.cuenta_usuarios (
	dni,
	con_cuenta,
	monto
	)
values( 99999996, 1, 0);




-- EN CREDIAPP

-- USUARIOS ------------------------------------------------------

insert
	into
	credipyme_master.usuarios
(dni,
	usuario,
	clave,
	nombres,
	apellido_paterno,
	apellido_materno,
	sexo,
	direccion,
	distrito_id,
	provincia_id,
	departamento_id,
	fecha_nacimiento,
	telefono,
	correo_corporativo,
	cargo_id,
	agencia_id,
	habilitado,
	actualizo_clave,
	usuario_real,
	datos_creacion,
	datos_actualizacion,
	created_at,
	updated_at)
values(99999992, 'caja_hyo', '$2y$10$Hvj1259OxC//IiWS6gydxeZlMxo5ScEpMlAVutn0OQtjdvxyLkW4O', 'CAJA_HYO', 'CAJA_HYO', 'CAJA_HYO', 'M', null, 1006, 103, 12, '1999-01-01', null, '-', 3, 5, 1, 0, 0, null, null, '2025-08-23 12:00:00', null);


insert
	into
	credipyme_master.usuarios
(dni,
	usuario,
	clave,
	nombres,
	apellido_paterno,
	apellido_materno,
	sexo,
	direccion,
	distrito_id,
	provincia_id,
	departamento_id,
	fecha_nacimiento,
	telefono,
	correo_corporativo,
	cargo_id,
	agencia_id,
	habilitado,
	actualizo_clave,
	usuario_real,
	datos_creacion,
	datos_actualizacion,
	created_at,
	updated_at)
values(99999993, 'caja_pampas', '$2y$10$Hvj1259OxC//IiWS6gydxeZlMxo5ScEpMlAVutn0OQtjdvxyLkW4O', 'CAJA_PAMPAS', 'CAJA_PAMPAS', 'CAJA_PAMPAS', 'M', null, 1006, 103, 12, '1999-01-01', null, '-', 3, 5, 1, 0, 0, null, null, '2025-08-23 12:00:00', null);


-- CAJAS ------------------------------------------------------

insert
	into
	credipyme_master_5.caja_registros 
	(
	dni,
	agencia_id,
	comentario_apertura,
	comentario_cierre,
	monto_apertura,
	monto_cierre_ingresos,
	monto_cierre_egresos,
	created_at,
	updated_at)
values(99999992, 5, 'CAJA HYO', 'CAJA HYO', 0, 0, 0, '2025-08-23 12:00:00', '2025-08-23 12:00:00');

insert
	into
	credipyme_master_5.caja_registros 
	(
	dni,
	agencia_id,
	comentario_apertura,
	comentario_cierre,
	monto_apertura,
	monto_cierre_ingresos,
	monto_cierre_egresos,
	created_at,
	updated_at)
values(99999993, 5, 'CAJA PAMPAS', 'CAJA PAMPAS', 0, 0, 0, '2025-08-23 12:00:00', '2025-08-23 12:00:00');

-- CUENTAS ------------------------------------------------------

insert
	into
	credipyme_master_5.cuenta_usuarios (
	dni,
	con_cuenta,
	monto
	)
values( 99999992, 1, 0);

insert
	into
	credipyme_master_5.cuenta_usuarios (
	dni,
	con_cuenta,
	monto
	)
values( 99999993, 1, 0);
