# Documentación de CredypymeApp

## 1. Resumen Ejecutivo
**CredypymeApp** es un sistema integral de gestión financiera, recursos humanos y logística (tipo ERP), orientado principalmente al sector de las microfinanzas y créditos empresariales en Perú.

Está desarrollado sobre una arquitectura monolítica modular, utilizando el stack de **Laravel 9** para el backend y **Vue.js 2 (con Inertia.js)** para el frontend. 

## 2. Arquitectura del Sistema y Stack Tecnológico

### Stack Tecnológico
- **Backend:** PHP 8.0.2+, Laravel Framework ^9.19
- **Frontend:** Vue 2.7.x, Inertia.js, Vite 4
- **UI Componentes:** PrimeVue 2, PrimeIcons
- **Base de Datos:** MySQL / MariaDB (vía Eloquent ORM y doctrine/dbal)
- **Generación de Documentos:** mPDF (PDF), PhpSpreadsheet (Excel), PhpWord (Word), docxtemplater (JS).

### Diagrama de Arquitectura (Terminal Style)

```text
  [ Navegador Web ] <--- HTTP / JSON ---> [ Servidor Web (Nginx/Apache) ]
     (Vue.js 2)                                          | (Vite Assets)
   (Inertia.js UI)                                       v
  +-------------------------------------------------------------------------+
  |                        LARAVEL 9 BACKEND (Monolito)                     |
  |                                                                         |
  |  +----------------+    +----------------+    +--------------------+     |
  |  | Routing (Web)  |--->|   Middleware   |--->|    Controllers     |     |
  |  |  (Inertia)     |    | (Auth/Sesión)  |    | (Lógica Aplicación)|     |
  |  +----------------+    +----------------+    +--------------------+     |
  |                                                        |                |
  |                   Módulos del Dominio (App\Models)     v                |
  |  +----------------+    +----------------+    +--------------------+     |
  |  |  1. Créditos   |    |     2. GTH     |    |   3. Logística     |     |
  |  | (Core Negocio) |    | (Recursos Hum) |    | (Activos/Suminist)|     |
  |  +----------------+    +----------------+    +--------------------+     |
  |                                                        |                |
  |  +----------------+    +----------------+    +--------------------+     |
  |  | Eloquent ORM   |    |  File Storage  |    | Docs Generation    |     |
  |  | (Base Datos)   |    |  (Local/S3)    |    | (mPDF, PHPWord)    |     |
  |  +----------------+    +----------------+    +--------------------+     |
  |                                                                         |
  +-------------------------------------------------------------------------+
                                       |
                                       v
                              [ Base de Datos MySQL ]
```

## 3. Módulos y Lógica de Negocio

El sistema está fuertemente modularizado dentro del directorio `app/Models` y `routes/`:

### 3.1 Módulo de Créditos (Core)
Es el núcleo del sistema, encargado de la originación, evaluación, desembolso y cobranza de créditos.
- **Clientes:** Gestión de perfiles, avales, negocios, grupos de clientes, y parientes.
- **Crédito:** Flujo completo que incluye Propuestas, Evaluaciones Financieras (flujo de caja, activos, pasivos), Aprobación, Cronogramas y Cuotas.
- **Caja y Transacciones:** Manejo del efectivo, desembolsos, pagos de cuotas, moras, transferencias y comisiones (Billeteo, Cierres).
- **Inversión y Cuentas:** Registro de cuentas bancarias y movimientos asociados.

### 3.2 Módulo GTH (Gestión de Talento Humano)
Control de los recursos humanos de la empresa.
- **Asistencias:** Marcajes (entradas/salidas), faltas, tardanzas, licencias y justificaciones.
- **Planillas:** Gestión de remuneraciones, vacaciones, y sistemas de pensiones (AFP/ONP).
- **Evaluaciones:** Funciones para "Colaborador del Mes", equipos, y exámenes a los colaboradores.

### 3.3 Módulo de Logística
Administración de bienes y recursos de la organización.
- **Activos Fijos:** Compras, asignaciones a colaboradores/agencias, envíos y ventas de activos.
- **Suministros:** Control de inventarios, proveedores, compras, y devoluciones.

### 3.4 Módulo General
Configuraciones base del sistema: Agencias, Áreas de Trabajo, Cargos, Permisos de Usuario, Feriados y ubicación geográfica (Ubigeo Perú).

