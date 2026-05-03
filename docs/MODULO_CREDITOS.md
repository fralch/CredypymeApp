# Módulo Créditos

## Descripción

El módulo **Créditos** es el núcleo del sistema ERP, encargado de gestionar todo el ciclo de vida de los créditos y operaciones financieras de la empresa de microfinanzas.

### Funcionalidades Principales

- Gestión de clientes y potenciales borrowers
- Originación y evaluación de solicitudes de crédito
- Aprobación de créditos con evaluación de riesgo
- Desembolso de capital
- Cobranza de cuotas y moras
- Gestión de caja (apertura, transacciones, cierre)
- Operaciones bancarias y transferencias
- Reportes y controles de gestión

---

## Estructura de Carpetas

```
modules/Creditos/
├── Application/
│   └── UseCases/
│       └── GetCreditosContextUseCase.php
├── Domain/
│   ├── Contracts/
│   └── Entities/
├── Infrastructure/
│   ├── DependencyInjection/
│   │   └── CreditosModuleDependencies.php
│   ├── Persistence/
│   │   ├── Eloquent/
│   │   │   ├── Caja/          (12 modelos)
│   │   │   ├── Clientes/      (13 modelos)
│   │   │   ├── Credito/       (18 modelos)
│   │   │   ├── Cuenta/        (7 modelos)
│   │   │   ├── Grupal/        (2 modelos)
│   │   │   ├── Herramientas/  (2 modelos)
│   │   │   ├── Inversion/     (2 modelos)
│   │   │   └── Mantenimiento/ (16 modelos)
│   │   └── Migrations/
│   │       ├── Inicial/
│   │       └── Principal/
│   └── Providers/
│       └── CreditosServiceProvider.php
└── Presentation/
    ├── Controllers/          (91 controladores)
    │   ├── Apis/
    │   ├── Caja/
    │   ├── Clientes/
    │   ├── Credito/
    │   ├── Cuenta/
    │   ├── Grupal/
    │   ├── Herramientas/
    │   ├── Inversion/
    │   ├── Mantenimiento/
    │   └── Reportes/
    ├── Routes/
    │   └── web.php (1056 líneas)
    └── Views/
```

---

## Diagrama de Flujo de Datos

```text
+=========================================================================+
|                         MÓDULO CRÉDITOS                                  |
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


FLUJO PRINCIPAL DEL CRÉDITO:
===========================

   CLIENTE ──► PROPUESTA ──► EVALUACIÓN ──► APROBACIÓN ──► DESEMBOLSO ──► COBRANZA
                 │              │              │              │              │
                 ▼              ▼              ▼              ▼              ▼
   [Clientes]  [Credito]    [Credito]     [Credito]      [Caja]        [Caja]
   Controller  Controller   Controller    Controller     Controller    Controller
                 │              │              │              │              │
                 └──────────────┴──────────────┴──────────────┴──────────────┘
                                    │
                                    ▼
                         +--------------------+
                         |  CaJa (CENTRAL)    |
                         +--------------------+
                                    │
         ┌──────────────────────────┼──────────────────────────┐
         ▼                          ▼                          ▼
   +----------+              +------------+            +------------+
   | Transac- │              |Desembolsos |            |  Pagos de  |
   | ciones   │              |            |            |   Cuotas   |
   +----------+              +------------+            +------------+


RELACIÓN CON OTROS MÓDULOS:
===========================

   CRÉDITOS
      │
      ├──────┬──────┬──────┐
      ▼      ▼      ▼      ▼
   GENERAL  GTH   LOGISTICA APLICACION
   (Agencia)(User)(Activos) (Versiones)
   (Ubigeo) (DNI) (Suminist.)
   (Cargos)      (Inventario)
```

---

## Controladores por Área

| Área | Cantidad | Descripción |
|------|----------|-------------|
| **Caja** | 18 | Gestión de caja, transacciones, desembolsos, cobranzas, cierre día |
| **Clientes** | 12 | Gestion de clientes, grupos, avales, negocios, prendas |
| **Credito** | 8 | Propuestas, evaluaciones financieras, aprobación, cronogramas |
| **Mantenimiento** | 10 | Productos, sectores, tipos, garantías, categorías |
| **Reportes** | 21 | Reportes de clientes, créditos, caja, moras, cobranzas |
| **Cuenta** | 5 | Cuentas bancarias, transferencias, movimientos |
| **Grupal** | 4 | Solicitudes grupales, aprobación, desembolso |
| **Inversion** | 3 | Inversiones meta, externas |
| **Herramientas** | 5 | Simulador, cambio agencia, mensajería |
| **Apis** | 2 | APIs externas para clientes y externos |

---

## Modelos Principales (Eloquent)

### Caja
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| Caja | caja_registros | Registro de cajas por agencia |
| Transaccion | caja_transacciones | Transacciones de ingreso/egreso |
| Desembolso | credito_desembolsos | Desembolsos de créditos |
| PagoCuota | credito_pago_cuotas | Pagos de cuotas |
| PagoMora | credito_pago_moras | Pagos de mora |
| AdelantoHaber | caja_adelanto_haberes | Adelantos de haberes |

### Clientes
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| Cliente | cliente_registros | Registro principal de clientes |
| Pariente | cliente_parientes | Parientes del cliente |
| Aval | cliente_avales | Avales/Garantes |
| Negocio | cliente_negocios | Negocios del cliente |
| Grupo | cliente_grupos | Grupos de crédito |

