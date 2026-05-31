Paso 1: Configuración Financiera y Provisión de Fondos
Este módulo establece las reglas de negocio sobre cómo se garantiza la solvencia para el pago de premios y define la jerarquía de responsabilidades entre los tres actores principales.
1.1. Relación Administración - Entidad (Operativa Externa)
•	Propósito: Deslindar la responsabilidad legal de PARTILOT sobre el origen del dinero de los premios.
•	Lógica de Funcionamiento: La Administración de Lotería debe transferir la totalidad de los premios obtenidos (según el escrutinio de los décimos depositados) directamente a la cuenta bancaria de la Entidad.
•	Justificación: Este es un acuerdo privado y externo; la vinculación de la administración termina con este ingreso. PARTILOT no tiene responsabilidad ni participa en esta transacción.
1.2. Provisión de Fondos en PARTILOT (Modelo Centralizado)
•	Propósito: Asegurar que la plataforma actúe como un garante técnico y financiero para los usuarios finales.
•	Flujo de Ingreso: La Entidad debe transferir el 100% del importe de los premios correspondientes a las participaciones vendidas a la cuenta de tesorería de PARTILOT.
•	Activación de Existencias: PARTILOT verifica el ingreso de forma manual/administrativa y asigna ese saldo exacto a las "existencias" de dicha entidad en el panel de control.
•	Bloqueo de Seguridad: Sin la validación de este ingreso por parte de PARTILOT, el sistema no permitirá procesar pagos online para esa entidad, garantizando que nunca se emita una orden de pago sin fondos reales en custodia.
1.3. Configuración del Modo de Pago (Ficha de Entidad)
La plataforma debe incluir un selector técnico en el perfil de cada entidad para definir su comportamiento operativo:
•	Modo PARTILOT (Predeterminado):
o	Funcionamiento: PARTILOT asume la gestión de las remesas bancarias.
o	Requisito: Es obligatorio el ingreso previo del 100% de los fondos en la cuenta de PARTILOT.
o	Uso: Es la forma predominante de gestión para asegurar la automatización y el control centralizado.
•	Modo ENTIDAD (Legacy/Alternativo):
o	Funcionamiento: La Entidad es quien descarga los ficheros de pago (Norma 34.14) desde su propio panel y los procesa en su propio banco.
o	Requisito: En este modo, no se requiere que la entidad ingrese fondos en PARTILOT, ya que el dinero sale de su propia cuenta bancaria.
o	Justificación: Se mantiene como opción por si en algún momento se decide que sea la entidad la que pague sus propias participaciones de forma directa.
Paso 2: Reclamación de Premios y Gestión de Cartera (Revisión Final)
Este módulo define la interacción del usuario con sus premios, permitiendo gestiones masivas y distribuciones personalizadas según la entidad emisora.
2.1. Escrutinio y Asignación de Premios
•	Proceso Previo: El sistema realiza el escrutinio oficial y asigna a cada participación el premio proporcional a la cantidad jugada.
•	Validación de Fondos: Las opciones de cobro solo se habilitan si la Entidad ha ingresado el 100% del premio en PARTILOT.
2.2. Selección Masiva y Reglas de Agrupación
El usuario accede a su Cartera y puede seleccionar varias participaciones premiadas para una única operación siguiendo estas reglas:
•	Para Transferencia Bancaria:
o	El usuario puede agrupar participaciones de cualquier entidad.
o	Condición: Todas las entidades de las participaciones seleccionadas deben estar habilitadas para el pago por parte de PARTILOT y haber realizado el ingreso de fondos correspondiente.
•	Para Donación y Código de Recarga (Operación Híbrida):
o	El usuario puede seleccionar múltiples participaciones premiadas siempre que pertenezcan a la misma Entidad.
o	Si intenta seleccionar participaciones de otra entidad para este fin, el sistema las deshabilitará visualmente para mantener la integridad del destino del dinero (donación a la propia entidad y código para su administración vinculada).
2.3. Modalidad de Cobro Híbrido (Donación + Código)
Cuando el usuario selecciona "Donar y Generar Código", el sistema suma el importe de las participaciones de la misma entidad y presenta la interfaz de asignación:
•	Distribución del Importe: El usuario decide mediante campos numéricos o barra deslizante qué cantidad va a donación y qué cantidad a código.
•	Gestión del Código:
o	El código aparece en pantalla tras la confirmación y se envía por correo electrónico.
o	Registro: El código queda guardado y disponible para consulta permanente en la cartera del usuario.
o	Inyección: El código se envía/inyecta a la web de la administración para su canje inmediato.
o	Liquidación B2B: Se genera automáticamente una solicitud de transferencia a favor de la Administración de Lotería asociada para cubrir el valor del código.
•	Gestión de la Donación:
o	Se solicitan datos fiscales si la entidad es de Interés General para la posterior emisión del certificado.
o	El dinero se refleja en los resúmenes como donado, permaneciendo en la cuenta de la entidad.
2.4. Transparencia y Consulta de Movimientos
El usuario podrá consultar en su historial de movimientos cada operación de forma pormenorizada. Si una transacción agrupó varias participaciones, el detalle mostrará: 
•	Ficha de la Participación: Datos completos de cada participación implicada (número jugado, entidad emisora, sorteo y estado actual). 
•	Desglose de la Operación:
o	Si fue Transferencia: Importe total enviado y cuenta IBAN de destino. 
o	Si fue Híbrido (Código + Donación): Cantidad exacta destinada a la entidad y cantidad convertida en código. 
o	Registro del Código: El código generado permanecerá visible y copiable en este apartado para su uso posterior. 
•	Estado del Proceso: Indicación de si la transferencia ha sido solicitada, exportada o ya procesada por PARTILOT. 
Paso 3: Panel de la Entidad y Pago Presencial
Este módulo define las herramientas de control para que la Entidad monitorice su contabilidad y gestione exclusivamente el cobro de participaciones físicas.
3.1. Dashboard Financiero de la Entidad (Monitorización)
La Entidad dispone de un panel con indicadores en tiempo real para auditar su situación :
•	Métricas de Venta: Total de participaciones vendidas físicas y digitales por separado. 
•	Importe Jugado: Valor neto de lotería en la participación, excluyendo el donativo. 
•	Contabilidad de Premios:
o	Total Premios: Suma total de premios generados por todas sus participaciones vendidas. 
o	Fondos Ingresados: Dinero total transferido y validado por PARTILOT. 
o	Pagado por Transferencia: Suma del dinero ya enviado a usuarios y el importe generado en códigos para la Administración. 
o	Donado: Importe tramitado por los usuarios como donación. 
o	Pagadas por Gestor: Importe acumulado de premios abonados físicamente por la entidad. 
o	Pendiente de Pago: Saldo restante disponible en la provisión de fondos de la entidad. 
o	Balance Proyectado (Caja Final): Se recalcula automáticamente como Saldo Disponible Actual + Donaciones Recibidas
o	
3.2. Panel de Pago Presencial (Restricción y Control)
Este panel es exclusivo para el abono de premios en efectivo en la sede de la entidad. 
•	Restricción Digital: El sistema bloquea cualquier intento de gestionar una participación digital de forma presencial. Toda participación digital o digitalizada queda registrada en la cartera del cliente y solo puede gestionarse a través de la plataforma online (transferencia, código o donación).
•	Identificación Física: El gestor introduce el número secuencial físico (individual o arco, ej. 345-356). El número de referencia se mantiene como campo opcional. 
•	Datos Mostrados por Participación: Al leer una participación o un arco, el sistema desglosa una tabla donde, por cada fila, se visualizan los siguientes datos técnicos :
1.	Número de participación: Identificador físico secuencial. 
2.	Sorteo jugado: Identificación del sorteo correspondiente. 
3.	Cantidad jugada: Importe neto de lotería de esa participación. 
4.	Premio: Importe exacto a pagar según el escrutinio. 
5.	Estado actual: Si está premiada, pagada, o si es una participación digital (en cuyo caso mostrará error para pago presencial). 
•	Flujo de Validación:
o	Si una participación ya fue pagada, el sistema indica fecha, hora y el gestor que realizó la operación. 
o	El gestor puede eliminar filas individuales de la operación antes de cerrar. 
•	Cierre de Operación:
o	Un campo dinámico suma el total de premios de las participaciones aptas para el cobro. 
o	Al pulsar "Confirmar cobro", las participaciones cambian su estado a "Pagada por gestor". 
•	Auditoría: Se habilita un registro de operaciones con fecha, hora y un desplegable para consultar el listado de participaciones pagadas en cada sesión. Se incluye un buscador para comprobar el estado de cualquier número de participación individual. 

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

5.2. Visualización en la Depuración de Errores (Pestaña 3)
Como indicaste anteriormente, al abrir un archivo generado para depurarlo:
•	Verás la tabla con el botón de Restaurar en cada fila.
•	Al ser la misma interfaz que en "Pendientes de Gestionar", el Admin tiene la seguridad de que, si restaura una fila, el sistema se encarga de ir "hacia atrás" en toda la cadena: libera las participaciones, devuelve el dinero a las entidades correctas y limpia el lote bancario.