## 4. Diagrama Entidad-Relación Core (Terminal Style)

Este diagrama ilustra las relaciones principales del flujo de créditos, el más crítico del sistema:

```text
+-------------------+        +-------------------+        +-------------------+
|      CLIENTE      |1-----N |      CREDITO      |1-----N |       CUOTA       |
+-------------------+        +-------------------+        +-------------------+
| id (PK)           |        | id (PK)           |        | id (PK)           |
| dni / ruc         |        | cliente_id (FK)   |        | credito_id (FK)   |
| nombres           |        | monto_aprobado    |        | numero_cuota      |
| estado_civil      |        | tasa_interes      |        | monto_capital     |
| calificacion      |        | estado            |        | fecha_vencimiento |
+-------------------+        +-------------------+        +-------------------+
          | 1                          | 1                          | 1
          | N                          | N                          | N
+-------------------+        +-------------------+        +-------------------+
|      NEGOCIO      |        |    CRONOGRAMA     |        |     PAGOCUOTA     |
+-------------------+        +-------------------+        +-------------------+
| id (PK)           |        | id (PK)           |        | id (PK)           |
| cliente_id (FK)   |        | credito_id (FK)   |        | cuota_id (FK)     |
| nombre_comercial  |        | fecha_emision     |        | caja_id (FK)      |
| ruc               |        | total_interes     |        | monto_pagado      |
| direccion         |        | total_pagar       |        | fecha_pago        |
+-------------------+        +-------------------+        +-------------------+
                                                                    | N
                                                                    | 1
                                                          +-------------------+
                                                          |       CAJA        |
                                                          +-------------------+
                                                          | id (PK)           |
                                                          | agencia_id (FK)   |
                                                          | usuario_id (FK)   |
                                                          | saldo_inicial     |
                                                          | saldo_final       |
                                                          +-------------------+
```

## 5. Rutas y APIs

El sistema divide sus puntos de entrada (rutas) de forma semántica en archivos dedicados:
- `routes/web.php`: Rutas públicas, autenticación (Login, Logout), Home y Dashboard.
- `routes/general.php`: Rutas de configuración base (Agencias, Usuarios, Ubigeos).
- `routes/creditos.php`: Rutas para gestión de solicitudes, evaluación, aprobación, desembolsos y cobranzas.
- `routes/gth.php`: Rutas para marcajes de asistencia, planillas y evaluaciones.
- `routes/logistica.php`: Rutas para inventarios y activos.

## 6. Comandos Útiles de Desarrollo

Para iniciar o trabajar con este proyecto, asumiendo que tienes PHP 8.x, Composer y Node.js instalados:

```bash
# Instalar dependencias backend
composer install

# Instalar dependencias frontend
npm install

# Compilar assets del frontend para desarrollo
npm run dev

# Compilar assets para producción
npm run build

# Configurar el archivo de entorno
cp .env.example .env
php artisan key:generate

# Levantar servidor de desarrollo
php artisan serve
```

## 7. Estructura de Modelos de Base de Datos (Detallada)

### 7.1 Modelo de Datos Principal - Créditos

#### Cliente (`app/Models/Creditos/Clientes/Cliente.php`)
- **Tabla:** `cliente_registros`
- **Primary Key:** `id`
- **Campos principales:**
  - Identificación: `dni`, `apellido_paterno`, `apellido_materno`, `nombres`
  - Datos personales: `fecha_nacimiento`, `estado_civil`, `sexo`, `hijos`
  - Localización: `departamento_id`, `provincia_id`, `distrito_id`, `direccion`, `telefonos`
  - Referencias: `agencia_id`, `asesor_id`, `promotor_id`
  - Crédito: `monto_maximo`, `calificacion`, `central_riesgo`
  - Documentación: `imagen_dni`, `observaciones`
- **Relaciones:** `hasMany(Comentario::class)`

