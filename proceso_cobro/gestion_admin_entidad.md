Contexto y regla general
El momento en que la plataforma genera los archivos PDF (que contienen los códigos QR
únicos de las participaciones) es el punto crítico del proceso. A partir de ese instante se pierde
el control sobre los códigos, porque el PDF ya puede imprimirse.
REGLA GENERAL: el sistema nunca genera los PDF hasta que la cuota de gestión ha sido
cobrada. El momento exacto en que se cobra depende de quién diseña:
• Si diseña la Administración: el cobro de la cuota de gestión se produce cuando el diseño está
terminado y aprobado, justo antes de generar los PDF.
• Si diseña la Entidad: el cobro de la cuota de gestión se produce antes de que la Entidad entre al
editor. No puede diseñar sin haber pagado antes.
1. Configuración base: los dos switches
Dentro de la ficha de cada entidad, la Administración encuentra dos switches independientes.
Por defecto ambos están en OFF: la Administración asume los dos conceptos. Puede activar
cada uno por separado en función de lo que quiera trasladar a la Entidad.
Solo la Administración ve y puede cambiar estos switches. La Entidad no ve nada de esto.
Switch 1 — Cuota de gestión PARTILOT
¿Qué es? El coste por usar la plataforma para generar y gestionar las participaciones del set.
OFF (defecto) La Administración paga la cuota de gestión de PARTILOT.
ON La Entidad paga la cuota de gestión de PARTILOT.
Switch 2 — Diseño e impresión de participaciones
¿Qué es? El coste del servicio de diseño y/o impresión física de las participaciones cuando se
usa una imprenta de PARTILOT.
OFF (defecto) La Administración paga el coste de diseño e impresión.
ON La Entidad paga el coste de diseño e impresión.

Documento técnico para equipo de desarrollo — Confidencial
• Los dos switches son independientes. Se puede activar uno, los dos o ninguno en cualquier
combinación.
• Al guardar la entidad, sale un modal que confirma la configuración elegida. El modal tiene opción de
«no volver a mostrar».
• Cada switch determina a quién se le emite la factura de ese concepto y contra qué medio de pago
se lanza el cargo.
Medios de pago
Entidad Siempre paga por TPV con tarjeta tokenizada.
Administración —
por defecto
Paga por TPV con tarjeta tokenizada, igual que la Entidad. Una
Administración que solo hace uno o dos trabajos paga puntualmente por
tarjeta sin ninguna configuración adicional.
Administración —
remesa (activado por
PARTILOT)
Si PARTILOT activa la modalidad de remesa en la ficha de la
Administración, el sistema le bloquea el pago por tarjeta y le factura
periódicamente (configurable: mensual o quincenal) todos los conceptos
acumulados en ese período: cuotas de gestión que asuma y costes de
impresión. La Administración debe tener una cuenta bancaria (IBAN)
registrada en su ficha para esta modalidad.
La modalidad de remesa solo la activa PARTILOT desde el panel de Super Admin. La
Administración no puede elegir ni cambiar su modalidad de pago por sí misma.
2. Caso 1 — Switch 1 OFF · Switch 2 OFF (ambos por
defecto)
La Administración asume los dos conceptos: cuota de gestión y diseño/impresión.
2.1 La Administración diseña ella misma
• La Administración diseña la participación en la plataforma.
• Cuando termina, el sistema envía el diseño a la Entidad para que lo apruebe.
• La Entidad da el OK al diseño. Esto desencadena el cobro de la cuota de gestión a la
Administración.
• → Hasta que la Administración no confirme el pago, los PDF no se generan.
• Confirmado el cobro, el sistema genera los PDF.
• A partir de este punto la Administración puede enviar a imprimir a una imprenta de PARTILOT o
editar el diseño si detecta algún error.
– Si edita el diseño: el sistema anula el OK anterior de la Entidad y le reenvía el diseño
corregido para que lo apruebe de nuevo. No se vuelve a cobrar la cuota de gestión: el sistema
tiene registrado que ya fue cobrada para este set y no la requiere de nuevo.

Documento técnico para equipo de desarrollo — Confidencial
– Si no edita: puede enviar directamente a imprimir a una imprenta de PARTILOT.
Pago pendiente (Switch 1 OFF — paga la Administración): si la Administración no finaliza el pago,
la Entidad no ve nada, ya que ella ya dio su OK y no tiene ninguna acción pendiente. La próxima
vez que la Administración acceda al set, el sistema le retoma el proceso de pago directamente.
Envío a imprenta de PARTILOT: justo antes de enviar el trabajo a la imprenta, el sistema cobra el
coste de impresión a la Administración. Confirmado ese pago, el trabajo se envía a la imprenta, que
envía una prueba. La Administración da el OK (puede consultar a la Entidad) y la imprenta procede
a producción.
2.2 La imprenta de PARTILOT diseña e imprime
• La Administración envía el trabajo a la imprenta (logos, textos, observaciones).
• La imprenta diseña y sube una prueba al panel.
• La Administración aprueba la prueba (puede consultar a la Entidad antes).
• → El sistema cobra la cuota de gestión a la Administración. Hasta que no se confirme, los PDF no
se generan.
• Confirmado el cobro de la cuota, se generan los PDF.
• La Administración decide enviar los PDF a la imprenta de PARTILOT.
• → Justo antes de enviar el trabajo a la imprenta, el sistema cobra el coste de impresión a la
Administración.
• Confirmado ese pago, el trabajo se envía a la imprenta y procede a producción.
3. Caso 2 — Switch 1 ON · Switch 2 ON
La Entidad asume los dos conceptos: cuota de gestión y diseño/impresión.
En todos los sub-casos de este bloque, como es la Entidad quien diseña, el cobro de la cuota de
gestión se produce antes de que entre al editor. No puede diseñar sin haber pagado.
3.1 La Entidad diseña ella misma
• La Entidad accede a la sección de diseño.
• → Antes de entrar al editor, el sistema le cobra la cuota de gestión. Sin pago confirmado, no hay
acceso al editor.
• Confirmado el cobro, la Entidad accede al editor, diseña y guarda.
• El sistema genera los PDF y la Entidad los descarga.

