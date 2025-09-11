-- Desactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 0;

update usuarios set correo_corporativo = '-'; 

update
	usuarios
set
	clave = '$2y$10$Hvj1259OxC//IiWS6gydxeZlMxo5ScEpMlAVutn0OQtjdvxyLkW4O',
	habilitado = 0 ,
	direccion = null,
	fecha_nacimiento = '1999-01-01',
	telefono = null,
	usuario_real = 0,
	datos_creacion = null,
	datos_actualizacion = null
where
	agencia_id in (1, 4, 5, 6)
	and usuario not in ('huber_aa','brandon_pq', 'personal_emp', 'cartera_dscto_agencias', 'cartera_recup_admin','soporte_ti');

delete from usuarios_cesados 
where usuario_id in
(select dni from usuarios where agencia_id in (1,4,5,6) 
and usuario not in ('huber_aa','brandon_pq', 'personal_emp', 'cartera_dscto_agencias', 'cartera_recup_admin','soporte_ti'));

delete 
from usuarios_permisos 
where usuario_id in
(select dni from usuarios where agencia_id in (1,4,5,6) 
and usuario not in ('huber_aa','brandon_pq', 'personal_emp', 'cartera_dscto_agencias', 'cartera_recup_admin','soporte_ti'));

update usuarios_permisos set acceso_agencias ='[{"agencia_id":2}]' where usuario_id in (select dni from usuarios where agencia_id = 2 and habilitado = 1);
update usuarios_permisos set acceso_agencias ='[{"agencia_id":3}]' where usuario_id in (select dni from usuarios where agencia_id = 3 and habilitado = 1);
update usuarios_permisos set acceso_agencias ='[{"agencia_id":5}]' where usuario_id in (select dni from usuarios where agencia_id = 5 and habilitado = 1);

update usuarios_permisos set acceso_agencias = '[{"agencia_id":2},{"agencia_id":3},{"agencia_id":5}]' where usuario_id = 11111111; 

-- Reactivar verificación de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;