#### Crédito (`app/Models/Creditos/Credito/Credito.php`)
- **Tabla:** `credito_registros`
- **Primary Key:** `id`
- **Campos principales:**
  - Asociación: `cliente_id`, `agencia_id`, `asesor_id`, `cobrador_id`, `aprobacion_id`, `grupo_credito_id`
  - Montos: `capital_total`, `capital_pagado`, `interes_total`, `interes_pagado`, `mora_total`, `mora_pagado`, `redondeo_total`, `redondeo_pagado`, `saldo_total`, `acumulado`
  - Estado: `estado_id`, `calificacion`, `cuota_actual`, `cuotas_pendientes`, `cuotas_vencidas`
  - Fechas: `fecha_desembolso`, `fecha_vencimiento`, `fecha_ultimo_pago`, `dias_atraso`
  - Cancelación: `fecha_hora_cancelado`, `caja_cancelado_id`, `documento_cancelado`

#### Cuota (`app/Models/Creditos/Credito/Cuota.php`)
- **Tabla:** `credito_cuotas`
- **Primary Key:** `id`
- **Campos:**
  - `credito_id`, `numero_cuota`, `fecha_vencimiento`, `dias_atraso`
  - Montos: `cuota`, `acumulado`, `capital`, `capital_pagado`, `interes`, `interes_pagado`, `redondeo`, `redondeo_pagado`
  - `estado`

#### Propuesta (`app/Models/Creditos/Credito/Propuesta.php`)
- **Tabla:** `credito_propuestas`
- **Primary Key:** `id`
- **Campos:**
  - Datos básicos: `cliente_id`, `agencia_id`, `negocio_id`, `promotor_id`
  - Parientes/Avales: `pariente_id`, `aval_id`, `pariente_aval_id`
  - Financieros: `monto`, `tasa_interes`, `plazo`, `periodo_pago`, `cuota`
  - Producto: `sector_id`, `producto_id`, `subproducto_id`, `tipo_id`, `garantia_id`, `valor_garantia`
  - Especiales: `es_especial`, `mora_adicional`, `dias_gracia`, `con_dias_gracia`, `prendario`, `prendas`
  - Estado: `estado_id`, `fecha_propuesta`
  - Comentarios: `comentario_propuesta`, `comentario_garantia`, `comentario_desaprobacion`

#### Caja (`app/Models/Creditos/Caja/Caja.php`)
- **Tabla:** `caja_registros`
- **Primary Key:** `id`
- **Campos:**
  - `dni`, `agencia_id`
  - Apertura: `monto_apertura`, `comentario_apertura`, `datos_apertura`
  - Cierre: `monto_cierre_ingresos`, `monto_cierre_egresos`, `comentario_cierre`, `datos_cierre`

#### Transacciones de Caja (`app/Models/Creditos/Caja/Transaccion.php`)
- **Tabla:** `caja_transacciones`
- **Campos:**
  - Identificación: `caja_id`, `credito_id`, `cliente_id`, `usuario_id`
  - Tipo: `tipo` (ingreso/egreso), `categoria_id`, `subcategoria_id`, `comprobante_id`
  - Montos: `monto`, `monto_parcial`, `monto_detraccion`, `monto_deposito`
  - Referencias: `pagador`, `beneficiario`, `banco_id`, `numero_operacion`
  - Estado: `estado`, `aprobado`

### 7.2 Modelo de Datos - GTH (Gestión de Talento Humano)

#### Usuario (`app/Models/Gth/Usuarios/Usuario.php`)
- **Primary Key:** `dni` (no usa ID auto-incremental)
- **Campos:**
  - Autenticación: `dni`, `usuario`, `clave`, `actualizo_clave`
  - Personal: `nombres`, `apellido_paterno`, `apellido_materno`, `sexo`, `fecha_nacimiento`
  - Contacto: `telefono`, `correo_corporativo`, `direccion`
  - Organizacional: `cargo_id`, `agencia_id`, `habilitado`
  - Ubicación: `departamento_id`, `provincia_id`, `distrito_id`
- **Accesor:** `getNombreCompletoAttribute()` - concatena apellido paterno, materno y nombres

#### Modelo de Asistencia (`app/Models/Gth/Asistencias/Marcaje.php`)
- **Campos:** `usuario_dni`, `fecha`, `hora_entrada`, `hora_salida`, `tipo` (entrada/salida)

#### Permiso (`app/Models/Gth/Asistencias/Permiso.php`)
- **Campos:** `usuario_dni`, `tipo`, `fecha_inicio`, `fecha_fin`, `motivo`, `estado`

#### Licencia (`app/Models/Gth/Asistencias/Licencia.php`)
- **Campos:** `usuario_dni`, `categoria_id`, `fecha_inicio`, `fecha_fin`, `dias`, `observaciones`

