# Proceso de cobro SIPART — Estado actual y propuesta de implementación

**Documento para revisión con el cliente**  
**Fecha:** 19 de mayo de 2026  
**Versión:** 1.0  

---

## 1. Objetivo de este documento

Este informe resume:

1. **Qué funcionalidades de cobro existen hoy** en la plataforma SIPART (web Laravel + app móvil).
2. **Qué solicita el diseño funcional** descrito en los documentos de la carpeta `proceso_cobro/`.
3. **Las diferencias principales** entre ambos enfoques.
4. **Una propuesta de implementación por fases**, para acordar prioridades, alcance y calendario con el cliente.

El objetivo es facilitar una conversación clara sobre qué ya está hecho, qué falta por construir y qué decisiones de negocio hay que cerrar antes de desarrollar.

---

## 2. Resumen ejecutivo

### Lo que ya funciona hoy

La plataforma dispone de una **base operativa de cobro online** que permite a los usuarios:

- Digitalizar participaciones físicas y tenerlas en su cartera digital.
- Consultar premios tras el escrutinio.
- Solicitar cobro por **transferencia bancaria** (introduciendo IBAN y datos personales).
- Repartir el importe entre **donación** y **código de recarga** (solo participaciones de la misma entidad).
- Caducidad de participaciones en cartera a los **3 meses** desde la fecha del sorteo.

Desde el panel de configuración, las entidades pueden **generar órdenes de pago SEPA** a partir de las solicitudes de cobro recibidas. En la app del gestor existe un **módulo básico de pago presencial** de participaciones con premio.

### Lo que describe el diseño del cliente

El documento funcional plantea un **sistema financiero integral** en el que:

- Ningún cobro online se procesa hasta que la entidad haya ingresado los fondos en PARTILOT y un Super Admin los haya validado (entidad **“Solvente”**).
- Las transferencias pueden agrupar participaciones de **varias entidades** en una sola solicitud del usuario.
- La transferencia bancaria requiere **confirmación por email** (doble opt-in) antes de pasar a remesa.
- Existe **contabilidad en tiempo real** por entidad (saldos, transferencias, códigos, donaciones, balance proyectado).
- Un **panel central de remesas** con tres estados (No verificadas / Pendientes / Gestionadas), trazabilidad completa y posibilidad de restaurar pagos fallidos.
- Un **panel de pago presencial** con reglas estrictas para evitar cobros duplicados (online vs sede física).

### Conclusión general

**Aproximadamente un 30–40% del flujo descrito está cubierto** con la implementación actual. La base técnica (cartera, cobros, donaciones, códigos prepago, remesas SEPA por entidad) es un buen punto de partida, pero faltan los mecanismos de **garantía de fondos**, **contabilidad**, **verificación por email**, **gestión centralizada de remesas** y **panel presencial completo** que el diseño funcional define como pilares del sistema.

---

## 3. Estado actual — Detalle por área

### 3.1. Cartera digital y digitalización

| Funcionalidad | Estado | Notas |
|---|---|---|
| Vincular participación física al usuario (QR / referencia) | ✅ Implementado | La participación queda en la cartera del usuario |
| Ver importe de premio tras escrutinio | ✅ Implementado | Cruce con acta de premios del sorteo |
| Caducidad 3 meses post-sorteo | ✅ Implementado | Participaciones no cobradas caducan |
| Cierre de digitalización antes del sorteo o al activar pago presencial | ❌ No implementado | Hoy se puede digitalizar sin restricción de plazo |
| Bloqueo de cobro si la entidad no ha ingresado fondos | ❌ No implementado | No existe concepto de entidad “Solvente” |

### 3.2. Selección de participaciones y reglas de agrupación

| Funcionalidad | Estado | Notas |
|---|---|---|
| Selección múltiple en cartera | ✅ Implementado | App: pantalla “Cobrar / Gestionar” |
| Donación y código: solo misma entidad | ✅ Implementado | Backend y app validan monoentidad |
| Transferencia: varias entidades en una solicitud | ❌ No implementado | Hoy transferencia también exige misma entidad |
| Reserva de participaciones durante la selección | ❌ No implementado | No hay bloqueo temporal anti-doble cobro |
| Mensajes de restricción en pantalla | ⚠️ Parcial | Existen avisos básicos; faltan los del diseño funcional |

