# Módulo General

## Descripción

El módulo **General** contiene la configuración base del sistema y funcionalidades transversales que son utilizadas por los demás módulos. Es el módulo de soporte fundamental.

### Funcionalidades Principales

- Gestión de agencias (sucursales)
- Administración de áreas de trabajo
- Gestión de cargos y permisos
- Control de sesiones y usuarios
- Datos geográficos (Ubigeo Perú)
- Configuración de banco
- Feriados
- Permisos por rol y usuario

---

## Estructura de Carpetas

```
modules/General/
├── Application/
│   └── UseCases/
│       └── ObtenerContextoGeneralCasoDeUso.php
├── Domain/
│   ├── Contracts/
│   └── Entities/
├── Infrastructure/
│   ├── DependencyInjection/
│   │   └── GeneralModuleDependencies.php
│   ├── Persistence/
│   │   ├── Eloquent/
│   │   │   ├── Credicheck/    (1 modelo)
│   │   │   └── General/      (23 modelos)
│   │   └── Migrations/
│   └── Providers/
│       └── GeneralServiceProvider.php
└── Presentation/
    ├── Controllers/          (13 controladores)
    │   └── Credicheck/
    ├── Routes/
    │   └── web.php
    └── Views/
```

---

## Diagrama de Flujo de Datos

```text
+=========================================================================+
|                          MÓDULO GENERAL                                 |
+=========================================================================+

[ENTRADA]                    PROCESO                      [SALIDA]
-----------                  -------                       -------

                                    ┌─────────────┐
USUARIO ──────► CONTROLLER ───────►│  LÓGICA DE  │──────► VISTA
(Peticion HTTP)                   │   NEGOCIO   │      (Inertia/Vue)
                                   └─────────────┘
                                         │
                                         ▼
                              ┌──────────────────┐
                              │   PERISTENCIA    │
                              │   (Eloquent ORM) │
                              └──────────────────┘
                                         │
                                         ▼
                              ┌──────────────────┐
                              │  BASE DE DATOS   │
                              │  (MySQL/MariaDB) │
                              └──────────────────┘


FUNCIONALIDADES CENTRALIZADAS:
===============================

  +-------------+      +-------------+      +-------------+
  │   AGENCIAS  │      │   PERMISOS  │      │  UBIGEO     │
  │  (Central)  │      │   (Central) │      │  (Central)  │
  +-------------+      +-------------+      +-------------+
       │                    │                    │
       │                    │                    │
       ▼                    ▼                    ▼
  +----------+        +----------+        +----------+
  │ Creditos │        │   GTH    │        │ Logistica │
  │  Cajas   │        │ Usuarios │        │  Activos  │
  │ Sucursal │        │  Cargos  │        │  Ubicación│
  +----------+        +----------+        +----------+
       │                    │                    │
       └────────────────────┴────────────────────┘
                           │
                           ▼
                  +-----------------+
                  │   SESIONES      │
                  │   (Auth Global) │
                  +-----------------+


FLUJO DE AUTENTICACIÓN:
=======================

   USUARIO ──► LOGIN ──► SESION ──► PERMISOS ──► MENÚ
                │          │            │          │
                ▼          ▼            ▼          ▼
           [General]   [General]   [General]    [Todos]
                           │
                           ▼
                    +-------------+
                    │  AGENCIA    │
                    │ (Multitenant)│
                    +-------------+
```

---

## Controladores

| Área | Cantidad | Descripción |
|------|----------|-------------|
| **Credicheck** | 13 | Gestión de agencias, áreas, cargos, permisos, usuarios, ubigeo, bancos, feriados |

---

## Modelos Principales (Eloquent)

### Configuración General
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| Agencia | general_agencias | Sucursales de la empresa |
| AreaTrabajo | general_areas_trabajo | Áreas organizacionales |
| Cargo | general_cargos | Cargos/Puestos |
| Permiso | general_permisos | Permisos del sistema |
| Cargos_permiso | general_cargos_permisos | Relación cargos-permisos |
| Usuarios_permiso | general_usuarios_permisos | Relación usuarios-permisos |
| Sesion | sesiones | Control de sesiones activas |