### 7.3 Modelo de Datos - Logística

#### Activo (`app/Models/Logistica/Activos/Activo.php`)
- **Tabla:** `activo_inventario`
- **Primary Key:** `id`
- **Campos:**
  - Identificación: `agencia_id`, `nombre_id`, `codigo`, `tipo_id`
  - Localización: `responsable_id`, `ubicacion_id`
  - Características: `descripcion`, `cantidad`, `marca`, `modelo`, `placa`, `color`, `caracteristicas`
  - Valor: `fecha_compra`, `valor_compra`, `vida_util`, `porcentaje_depreciacion`, `valor_actual`
  - Estado: `condicion_id`, `estado_id`
  - Control: `fecha_ultimo_inventario`

#### Suministro (`app/Models/Logistica/Suministros/Suministro.php`)
- **Campos:**
  - Identificación: `codigo`, `nombre`, `tipo_id`
  - Inventario: `stock_actual`, `stock_minimo`, `stock_maximo`, `unidad_medida`
  - Proveedor: `proveedor_id`
  - Valuación: `costo_unitario`, `precio_venta`

### 7.4 Modelo de Datos - Créditos Grupal

#### Solicitud Grupal (`app/Models/Creditos/Grupal/Solicitud.php`)
- **Campos:** `agencia_id`, `grupo_id`, `estado`, `monto_total`, `cantidad_integrantes`, `fecha_solicitud`

#### Solicitud Crédito Grupal (`app/Models/Creditos/Grupal/SolicitudCredito.php`)
- **Campos:** `solicitud_id`, `cliente_id`, `monto`, `estado`, `evaluacion`

### 7.5 Modelo de Datos - Inversiones

#### Inversión Meta (`app/Models/Creditos/Inversion/InversionMeta.php`)
- **Campos:** `agencia_id`, `usuario_id`, `monto`, `tasa`, `fecha_inicio`, `fecha_fin`, `estado`

#### Movimiento Inversión (`app/Models/Creditos/Inversion/InversionMetaMovimiento.php`)
- **Campos:** `inversion_meta_id`, `tipo` (depósito/retiro), `monto`, `fecha`, `observaciones`

## 8. Estructura de Rutas del Sistema

### 8.1 Organización de Rutas

El sistema utiliza una estrategia de prefijos para organizar las rutas por módulos:

```
routes/
├── web.php          (Rutas públicas y de autenticación)
├── api.php          (APIs externas)
├── creditos.php     (Módulo de Créditos - ~68KB)
├── general.php      (Configuraciones generales)
├── gth.php          (Gestión de Talento Humano)
└── logistica.php    (Administración de activos y suministros)
```

### 8.2 Detalle de Rutas - Créditos (`routes/creditos.php`)

```
/creditos/
├── clientes/                    (Gestión de clientes)
│   ├── listado_registro         (Listado principal)
│   ├── movimiento               (Historial de movimientos)
│   ├── transferencia            (Transferencias de clientes)
│   ├── prendas                 (Garantías prendarias)
│   └── grupo                    (Grupos de crédito)
├── credito/                     (Gestión de créditos)
│   ├── propuesta                (Propuestas de crédito)
│   ├── aprobacion               (Aprobaciones)
│   ├── evaluacion_financiera    (Evaluación de riesgo)
│   ├── cronograma               (Cronogramas de pago)
│   ├── documentos_financieros   (Documentación)
│   └── carrito                 (Carrito de seguimiento)
├── grupal/                      (Crédito grupal)
│   ├── solicitud                (Solicitudes grupales)
│   ├── aprobacion              (Aprobación grupal)
│   ├── desembolso              (Desembolso grupal)
│   └── documentos              (Documentación grupal)
├── caja/                        (Caja - Operaciones financieras)
│   ├── apertura_caja           (Apertura de caja)
│   ├── transaccion             (Transacciones)
│   ├── desembolso              (Desembolsos)
│   ├── cobranza               (Cobranza)
│   ├── pago_cuota             (Pago de cuotas)
│   ├── pago_mora              (Pago de mora)
│   ├── cierre_caja            (Cierre de caja)
│   ├── cierre_dia             (Cierre del día)
│   └── anticipo_haberes        (Adelanto de haberes)
├── cuenta/                      (Cuentas bancarias)
│   ├── mi_cuenta              (Cuenta del usuario)
│   ├── transferencia          (Transferencias)
│   └── envio                  (Envíos)
├── inversion/                   (Inversiones)
│   ├── inversion_meta         (Metas de inversión)
│   └── inversion_externa      (Inversiones externas)
├── herramientas/                (Utilidades)
│   ├── simulador_creditos     (Simulador de créditos)
│   ├── cambio_agencia         (Cambio de agencia)
│   └── mensajeria             (Sistema de mensajería)
├── reportes/                    (Reportes)
│   ├── cliente               (Reportes de clientes)
│   ├── credito               (Reportes de créditos)
│   ├── caja                  (Reportes de caja)
│   ├── mora                  (Reportes de morosidad)
│   ├── cobranza              (Reportes de cobranza)
│   └── productividad         (Reportes de productividad)
└── mantenimiento/               (Catálogos)
    ├── credito/              (Productos, sectores, tipos)
    └── transacciones/        (Categorías, subcategorías)
```

