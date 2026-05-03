# Arquitectura y prácticas financieras (SOLID)

## Objetivo

Elevar mantenibilidad y robustez de operaciones financieras aplicando:

- Separación estricta de responsabilidades (Presentation/Application/Domain/Infrastructure)
- Transacciones consistentes (por conexión y coordinadas)
- Auditoría trazable de operaciones
- Validaciones del lado servidor
- Preparación para idempotencia y reintentos

## Buenas prácticas relevantes (sector financiero)

- **Idempotencia en APIs financieras**: permitir reintentos sin duplicar cargos/movimientos mediante una clave idempotente por operación y usuario. (https://www.atifalam.com/library/system-design/examples/payments-design-stages/, https://www.zigpoll.com/content/what-are-the-best-practices-for-designing-a-scalable-api-backend-to-handle-high-concurrency-and-realtime-data-processing-in-a-fintech-application)
- **Auditoría / trazabilidad**: toda operación financiera debe dejar una huella (quién, qué, cuándo, desde dónde, entrada/salida). En modelos avanzados se recomienda un log append-only o ledger. (https://www.atifalam.com/library/system-design/examples/payments-design-stages/)
- **Atomicidad e integración**: cuando se requiere publicar eventos o integrar con otros sistemas, el patrón Transactional Outbox reduce la pérdida de eventos frente a fallos. (https://learn.microsoft.com/en-us/azure/architecture/best-practices/transactional-outbox-cosmos)
- **Consistencia y concurrencia**: los saldos se deben actualizar con transacciones y reglas claras para prevenir estados intermedios e inconsistencias.

## Decisiones aplicadas en este proyecto

### 1) Multi-agencia: resolución centralizada de conexiones

Se centralizó la obtención/configuración de conexiones por agencia:

- Resolvedor: [ResolvedorConexionAgencia.php](file:///d:/CODE/CredypymeApp/app/MultiAgencia/ResolvedorConexionAgencia.php)
- Uso: middleware y comandos CLI (migraciones/seeders)

En `local/development/testing` se fuerza a `master`/`records` para simplificar ejecución local.

### 2) Auditoría central de operaciones

Se agregó una tabla de auditoría transaccional (en `master`) con soporte de clave idempotente:

- Migración: [2026_05_03_000001_create_auditoria_operaciones_table.php](file:///d:/CODE/CredypymeApp/modules/General/Infrastructure/Persistence/Migrations/2026_05_03_000001_create_auditoria_operaciones_table.php)
- Servicio: [RegistradorAuditoria.php](file:///d:/CODE/CredypymeApp/app/Auditoria/RegistradorAuditoria.php)

Campos clave:

- `modulo`, `codigo_operacion`
- `usuario_id`, `agencia_id`
- `idempotencia_clave` (único por operación/usuario)
- `datos_entrada`, `datos_salida` (JSON)
- `ip`, `user_agent`, `request_id`

### 3) Transacciones coordinadas (multi-conexión)

Se incorporó un coordinador simple para iniciar/confirmar/rollback sobre múltiples conexiones:

- [CoordinadorTransacciones.php](file:///d:/CODE/CredypymeApp/app/Finanzas/CoordinadorTransacciones.php)

Nota: esto mejora consistencia en condiciones normales, pero no reemplaza un enfoque distribuido (outbox/saga) ante fallos entre commits.

### 4) Validación financiera en backend

Se incorporó validación de servidor para movimientos bancarios:

- Request: [RegistrarMovimientoBancarioRequest.php](file:///d:/CODE/CredypymeApp/modules/Creditos/Presentation/Requests/Cuenta/RegistrarMovimientoBancarioRequest.php)
- Controlador: [CuentaBancariaController.php](file:///d:/CODE/CredypymeApp/modules/Creditos/Presentation/Controllers/Cuenta/CuentaBancariaController.php)

Reglas mínimas:

- `monto > 0`
- estructura JSON válida para el detalle
- restricción de agencia consistente para reducir escenarios no atómicos
- rechazo de reintentos duplicados con `Idempotency-Key` (409)

## Convenciones de nomenclatura en español

Estándar aplicado en capas Clean:

- Clases: `PascalCase` en español (ej. `RepositorioContextoCreditosDesdeConfig`)
- Métodos: `camelCase` en español (ej. `obtenerContexto`, `ejecutar`)
- Archivos: mismo nombre que la clase

Ejemplo aplicado en los “casos de uso de contexto” de todos los módulos:

- `Get*ContextUseCase` → `ObtenerContexto*CasoDeUso`
- `*ContextRepository` → `RepositorioContexto*`

