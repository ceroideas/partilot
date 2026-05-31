Bloque 1: Provisión de Fondos y Activación del Sistema (Revisado)
Este bloque establece la base técnica necesaria para que el usuario pueda interactuar con sus premios. El sistema actúa como un "semáforo" que solo cambia a verde cuando los fondos están garantizados. 
1.1. Flujo de Acciones y Procesos Internos
Paso	Acción (Actor)	Proceso Interno de la Plataforma (Backend)
1	Administración de Lotería transfiere premios a la Entidad.	Operación externa. 
2	Entidad transfiere el 100% del importe a la cuenta de PARTILOT.	El sistema queda a la espera de la conciliación bancaria manual. 
3	El Super Admin de PARTILOT recibe aviso de ingreso.	Accede al panel de control de la Entidad emisora. 
4	El Super Admin valida el importe en el sistema.	1. Actualiza Saldo_Premios_Ingresado. 


2. El estado de la entidad cambia a "Solvente".
5	Activación del módulo de cobro.	Se habilita la lógica de negocio para procesar transferencias, códigos y donaciones. 
1.2. Interfaz y Avisos (Segmentación de Notificaciones)
El motor de notificaciones debe filtrar a los destinatarios basándose en la trazabilidad de la participación:
•	Usuarios con Participaciones Digitales (Nativas):
o	Acción: Envío automático de Notificación Push/Email. 
o	Mensaje: "¡Enhorabuena! Los premios de [Entidad] ya están disponibles. Accede a tu cartera para gestionarlos".
•	Usuarios con Participaciones Físicas DIGITALIZADAS:
o	Acción: Envío automático de Notificación Push/Email. 
o	Condición: El usuario debe haber escaneado el QR o introducido la referencia previamente, vinculando la participación a su perfil. 
o	Mensaje: "El premio de tu participación física de [Entidad] ya puede ser cobrado online".
•	Usuarios con Participaciones Físicas NO DIGITALIZADAS:
o	Acción: Ninguna.
o	Razón: El sistema no posee datos de contacto ni de propiedad sobre estos soportes. El usuario recibirá la información de premio únicamente al intentar digitalizarla o consultarla manualmente en la web/app. 
1.3. Lógica de Backend (Seguridad)
El sistema debe implementar un validador de estado por entidad:
IF (User_Claim_Request) AND (Entity_Status != "Solvente") THEN Return_Error ("Entidad en proceso de provisión de fondos"). 

Bloque 2: Flujo de Usuario (Digitalización, Cartera y Selección) - Revisado
Este bloque detalla el proceso desde que el usuario identifica su participación hasta que decide el destino de su premio, con especial foco en las restricciones de agrupación.
2.1. Acciones del Usuario y Procesos del Sistema
Paso	Acción del Usuario (Frontend)	Proceso Interno de la Plataforma (Backend)
1	Accede a la App/Web e inicia sesión con sus credenciales.	Valida sesión y carga el perfil del usuario.
2	Entra en "Cobrar participaciones" y escanea el QR (App) o introduce la referencia (Web).	1. Consulta la BD para verificar que la referencia existe. 


