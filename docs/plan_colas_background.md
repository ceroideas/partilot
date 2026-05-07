# Plan cerrado de implementación: colas en background

## Objetivo

Mover operaciones masivas a segundo plano sin bloquear UI, garantizando:

- ejecución robusta aunque el navegador se cierre;
- trazabilidad completa por tarea;
- progreso/estado visible en panel;
- notificación al completar o fallar;
- idempotencia y control de concurrencia.

## Alcance inicial

Operaciones incluidas en este plan:

- creación de participaciones;
- asignación de participaciones;
- devoluciones.

## Arquitectura propuesta

- Driver de cola: `database` (rápido de activar para primera fase).
- Worker persistente con `queue:work` (supervisor/servicio en producción).
- Orquestación común en tabla `background_tasks`.
- Jobs por dominio:
  - `ProcessParticipationCreationTask`
  - `ProcessParticipationAssignmentTask`
  - `ProcessDevolutionTask`
- Procesamiento por lotes (`chunking`) para volumen alto.
- Lock por recurso (`resource_key`) para evitar carreras.

## Modelo de datos

### Nueva tabla: `background_tasks`

Campos:

- `id`
- `uuid` (público para frontend)
- `type` (`participation_creation|participation_assignment|devolution`)
- `status` (`pending|running|completed|failed|cancelled`)
- `requested_by_user_id`
- `entity_id` nullable
- `administration_id` nullable
- `set_id` nullable
- `resource_key` nullable (ej. `set:123`)
- `payload` JSON (input normalizado)
- `progress_total` int default `0`
- `progress_done` int default `0`
- `progress_percent` tinyint default `0`
- `result_summary` JSON nullable
- `error_message` text nullable
- `started_at`, `finished_at`
- `created_at`, `updated_at`

Índices:

- `(status, created_at)`
- `(requested_by_user_id, created_at)`
- `(type, status)`
- `uuid` unique
- opcional: `(resource_key, status)` para detectar tareas activas por recurso.

## Contrato backend (API)

### 1) Crear tarea

- `POST /api/background-tasks`

Body:

- `type`
- `payload` (según operación)
- contexto (`set_id`, `entity_id`, etc.)

Respuesta:

- `task_uuid`
- `status: pending`
- `poll_url`

### 2) Consultar estado

- `GET /api/background-tasks/{uuid}`

Respuesta:

- estado actual;
- progreso (`done/total/%`);
- resumen o error final.

### 3) Listar recientes del usuario

- `GET /api/background-tasks?mine=1&limit=20`

### 4) Cancelar (fase opcional)

- `POST /api/background-tasks/{uuid}/cancel`

## Flujo por operación

### A) Creación de participaciones

1. Validación sincrónica mínima.
2. Crear `background_task` + `dispatch` de job.
3. Job:
   - marca `running`;
   - revalida set/permisos;
   - crea en `chunks` (500-1000, ajustable);
   - actualiza progreso por chunk;
   - recalcula métricas y marca `completed`.
4. En error: marca `failed` + `error_message`.

### B) Asignación de participaciones

Input típico:

- vendedor/rango/set/reserva.

Ejecución:

- job en chunks por IDs/referencias;
- lock transaccional de filas afectadas por chunk;
- validación de estado asignable en tiempo real;
- aplicación de cambios bulk;
- resumen final (`asignadas`, `omitidas`, `conflictos`).

### C) Devoluciones

Ejecución:

- job encapsula reglas actuales (incluyendo premio especial);
- chunk por participaciones seleccionadas;
- validación de estado al aplicar (evitar stale updates);
- resumen final (`devueltas`, `liquidadas`, `no aplicadas`).

## Idempotencia y concurrencia

- `task_hash` opcional (`type + payload normalizado + recurso`) para reutilizar tarea activa equivalente.
- `resource_key` con bloqueo lógico: no permitir 2 tareas activas sobre mismo set/recurso crítico.
- En jobs: transacciones por chunk + condiciones defensivas (`where status in (...)`) para no pisar cambios concurrentes.

## UI y notificaciones

### Primera etapa (simple y robusta)

- Polling cada `5-10s` mientras haya tareas `pending/running`.
- Al cambiar a `completed/failed`:
  - guardar marca local para no repetir popup;
  - disparar PNotify con resultado.

### Futuro

- Reemplazar polling por push (`websocket`/eventos).

## Operación e infraestructura

Configuración:

- `.env`: `QUEUE_CONNECTION=database`

Comandos base:

- `php artisan queue:table`
- `php artisan migrate`
- `php artisan queue:work --queue=default --tries=3 --timeout=120`

Producción:

- supervisor con auto-restart;
- logs separados para worker;
- monitoreo de `failed_jobs`.

## Plan de implementación por fases

### Fase 1 - Base común

- migración `background_tasks`;
- modelo + `BackgroundTaskService`;
- endpoints crear/consultar/listar;
- worker database operativo;
- polling UI + PNotify al finalizar.

### Fase 2 - Creación de participaciones

- job `ProcessParticipationCreationTask`;
- mover lógica masiva al job + chunking;
- progreso y resumen final.

### Fase 3 - Asignación

- job `ProcessParticipationAssignmentTask`;
- locks + validación concurrente;
- resumen de conflictos/omitidas.

### Fase 4 - Devoluciones

- job `ProcessDevolutionTask`;
- encapsular reglas actuales + trazabilidad completa.

### Fase 5 - Estabilidad

- pruebas de carga/fallos;
- ajustes de chunk size / timeout / retries;
- checklist operativa final.

## Pruebas de estabilidad (criterios)

Escenarios:

- volumen: `10k / 50k / 100k` participaciones;
- concurrencia: `2-3` tareas simultáneas sobre mismo recurso;
- fallo intermedio con retry;
- reinicio de worker durante ejecución.

Validaciones:

- integridad de conteos por estado;
- cero duplicados;
- sumatorios coherentes por operación.

KPIs:

- tiempo total por tarea;
- throughput por chunk;
- tasa de fallo/retry;
- impacto en tiempo de respuesta web.

## Notas de arranque

- Documento preparado como punto de partida.
- No iniciar implementación hasta confirmar ajustes pendientes en `main`.
