# Módulo Aplicación

## Descripción

El módulo **Aplicación** gestiona las versiones y actualizaciones de las aplicaciones móviles connectadas al sistema. Es un módulo ligero de soporte para el control de versiones de las apps clientes.

### Funcionalidades Principales

- Registro de versiones de aplicaciones
- Control de versiones activas
- Gestión de actualizaciones

---

## Estructura de Carpetas

```
modules/Aplicacion/
├── Infrastructure/
│   ├── Persistence/
│   │   ├── Eloquent/
│   │   │   └── VersionesAplicacion.php
│   │   └── Migrations/
│   └── Providers/
│       └── AplicacionServiceProvider.php
└── Presentation/
    └── (Sin controladores - acceso via API)
```

---

## Diagrama de Flujo de Datos

```text
+=========================================================================+
|                        MÓDULO APLICACIÓN                                |
+=========================================================================+

[ENTRADA]                    PROCESO                      [SALIDA]
-----------                  -------                       -------

                                    ┌─────────────┐
APP MÓVIL ────► API ──────────────►│  LÓGICA DE  │──────► JSON
(Peticion HTTP)                   │   NEGOCIO   │
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


FUNCIONALIDAD:
==============

   APP MÓVIL                        SISTEMA
       │                              │
       │───► SOLICITA VERSIÓN ───────►│
       │         (API Call)           │
       │                              │
       ◄─── RESPUESTA ────────────────│
       │   (Versión actual)          │
       │                              │
       ▼                              ▼
  ACTUALIZA                   REGISTRA VERSIÓN
  (si hay nueva)              (Admin)


MÓDULO DE APOYO:
================

   APLICACIÓN
       │
       ▼
   +-------------+     +-------------+
   │ VERSIONES   │────► CRÉDITOS   │
   │  (Central)  │     │   (APIs)    │
   +-------------+     +-------------+
       │                    │
       │                    ▼
       │              +-------------+
       │              │    GTH      │
       │              │   (APIs)    │
       │              +-------------+
       │
       └──────────────► TODOS LOS MÓDULOS
                       (Referencia cruzada)
```

---

## Modelo Principal (Eloquent)

| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| VersionesAplicacion | app_versiones | Registro de versiones de apps |

### Estructura del Modelo
- `id` - ID único
- `app_name` - Nombre de la aplicación
- `version` - Número de versión
- `version_code` - Código de versión interno
- `url_descarga` - URL de descarga
- `notas` - Notas de actualización
- `es_obligatoria` - Si la actualización es obligatoria
- `estado` - Activo/Inactivo
- `fecha_lanzamiento` - Fecha de lanzamiento

---

## APIs

> **Estado**: ❌ No implementadas (API no expuesta)

El modelo existe pero no hay rutas de API registradas para este módulo. Las apps móviles no pueden consultar versiones directamente.

---

## Dependencias con Otros Módulos

| Módulo | Dependencia | Uso |
|--------|-------------|-----|
| **General** | Baja | Agencia base |
| **Creditos** | Media | APIs para clientes |
| **Gth** | Baja | APIs para empleados |
| **Logistica** | Baja | APIs |

---

## Notas para Desarrolladores

1. **Módulo ligero**: Solo maneja versiones, sin lógica compleja
2. **Acceso via API**: No tiene interfaz web propia
3. **Multi-app**: Soporta múltiples aplicaciones (Android, iOS)
4. **Obligatoriedad**: Campo para indicar si la actualización es obligatoria

---

## Comandos Útiles

```bash
# Ejecutar migraciones del módulo
php artisan migrate:modules --module=aplicacion

# Ver rutas del módulo
php artisan route:list --path=api/app
```