### 8.3 Detalle de Rutas - GTH (`routes/gth.php`)

```
/gth/
├── asi/                         (Asistencias)
│   ├── validar_asistencia      (Validar asistencia)
│   ├── marcado_asistencia      (Marcaje de entrada/salida)
│   └── validacion_token        (Validar token)
├── usu/                         (Usuarios)
│   ├── usuarios                (Gestión de usuarios)
│   ├── usuarios_gestion        (Administración)
│   ├── horario_asignado       (Horarios)
│   └── cesado                 (Usuarios cesados)
├── falta/                       (Faltas)
├── tardanza/                   (Tardanzas)
├── justificacion/               (Justificaciones)
├── licencia/                    (Licencias)
│   └── categoria               (Categorías de licencia)
├── permiso/                     (Permisos)
├── planilla/                    (Planillas)
│   ├── general                 (Planilla general)
│   ├── usuario                 (Planilla por usuario)
│   ├── vacaciones             (Vacaciones)
│   └── sistema_pensiones      (AFP/ONP)
└── colaborador_mes/            (Programa "Colaborador del Mes")
    ├── evaluacion              (Evaluaciones)
    ├── examen                  (Exámenes)
    ├── pregunta               (Preguntas)
    ├── categoria              (Categorías)
    ├── equipo                  (Equipos)
    └── resultados             (Resultados)
```

### 8.4 Detalle de Rutas - Logística (`routes/logistica.php`)

```
/logistica/
├── activo/                      (Activos fijos)
│   ├── inventario              (Inventario de activos)
│   ├── compra                  (Compras)
│   ├── asignacion              (Asignaciones)
│   ├── envio                   (Envíos)
│   └── venta                   (Ventas de activos)
├── suministro/                  (Suministros)
│   ├── inventario              (Inventario)
│   ├── compra                  (Compras)
│   ├── asignacion              (Asignaciones)
│   ├── envio                   (Envíos)
│   ├── proveedor               (Proveedores)
│   ├── devolucion              (Devoluciones)
│   └── venta                   (Ventas)
└── reporte/                     (Reportes)
```

### 8.5 Rutas API Externas (`routes/api.php`)

El sistema expose APIs para integración externa:

```
/api/
├── acceso/cliente/{dni}/{clave}     (Autenticación de clientes)
├── cliente/
│   ├── datos_personales            (Datos del cliente)
│   ├── historial_creditos         (Historial crediticio)
│   └── datos_pariente_aval        (Parientes y avales)
├── caj/
│   ├── desembolso_externo          (Búsqueda para desembolso)
│   ├── cobranza_externa           (Pagos externos)
│   └── pago_cuotas/listar         (Listar pagos de cuotas)
├── inv/
│   └── meta_externa               (Inversiones externas)
└── cli/
    └── listado_externa             (Búsqueda de clientes)
```

## 9. Arquitectura de Controladores

### 9.1 Estructura de Directorios

