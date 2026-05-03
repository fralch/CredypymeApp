# Módulo GTH (Gestión de Talento Humano)

## Descripción

El módulo **GTH** gestiona todos los procesos relacionados con los recursos humanos de la organización: asistencia, planillas, evaluaciones de desempeño, y administración de empleados.

### Funcionalidades Principales

- Control de asistencia y marcajes
- Gestión de permisos, licencias y justificaciones
- Administración de planillas y remuneraciones
- Control de vacaciones
- Sistema de pensiones (AFP/ONP)
- Programa "Colaborador del Mes" (evaluaciones, exámenes)
- Gestión de usuarios y horarios
- Panel de información del empleado

---

## Estructura de Carpetas

```
modules/Gth/
├── Application/
│   └── UseCases/
│       └── ObtenerContextoGthCasoDeUso.php
├── Domain/
│   ├── Contracts/
│   └── Entities/
├── Infrastructure/
│   ├── DependencyInjection/
│   │   └── GthModuleDependencies.php
│   ├── Persistence/
│   │   ├── Eloquent/
│   │   │   ├── Asistencias/     (8 modelos)
│   │   │   ├── ColaboradorMes/  (10 modelos)
│   │   │   ├── Mantenimiento/   (3 modelos)
│   │   │   ├── Planillas/       (5 modelos)
│   │   │   └── Usuarios/        (3 modelos)
│   │   └── Migrations/
│   └── Providers/
│       └── GthServiceProvider.php
└── Presentation/
    ├── Controllers/          (33 controladores)
    │   ├── Apis/
    │   ├── Asistencias/
    │   ├── ColaboradorMes/
    │   ├── Mantenimiento/
    │   ├── Planillas/
    │   └── Usuarios/
    ├── Routes/
    │   └── web.php
    └── Views/
```

---

## Diagrama de Flujo de Datos

```text
+=========================================================================+
|                           MÓDULO GTH                                    |
+=========================================================================+

[ENTRADA]                    PROCESO                      [SALIDA]
-----------                  -------                      -------

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


FLUJO PRINCIPAL DE GTH:
======================

                    ┌─────────────┐
   EMPLEADO ───────►│ ASISTENCIA  │──────► MARCAJE ──► REPORTE
   (Entrada)        │  (Central)  │        (Ent/Sal)   (Resumen)
                    └─────────────┘
                          │
         ┌────────────────┼────────────────┐
         ▼                ▼                ▼
   +----------+    +------------+   +----------+
   | PERMISOS  │    │ LICENCIAS  │   |FALTAS/   |
   │          │    │            │   │TARDANZAS  |
   +----------+    +------------+   +----------+
         │                │                │
         └────────────────┴────────────────┘
                          │
                          ▼
                   +------------+
                   │  PLANILLA  │
                   │  (Central) │
                   +------------+
                          │
         ┌────────────────┼────────────────┐
         ▼                ▼                ▼
   +----------+    +------------+   +----------+
   |VACACIONES│    │SUELDO/     │   │PENSIÓN   |
   │          │    │REMUNERAC.  │   │(AFP/ONP) │
   +----------+    +------------+   +----------+


RELACIÓN CON OTROS MÓDULOS:
===========================

   GTH
    │
    ├──────┬──────┐
    ▼      ▼      ▼
GENERAL  CREDITOS APLICACION
(Agencia)(Cobrador) (Versiones)
(Cargos)(Desembolso)
(Ubigeo)


FLUJO DE EVALUACIONES (Colaborador del Mes):
=============================================

   ADMIN              EVALUADOR              EMPLEADO
    │                    │                      │
    ▼                    ▼                      │
CREAR ───────────────► ASIGNAR ──────────────► RESPONDER
EVALUACIÓN            EVALUACIÓN              EVALUACIÓN
    │                    │                      │
    ◄────────────────────┴──────────────────────┘
    │              RESULTADOS
    │
    ▼
 REPORTES
```

---

## Controladores por Área

| Área | Cantidad | Descripción |
|------|----------|-------------|
| **Usuarios** | 4 | Gestión de usuarios, horarios, información |
| **Asistencias** | 8 | Marcajes, permisos, licencias, justificaciones, tardanzas, faltas |
| **Planillas** | 5 | Planilla general, vacaciones, sistema de pensiones |
| **ColaboradorMes** | 10 | Evaluaciones, exámenes, preguntas, equipos, resultados |
| **Mantenimiento** | 3 | Categorías de licencia, horarios, cierre día |
| **Apis** | 1 | Validación de asistencia |

---

## Modelos Principales (Eloquent)

### Usuarios
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| Usuario | usuarios | Registro de empleados (usa DNI como PK) |
| UsuarioHorario | usuarios_horarios | Horarios asignados |
| Cesado | usuarios_cesados | Empleados cesados |

### Asistencias
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| Marcaje | asistencia_marcajes | Entradas/salidas |
| Permiso | asistencia_permisos | Permisos varios |
| Licencia | asistencia_licencias | Licencias por categoría |
| Justificacion | asistencia_justificaciones | Justificaciones de ausencias |
| Tardanza | asistencia_tardanzas | Tardanzas registradas |
| Falta | asistencia_faltas | Faltas registradas |
| TokenAsistencia | asistencia_tokens | Tokens para marcaje |

