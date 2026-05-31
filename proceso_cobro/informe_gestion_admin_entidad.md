# Gestión Administración–Entidad (cuotas y diseño) — Estado actual y propuesta

**Documento para revisión con el cliente**  
**Fecha:** 19 de mayo de 2026  
**Versión:** 1.0  
**Referencia funcional:** `gestion_admin_entidad.md`

---

## 1. Objetivo de este documento

Este informe resume el módulo descrito en `gestion_admin_entidad.md`: **quién paga la cuota de gestión PARTILOT y el coste de diseño/impresión** al crear un set, **cuándo** se cobra y **cuándo** se generan los PDF con códigos QR.

Complementa el informe del **proceso de cobro de premios** (`informe_estado_actual_y_propuesta.md`). Son dos áreas distintas:

| Área | Cuándo ocurre | Qué regula |
|------|---------------|------------|
| **Este documento** | Antes del sorteo, al crear/diseñar el set | Cuota plataforma + diseño/impresión |
| **Proceso de cobro de premios** | Después del escrutinio | Cobro de premios por usuarios |

---

## 2. Resumen ejecutivo

### Qué pide el cliente

Un sistema donde **nunca se generen los PDF** hasta que la **cuota de gestión PARTILOT** esté cobrada, con dos switches por entidad (solo visibles para la Administración) que deciden:

- **Switch 1:** ¿Quién paga la cuota de gestión? (Administración o Entidad)
- **Switch 2:** ¿Quién paga diseño e impresión en imprenta PARTILOT? (Administración o Entidad)

El **momento del cobro** depende de quién diseña:

- Si diseña la **Administración** → cobro de cuota al **aprobar** la entidad el diseño, antes de PDF.
- Si diseña la **Entidad** → cobro de cuota **antes de entrar al editor**.

Además: medios de pago (TPV con tarjeta tokenizada; remesa periódica para administraciones), factura en cada cobro y cuota de gestión **una sola vez por set**.

### Qué hay hoy

SIPART ya dispone de:

- Editor de diseño y generación de PDF con QR.
- Presupuestos de imprenta (`PrintConfiguration`) y cobro Stripe en envíos a imprenta.
- Flujo “Diseño e impresión PARTILOT” con pago online.
- Bloqueo de edición cuando hay participaciones asignadas o vendidas.

**No existe** el motor comercial del documento: switches, gate de pago antes de PDF, aprobación entidad → cobro, facturación automática ni remesa periódica para administraciones.

### Conclusión general

Aproximadamente **un 25–35% del flujo está cubierto** a nivel técnico (diseño, PDF, Stripe puntual, imprenta). Falta construir el **sistema de reglas de facturación del set**, que es el eje del documento del cliente.

---

## 3. Mensajes clave para la reunión con el cliente

Estos puntos conviene transmitirlos de forma directa:

### Mensaje 1 — Es un módulo distinto al cobro de premios

> “El documento de gestión admin–entidad no habla de premios ni cartera de usuarios. Regula **cuánto paga cada actor por usar la plataforma y la imprenta al crear el set**, antes de que exista ningún sorteo.”

### Mensaje 2 — La regla de oro cambia el comportamiento actual

> “Hoy los PDF se pueden generar sin haber cobrado la cuota de gestión. El diseño del cliente exige que **el pago de la cuota sea el candado** que desbloquea la generación de PDF. Ese es el cambio estructural más importante.”

### Mensaje 3 — Partimos con buena base técnica

> “No hay que construir diseño, PDF ni pasarela desde cero. Ya tenemos editor, QR, presupuestos de imprenta y Stripe. El trabajo principal es **reglas de negocio, estados del set y quién paga cuándo**.”

### Mensaje 4 — Cuatro combinaciones, un solo motor

> “Los dos switches generan cuatro escenarios (admin/entidad paga gestión × admin/entidad paga impresión). Hay que implementar **un motor de reglas único** que, según configuración y quién diseña, decida pagador, momento de cobro y bloqueos de pantalla.”

### Mensaje 5 — Hay decisiones de negocio pendientes

> “Antes de desarrollar conviene cerrar: **importe de la cuota de gestión** (fija por set, por participación, configurable por administración), **facturación** (PDF interno vs integración contable) y **alcance de la remesa** para administraciones en la primera entrega.”