2. Comprueba que no haya sido digitalizada antes por otro usuario.
3	Visualiza el resultado en pantalla: "Enhorabuena, tienes [X]€ de premio".	Cruza la referencia con el acta de premios del sorteo asignado.
4	La participación se guarda automáticamente en su Cartera.	Crea un registro de propiedad: ID_Usuario <-> ID_Participacion.
5	El usuario entra en su Cartera y selecciona las participaciones que desea gestionar.	El sistema suma los importes de todas las participaciones seleccionadas.
6	El sistema muestra el Total a Cobrar y habilita las opciones según la selección realizada.	Verifica que la Entidad de cada participación esté en estado "Solvente" para permitir el avance.
2.2. Lógica de Agrupación y Restricciones Operativas
Es fundamental que los programadores implementen estas reglas en la lógica de selección (multi-select):
•	Opción Transferencia Bancaria:
o	Regla: El usuario puede agrupar participaciones de cualquier entidad.
o	Backend: El sistema genera una única solicitud de transferencia, pero internamente debe prorratear el descuento del saldo de premios de cada entidad involucrada.
•	Opción Código de Recarga o Donación (Monoentidad):
o	Regla: Si el usuario elige generar un código o realizar una donación, solo puede seleccionar participaciones de la misma entidad.
o	Frontend: Al seleccionar la primera participación para código/donación, el sistema debe "grisar" o deshabilitar las participaciones de otras entidades en la lista de la cartera.
o	Razón: El código de recarga solo es válido en la administración asignada a esa entidad específica, y la donación debe dirigirse a la asociación emisora de la papeleta.
2.3. Mensajes y Avisos en Pantalla (UI/UX)
•	Aviso de Restricción: "Para generar un código o realizar una donación, selecciona participaciones de una única entidad".
•	Validación de Importe: "Has seleccionado [X]€ procedentes de [Nombre Entidad]".
•	Confirmación de Cartera: "Participación física vinculada correctamente a tu cuenta. Ya puedes gestionarla".
Bloque 3: Ejecución del Cobro (Transferencia, Donación y Código) - Finalizado
Este bloque define el proceso técnico y comunicativo donde la intención del usuario se convierte en transacciones financieras y registros contables.
3.1. Flujo de Acciones y Procesos Internos (Secuencial)
Paso	Acción del Usuario (Frontend)	Proceso Interno (Backend)
1	Elige modalidad de cobro. 	Prepara el entorno (Transferencia o Código/Donación). 
2	Opción Transferencia: Introduce IBAN. 	Valida formato y dispara Email de Doble Opt-in. 
3	Confirmación Email: Pulsa enlace en correo. 	Valida token y mueve solicitud a "Pendientes de Exportar". 
4	Reparto Código + Donación: Ajusta importes y pulsa Aceptar. 	Punto de No Retorno: Bloquea el importe total contra el saldo de la entidad. 
5	Paso 1: Generación de Código: Visualiza el código en pantalla. 	Crea código único y genera solicitud de transferencia automática a la Administración. 
6	Paso 2: Datos de Donación: Pantalla de datos fiscales. 	Muestra formulario de NIF/NIE, Nombre y Dirección. 
7	Finalización: Pulsa "Enviar datos". 	Añade datos al Excel de la Entidad para certificados. 
3.2. Comunicaciones y Mensajería Automática
Para que el sistema sea transparente, el backend debe disparar las siguientes comunicaciones:
•	Comunicaciones al Usuario:
o	Email de Verificación (Transferencia): Contiene el enlace para confirmar la solicitud o el botón para cancelarla si el usuario no la reconoce. 
o	Pantalla y Email de Código: Al confirmar el reparto, recibe el código de recarga con las instrucciones de canje en la web de la administración asignada. 
o	Aviso de Confirmación de Donación: Mensaje en pantalla agradeciendo la donación e indicando que la entidad gestionará su certificado. 
o	Notificación de Pago Realizado: Una vez el Super Admin exporta la remesa y pulsa el botón de informar, el usuario recibe un email confirmando que su transferencia ya ha sido emitida por el banco. 
•	Comunicaciones B2B (Sistema):
o	Aviso a la Administración de Lotería: Email informativo indicando que se ha generado un nuevo código de recarga y que hay una transferencia en curso a su favor para cubrir dicho saldo. 
o	Alerta de Solvencia para la Entidad: Si el saldo disponible baja de un umbral crítico debido a los cobros, el sistema notifica a la entidad. 
3.3. Actualización de Saldos y Campos de Información
Tras cada operación confirmada, el sistema actualiza el panel de la Entidad:
•	Saldo Total Premios: Importe inicial. 
•	Cobrado por Transferencia: Solicitudes verificadas. 
•	Códigos Generados: Suma de importes convertidos en códigos (Irreversible). 
•	Donaciones Recibidas: Suma de importes donados (Irreversible). 
•	Saldo Disponible Actual: Total - (Transferencias + Códigos + Donaciones). 
•	Balance Proyectado (Caja Final): Saldo Disponible Actual + Donaciones Recibidas. 
Bloque 4: Panel de la Entidad y Pago Presencial (Cierre de Digitalización y Garantía de Liquidez)
Este bloque define el proceso para "congelar" la responsabilidad financiera de cada parte antes de que empiece el flujo de dinero.
4.1. El Muro de Seguridad: Deadline de Digitalización
Para evitar problemas de liquidez y disputas entre la Entidad y PARTILOT, el sistema implementa un cierre estricto:
•	Regla de Oro: La opción "Digitalizar Participación Física" (escanear QR o meter referencia de papel) desaparece de la App/Web en el momento exacto que se determine (ej. 24h antes del sorteo).
•	Consecuencia: Tras el sorteo, el sistema ya sabe exactamente cuántas participaciones son responsabilidad de PARTILOT (Digitales + Digitalizadas antes del corte) y cuántas son responsabilidad de la Entidad (Físicas restantes).
•	Blindaje: La entidad ingresa en PARTILOT la cantidad exacta calculada tras el sorteo. A partir de ahí, nadie más puede digitalizar. Si un usuario no digitalizó a tiempo, obligatoriamente debe ir a la sede de la entidad. Así, la entidad no se encuentra con "sorpresas" de pagos online que no ha previsto.
4.2. Flujo Técnico Detallado (Pasos del 1 al 20)
Paso	Actor	Acción / Evento	Proceso Interno del Sistema (Backend)
1	Sistema	Cierre del Endpoint.	Desactiva la función post_digitalize_ticket. Ya no se aceptan más vínculos papel-usuario.
2	Sorteo	Celebración del Sorteo.	Evento externo. El sistema queda a la espera de los resultados.
3	Admin	Carga de Escrutinio.	Se introducen los premios oficiales.
4	Sistema	Cálculo de Deuda Online.	Suma: (Premios Ventas Digitales) + (Premios Papel Digitalizado pre-sorteo).
5	Entidad	Recepción de Liquidación.	Ve en su panel: "Ingreso obligatorio para activar sistema: [X]€".
6	Entidad	Ingreso en PARTILOT.	Transfiere el 100% de la Deuda Online calculada.
7	Super Admin	Validación de Fondos.	Confirma el ingreso. El sistema activa los pagos online y el panel presencial.
8	Usuario	Intento de digitalización tardía.	La App muestra: "Plazo de digitalización cerrado. Cobro exclusivo en sede física".
9	Entidad	Acceso al Panel Presencial.	El gestor abre el módulo para pagar las papeletas físicas que NO se digitalizaron.
10	Entidad	Modo Individual: Introducción ID.	Introduce el Número de Orden (identificador físico del papel).
11	Sistema	Validación de "Propiedad".	Comprueba en la BD si ese número de orden fue digitalizado antes del corte.
12	Sistema	Bloqueo por Custodia.	IF digitalizada == TRUE THEN Error: "Este premio ya está en PARTILOT. El usuario debe cobrarlo online".
13	Entidad	Modo Arco: Rango de números.	El gestor mete el rango de la libreta de participaciones (ej. 1 al 100).
14	Sistema	Mapeo de Disponibilidad.	El sistema marca en gris/bloqueado los números que ya están en el sistema digital.
15	Entidad	Pago Físico.	El gestor marca los números de las papeletas que tiene en mano y entrega el dinero.
16	Entidad	Confirmación Final.	Pulsa "Confirmar Pago Presencial" para ese grupo de números.
17	Sistema	Cambio de Estado.	Marca los registros como PAID_OFFLINE.
18	Sistema	Actualización Contable.	Descuenta el dinero del saldo que la entidad tiene en su propia cuenta.
19	Sistema	Auditoría LOPD.	Registra la operación. Si el gestor intenta ver quién cobró online, el sistema oculta los datos.
20	Sistema	Consolidación de Saldos.	Actualiza el campo "Balance Proyectado" para la liquidación final tras 3 meses.
Exportar a Hojas de cálculo
________________________________________
4.3. Resumen de Seguridad para la Entidad
Este flujo garantiza que la entidad nunca tendrá un problema de liquidez inesperado por culpa de la plataforma, porque:
1.	Sabe lo que debe ingresar desde el minuto 1 después del escrutinio.
2.	Tiene el control total del resto del dinero de los premios para sus pagos en sede.
3.	No hay fugas: Nadie puede "convertir" una participación física en digital después de que la entidad haya hecho sus cuentas.
4.4. Reglas Críticas para Programadores
•	Integridad del Ingreso: Una vez la entidad ingresa el dinero de las participaciones online (nativas + digitalizadas pre-sorteo), ese dinero ya está en PARTILOT. La entidad no puede pagar esas participaciones en su sede, porque ya ha delegado el pago en la plataforma.
•	Seguridad de los Tiempos: El sistema debe impedir cualquier cambio de estado de "Físico" a "Digitalizado" una vez pasado el deadline. Si un usuario intenta digitalizar tarde, la App mostrará: "El plazo de digitalización ha finalizado. Debes acudir a la sede de la entidad para el cobro presencial".
•	Conciliación Final: Al terminar los 3 meses de plazo legal, el sistema cruzará lo pagado online y lo marcado como pagado presencial para liquidar las donaciones y los premios caducados (sobrantes). 
•	También tendrá que haber en las condiciones legales una referencia a que si las participaciones digitales tienen 3 meses para cobrarse, igual que las físicas. Si no se cobra ese dinero pasara a la plataforma. Quizás se podría subir la caducidad a 4-5 meses. 

