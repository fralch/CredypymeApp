# Módulo Logística

## Descripción

El módulo **Logística** gestiona la administración de activos fijos e inventarios de la organización. Controla la compra, venta, asignación y movimiento de bienes de la empresa.

### Funcionalidades Principales

- Inventario de activos fijos
- Gestión de suministros e inventario
- Compras de activos y suministros
- Asignación a colaboradores y agencias
- Envíos entre agencias
- Ventas de activos y suministros
- Control dedevoluciones
- Historial y reportes
- Mantenimiento de catálogos

---

## Estructura de Carpetas

```
modules/Logistica/
├── Application/
│   └── UseCases/
│       └── ObtenerContextoLogisticaCasoDeUso.php
├── Domain/
│   ├── Contracts/
│   └── Entities/
├── Infrastructure/
│   ├── DependencyInjection/
│   │   └── LogisticaModuleDependencies.php
│   ├── Persistence/
│   │   ├── Eloquent/
│   │   │   ├── Activos/       (16 modelos)
│   │   │   ├── Suministros/   (14 modelos)
│   │   │   └── General/       (4 modelos)
│   │   └── Migrations/
│   └── Providers/
│       └── LogisticaServiceProvider.php
└── Presentation/
    ├── Controllers/          (26 controladores)
    │   ├── Activos/
    │   └── Suministros/
    ├── Routes/
    │   └── web.php
    └── Views/
```

---

## Diagrama de Flujo de Datos

```text
+=========================================================================+
|                         MÓDULO LOGÍSTICA                               |
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


DOS SUBMÓDULOS INDEPENDIENTES:
===============================

                    LOGÍSTICA (Central)
                          │
          ┌───────────────┴───────────────┐
          ▼                               ▼
   +----------------+             +----------------+
   │    ACTIVOS     │             │  SUMINISTROS   │
   │   (Central)    │             │   (Central)    │
   +----------------+             +----------------+
          │                               │
          ▼                               ▼
   +----------+                   +----------+
   │ Fijos/   │                   │Inventario│
   │ Equipos  │                   │ Material │
   +----------+                   +----------+


FLUJO DE ACTIVOS:
=================

  COMPRA ──► INVENTARIO ──► ASIGNACIÓN ──► ENVÍO ──► BAJA/VENTA
    │           │              │            │          │
    ▼           ▼              ▼            ▼          ▼
  [Activos]  [Activos]     [Activos]   [Activos]   [Activos]
  Compra    Inventario   Asignacion    Envio       Venta


FLUJO DE SUMINISTROS:
=====================

  COMPRA ──► ALMACÉN ──► ASIGNACIÓN ──► ENVÍO ──► DEVOLUCIÓN
    │           │           │            │          │
    ▼           ▼           ▼            ▼          ▼
[Suministros] [Almacen] [Asignacion]  [Envio]    [Devolucion]


RELACIÓN CON OTROS MÓDULOS:
===========================

   LOGISTICA
      │
      ├──────┬──────┐
      ▼      ▼      ▼
   GENERAL  GTH   CREDITOS
   (Agencia)(User)(Garantías)
   (Ubigeo) (Activos)
```

---

## Controladores por Submódulo

### Activos (Activos Fijos)
| Área | Cantidad | Descripción |
|------|----------|-------------|
| Inventario | 1 | Inventario de activos |
| Compra | 1 | Compras de activos |
| Asignacion | 1 | Asignación a usuarios |
| Envio | 1 | Envío entre agencias |
| Venta | 1 | Venta de activos |
| Categorias | 1 | Tipos de activos |
| Tipos | 1 | Clasificación |
| Nombres | 1 | Nombres de activos |
| Ubicaciones | 1 | Ubicaciones físicas |
| Records | 1 | Historial/Registros |

### Suministros (Inventario)
| Área | Cantidad | Descripción |
|------|----------|-------------|
| Inventario | 1 | Inventario de suministros |
| Compra | 1 | Compras |
| Almacen | 1 | Gestión de almacén |
| Asignacion | 1 | Asignación |
| Envio | 1 | Envío entre agencias |
| Devolucion | 1 | Devoluciones |
| Venta | 1 | Venta de suministros |
| Tipo | 1 | Tipos de suministro |
| Proveedor | 1 | Gestión de proveedores |
| Medicion | 1 | Unidades de medida |

---

## Modelos Principales (Eloquent)