### Mensaje 6 — Alcance recomendable por fases

> “Se puede entregar un **MVP en ~115–155 h** (switches, cuota, bloqueo PDF, cobro Stripe al pagador correcto). El documento completo (aprobación entidad, tarjetas guardadas, remesa admin, facturas) ronda **265–375 h**.”

---

## 4. Qué pide el documento — Desglose funcional

### 4.1. Configuración base (dos switches)

| Elemento | Descripción |
|----------|-------------|
| **Switch 1 — Cuota gestión PARTILOT** | OFF (defecto): paga Administración. ON: paga Entidad. |
| **Switch 2 — Diseño e impresión** | OFF (defecto): paga Administración. ON: paya Entidad. |
| **Visibilidad** | Solo Administración ve y configura. Entidad no ve los switches. |
| **Modal al guardar** | Confirmación de configuración; opción “no volver a mostrar”. |
| **Independencia** | Los dos switches se combinan libremente (4 casos). |

### 4.2. Medios de pago

| Actor | Medio |
|-------|--------|
| **Entidad** | Siempre TPV con tarjeta tokenizada. |
| **Administración (defecto)** | TPV puntual con tarjeta. |
| **Administración (remesa)** | Si Super Admin activa remesa: facturación periódica (mensual/quincenal) de conceptos acumulados; requiere IBAN en ficha; **no puede pagar por tarjeta** en ese modo. |

### 4.3. Regla de generación de PDF

> **Nunca** se generan PDF hasta cobrada la cuota de gestión.

| Quién diseña | Cuándo se cobra la cuota |
|--------------|--------------------------|
| Administración | Tras OK de la Entidad al diseño, **antes** de PDF. |
| Entidad | **Antes** de acceder al editor. |

Tras PDF generados: diseño bloqueado si hay participaciones asignadas o vendidas (ya parcialmente implementado).

### 4.4. Matriz de casos (resumen del documento)

| Sw1 (gestión) | Sw2 (impresión) | Quién paga cuota | Quién paga impresión |
|---------------|-----------------|------------------|----------------------|
| OFF | OFF | Administración | Administración |
| ON | OFF | Entidad | Administración |
| ON | ON | Entidad | Entidad |
| OFF | ON | Administración | Entidad |

**Impresión PARTILOT:** cobro justo **antes** de enviar el trabajo a la imprenta (al pagador que marque Switch 2).

**Cuota de gestión:** se cobra **una sola vez por set**. Si el diseño se edita y se vuelve a aprobar, **no** se recobra la cuota.

### 4.5. Flujo de aprobación (cuando diseña la Administración)

1. Administración diseña y guarda.
2. Sistema envía diseño a Entidad para aprobación.
3. Entidad da OK → se dispara cobro de cuota (al pagador del Switch 1).
4. Sin pago confirmado → PDF bloqueados.
5. Si Entidad da OK pero no paga → Admin ve set “pendiente pago cuota por entidad”.
6. Si Admin edita diseño → se anula OK anterior; Entidad debe re-aprobar; **no** se recobra cuota si ya fue pagada.

### 4.6. Comunicaciones y facturación

- Factura automática en cada cobro (dos líneas si gestión + impresión en un mismo cargo).
- Notificación panel + email con enlace a factura.
- Botón “Enviar a imprenta” solo si hay imprenta configurada y diseño guardado.

---

## 5. Estado actual en SIPART

### 5.1. Lo que ya funciona (reutilizable)