Pasos 4 y 5: Gestión Centralizada de Remesas y Control de Integridad
Este módulo describe el funcionamiento del núcleo operativo de PARTILOT, donde el Super Administrador procesa las solicitudes de pago masivo asegurando la transparencia total y el cuadre de caja por entidad.
4.1. Estructura de la Tabla de Control y Log de Auditoría
•	Propósito: Centralizar en una sola vista todos los datos necesarios para el pago y la trazabilidad histórica de cada solicitud.
•	Diseño de la Tabla: Cada solicitud se presenta en una fila con las siguientes columnas:
Columna	Datos Incluidos
Identificación	ID Cliente, Nombre y Apellidos, Documento (NIF/NIE/TIE) y Email.
Importe	Cantidad exacta del premio a transferir.
Cuenta Bancaria	IBAN validado mostrado con máscara de seguridad (ej. ****5678).
Solicitud	Fecha y hora exacta en la que el usuario realizó la petición.
Observaciones (Log)	Columna de historial vivo donde aparecen cronológicamente todos los estados de esa solicitud en la misma fila: origen (Web/App), cuándo se exportó, quién realizó la exportación, si fue rescatada, restaurada o si se envió el email de confirmación. Todas con fecha y hora de cada estado. Si se exporta varias veces también se indica y si se envia  mail varias veces también se indica.

