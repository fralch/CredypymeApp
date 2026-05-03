# Informe comparativo: refactor financiero

## Resumen ejecutivo

La refactorización se enfocó en reducir riesgo operativo y deuda técnica en flujos financieros (movimientos bancarios y multi-agencia), aplicando principios SOLID y prácticas comunes del sector.

## Antes

- Conexiones por agencia replicadas en múltiples puntos (middleware, comandos, seeders, controladores).
- Operaciones bancarias con múltiples escrituras sin transacción coordinada.
- Validaciones críticas delegadas al frontend (riesgo de datos inválidos en backend).
- Trazabilidad/auditoría fragmentada (casos puntuales) y sin soporte de idempotencia.

## Después

### Legibilidad

- Resolución de conexión multi-agencia consolidada en [ResolvedorConexionAgencia.php](file:///d:/CODE/CredypymeApp/app/MultiAgencia/ResolvedorConexionAgencia.php).
- Casos de uso/repositorios renombrados a español de forma consistente en los módulos (ej. [ObtenerContextoCreditosCasoDeUso.php](file:///d:/CODE/CredypymeApp/modules/Creditos/Application/UseCases/ObtenerContextoCreditosCasoDeUso.php)).

### Mantenibilidad

- Menos duplicación: comandos/middleware/seeders usan el mismo resolvedor.
- Validación backend explícita con FormRequest en [RegistrarMovimientoBancarioRequest.php](file:///d:/CODE/CredypymeApp/modules/Creditos/Presentation/Requests/Cuenta/RegistrarMovimientoBancarioRequest.php).
- Coordinación transaccional multi-conexión encapsulada en [CoordinadorTransacciones.php](file:///d:/CODE/CredypymeApp/app/Finanzas/CoordinadorTransacciones.php).

### Escalabilidad y robustez

- Auditoría central para operaciones con tabla `auditoria_operaciones`:
  - Migración: [2026_05_03_000001_create_auditoria_operaciones_table.php](file:///d:/CODE/CredypymeApp/modules/General/Infrastructure/Persistence/Migrations/2026_05_03_000001_create_auditoria_operaciones_table.php)
  - Escritura: [RegistradorAuditoria.php](file:///d:/CODE/CredypymeApp/app/Auditoria/RegistradorAuditoria.php)
- Base para idempotencia: rechazo de duplicados por `Idempotency-Key` en [CuentaBancariaController.php](file:///d:/CODE/CredypymeApp/modules/Creditos/Presentation/Controllers/Cuenta/CuentaBancariaController.php).

## Evidencia (pruebas)

- Se añadieron pruebas unitarias de resolución de conexión:
  - [ResolvedorConexionAgenciaTest.php](file:///d:/CODE/CredypymeApp/tests/Unit/ResolvedorConexionAgenciaTest.php)
- Suite `Unit` ejecutada localmente con éxito (`7 passed`).

## Pendiente recomendado (siguiente iteración)

- Estandarizar validaciones financieras para el resto de endpoints críticos (caja, cuentas, desembolsos).
- Introducir estrategia de consistencia cross-DB para operaciones que escriben en `master_{agencia}` y `master` (outbox/saga).
- Homogeneizar nomenclatura española en controladores legacy sin romper rutas (renombre incremental con tests de regresión).