Documento técnico para equipo de desarrollo — Confidencial
3.2 La imprenta de PARTILOT diseña e imprime
• La Entidad elige la opción de enviar a la imprenta a diseñar e imprimir.
• → Antes de enviar el trabajo, el sistema cobra a la Entidad los dos conceptos juntos: cuota de
gestión + coste de impresión, con factura desglosada en dos líneas.
• Con el cobro confirmado, el sistema envía el trabajo a la imprenta.
• La imprenta diseña y sube una prueba al panel.
• La Entidad aprueba la prueba.
• El sistema genera los PDF y la imprenta procede a producción.
3.3 La Entidad diseña y luego envía solo a imprimir
• La Entidad accede a la sección de diseño.
• → Antes de entrar al editor, el sistema le cobra la cuota de gestión. Sin pago confirmado, no hay
acceso al editor.
• Confirmado el cobro, la Entidad accede al editor, diseña y guarda.
• El sistema genera los PDF.
• La Entidad elige enviar los PDF a una imprenta de PARTILOT para su impresión.
• → En ese momento el sistema le cobra el coste de impresión por separado.
• Con el cobro del coste de impresión confirmado, los PDF se envían a la imprenta para producción.
4. Caso 3 — Switch 1 ON · Switch 2 OFF (u otras
combinaciones mixtas)
4.1 Flujo
La Administración diseña y la Entidad paga la cuota de gestión (Switch 1 ON). El coste de
impresión va según el Switch 2.
• La Administración diseña y guarda el diseño en la plataforma.
• El sistema envía una imagen del diseño a la Entidad para que lo apruebe.
• La Entidad da el OK al diseño. Esto desencadena el cobro de la cuota de gestión a la Entidad.
• → Los PDF quedan bloqueados hasta que la Entidad confirma el pago.
• Si la Entidad da el OK pero no finaliza el pago: la Administración ve el set en estado «pendiente de
pago de cuota por la Entidad» y no puede descargar los PDF hasta que ese pago se complete. La
Entidad, la próxima vez que acceda al set, el sistema le retoma el proceso de pago directamente sin
necesidad de volver a dar el OK.
• Confirmado el cobro, el sistema genera los PDF y notifica a la Administración.
• A partir de este punto la Administración puede enviar a imprimir a una imprenta de PARTILOT o
editar el diseño si detecta algún error.

Documento técnico para equipo de desarrollo — Confidencial
– Si edita el diseño: el sistema anula el OK anterior de la Entidad y le reenvía el diseño
corregido para que lo apruebe de nuevo. No se vuelve a cobrar la cuota de gestión: el sistema
tiene registrado que ya fue cobrada para este set y no la requiere de nuevo.
– Si no edita: puede enviar directamente a imprimir a una imprenta de PARTILOT. El coste de
impresión se cobra según el Switch 2: a la Administración si está en OFF, a la Entidad si está
en ON.
5. Resumen: cuándo y a quién se cobra
La combinación de los dos switches determina el comportamiento en cada caso:
Sw
1
Sw
2
Cuota de gestión: cuándo se cobra y a
quién
Quién paga la
cuota gestion
Quién paga
diseño/impresión
OFF OFF
La Administración diseña. Cuando el diseño
está aprobado, el sistema cobra cuota gestión
a la Administración antes de generar los PDF.
Administración Administración
ON OFF La Entidad diseña. El sistema cobra a la
Entidad antes de entrar al editor. Entidad Administración
ON ON La Entidad diseña. El sistema cobra a la
Entidad antes de entrar al editor. Entidad Entidad
OFF ON
La Administración diseña. Cuando el diseño
está aprobado, el sistema cobra a la
Administración antes de generar los PDF.
Administración Entidad
Si diseña la Administración: el cobro de la cuota se produce al aprobar el diseño, antes de generar
los PDF. Si diseña la Entidad: el cobro se produce antes de entrar al editor, el acceso al diseño está
condicionado al pago.
6. Notas técnicas para el desarrollo
• El botón «Enviar a imprenta» solo aparece si hay una imprenta asociada configurada en el sistema.
• El botón «Enviar a imprenta» solo está activo si el diseño está guardado.
• Una vez generados los PDF, el diseño queda bloqueado. No se puede modificar mientras haya
participaciones asignadas o vendidas.
• El sistema siempre genera factura en el momento del cobro. Si se cobran dos conceptos (gestión +
impresión) en un mismo cargo, la factura los desglosa en dos líneas separadas.
• Tanto la Administración como la Entidad reciben notificación (panel + email) al producirse cada
cobro, con enlace a la factura.
• La cuota de gestión de un set se cobra una sola vez. El sistema almacena el estado de cobro por
set. Si el diseño se edita y se vuelve a aprobar, no se genera un nuevo cobro de gestión para ese
set.