4.2. Flujo Operativo: Pestaña 3 y Depuración de Errores
Pestaña 3: Gestionadas / Exportadas (Histórico y Reversión)
•	Propósito: Mantener el registro de todos los pagos emitidos y permitir la subsanación de errores bancarios de forma intuitiva.
•	Histórico de Ficheros: Se listan todos los archivos generados con sus metadatos: número de solicitudes incluidas, importe total del lote, fecha, hora y el usuario que realizó la exportación.
•	Consulta y Apertura de Archivos:
o	El Administrador puede abrir cualquier archivo generado para consultar su contenido.
o	Interfaz Espejo: Al abrir el archivo, la visualización de las solicitudes debe ser idéntica a la de la pestaña de "Pendientes de Gestionar", mostrando la misma tabla de datos y las mismas opciones de control.
•	Depuración de Errores (Flujo de Corrección):
1.	Identificación: Tras recibir un rechazo del banco, el Administrador abre el archivo correspondiente y localiza la solicitud conflictiva.
2.	Eliminación: Utiliza el botón de eliminar para quitar la solicitud del lote.
3.	Regeneración: Una vez limpia la lista de errores, el sistema habilita un botón para volver a exportar el archivo. El nuevo fichero contendrá únicamente las solicitudes correctas, manteniendo la integridad de la suma total ante el banco.
•	Trazabilidad de Eliminadas: Existe una sección específica que muestra las solicitudes que han sido eliminadas de los lotes a modo informativo, conservando todo su historial de observaciones para futuras consultas.
•	Acción de Restaurar (Reversión Individual):
o	Si una transferencia es fallida de forma definitiva, se pulsa "Restaurar".
o	Efecto: La solicitud sale de la lista, el importe se reintegra automáticamente al saldo de la Entidad y la participación del usuario vuelve al estado "Pendiente de Cobro".
o	El evento se registra con detalle en la columna de Observaciones.
•	Confirmación Final: Tras verificar que el banco ha procesado el archivo con éxito, el Administrador pulsa el botón "Confirmar Pago", lo que desencadena el envío masivo de emails de "Transferencia Realizada" a los usuarios del lote.

