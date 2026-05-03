# Flujo Correcto De Ingreso De Datos

## 1) Preparacion Inicial (una sola vez)
1. Configurar `.env` con credenciales de BD.
2. Ejecutar bootstrap completo:
   - `php artisan bootstrap:initial-data --force`
3. Verificar acceso:
   - URL: `http://127.0.0.1:8000/login`
   - Usuario: `admin`
   - Clave: `admin123`

## 2) Orden Correcto Para Ingresar Datos De Negocio
El orden recomendado para evitar errores de llaves foraneas es:

1. **General**
   - Agencias
   - Cargos
   - Usuarios
   - Permisos
2. **Catalogos Creditos**
   - Estados de credito
   - Clientes base
3. **Operacion Creditos**
   - Grupos
   - Grupo clientes
   - Solicitudes grupales
   - Grupo solicitud creditos
4. **Operacion GTH / Logistica**
   - Segun habilitacion de cada modulo

## 3) Comandos Operativos

### Migraciones
- Todas:
  - `php artisan migrate:modules migrate --force`
- Estado:
  - `php artisan migrate:modules status`
- Un modulo:
  - `php artisan migrate:modules migrate --module=creditos --force`

### Seeders
- Todos:
  - `php artisan seed:modules --force`
- Un modulo:
  - `php artisan seed:modules --module=general --force`
  - `php artisan seed:modules --module=creditos --force`

## 4) Seeders Iniciales Actuales
- `Modules\General\Infrastructure\Persistence\Seeders\GeneralBootstrapSeeder`
  - Crea/actualiza agencia principal, cargo administrador, usuario admin y version base.
- `Modules\Creditos\Infrastructure\Persistence\Seeders\CreditosCatalogosSeeder`
  - Carga estados de credito y cliente base por agencia.
- `Modules\Creditos\Infrastructure\Persistence\Seeders\CreditosPermisosSeeder`
  - Carga permisos base de Creditos.

## 5) Validacion Rapida Post-Carga
1. `php artisan migrate:modules status` debe mostrar migraciones en `Ran`.
2. `php artisan seed:modules --force` debe terminar sin error.
3. Probar:
   - `/` => 200
   - `/login` => 200
4. Confirmar datos minimos en BD:
   - `solucion_master.agencias`
   - `solucion_master.usuarios`
   - `solucion_master.permisos`
   - `solucion_master_1.credito_estados`