### 3.3. Cobro por transferencia bancaria

| Funcionalidad | Estado | Notas |
|---|---|---|
| Usuario introduce IBAN y datos personales | ✅ Implementado | Validación formato IBAN y NIF español |
| Registro de solicitud de cobro | ✅ Implementado | Modelo `ParticipationCollection` con participaciones vinculadas |
| Email de confirmación al registrar | ✅ Implementado | Email informativo: “solicitud registrada” |
| Email de doble opt-in (confirmar / cancelar enlace) | ❌ No implementado | El cobro se registra de forma inmediata |
| Estado “No verificada” hasta confirmar email | ❌ No implementado | No existe este estado intermedio |
| Email “Transferencia emitida” tras confirmación bancaria | ❌ No implementado | No hay botón admin para avisar al usuario post-banco |
| Restauración por error de IBAN con devolución de saldo | ⚠️ Parcial | Se puede eliminar la solicitud y liberar participaciones; sin contabilidad ni log detallado |

### 3.4. Donación y código de recarga

| Funcionalidad | Estado | Notas |
|---|---|---|
| Reparto donación / código con selector | ✅ Implementado | App con barra deslizante o 100% donación si no hay API |
| Operación irreversible (donación + código) | ✅ Implementado | Marca `donated_at`; no reversible |
| Generación de código vía API prepago | ✅ Implementado | Configurable por administración (propia o PARTILOT) |
| Código visible en pantalla y por email | ⚠️ Parcial | Generación y email básico; consulta permanente en cartera limitada |
| Solicitud automática de transferencia a la Administración de Lotería | ❌ No implementado | No hay flujo B2B por valor del código |
| Datos fiscales para certificado de donación | ⚠️ Parcial | Se recogen datos; falta exportación Excel para la entidad |
| Email informativo a la administración por nuevo código | ❌ No implementado | — |

### 3.5. Panel de entidad y pago presencial

| Funcionalidad | Estado | Notas |
|---|---|---|
| Dashboard financiero (saldos, transferencias, donaciones, balance) | ❌ No implementado | No hay panel de métricas descrito |
| Modo de pago entidad: PARTILOT vs ENTIDAD (legacy) | ❌ No implementado | No hay selector en ficha de entidad |
| Panel presencial web (individual + arco) | ❌ No implementado | — |
| Panel presencial en app gestor | ⚠️ Parcial | Existe flujo básico: validar participaciones con premio y registrar pago |
| Bloqueo de pago presencial si participación ya digitalizada/cobrada online | ❌ No implementado | No hay validación de custodia descrita |
| Estado `PAID_OFFLINE` y descuento de saldo propio de entidad | ❌ No implementado | Se usa estado `pagada` genérico |
| Auditoría de sesiones de pago presencial | ⚠️ Parcial | Log de actividad básico (`paid`) |

### 3.6. Provisión de fondos y activación del sistema

| Funcionalidad | Estado | Notas |
|---|---|---|
| Entidad ingresa fondos de premios en PARTILOT | ❌ No implementado | Proceso manual externo; sin registro en sistema |
| Super Admin valida ingreso y activa cobros | ❌ No implementado | — |
| Estado “Solvente” por entidad | ❌ No implementado | — |
| Cálculo automático de “Deuda Online” tras escrutinio | ❌ No implementado | — |
| Notificaciones push/email al activar cobros (por tipo participación) | ❌ No implementado | Infraestructura de notificaciones existe; falta el disparo automático |

### 3.7. Gestión de remesas (Super Admin)