4.3. Lógica Avanzada de Restauración y Trazabilidad Multientidad
Para que el Super Admin pueda gestionar fallos en cuentas bancarias de forma segura, el sistema debe operar bajo estas reglas:
A. Estructura de la Solicitud (El "Contenedor")
Cada solicitud de transferencia se comporta como un contenedor. Para poder restaurarla correctamente, debe guardar internamente el desglose:
•	Metadatos de la Solicitud: ID de transacción, IBAN (con máscara), Usuario y Fecha.
•	Desglose de Origen (La "Receta"): Una lista vinculada que indique:
o	ID de Participación 1 -> Entidad A -> Importe X.
o	ID de Participación 2 -> Entidad B -> Importe Y.
o	ID de Participación 3 -> Entidad A -> Importe Z.
B. Funcionamiento del Botón "Restaurar" (Pestaña 3 y 2)
Si el Admin detecta que un IBAN es erróneo o el banco devuelve el pago, al pulsar Restaurar:
1.	Identificación de Componentes: El sistema lee el "desglose de origen" de esa solicitud específica.
2.	Reversión de Estados Individuales:
o	Todas las participaciones incluidas en esa solicitud cambian su estado de "En proceso de pago" a "Premiada" (disponible para cobro de nuevo).
3.	Devolución de Saldos por Entidad:
o	El sistema suma los importes por cada entidad (siguiendo el ejemplo anterior: X+Z para Entidad A, e Y para Entidad B).
o	Abono Automático: Se reingresan esas cantidades exactas al saldo de premios de cada entidad respectiva en PARTILOT.
4.	Registro en Log de Observaciones:
o	Se escribe automáticamente en la fila: "Solicitud restaurada por [Admin] el [Fecha/Hora]. Motivo: Error en cuenta. Fondos devueltos a Entidad A y Entidad B. Participaciones liberadas."
C. Experiencia del Usuario tras la Restauración
•	Al usuario le desaparecerá el movimiento de "Transferencia solicitada" de su cartera.
•	Las participaciones volverán a aparecerle con el botón "Cobrar" activo.
•	Se le puede enviar una notificación automática indicando: "Su solicitud de cobro ha sido cancelada por un error en los datos bancarios. Por favor, inicie el proceso de nuevo revisando su IBAN".
________________________________________
5.1. Control de Integridad y Principio de "Caja Única"
•	Lógica de Saldo: Cada vez que se genera una remesa o un código, el saldo disponible de la Entidad en PARTILOT disminuye automáticamente.
•	Reajuste Automático: Si se restaura o anula una solicitud, el importe se abona de inmediato al saldo de la Entidad para evitar descuadres.
•	Check de Solvencia: El sistema impide generar cualquier remesa si el importe total seleccionado supera el saldo disponible actual de la entidad.
•	Resumen Global de Control: El panel muestra un balance final por entidad que incluye: fondos ingresados, pagado por transferencias, cantidad en códigos, donaciones y el remanente exacto por pagar.

Bloque 5: Panel de Gestión de Remesas y Control de Calidad (Super Admin)
Este bloque describe las herramientas del Super Admin para procesar los pagos bancarios masivos y gestionar la comunicación con los usuarios de forma segura y manual.
5.1. Estructura de Control por Pestañas
Pestaña	Descripción	Acción del Sistema
1. No Verificadas	Solicitudes donde el usuario aún no ha pulsado el enlace de su email.	El sistema mantiene el importe "congelado" pero no lo suma a la deuda real.
2. Pendientes	Solicitudes verificadas (Doble Opt-in realizado) listas para exportar.	El sistema permite la selección múltiple para generar el archivo 34.14.
3. Gestionadas	Histórico de todas las solicitudes que ya han sido incluidas en una remesa.	Aquí se gestionan los envíos de emails por lotes y las restauraciones.