### Crédito
| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| Credito | credito_registros | Crédito activo |
| Propuesta | credito_propuestas | Propuestas de crédito |
| Aprobacion | credito_aprobaciones | Aprobaciones de crédito |
| Cuota | credito_cuotas | Cuotas del crédito |
| Cronograma | credito_cronogramas | Cronograma de pagos |
| Evaluacion_financiera | credito_evaluaciones_financieras | Evaluación de riesgo |

---

## Rutas Principales

```
/creditos/
├── clientes/
│   ├── listado_registro       → Listado principal de clientes
│   ├── movimiento             → Historial de movimientos
│   ├── transferencia          → Transferencias de clientes
│   └── grupo                  → Grupos de crédito
├── credito/
│   ├── propuesta              → Propuestas de crédito
│   ├── aprobacion             → Aprobaciones
│   ├── evaluacion_financiera  → Evaluación de riesgo
│   └── cronograma             → Cronogramas de pago
├── caja/
│   ├── apertura_caja          → Apertura de caja
│   ├── transaccion            → Transacciones
│   ├── desembolso             → Desembolsos
│   ├── cobranza               → Cobranza de cuotas
│   ├── cierre_caja            → Cierre de caja
│   └── cierre_dia             → Cierre del día
├── cuenta/
│   ├── mi_cuenta              → Cuenta del usuario
│   └── transferencia          → Transferencias
├── grupal/
│   ├── solicitud              → Solicitudes grupales
│   ├── aprobacion             → Aprobación grupal
│   └── desembolso             → Desembolso grupal
├── inversion/
│   ├── inversion_meta         → Metas de inversión
│   └── inversion_externa      → Inversiones externas
└── reportes/
    ├── cliente                → Reportes de clientes
    ├── credito                → Reportes de créditos
    ├── caja                   → Reportes de caja
    └── mora                   → Reportes de morosidad
```

---

## APIs Externas

> **Estado**: ✅ Bien implementadas (archivo dedicado `api.php`)

### Controladores de API
| Controlador | Descripción |
|-------------|-------------|
| ApiClienteController | Autenticación y datos de clientes |
| ApiExternoController | Operaciones externas (cobranza, inversiones) |
| ApiSmsController | Envío de SMS |
| ApiWhatsAppController | Notificaciones WhatsApp |

### Endpoints

```
/api/
├── acceso/cliente/{dni}/{clave}     → Autenticación de clientes
├── cliente/
│   ├── datos_personales              → Datos del cliente
│   ├── datos_personales (POST)       → Actualizar datos
│   └── historial_creditos            → Historial crediticio
├── caj/
│   ├── desembolso_externo/buscar     → Buscar para desembolso
│   ├── cobranza_externa/informacion_credito → Info de crédito
│   ├── cobranza_externa/pagar        → Registrar pago
│   ├── cobranza_externa/cancelar     → Cancelar pago
│   ├── pago_cuotas/listar            → Listar pagos de cuotas
│   ├── pago_moras/listar            → Listar pagos de mora
│   └── pago_notificaciones/listar    → Notificaciones
├── inv/
│   ├── meta_externa/buscar          → Buscar inversiones
│   ├── meta_externa/listar_recursos → Listar recursos
│   └── meta_externa/abonar          → Abonar inversión
└── cli/
    ├── listado_externa/buscar        → Buscar cliente
    ├── listado_externa/datos_cliente → Datos del cliente
    ├── listado_externa/verificar     → Verificar cliente
    ├── listado_externa/buscar_parientes_avales → Parientes/Avales
    └── listado_externa/datos_pariente_aval → Datos de aval
```

**Total: 20 endpoints de API**

---

## Servicios y Configuración

### Configuración
```php
// config/modules/creditos.php
return [
    'name' => 'Creditos',
    'enabled' => true,
    'ui_theme' => 'creditos-default',
    'features' => [
        'context_endpoint' => true,
        'ui_endpoint' => true,
    ],
];
```

### Endpoints de Contexto (Clean Architecture)
- `GET /clean/creditos/context` - Contexto del módulo
- `GET /clean/creditos/ui` - Interfaz del módulo

---

## Dependencias con Otros Módulos

| Módulo | Dependencia | Uso |
|--------|------------|-----|
| **General** | Alta | Agencias, Ubigeo, Cargos, Permisos |
| **Gth** | Media | Usuarios (empleados), Horarios |
| **Logistica** | Baja | Activos (garantías prendarias) |
| **Aplicacion** | Baja | Versiones de la app |

---

## Notas para Desarrolladores

1. **Sistema de Permisos**: Cada controlador verifica permisos usando `PermisosController`
2. **Sesión**: Usa `session()->all()` para obtener el usuario actual
3. **Multi-tenant**: Las conexiones a BD dependen de la agencia del usuario
4. **Evaluación Financiera**: Módulos complejos con flujo de caja, activos, pasivos
5. **Generación de Documentos**: Usa mPDF para PDF, PhpSpreadsheet para Excel

---

## Comandos Útiles

```bash
# Ejecutar migraciones del módulo
php artisan migrate:modules --module=creditos

# Ver rutas del módulo
php artisan route:list --path=creditos
```