| Funcionalidad | Estado | Notas |
|---|---|---|
| Pestaña “No verificadas” (sin confirmar email) | ❌ No implementado | — |
| Pestaña “Pendientes” (listas para exportar 34.14) | ⚠️ Parcial | “Pagos pendientes” por entidad en configuración |
| Pestaña “Gestionadas” (histórico + restauración) | ⚠️ Parcial | Órdenes SEPA con XML; sin flujo completo de restauración |
| Tabla con log de observaciones por solicitud | ❌ No implementado | — |
| Exportación Norma 34.14 centralizada | ⚠️ Parcial | SEPA por entidad, no panel central multientidad |
| Restaurar solicitud → devolver saldo por entidad + liberar participaciones | ⚠️ Parcial | Solo liberación de participaciones al borrar solicitud |
| Botón “Confirmar pago” → email masivo a usuarios del lote | ❌ No implementado | — |
| Verificación de solvencia antes de generar remesa | ❌ No implementado | — |

**Leyenda:** ✅ Implementado · ⚠️ Parcial · ❌ No implementado

---

## 4. Diferencias clave respecto al diseño funcional

### 4.1. Sin garantía de fondos previa al cobro

**Hoy:** Un usuario puede solicitar cobro en cuanto su participación tiene premio, independientemente de si la entidad ha transferido fondos a PARTILOT.

**Cliente solicita:** Bloqueo total hasta validación manual del ingreso y estado “Solvente”.

**Impacto:** Es el cambio estructural más importante. Afecta a cartera, APIs de cobro, panel entidad y remesas.

---

### 4.2. Transferencia multientidad vs monoentidad

**Hoy:** Tanto transferencia como donación/código exigen participaciones de la **misma entidad**.

**Cliente solicita:**
- **Transferencia:** puede agrupar participaciones de **varias entidades**.
- **Donación/código:** solo **una entidad** (correcto en la implementación actual).

**Impacto:** Cambio en backend, app y lógica de remesas/restauración (desglose por entidad en cada solicitud).

---

### 4.3. Doble opt-in en transferencias

**Hoy:** Al pulsar “Enviar”, la solicitud queda registrada y las participaciones marcadas como cobradas. El email es solo informativo.

**Cliente solicita:** Flujo en dos pasos:
1. Usuario introduce IBAN → email con enlace de confirmación.
2. Solo tras confirmar → la solicitud pasa a “Pendientes de gestionar”.

**Impacto:** Nuevos estados, tokens de verificación, pestaña “No verificadas” y posible expiración de solicitudes no confirmadas.

---

### 4.4. Contabilidad de caja por entidad

**Hoy:** No hay registro de saldos (fondos ingresados, pagado, donado, disponible, balance proyectado).

**Cliente solicita:** Panel en tiempo real con métricas contables y descuento automático al generar remesas, códigos o donaciones.

**Impacto:** Nuevos campos en base de datos, movimientos contables y dashboard entidad.

---

### 4.5. Panel de remesas centralizado

**Hoy:** Cada entidad gestiona sus órdenes SEPA desde su configuración. No hay vista Super Admin unificada con tres pestañas, log por fila ni restauración contable multientidad.

**Cliente solicita:** Centro de operaciones PARTILOT con trazabilidad completa, edición de lotes, regeneración de XML y emails post-confirmación bancaria.

**Impacto:** Nuevo módulo web de administración con lógica de estados y auditoría.

---

### 4.6. Pago presencial y cierre de digitalización

**Hoy:** Pago gestor básico en app; digitalización sin plazo de cierre; no hay bloqueo cruzado online/presencial.

**Cliente solicita:** Panel presencial completo (web y/o app), bloqueo de participaciones ya gestionadas online, estado `PAID_OFFLINE`, y reglas de cierre de digitalización.

**Impacto:** Depende de acordar **cuándo** se cierra la digitalización (ver sección 6).

---

## 5. Propuesta de implementación por fases

La propuesta divide el trabajo en **cinco fases** ordenadas por dependencias técnicas y valor de negocio. Cada fase es entregable y conversable por separado.

---

### Fase 1 — Fundamentos financieros y activación

**Objetivo:** Que ningún cobro online se procese sin fondos garantizados.