| Componente | Estado | Relación con el documento |
|------------|--------|---------------------------|
| Editor de diseño + PDF + QR | ✅ | Base del flujo; falta **bloquear** hasta pago cuota |
| Bloqueo diseño por participaciones asignadas/vendidas | ✅ | Cubre nota técnica post-PDF; no sustituye gate de pago |
| `PrintConfiguration` (tarifas diseño, participación, tacos) | ✅ | Sirve para **coste impresión**; hay que separar **cuota gestión PARTILOT** |
| Stripe + `PrintOrder` (pago, estados, auditoría, webhook) | ✅ | Patrón reutilizable; hoy es pago **puntual**, no tarjeta guardada |
| Flujo “Diseño e impresión PARTILOT” (`DesignExternalInvitation`) | ✅ | Similar a imprenta externa; sin switches ni pagador variable |
| “Enviar a imprenta” (`sendToPrint`) | ✅ | Cobro Stripe antes de enviar; encaja con “cobrar impresión antes de imprenta” |
| Panel órdenes imprenta | ⚠️ Parcial | Gestión interna; falta rol imprenta y alineación completa con spec |
| Permiso `permission_design` (gestores entidad) | ✅ | Controla acceso al diseño; **no** equivale a switches de facturación |
| IBAN entidad (`billing_iban`) / admin (`account`) | ⚠️ Campos | Existen; sin lógica de remesa ni cobro vinculado |
| Emails / notificaciones | ✅ | Infraestructura reutilizable |

**Leyenda:** ✅ Implementado · ⚠️ Parcial

### 5.2. Lo que no existe hoy

| Requisito del documento | Gap |
|-------------------------|-----|
| Switch 1 y Switch 2 en ficha entidad | No implementado |
| Modal confirmación al guardar entidad | No implementado |
| Cuota gestión PARTILOT (concepto separado de tarifa imprenta) | No implementado |
| PDF bloqueados hasta pagar cuota gestión | No — PDF se generan sin gate de pago |
| Cobro cuota antes del editor (entidad diseña) | No implementado |
| Cobro cuota tras OK entidad (admin diseña) | No implementado |
| Flujo aprobación diseño entidad → cobro | No implementado |
| Estado “pendiente pago cuota por entidad” | No implementado |
| Cuota gestión cobrada una vez por set (memoria al re-editar) | No implementado |
| Cobro al pagador correcto según switches | Stripe cobra al usuario activo; sin routing por config |
| Tarjeta tokenizada (entidad/admin) | PaymentIntent por operación; sin guardar método de pago |
| Remesa periódica administración (Super Admin) | No implementado |
| Factura automática con líneas desglosadas | No implementado |
| Email + enlace factura tras cada cobro | No implementado |

---

## 6. Diferencias clave respecto al diseño funcional

### 6.1. PDF sin candado de pago

**Hoy:** Cualquier usuario con permiso puede exportar PDF tras diseñar.  
**Cliente solicita:** PDF solo tras cobro confirmado de cuota de gestión.  
**Impacto:** Cambio en `DesignController`, jobs PDF y UX del set (estados visibles).

### 6.2. Un solo concepto de “precio diseño” mezclado

**Hoy:** `PrintConfiguration.price_design` se usa en presupuesto imprenta externa.  
**Cliente solicita:** **Cuota gestión PARTILOT** (uso plataforma) separada de **coste diseño/impresión imprenta**.  
**Impacto:** Nuevo campo/tarifa de cuota gestión y refactor de presupuestos.

### 6.3. Sin flujo Administración diseña → Entidad aprueba → Cobro

**Hoy:** No hay envío de diseño a entidad para OK previo a PDF ni cobro condicionado.  
**Cliente solicita:** Flujo completo con re-aprobación si admin edita (sin recobrar gestión).  
**Impacto:** Nuevos estados de set/diseño y pantallas entidad.

### 6.4. Pagos siempre al usuario que opera

**Hoy:** Quien pulsa “pagar” en Stripe es quien paga, sin mirar switches.  
**Cliente solicita:** Switches determinan si paga entidad o administración en cada concepto.  
**Impacto:** Motor de pagador + checkout dirigido al actor correcto.

### 6.5. Sin facturación ni remesa admin

**Hoy:** Cobro Stripe registrado en pedido; sin factura ni acumulado periódico.  
**Cliente solicita:** Factura en cada cobro; remesa opcional para administraciones.  
**Impacto:** Módulo facturación + job facturación periódica (fase avanzada).

---

## 7. Propuesta de implementación por bloques

Orden sugerido por dependencias técnicas.

---

### Bloque A — Configuración comercial

**Objetivo:** Definir reglas por entidad y administración.

**Entregables:**
- Campos en entidad: `entity_pays_management_fee`, `entity_pays_print_fee` (switches).
- Modal confirmación al guardar entidad (con “no volver a mostrar”).
- Ficha administración: modalidad remesa activable solo por Super Admin (mensual/quincenal, IBAN obligatorio).