```
app/Http/Controllers/
├── Controller.php                 (Clase base)
├── Apis/                         (Controladores de API)
│   ├── ApiClienteController.php
│   ├── ApiExternoController.php
│   ├── ApiSmsController.php
│   ├── ApiSolicitudController.php
│   └── ApiWhatsAppController.php
├── Creditos/
│   ├── Caja/                     (17 controladores)
│   ├── Clientes/                (12 controladores)
│   ├── Credito/                 (8 controladores)
│   ├── Grupal/                  (4 controladores)
│   ├── Cuenta/                  (5 controladores)
│   ├── Inversion/               (3 controladores)
│   ├── Herramientas/            (5 controladores)
│   ├── Mantenimiento/            (10 controladores)
│   └── Reportes/                (21 controladores)
├── General/
│   ├── GeneralController.php
│   ├── AgenciaController.php
│   ├── AreaTrabajoController.php
│   ├── BancoController.php
│   ├── CargosController.php
│   └── PermisosController.php
├── Gth/
│   ├── GthController.php
│   ├── Usuarios/                (4 controladores)
│   ├── Asistencias/            (8 controladores)
│   ├── Planillas/              (5 controladores)
│   ├── ColaboradorMes/         (10 controladores)
│   └── MiPanelGth/              (1 controlador)
└── Logistica/
    ├── Activos/                 (6 controladores)
    └── Suministros/             (8 controladores)
```

### 9.2 Patrón de Implementación

Los controladores siguen un patrón consistente:

```php
class ClientesController extends Controller
{
    public function listado_registro()
    {
        // 1. Verificar sesión
        $x = session()->all();
        if (empty($x['usuario_dni'])) {
            return redirect('/');
        }

        // 2. Verificar permisos
        $band = (new PermisosController)->verificarPermiso(
            $x['usuario_dni'],
            'LISTADO_REGISTRO',
            'CREDITOS_CLIENTES'
        );

        if ($band == 1) {
            // 3. Cargar datos necesarios
            $distritos = Distrito::orderBy('distrito', 'asc')->get();

            // 4. Renderizar vista Inertia
            return Inertia::render('Creditos/Clientes/listado_registro', [
                'distritos' => $distritos
            ]);
        } else {
            // 5. Respuesta de error
            return view('cuatrocientoscuatro')->with('mensajeTitulo', '¡Ups!')
                ->with('mensajeContenido', 'No tienes permitido ver este contenido.');
        }
    }
}
```

## 10. Arquitectura Multi-Tenant del Sistema

### 10.1 Esquema de Base de Datos por Agencia

El sistema implementa una arquitectura multi-tenant donde cada agencia tiene su propia base de datos:

```php
// En las migraciones
Schema::connection('master_' . $agencia_id)->create('nombre_tabla', function (Blueprint $table) {
    // Definición de campos
});
```

### 10.2 Configuración de Conexiones

Las conexiones se configuran dinámicamente usando el patrón:
- Base de datos master: `master_{agencia_id}`
- Permite aislamiento completo de datos por agencia
- La agencia se determina por la sesión del usuario

## 11. Componentes Vue.js y Frontend

### 11.1 Estructura de Vistas

```
resources/js/
├── app.js              (Punto de entrada Vue/Inertia)
├── bootstrap.js       (Configuración de Axios)
├── assets/            (Recursos estáticos)
└── Pages/
    ├── Creditos/      (~70 componentes Vue)
    ├── Gth/           (Gestión de talento humano)
    ├── Logistica/     (Activos y suministros)
    ├── General/       (Configuraciones)
    └── Asistencia/    (Sistema de marcado)
```

### 11.2 Librerías Frontend Utilizadas

| Paquete | Versión | Propósito |
|---------|---------|-----------|
| vue | ^2.7.13 | Framework UI principal |
| @inertiajs/inertia | ^0.11.1 | Adapter Inertia para servidor |
| @inertiajs/inertia-vue | ^0.8.0 | Adapter Inertia para Vue 2 |
| primevue | ^2.10.1 | Componentes UI |
| primeicons | ^6.0.1 | Iconos |
| axios | ^1.1.2 | Cliente HTTP |
| vuelidate | ^0.7.6 | Validación de formularios |
| vue2-datepicker | ^3.10.4 | Selector de fechas |
| luxon | ^3.3.0 | Manipulación de fechas |
| docxtemplater | ^3.22.1 | Generación Word |
| pizzip | ^3.0.6 | Manipulación ZIP |
| file-saver | ^2.0.2 | Guardado de archivos |

## 12. Dependencias Composer (Backend)