**Entregables:**
- Campos en entidad/administración: fondos ingresados, saldo disponible, estado (pendiente / solvente), modo de pago (PARTILOT / ENTIDAD legacy).
- Panel Super Admin: registrar y validar ingreso de fondos por entidad.
- Bloqueo en APIs de cobro y donación si la entidad no está solvente.
- Cálculo de “Deuda Online” tras escrutinio (premios digitales + digitalizadas según regla acordada).
- Notificaciones automáticas al activar cobros (segmentadas: digital nativa, física digitalizada).

**Beneficio para el cliente:** Seguridad financiera y control antes de abrir cobros a usuarios.

**Estimación orientativa:** Base imprescindible para el resto de fases.

---

### Fase 2 — Flujo de usuario: transferencia multientidad y verificación

**Objetivo:** Alinear el cobro por transferencia con el diseño funcional.

**Entregables:**
- Permitir transferencia con participaciones de varias entidades (mantener monoentidad en donación/código).
- Desglose por entidad en cada solicitud (participación → entidad → importe).
- Email de doble opt-in con enlace confirmar / cancelar.
- Estados de solicitud: No verificada → Pendiente → Gestionada.
- Reserva temporal de participaciones durante la selección en cartera.

**Beneficio para el cliente:** Experiencia de usuario según especificación y menos errores de IBAN.

---

### Fase 3 — Panel central de remesas (Super Admin)

**Objetivo:** Operativa bancaria centralizada con trazabilidad y corrección de errores.

**Entregables:**
- Tres pestañas: No verificadas / Pendientes / Gestionadas.
- Tabla unificada con log de observaciones por solicitud.
- Generación de remesa Norma 34.14 (lotes con ID único).
- Restaurar solicitud individual: liberar participaciones, devolver saldo por entidad, registrar en log.
- Regenerar XML tras eliminar líneas erróneas del lote.
- Botón “Confirmar pago” → email masivo “Transferencia emitida”.

**Beneficio para el cliente:** Control operativo total y subsanación de devoluciones bancarias sin descuadres.

---

### Fase 4 — Donación/código B2B y dashboard entidad

**Objetivo:** Completar la contabilidad y la trazabilidad del reparto híbrido.

**Entregables:**
- Dashboard financiero entidad: total premios, fondos ingresados, pagado por transferencia, códigos, donaciones, pagado presencial, pendiente, balance proyectado.
- Solicitud automática de transferencia a administración por valor del código generado.
- Export Excel de donaciones para certificados fiscales.
- Email a administración al generar código.
- Alerta de solvencia baja (umbral configurable).

**Beneficio para el cliente:** Visibilidad contable en tiempo real para entidades y administraciones.

---

### Fase 5 — Panel presencial y reglas de digitalización

**Objetivo:** Cerrar el ciclo físico/digital sin riesgo de cobros duplicados.

**Entregables:**
- Regla de cierre de digitalización (según decisión del cliente — ver sección 6).
- Panel presencial web (individual + arco) con validaciones de custodia.
- Estado `PAID_OFFLINE`, descuento de saldo propio de entidad.
- Bloqueo LOPD: no revelar identidad del usuario que cobró online.
- Habilitación del panel presencial bajo petición explícita de la entidad.
- Mejora del módulo gestor en app (alineado con reglas web).

**Beneficio para el cliente:** Liquidez predecible para entidades y cierre contable a 3 meses sin sorpresas.

---

## 6. Decisiones pendientes de acordar con el cliente

Antes de iniciar el desarrollo, conviene cerrar estas preguntas:

### 6.1. Cierre de digitalización

Los documentos funcionales plantean dos criterios distintos:

| Opción A | Cierre automático X horas antes del sorteo (ej. 24h) |
| Opción B | Cierre cuando la entidad activa el panel de pago presencial |

**Recomendación:** Definir una única regla oficial. Opción A es más predecible para usuarios; Opción B da más flexibilidad a la entidad pero complica el cálculo de “Deuda Online”.

---

### 6.2. Remesas multientidad

Cuando un usuario agrupa participaciones de varias entidades en una transferencia:

| Opción A | Una remesa central PARTILOT que paga al usuario (desglose contable interno por entidad) |
| Opción B | Una solicitud usuario multientidad, pero remesas SEPA separadas por entidad |

