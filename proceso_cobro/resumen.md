Idea central
Hoy SIPART ya cobra online (cartera, transferencia, donación/código, remesas SEPA por entidad y pago gestor básico). Lo que falta es el marco financiero de control: fondos garantizados, contabilidad por entidad, verificación por email y gestión centralizada de pagos.

Tres bloques que hay que construir
1. Activar cobros con seguridad (imprescindible)
La entidad ingresa fondos → PARTILOT los valida → se habilita el cobro. Sin esto, no hay garantía de que haya dinero real detrás de cada pago.

2. Mejorar el cobro del usuario (transferencia)

Transferencia con participaciones de varias entidades (donación/código sigue siendo de una sola).
Confirmación por email antes de incluir la solicitud en remesa.
3. Operativa PARTILOT (remesas y presencial)

Panel Super Admin para gestionar pagos (pendientes, exportados, errores, restauraciones).
Dashboard contable para la entidad.
Panel de pago en sede con reglas para evitar cobros duplicados (online vs físico).
Orden recomendado (5 fases → 3 hitos)
Hito	Fases	Qué consigue
MVP seguro
1 + 2 + 3
Cobros solo con fondos validados, transferencia multientidad con email, remesas centralizadas
Contabilidad completa
+ Fase 4
Dashboard entidad, flujo B2B códigos, export donaciones
Cierre físico/digital
+ Fase 5
Panel presencial completo y reglas de digitalización
4 decisiones que el cliente debe cerrar antes de empezar
¿Cuándo se cierra la digitalización? (24h antes del sorteo o al activar pago presencial)
¿Remesa única multientidad o una remesa por entidad?
¿Modo “entidad paga desde su banco” entra ya o más adelante?
¿Panel presencial en web, app gestor o ambos?

/**/

Estimación en horas (solo trabajo, sin coste)
Estimación para 1 desarrollador full-stack familiarizado con el proyecto. Incluye backend, panel web, ajustes app y pruebas básicas. No incluye diseño UX detallado, documentación extensa ni despliegues complejos.

Fase	Contenido	Horas
1
Fondos, estado “Solvente”, bloqueo cobros, cálculo deuda online, notificaciones
80 – 100 h
2
Transferencia multientidad, doble opt-in email, estados solicitud, reserva en cartera
70 – 90 h
3
Panel remesas Super Admin (3 estados, log, restaurar, regenerar XML, emails post-banco)
100 – 130 h
4
Dashboard financiero entidad, B2B códigos, Excel donaciones, alertas
80 – 100 h
5
Panel presencial, cierre digitalización, bloqueos online/presencial, alinear app gestor
100 – 140 h
Totales orientativos
Alcance	Horas
Solo MVP seguro (Fases 1 + 2 + 3)
250 – 320 h
Proyecto completo (Fases 1–5)
430 – 560 h
Desglose aproximado del total completo
Área	% del esfuerzo	Horas (aprox.)
Backend Laravel (APIs, modelos, contabilidad, emails)
~45%
195 – 250 h
Panel web administración / entidad
~35%
150 – 195 h
App móvil (cartera + gestor)
~12%
50 – 70 h
Pruebas e integración
~8%
35 – 45 h
Notas sobre la estimación
La Fase 5 es la más variable: depende de las 4 decisiones del cliente (+20–40 h si hay cambios de criterio a mitad de desarrollo).
Si el modo ENTIDAD legacy entra en Fase 1, sumar ~25–35 h.
Trabajo en paralelo (backend + frontend) puede reducir el calendario, no las horas totales.
Mensaje clave para la reunión: con ~250–320 h se puede tener un cobro online seguro y operable; el resto (~180–240 h) completa contabilidad, presencial y cierre del ciclo físico/digital.

¿Quieres que añada esta versión resumida al inicio del .md como “versión reunión cliente”?