### Paquetes Principales

| Paquete | Versión | Propósito |
|---------|---------|-----------|
| laravel/framework | ^9.19 | Framework PHP |
| inertiajs/inertia-laravel | ^0.6.5 | Driver Inertia para Laravel |
| doctrine/dbal | ^3.6 | Manipulación de schema |
| mpdf/mpdf | ^8.1 | Generación de PDF |
| phpoffice/phpspreadsheet | ^1.23 | Excel |
| phpoffice/phpword | ^1.1 | Word |
| guzzlehttp/guzzle | ^7.10 | HTTP Client |
| tightenco/ziggy | ^1.4 | Rutas en JS |
| ifsnop/mysqldump-php | ^2.12 | Backup MySQL |
| ua-parser/uap-php | ^3.9 | Parsing User-Agent |

## 13. Características Técnicas Destacadas

### 13.1 Sistema de Permisos
- Implementado en `app/Http/Controllers/General/PermisosController.php`
- Verifica permisos por: usuario, módulo, submodulo
- Ejemplo de uso:
  ```php
  $band = (new PermisosController)->verificarPermiso(
      $usuario_dni,
      'LISTADO_REGISTRO',
      'CREDITOS_CLIENTES'
  );
  ```

### 13.2 Evaluación Financiera
Modelos especializados en `app/Models/Creditos/Credito/`:
- `Evaluacion_financiera`: Evaluación principal
- `Ef_activo_corriente`: Activos corrientes
- `Ef_activo_no_corriente`: Activos no corrientes
- `Ef_pasivo_corriente`: Pasivos corrientes
- `Ef_flujo_caja`: Flujo de caja
- `Ef_convenio`: Convenios

### 13.3 Generación de Documentos
- **PDF**: mPDF para Boucher, constancias, notificaciones
- **Excel**: PhpSpreadsheet para reportes
- **Word**: PhpWord para documentos template
- **Client-side**: docxtemplater para documentos Word desde Vue

### 13.4 Integraciones Externas
- **SMS**: ApiSmsController para envío de mensajes
- **WhatsApp**: ApiWhatsAppController para notificaciones
- **Equifax**: Reporte de historial crediticio
- **Cobranza Externa**: APis para terceros

## 14. Variables de Entorno Importantes

```
APP_NAME=CredypymeApp
APP_ENV=local|production|testing
APP_KEY=base64:...

# Base de datos
DB_CONNECTION=mysql
DB_HOST=...
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

# Multi-agency
S_TEST_DATABASE=master_db

# Configuraciones de correo, SMS, WhatsApp, etc.
```

## 15. Comandos de Desarrollo Avanzados

```bash
# Regenerar claves de aplicación
php artisan key:generate

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# RegenerarAutoload
composer dump-autoload

# Instalar dependencias de producción
composer install --no-dev --optimize-autoloader

# Build de assets
npm run build

# Desarrollo con hot-reload
npm run dev

# Ejecutar migraciones
php artisan migrate

# Ver rutas disponibles
php artisan route:list

# Ver eventos
php artisan event:list
```

## 16. Notas Adicionales
- **Inertia.js:** El proyecto no usa APIs REST separadas para el frontend de forma tradicional; utiliza Inertia.js, lo que permite retornar componentes de Vue desde los controladores de Laravel (`Inertia::render()`), comportándose como un SPA (Single Page Application) sin la complejidad de manejar el enrutamiento y estado en el cliente.
- **PrimeVue:** Se utiliza la versión 2.x, lo cual es importante notar si se buscan documentaciones o se desean actualizar componentes.
- **Reglas de Negocio:** Las reglas de negocio de evaluación financiera (flujo de caja, activos, pasivos) se encuentran implementadas en los modelos `Evaluacion_financiera`, `Ef_activo_corriente`, `Ef_flujo_caja`, etc., demostrando un análisis de riesgo de crédito estructurado.
- **Arquitectura Multi-Tenant:** El sistema maneja múltiples agencias con bases de datos separadas, lo cual es crítico para el diseño y escalabilidad.
- **Gestión de Sesiones:** Usa sesiones PHP tradicionales (no JWT), validando en cada request el `usuario_dni` en sesión.
- **Primary Keys Custom:** El modelo `Usuario` usa `dni` como primary key en lugar de `id` auto-incremental.