### Planillas
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| PlanillaGeneral | planilla_general | Planilla mensual |
| PlanillaUsuario | planilla_usuario | Detalle por empleado |
| Vacacion | planilla_vacaciones | Registro de vacaciones |
| SistPension | planilla_sist_pension | AFP/ONP |

### ColaboradorMes
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| Evaluacion | colaborador_evaluaciones | Evaluaciones del programa |
| Examen | colaborador_examenes | Exámenes aplicados |
| Pregunta | colaborador_preguntas | Preguntas de evaluación |
| Equipo | colaborador_equipos | Equipos de trabajo |
| Categoria | collaborator_categorias | Categorías del programa |

---

## Rutas Principales

```
/gth/
├── asi/                         (Asistencias)
│   ├── validar_asistencia      → Validar asistencia
│   ├── marcado_asistencia      → Marcaje de entrada/salida
│   └── validacion_token        → Validar token
├── usu/                         (Usuarios)
│   ├── usuarios                → Gestión de usuarios
│   ├── usuarios_gestion        → Administración
│   ├── horario_asignado       → Horarios asignados
│   └── cesado                 → Usuarios cesados
├── falta/                       (Faltas)
├── tardanza/                   (Tardanzas)
├── justificacion/               (Justificaciones)
├── licencia/                    (Licencias)
│   └── categoria               → Categorías de licencia
├── permiso/                     (Permisos)
├── planilla/                    (Planillas)
│   ├── general                 → Planilla general
│   ├── usuario                 → Planilla por usuario
│   ├── vacaciones             → Vacaciones
│   └── sistema_pensiones      → AFP/ONP
└── colaborador_mes/            (Colaborador del Mes)
    ├── evaluacion              → Evaluaciones
    ├── examen                  → Exámenes
    ├── pregunta               → Preguntas
    ├── categoria              → Categorías
    ├── equipo                  → Equipos
    └── resultados             → Resultados
```

---

## APIs

> **Estado**: ⚠️ Implementadas pero mezcladas en `web.php` (no en archivo api.php dedicado)

### Controladores de API
| Controlador | Descripción |
|-------------|-------------|
| ApiSolicitudController | Gestión de solicitudes (licencias, permisos) |

### Endpoints (ubicados en `/gth/sol/`)

```
/gth/sol/
├── POST   guardar                    → Guardar solicitud
├── POST   editar                    → Editar solicitud
├── GET    listar_aprobadores/{dni}  → Listar aprobadores
├── GET    licencia/listar_categorias → Categorías de licencia
├── GET    listar/{tipo}/{fecha_inicio}/{fecha_fin}/{usuario} → Listar
├── GET    licencia/ver/{id}         → Ver licencia
├── GET    permiso/ver/{id}           → Ver permiso
├── PUT    licencia/eliminar/{id}     → Eliminar licencia
├── PUT    permiso/eliminar/{id}     → Eliminar permiso
├── GET    listar_solicitudes/{estado}/{fecha_inicio}/{fecha_fin}/{usuario}
├── POST   aprobar_solicitud         → Aprobar solicitud
├── GET    licencia/listar          → Listar licencias
├── GET    permiso/listar            → Listar permisos
├── GET    permiso/listar_permiso_retorno/{fecha}/{agencia}
├── POST   permiso/verificar_permiso_retorno
├── GET    licencia/listar_feriados  → Listar feriados
└── GET    licencia/listar_usuarios → Listar usuarios
```

**Total: 17 endpoints** (ubicados en `routes/web.php`, no en `api.php`)

### Recomendación
Las APIs deberían moverse a un archivo `api.php` dedicado para mejor organización.

---

## Servicios y Configuración

### Configuración
```php
// config/modules/gth.php
return [
    'name' => 'Gth',
    'enabled' => true,
    'ui_theme' => 'gth-default',
    'features' => [
        'context_endpoint' => true,
        'ui_endpoint' => true,
    ],
];
```

### Endpoints de Contexto (Clean Architecture)
- `GET /clean/gth/context` - Contexto del módulo
- `GET /clean/gth/ui` - Interfaz del módulo

---

## Dependencias con Otros Módulos

| Módulo | Dependencia | Uso |
|--------|-------------|-----|
| **General** | Alta | Agencias, Ubigeo, Cargos |
| **Creditos** | Media | Cobradores, Desembolsos |
| **Aplicacion** | Baja | Versiones de la app |

---

## Notas para Desarrolladores

1. **Usuario con DNI como PK**: El modelo `Usuario` usa `dni` como primary key, no un ID auto-incremental
2. **Tokens de Asistencia**: Sistema de tokens para validar marcajes externos
3. **Sistema de Permisos**: Verifica permisos por módulo y acción
4. **Horarios**: Sistema flexible de horarios por usuario
5. **Planilla**: Integración con sistema de pensiones (AFP/ONP)

---

## Comandos Útiles

```bash
# Ejecutar migraciones del módulo
php artisan migrate:modules --module=gth

# Ver rutas del módulo
php artisan route:list --path=gth
```