**Beneficio:** Base para todos los flujos de cobro sin ambigüedad.

**Horas orientativas:** 25 – 35 h

---

### Bloque B — Cuota gestión y bloqueo de PDF

**Objetivo:** Implementar la regla de oro del documento.

**Entregables:**
- Concepto “cuota gestión PARTILOT” (precio configurable).
- Estado por set: cuota pendiente / pagada, pagador, timestamp.
- Bloqueo de exportación PDF y jobs hasta `management_fee_paid`.
- Pantallas de set con estado visible (“pendiente pago cuota”, “listo para PDF”).

**Beneficio:** Control real sobre generación de códigos QR.

**Horas orientativas:** 50 – 70 h

---

### Bloque C — Aprobación entidad (admin diseña)

**Objetivo:** Flujo OK entidad → cobro → PDF.

**Entregables:**
- Estados: borrador → pendiente aprobación → aprobado → pendiente pago → PDF disponibles.
- Pantalla entidad: ver diseño, aprobar/rechazar.
- Re-aprobación si admin edita; no recobrar cuota si ya pagada.
- Vista admin: “pendiente pago cuota por entidad”.

**Beneficio:** Alineación con casos Sw1 ON (entidad paga gestión) y flujos mixtos.

**Horas orientativas:** 40 – 55 h

---

### Bloque D — Pagos (TPV y remesa administración)

**Objetivo:** Cobrar al pagador correcto con medios del documento.

**Entregables:**
- Checkout Stripe al pagador según switches y momento del flujo.
- Tarjeta tokenizada (Stripe Customer + Payment Methods) para entidad y admin en modo TPV.
- Cola de cargos pendientes para admin en modalidad remesa.
- Job/cron facturación periódica admin.

**Beneficio:** Medios de pago completos según spec.

**Horas orientativas:** 70 – 100 h

---

### Bloque E — Facturación

**Objetivo:** Factura y comunicación tras cada cobro.

**Entregables:**
- Modelo factura, numeración, PDF.
- Desglose en dos líneas (gestión + impresión) cuando aplique.
- Email + notificación panel con enlace a factura.

**Beneficio:** Trazabilidad fiscal y comunicación al cliente.

**Horas orientativas:** 35 – 50 h

---

### Bloque F — Alineación flujos imprenta existentes

**Objetivo:** Unificar envío a imprenta con switches y cobros.

**Entregables:**
- Refactor presupuestos: separar cuota gestión vs coste impresión.
- Cobro impresión al pagador del Switch 2 antes de enviar a imprenta.
- Corregir caminos donde hoy el pago no es obligatorio o está hardcodeado.
- Completar gaps conocidos (checkout en todos los caminos, bloqueos post-envío).

**Beneficio:** Coherencia entre diseño propio, imprenta PARTILOT y configuración comercial.

**Horas orientativas:** 45 – 65 h

---

## 8. Estimación en horas

Estimación para **1 desarrollador full-stack** familiarizado con el proyecto. Incluye backend, panel web y pruebas básicas. No incluye integración contable externa ni diseño UX detallado.

| Alcance | Bloques incluidos | Horas |
|---------|-------------------|------:|
| **MVP comercial** | A + B + D (solo Stripe puntual, sin remesa admin) + F parcial | **115 – 155 h** |
| **Con aprobación entidad** | MVP + Bloque C | **155 – 210 h** |
| **Documento completo** | A + B + C + D + E + F | **265 – 375 h** |

### Desglose por bloque

| Bloque | Contenido | Horas |
|--------|-----------|------:|
| A | Switches entidad + remesa admin (config) | 25 – 35 |
| B | Cuota gestión + bloqueo PDF | 50 – 70 |
| C | Aprobación entidad → cobro | 40 – 55 |
| D | TPV tokenizado + remesa admin | 70 – 100 |
| E | Facturación automática | 35 – 50 |
| F | Alineación imprenta | 45 – 65 |

### Notas sobre la estimación

- **Bloque D** es el más variable (tokenización Stripe, cumplimiento PCI, remesa periódica).
- Si la **cuota de gestión** depende de reglas complejas (por participación, por administración, descuentos), sumar **15–25 h** al Bloque B.
- **Integración contabilidad externa** (no incluida): **+40–80 h** según proveedor.
- Trabajo en paralelo reduce calendario, no horas totales.