### Activos
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| Activo | activo_inventario | Registro principal de activos |
| Categoria | activo_categorias | Categorías de activos |
| Tipo | activo_tipos | Tipos de activos |
| Nombre | activo_nombres | Nombres/desripciones |
| Ubicacion | activo_ubicaciones | Ubicaciones físicas |
| Responsable | activo_responsables | Responsables |
| Compra | activo_compras | Compras realizadas |
| Asignacion | activo_asignaciones | Asignaciones a usuarios |
| Envio | activo_envios | Envíos entre agencias |
| Venta | activo_ventas | Ventas de activos |
| Condicion | activo_condiciones | Estado de conservación |
| Estado | activo_estados | Estado del activo |

### Suministros
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| Suministro | suministro_inventario | Registro de suministros |
| Tipo | suministro_tipos | Tipos de suministros |
| Proveedor | suministro_proveedores | Proveedores |
| Medicion | suministro_mediciones | Unidades de medida |
| Compra | suministro_compras | Compras |
| CompraDetalle | suministro_compra_detalles | Detalle de compras |
| Almacen | suministro_almacen | Gestión de almacén |
| Asignacion | suministro_asignaciones | Asignaciones |
| AsignacionDetalle | suministro_asignacion_detalles | Detalle asignación |
| Envio | suministro_envios | Envíos |
| Devolucion | suministro_devoluciones | Devoluciones |
| Venta | suministro_ventas | Ventas |

---

## APIs

> **Estado**: ❌ Sin APIs implementadas

> **Nota**: Este módulo no expose APIs públicas. Todas las funcionalidades se acceden a través de la interfaz web (Inertia/Vue). Las apps móviles no pueden acceder a inventarios de activos/suministros directamente.

**Recomendación**: Deberían implementarse APIs para consulta de inventarios y operaciones básicas.

---

## Rutas Principales

```
/logistica/
├── activo/                      (Activos)
│   ├── inventario              → Inventario de activos
│   ├── compra                  → Compras
│   ├── asignacion              → Asignaciones
│   ├── envio                   → Envíos
│   ├── venta                   → Ventas
│   ├── categorias              → Categorías
│   ├── tipos                   → Tipos
│   ├── nombres                 → Nombres
│   └── ubicaciones             → Ubicaciones
├── suministro/                  (Suministros)
│   ├── inventario              → Inventario
│   ├── compra                  → Compras
│   ├── almacen                 → Almacén
│   ├── asignacion              → Asignaciones
│   ├── envio                   → Envíos
│   ├── devolucion              → Devoluciones
│   ├── venta                   → Ventas
│   ├── tipo                    → Tipos
│   ├── proveedor               → Proveedores
│   └── medicion                → Unidades de medida
└── reporte/                     (Reportes)
    └── general                 → Reporte general
```

---

## Servicios y Configuración

### Configuración
```php
// config/modules/logistica.php
return [
    'name' => 'Logistica',
    'enabled' => true,
    'ui_theme' => 'logistica-default',
    'features' => [
        'context_endpoint' => true,
        'ui_endpoint' => true,
    ],
];
```

### Endpoints de Contexto (Clean Architecture)
- `GET /clean/logistica/context` - Contexto del módulo
- `GET /clean/logistica/ui` - Interfaz del módulo

---

## Dependencias con Otros Módulos

| Módulo | Dependencia | Uso |
|--------|-------------|-----|
| **General** | Alta | Agencias, Ubigeo |
| **Gth** | Media | Asignación a usuarios/colaboradores |
| **Creditos** | Baja | Garantías prendarias |

---

## Características Especiales

### Inventario de Activos
- Control por código único
- Seguimiento de ubicación
- Historial de asignaciones
- Depreciación de activos
- Vida útil y mantenimiento

### Gestión de Suministros
- Control de stock (mínimo, máximo)
- Unidades de medida
- Proveedores preferidos
- Almacén por agencia

### Envíos entre Agencias
- Tracking de envíos
- Confirmación de recepción
- Historial completo

---

## Notas para Desarrolladores

1. **Dos submódulos independientes**: Activos y Suministros funcionan de forma separada
2. **Códigos únicos**: Cada activo tiene código único en el sistema
3. **Estados y condiciones**: Control de estado del activo (nuevo, usado, dado de baja)
4. **Proveedores**: Gestión de proveedores para compras
5. **Historial completo**: Todos los movimientos quedan registrados

---

## Comandos Útiles

```bash
# Ejecutar migraciones del módulo
php artisan migrate:modules --module=logistica

# Ver rutas del módulo
php artisan route:list --path=logistica
```