### Ubigeo (Perú)
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| Departamento | ubigeo_peru_departments | Departamentos |
| Provincia | ubigeo_peru_provinces | Provincias |
| Distrito | ubigeo_peru_districts | Distritos |

### Otros
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| Banco | general_bancos | Bancos disponibles |
| Feriado | general_feriados | Feriados del año |
| Cierre | general_cierres | Control de cierres |
| Ciiu | general_ciiu | Códigos CIIU |
| User | users | Usuarios del sistema (auth) |

---

## APIs

> **Estado**: ❌ Limitado (solo 1 endpoint para promociones)

### Endpoints

```
/general/man/cre/promociones/listar_recursos_api
```

**Total: 1 endpoint** - Solo para listar recursos de promociones

---

## Rutas Principales

```
/general/
├── agencias/
│   ├── listado              → Listado de agencias
│   ├── crear                → Crear agencia
│   ├── editar               → Editar agencia
│   └── eliminar             → Eliminar agencia
├── areas/
│   ├── listado              → Áreas de trabajo
│   └── gestion              → Administración
├── cargos/
│   ├── listado              → Listado de cargos
│   ├── gestion              → Administración
│   └── permisos             → Permisos por cargo
├── permisos/
│   ├── usuarios             → Permisos de usuarios
│   ├── cargos               → Permisos de cargos
│   └── gestion              → Administración
├── usuarios/
│   ├── listado              → Listado de usuarios
│   ├── crear                → Crear usuario
│   └── editar               → Editar usuario
├── ubigeo/
│   ├── departamento         → Departamentos
│   ├── provincia            → Provincias
│   └── distrito             → Distritos
├── bancos/
│   └── listado              → Bancos
├── feriados/
│   └── listado              → Feriados
└── sesiones/
    └── usuarios_online      → Usuarios conectados
```

---

## Servicios y Configuración

### Configuración
```php
// config/modules/general.php
return [
    'name' => 'General',
    'enabled' => true,
    'ui_theme' => 'general-default',
    'features' => [
        'context_endpoint' => true,
        'ui_endpoint' => true,
    ],
];
```

### Endpoints de Contexto (Clean Architecture)
- `GET /clean/general/context` - Contexto del módulo
- `GET /clean/general/ui` - Interfaz del módulo

---

## Dependencias con Otros Módulos

| Módulo | Dependencia | Uso |
|--------|-------------|-----|
| **Creditos** | Alta | Agencias, Cargos, Permisos |
| **Gth** | Alta | Usuarios, Cargos, Agencias |
| **Logistica** | Alta | Agencias, Ubigeo |
| **Aplicacion** | Baja | Versiones |

---

## Características Especiales

### Sistema de Permisos
El sistema de permisos funciona a tres niveles:
1. **Usuario**: Permisos individuales por DNI
2. **Cargo**: Permisos heredados por cargo
3. **Módulo**: Verificación por nombre de permiso + contexto

```php
// Ejemplo de verificación
$band = (new PermisosController)->verificarPermiso(
    $usuario_dni,
    'LISTADO_REGISTRO',    // Permiso
    'CREDITOS_CLIENTES'    // Contexto/Módulo
);
```

### Arquitectura Multi-tenant
El módulo soporta múltiples agencias con bases de datos separadas:
- Conexión dinámica según agencia del usuario
- Aislamiento completo de datos por agencia
- Configuración en `.env` con sufijos `_1`, `_2`, `_3`, etc.

---

## Notas para Desarrolladores

1. **Sesión global**: El sistema usa sesiones PHP tradicionales, no JWT
2. **Multitenant**: Cada agencia tiene su propia DB configurada en `.env`
3. **Ubigeo**: Datos geográficos de Perú pre-cargados
4. **Cierre de día**: Control de cierres por agencia
5. **Permisos centralizados**: Cualquier módulo puede usar `PermisosController`

---

## Comandos Útiles

```bash
# Ejecutar migraciones del módulo
php artisan migrate:modules --module=general

# Ver rutas del módulo
php artisan route:list --path=general
```