---

## 9. Decisiones pendientes de acordar con el cliente

| # | Pregunta | Opciones / impacto |
|---|----------|-------------------|
| 1 | **Importe cuota gestión PARTILOT** | ¿Fija por set? ¿Por participación? ¿Configurable por administración? |
| 2 | **Primera entrega: remesa admin** | ¿Entra en MVP o se pospone (solo TPV puntual para todos)? |
| 3 | **Facturación** | ¿PDF interno PARTILOT suficiente o integración con software contable? |
| 4 | **Tarjeta tokenizada** | ¿Obligatoria en MVP o basta PaymentIntent por operación al inicio? |
| 5 | **Relación con imprenta** | ¿Completar gaps de panel imprenta en la misma fase o después? |

---

## 10. Matriz resumen: documento vs estado actual

| Área | Cliente solicita | Estado actual | Gap |
|------|------------------|---------------|-----|
| Switches entidad | 2 switches independientes | No existen | Alto |
| Bloqueo PDF hasta cuota | Regla de oro | PDF sin gate de pago | Alto |
| Cuota gestión vs impresión | Conceptos separados | Tarifa diseño mezclada | Alto |
| Aprobación entidad | Admin diseña → OK → cobro | No existe | Alto |
| Cobro según pagador (switches) | Routing comercial | Paga quien opera | Alto |
| TPV tokenizado | Entidad siempre | Pago puntual Stripe | Medio |
| Remesa administración | Super Admin activa | No existe | Alto |
| Factura automática | Cada cobro | No existe | Alto |
| Envío imprenta con cobro previo | Sí | Parcial (Stripe en algunos caminos) | Medio |
| Bloqueo diseño post-venta | Sí | Implementado | — |
| Cuota una vez por set | Sí | No | Alto |

---

## 11. Relación con el proceso de cobro de premios

| | Gestión admin–entidad | Cobro de premios |
|---|----------------------|------------------|
| **Momento** | Creación del set / diseño | Tras escrutinio |
| **Actores** | Administración, Entidad, PARTILOT | Usuario final, Entidad, Super Admin |
| **Dinero** | Cuota plataforma + imprenta | Premios, transferencias, donaciones |
| **Dependencia** | Independiente | Requiere fondos en PARTILOT (informe aparte) |

Pueden planificarse en ** paralelo** en desarrollo, pero conviene **priorizar** con el cliente cuál es más urgente para el negocio.

---

## 12. Próximos pasos sugeridos

1. Validar con el cliente el **resumen ejecutivo** y los **6 mensajes clave** (sección 3).
2. Cerrar las **5 decisiones** de la sección 9 (especialmente importe cuota y alcance MVP).
3. Elegir alcance: **MVP (~115–155 h)** vs **completo (~265–375 h)**.
4. Acordar si este módulo va **antes, después o en paralelo** al MVP de cobro de premios (~250–320 h).
5. Iniciar desarrollo por **Bloque A → B** como mínimo imprescindible.

---

## 13. Anexo — Referencia técnica (equipo)

| Componente | Ubicación / descripción |
|------------|-------------------------|
| Editor y PDF | `DesignController`, jobs `GenerateParticipationPdfJob`, etc. |
| Gate PDF actual | `authorizeDesignPdfExport()` — solo permisos, sin pago |
| Bloqueo diseño | `getSetDesignLockContext()` — participaciones asignadas/vendidas |
| Tarifas imprenta | `PrintConfiguration` (`price_design`, participación, tacos) |
| Cobro imprenta | `PrintOrder`, Stripe en `sendToPrint` / `DesignExternalInvitation` |
| Permiso diseño entidad | `permission_design` en gestores |
| Gaps imprenta documentados | `falta_imprenta.md`, `docs/impresion.md` |
| Ficha entidad | `Entity` — `billing_iban`; sin switches comerciales |
| Ficha administración | `Administration` — `account` (IBAN); sin remesa |

---

*Documento generado a partir del análisis de `gestion_admin_entidad.md` contrastado con el código en rama de desarrollo. Complementa `informe_estado_actual_y_propuesta.md` (cobro de premios).*