5.1. Flujo Técnico de Gestión y Edición de Lotes
5.1. Flujo Técnico de Exportación, Edición y Comunicación
Paso	Actor	Acción / Evento	Proceso Interno del Sistema (Backend)
1	Super Admin	Selección de Solicitudes.	Filtra por entidad, fecha o importe en la Pestaña 2 (Pendientes).
2	Super Admin	Generar Remesa 34.14.	1. Compila el archivo bancario. 
2. Asigna un ID de Lote único a ese grupo de solicitudes.
3	Sistema	Cambio de Estado.	Las solicitudes pasan a estado GESTIONADA y se mueven a la Pestaña 3.
4	Sistema	Silencio de Notificaciones.	En este punto NO se envía ningún email al usuario. El estado es "Enviado al banco".
5	Super Admin	Validación Bancaria.	El Admin sube el archivo a la banca online. Si el banco lo acepta y procesa...
6	Super Admin	Envío de Confirmación (Manual).	En la Pestaña 3, localiza el ID de Lote y pulsa el botón "Enviar Mail de Confirmación de Pago".
7	Sistema	Comunicación Masiva.	Dispara el email solo a los usuarios pertenecientes a ese lote: "Tu transferencia ha sido emitida con éxito".
8	Banco	Notificación de Error (Devolución).	Si un IBAN es erróneo, el banco devuelve la transferencia días después.
9	Super Admin	Localización de Incidencia.	Busca la solicitud específica dentro del lote en la Pestaña 3.
10	Super Admin	Restaurar Solicitud.	Pulsa el botón "Restaurar" para esa línea individual.
11	Sistema	Regresión Contable.	1. Vuelve la solicitud a la Cartera del usuario como "Premiada". 
2. El importe se suma de nuevo al Saldo Disponible de la entidad.
12	Sistema	Registro en Log.	En la columna "Observaciones", anota: "Restaurada por Admin el [Fecha] por error en IBAN".
13	Sistema	Edición de la Composición del Lote.	El sistema elimina la vinculación de esa solicitud restaurada con el ID de Lote original.
14	Sistema	Recálculo de Totales de Exportación.	El sistema actualiza automáticamente los datos informativos del lote: 
1. Nuevo_Total_Lote = Total_Anterior - Importe_Restaurado. 
2. Nº_Registros = Nº_Registros - 1.
15	Super Admin	Auditoría del Archivo.	El Admin puede descargar una versión actualizada del reporte del lote que coincida exactamente con los pagos que finalmente sí se ejecutaron con éxito.

5.2. Reglas Cruciales para Programadores
•	Edición de Lote (Paso 13 y 14): El archivo exportado no es un bloque estático. Debe ser modificable internamente. Si el Admin restaura una línea, el lote debe quedar "limpio" de esa operación para que las estadísticas de pago de la entidad sean veraces. •  
•	Integridad del Lote: El archivo exportado (el registro en la base de datos de PARTILOT) debe ser editable. Si una línea se borra del lote, el sistema debe "desvincularla" del ID de Lote para que no cuente en las estadísticas de ese pago.
•	Consistencia de Saldos: Es vital que el paso 9 (Sincronización de Caja) sea atómico. Si la restauración falla, el dinero no puede "desaparecer". Debe volver al panel de la entidad para que el balance proyectado sea correcto.
•	Sincronización con Cartera: Al restaurar, la participación debe aparecer de nuevo en la cartera del usuario como si nunca se hubiera solicitado el cobro, permitiéndole corregir su IBAN y volver a empezar el proceso del Bloque 3.
•	Gestión de Logs: El historial debe ser inalterable. Aunque la línea se elimine del lote para el recálculo, el Log General debe guardar que esa participación estuvo en el Lote X y fue restaurada por el Admin Y. •  Persistencia del Lote: El sistema debe entender que un ID de Lote puede tener múltiples "versiones" de archivo descargado, pero siempre basadas en los registros que queden vinculados a él en ese momento.
•	Limpieza de Datos: Al pulsar "Regenerar", el motor de exportación debe hacer una nueva consulta a la base de datos buscando solo los registros que mantengan ese ID_Lote. Esto asegura que la línea eliminada en el Paso 7 no aparezca en el nuevo archivo.  

Al abrir un archivo generado para depurarlo:
•	Verás la tabla con el botón de Restaurar en cada fila.
•	Al ser la misma interfaz que en "Pendientes de Gestionar", el Admin tiene la seguridad de que, si restaura una fila, el sistema se encarga de ir "hacia atrás" en toda la cadena: libera las participaciones, devuelve el dinero a las entidades correctas y limpia el lote bancario.