**Recomendación:** Aclarar quién ejecuta el pago bancario y cómo se cuadra con el ingreso de fondos por entidad.

---

### 6.3. Modo ENTIDAD (legacy)

¿Entra en la primera entrega o se pospone?

- **PARTILOT (predeterminado):** entidad ingresa fondos; PARTILOT gestiona remesas.
- **ENTIDAD (legacy):** entidad descarga ficheros 34.14 desde su panel y paga desde su banco; no requiere ingreso en PARTILOT.

**Recomendación:** Posponer modo ENTIDAD si no hay entidades que lo vayan a usar de inmediato.

---

### 6.4. Panel presencial: web, app o ambos

¿El panel de pago en sede debe existir en panel web, en app del gestor, o en ambos con la misma lógica?

**Estado actual:** App gestor con flujo básico. El diseño funcional describe un panel web completo.

---

### 6.5. Caducidad de premios no cobrados

Documento menciona 3 meses (alineado con implementación actual) y posibilidad de ampliar a 4–5 meses. Confirmar plazo definitivo y redacción en condiciones legales (destino del importe caducado).

---

## 7. Matriz resumen: documento cliente vs estado actual

| Área | Cliente solicita | Estado actual | Gap |
|---|---|---|---|
| Provisión fondos + Solvente | Bloqueo hasta ingreso validado | Sin control de fondos | Alto |
| Notificaciones al activar | Push/email segmentadas | No automatizadas | Alto |
| Transferencia multientidad | Sí | No (monoentidad) | Alto |
| Donación/código monoentidad | Sí | Sí | — |
| Doble opt-in transferencia | Sí | No | Alto |
| Contabilidad entidad | Dashboard completo | No existe | Alto |
| Remesas 3 pestañas | Super Admin central | SEPA por entidad | Alto |
| Restauración multientidad | Con saldos y log | Parcial | Medio |
| Código prepago | Por administración | Implementado reciente | Bajo |
| Donación irreversible | Sí | Sí | — |
| Caducidad 3 meses | Sí | Sí | — |
| Panel presencial | Web completo + reglas | App básica | Alto |
| Cierre digitalización | Sí | No | Alto |
| Modo PARTILOT / ENTIDAD | Selector en entidad | No | Medio |

---

## 8. Próximos pasos sugeridos

1. **Revisión de este documento** con el cliente y validación del resumen ejecutivo.
2. **Respuesta a las decisiones** de la sección 6 (especialmente cierre de digitalización y remesas multientidad).
3. **Priorización de fases** según urgencia de negocio (recomendación: Fase 1 → Fase 2 → Fase 3 como mínimo viable seguro).
4. **Estimación de esfuerzo y calendario** por fase una vez acordado el alcance.
5. **Inicio de desarrollo** en las ramas ya creadas (web + app).

---

## 9. Anexo — Referencia técnica (para el equipo)

Componentes actuales relevantes (no necesario para la reunión con cliente, útil para desarrollo):

| Componente | Ubicación / descripción |
|---|---|
| API cobro transferencia | `POST /api/wallet/cobro` → `ParticipationController::apiRegistrarCobro()` |
| API donación + código | `POST /api/wallet/donacion` → `apiRegistrarDonacion()` |
| Modelo solicitud cobro | `ParticipationCollection` + `ParticipationCollectionItem` |
| Remesas SEPA por entidad | `ConfigurationController::crearSepa()`, vistas `ordenes-pago-entidades` |
| Códigos prepago | `PrepagoCodigosService` (config por administración) |
| Pago presencial gestor | `apiValidateParticipationsForPayment`, `apiRegisterPayment` |
| Caducidad cartera | `ParticipationWalletValidityService` (3 meses post-sorteo) |
| App usuario cobro | `cobrar-gestionar.page.ts` |
| App gestor pago | `gestor-pago.page.ts` |

---

*Documento generado a partir del análisis comparativo entre la carpeta `proceso_cobro/` y el código en rama de desarrollo. Para dudas técnicas o ajustes de alcance, contactar con el equipo de desarrollo.*
