





























Índice
Procesos Panel de Control SIPART

Proceso de alta de una Administración de Loterías en SIPART			2

Proceso de Alta de una Nueva Entidad en SIPART					4

Proceso de Reserva de Lotería en SIPART						7

Proceso creación set participaciones en SIPART					9

Proceso registro Vendedor SIPART							11

Proceso de Venta de Participaciones de Lotería en SIPART			13

Proceso de Asignación de Tacos de Participaciones a un Vendedor en SIPART	15

Proceso de Cobro de Participaciones en el Sistema SIPART			17

	Diseño de Participaciones en el Panel de Control 					20

	Exportación de Participaciones en el Panel de Control 				22

	Proceso de Generación de Web de Venta en SIPART 				24

	Proceso de Generación de Iframes de Pago en SIPART 				25

Acción “Enviar a Diseñar” en SIPART 						27

Proceso de Envío a Imprimir en SIPART 						29

Proceso de Configuración de Sorteos de Lotería en SIPART			32

Proceso de Creación y Edición de Tipos de Sorteo en SIPART			34
Proceso de Asignación de Premios en SIPART					35

Proceso de alta de una Administración de Loterías en SIPART (Solo super Admin) DISEÑADO

El gestor del programa SIPART sigue los siguientes pasos para dar de alta una nueva Administración de Loterías en su panel de control:

Acceso al Panel de Control

El gestor inicia sesión en SIPART, accede a la sección Administraciones y clica sobre el botón Nueva Administración.

Introducción de Datos

Se solicita al gestor que ingrese los datos de la Administración de Loterías, incluyendo Nombre comercial, Nº Administración, Nombre de la Sociedad (opcional), Código de Receptor, Dirección, Localidad , Provincia, NIF, Email, Teléfono (Fijo o Móvil) y correo electrónico. En un siguiente paso, Datos Administrador: Nombre y apellidos, nif, fecha de nacimiento, email y teléfono (fijo o móvil) y por último otra sección donde se configuran los permisos y opciones que se acuerdan con la administración [Esta sección aún está por definir]

Verificación en la Base de Datos

El sistema consulta la base de datos para comprobar si ya existe un registro con el mismo nombre o CIF. (No puede haber dos Administraciones iguales, ni un mismo administrador en dos Administraciones)

Validación de Existencia

Si el sistema detecta una Administración previamente registrada con los mismos datos, se muestra un aviso indicando que la Administración ya existe y se devuelve al usuario a la pantalla de registro. Si no se encuentra ninguna coincidencia, el proceso de alta continúa.

Generación de Credenciales

El sistema genera un usuario con el email de la Administración y genera una contraseña aleatoria . 


Almacenamiento del Registro

Una vez generadas las credenciales, SIPART almacena el nuevo registro en la base de datos.

Envío de Correo Electrónico

Se envía un correo de bienvenida a la dirección de email proporcionada, incluyendo las credenciales de acceso.

[ Finalización del Proceso ]












Proceso de Alta de una Nueva Entidad en SIPART (Administrador de loterías) DISEÑADO

Acceso al Panel de Gestión

El gestor de la Administración de Loterías inicia sesión en el panel de gestión de SIPART.

Navegación a la Sección de Entidades

Dentro del panel, accede a la sección de "Entidades".
Hace clic sobre el botón de "Nueva Entidad".

Relleno del Formulario de Registro de Entidad

Introduce los siguientes datos: Razón social, CIF o NIF, Dirección completa, Teléfono de contacto, Email

En este punto: en el formulario hay un selector tipo switcher en el cual se activa o desactiva quien será el encargado de sufragar la cuota por gestión de participaciones, apagado lo hará la administración de lotería (por defecto) y si se activa será la entidad.

Validación de Datos de la Entidad

El sistema consulta la base de datos para verificar si ya existe una entidad con la misma razón social o CIF.

Si ya existe, se muestra un mensaje de error indicando la duplicidad y no permite continuar.

Si no existe, el sistema solicita información del gestor responsable.

Registro del Gestor Responsable

Se debe completar el formulario con los siguientes datos:
Nombre y apellidos del gestor, Número de carnet de identidad, Teléfono de contacto, fecha de nacimiento y Email.

Validación del Email del Gestor

El sistema busca en la base de datos si ya existe un usuario con el mismo email.

Si el email ya está registrado:

Se muestra una ventana con el usuario coincidente preguntando si se trata del mismo gestor.

Si el usuario indica "No", el sistema no permite continuar y devuelve al formulario.

Si el usuario indica "Sí", se vincula el rol de gestor a su perfil existente.
Se envía una notificación a la aplicación móvil y un email informando de su asignación como gestor de la entidad.


Si el email no está registrado:

El sistema almacena el email con la etiqueta de "gestor". Enviando un email indicando que tiene pendiente el registrarse con ese mismo email para que se le otorgue permisos de gestor.

Cuando el usuario se registre en el futuro con ese email, se le asignará automáticamente el rol de gestor, permitiéndole acceder al panel de la entidad.

Este proceso garantiza la correcta administración y vinculación de entidades y gestores dentro del sistema SIPART.































Proceso de Reserva de Lotería en SIPART (Administración de lotería)

Acceso al Panel de Gestión

La Administración inicia sesión en el panel de gestión de SIPART.

Navegación a la Sección de Reservas	

Dentro del panel, accede a la sección "Reservas".

Hace clic sobre el botón "Nueva Reserva".

Relleno del Formulario de Reserva

Se completa el formulario con los siguientes datos:
Para quién es la reserva: Seleccionando una entidad registrada, Número del sorteo, Nombre del sorteo,  , Imagen del décimo del sorteo (opcional), Precio del décimo.

Selección de series: Definiendo un rango de series completas (desde - hasta).

Selección de fracciones.

Validación de la Reserva:

Al hacer clic en "Guardar", el sistema verifica que no exista otra reserva del mismo sorteo con las mismas series o fracciones.

Si hay coincidencias:

El sistema muestra un aviso indicando qué series y fracciones coinciden con otra reserva existente.

No permite continuar hasta que se corrija el 	conflicto.

Si no hay coincidencias:

La reserva se almacena correctamente.

Se envía un email al gestor de la entidad vinculada con los detalles de la reserva:

Sorteo reservado.

Cantidad de dinero reservado.

Series y fracciones asignadas.

Este proceso garantiza una correcta gestión de reservas dentro del sistema SIPART, evitando duplicidades y asegurando una administración eficiente de la lotería.















Proceso creación set participaciones en SIPART

Acceso al Panel de Gestión

La Administración inicia sesión en el panel de gestión de SIPART.

Navegación a la Sección de Reservas

Dentro del panel, accede a la sección "Participaciones".

Hace clic sobre el botón "Nuevo Set".	

Relleno del Formulario de Creación

Se completa el formulario con los siguientes datos:

Selección de qué Entidad será el Set.

El sistema verifica que la entidad tiene al menos una reserva de Loteria asociada con décimos disponibles.

Si no hay ninguna reserva:

El sistema muestra un aviso de que no existen reservas para la entidad y muestra un botón para “Cancelar” y otro de “  ”

Si existen varias reservas:

El sistema esperará hasta que elijamos al menos una de ellas (pueden elegirse varias siempre y cuando sean del mismo sorteo)

Si solo existe una reserva el sistema no nos 	hará seleccionar, sino que la seleccionará de manera automática.

Selecciona si el set será físico o digital.

Se introduce el precio total de la participación, como el importe por cada número jugado y su donativo (la suma del importe jugado y el donativo nunca pueden superar el precio total de la participación).

Se introduce la cantidad de participaciones a crear (El importe jugado no puede superar al importe reservado).

Se introduce fecha límite, en la que se permite la venta (nunca puede ser posterior a la fecha del sorteo).

Validación de la Reserva

Al generar las participaciones, el sistema las registrará de manera diferente según sean físicas o digitales. Las participaciones físicas incluirán códigos de control de 20 dígitos asociados a una numeración consecutiva de referencia, mientras que las digitales no requieren esta información.

Se registrará el set de participaciones y se comprobará si aún quedan décimos disponibles.

Si quedan décimos disponibles:

El sistema nos pregunta si se quieren crear más participaciones.

Si no quedan décimos disponibles:

El sistema lleva a la pantalla de participaciones, donde en la tabla se puede ver el set creado.

Se envía un email al gestor de la entidad vinculada con los detalles del set.





















Proceso registro Vendedor SIPART DISEÑADO

Acceso al Panel de Gestión

El gestor inicia sesión en el panel de gestión de SIPART.

Navegación a la Sección de Vendedores

Dentro del panel, accede a la sección "Vendedores".

Hace clic sobre el botón "Nuevo Vendedor".	

Registro nuevo Vendedor

Nombre y apellidos del vendedor, Número de carnet de identidad, Teléfono de contacto y Email.

Validación Email del Vendedor

El sistema busca en la base de datos si ya existe un usuario con el mismo email.

Si el email ya está registrado:

Se muestra una ventana con el usuario coincidente preguntando si se trata del mismo usuario.

Si el gestor indica "No", el sistema no permite continuar y devuelve al formulario.

Si el gestor indica "Sí", se vincula el rol de vendedor a su perfil existente.

Se envía una notificación a la aplicación móvil y un email informando de su asignación como vendedor de la entidad.

Si el email no está registrado:

El sistema almacena el email con la etiqueta de "vendedor". Enviando un email indicando que tiene pendiente el registrarse con ese mismo email para que se le otorgue permisos de vendedor.

Cuando el usuario se registre en el futuro con ese email, se le asignará automáticamente el rol de vendedor, permitiéndole acceder al panel de venta.

Este proceso garantiza la correcta administración de vendedores dentro del sistema SIPART.
















Proceso de Venta de Participaciones de Lotería en SIPART

Acceso al Perfil de Vendedor

El vendedor inicia sesión en la aplicación SIPART y accede a su perfil de vendedor.

Selección del Tipo de Participación

El vendedor elige entre:

Participaciones Físicas

Participaciones Digitales

Venta de Participaciones Físicas

Se escanea el código QR de la participación.

El sistema consulta la base de datos para verificar su disponibilidad.

Si la participación no está disponible (anulada, devuelta o eliminada):

Se muestra un mensaje de error con el estado de la participación.

Si está disponible, se registra la venta y se repite el proceso para cada participación vendida.

El sistema calcula el importe total.

El vendedor selecciona el método de pago

Efectivo

Transferencia

Una vez recibido el pago, se presiona "Aceptar" y el sistema registra la venta.

Venta de Participaciones Digitales

 

Selecciona el método de pago y confirma la venta.

El sistema solicita un email de contacto del comprador.

Se verifica si el email está registrado en la base de datos

Si el email está registrado:

Se adjudican las participaciones al usuario.

Se envía una notificación y un email con el comprobante de compra.

Si el email no está registrado:

El sistema almacena las participaciones en espera.

Se envía un email informando al comprador que las participaciones se adjudicarán automáticamente al registrarse con el mismo email.

Este proceso garantiza un control eficiente en la venta de participaciones, asegurando la correcta asignación y registro de cada transacción en SIPART.























Proceso de Asignación de Tacos y Participaciones a un Vendedor en SIPART (Modo 1 - Vendedores)

Acceso al Panel de Control
El gestor accede a la sección Vendedores dentro del panel de control de SIPART.

Selección del Vendedor

Busca al vendedor deseado: 	A través de la tabla de vendedores. - Utilizando el buscador por nombre o identificador. - Aplicando filtros disponibles en la tabla.
Una vez localizado el vendedor, puede acceder a sus participaciones mediante el botón Acceso Rápido - Participaciones.

Visualización de Participaciones Asignadas
 Tabla de Participaciones del Vendedor: Se despliega una tabla con todos los tacos y participaciones asignados a ese vendedor (si los tiene).
Se muestran las acciones disponibles: Realizar una devolución. - Asignar tacos. - Asignar participaciones.
Asignación del Taco/s
 Formulario de Asignación:
Se debe elegir la reserva sobre la cual realizar la asignación.(Si solo hay una reserva, el sistema la seleccionará automáticamente.)
Se debe elegir el set de participaciones.(Si solo hay un set disponible, el sistema lo seleccionará automáticamente.)
Se muestra una lista de tacos disponibles para asignar.(Se pueden seleccionar uno o varios tacos para el vendedor.)


 
Asignación de Participaciones
Formulario de Asignación: La selección de reserva y set de participaciones funciona igual que en la asignación de tacos.
En lugar de tacos, se muestra una lista de todas las participaciones disponibles.
Se puede asignar un rango específico de participaciones al vendedor.


Confirmación y Notificación al Vendedor
Si el vendedor está Registrado en la aplicación:
Se envía una notificación a su aplicación SIPART.
Debe aceptar la adjudicación de las participaciones.
Una vez aceptado, la asignación queda registrada en el sistema.


Si el vendedor está en modo offline:
En lugar de una notificación digital, el sistema genera una ventana de firma.
El vendedor debe firmar digitalmente en la pantalla.
El documento firmado se envía automáticamente al correo de la entidad.

Proceso de Asignación de Tacos y Participaciones a un Vendedor en SIPART (Modo 2 - Participaciones)
El sistema SIPART permite la asignación de tacos y participaciones a los vendedores desde la sección Participaciones. A diferencia del Modo 1 (Vendedores), en este proceso la asignación se realiza seleccionando primero el set de participaciones y, posteriormente, el vendedor.

Proceso de Cobro de Participaciones en el Sistema SIPART

Acceso y Escaneo de Participaciones

El usuario entra en la aplicación y toca en el botón Escáner.
Escanea el código QR de una participación.

Consulta de Premio

El sistema consulta la base de datos para verificar si la participación está premiada.

Sin premio: 

El sistema informa al usuario que la participación no está premiada y permite continuar escaneando otra.
	
Con premio: 

El sistema muestra el importe del premio y ofrece dos opciones:
Escanear otra participación.
Almacenar la participación en la cartera de participaciones.

Gestión desde la Cartera de Participaciones

El usuario puede acceder a la sección Cartera de Participaciones para ver todas las participaciones premiadas.

Desde esta sección, el usuario tiene tres opciones para gestionar las participaciones:
	
Cobrar: 

Selecciona las participaciones que desea cobrar.
Presiona en Aceptar.
	
Introduce los datos personales necesarios:
	
Nombre y apellidos. - Fecha de nacimiento. - DNI. - Número de cuenta bancaria.
El sistema envía órdenes de transferencia a las entidades correspondientes, que procesan el pago.
	
Donar: 

Selecciona las participaciones a donar.
Presiona en Aceptar.
El sistema notifica a las entidades  el importe que el usuario ha decidido donar.
	
Cargar saldo:
	
Selecciona las participaciones a recargar como saldo.
Presiona en Aceptar.

El sistema envía órdenes de transferencia a las entidades, utilizando en este caso el número de cuenta de las administraciones gestoras.

El sistema genera los códigos de recarga y los envía a las administraciones gestoras que introducen es sus webs para cuando las canjee el usuario puedan hacerse efectivas.

Comunicación usuario

En cualquiera de las tres opciones el sistema manda un email al usuario con un justificante de la acción realizada.

Este flujo asegura un proceso claro y transparente para que los usuarios puedan gestionar sus premios de manera eficiente y segura en SIPART.










Diseño de Participaciones en el Panel de Control
Descripción General
El apartado Diseño de Participaciones dentro del panel de control permite a la administración de loterías y a las entidades configurar el formato de sus participaciones. Dependiendo de la configuración establecida en el set de participaciones, la administración puede diseñarlas directamente o delegar esta tarea a las entidades.

Asignación de Permisos para el Diseño
La administración de loterías accede a la sección Set de Participaciones.
Puede seleccionar un set existente o crear uno nuevo.
En la configuración del set, habilita o deshabilita la opción para que la entidad pueda diseñar su participación (también se puede elegir esta opción durante la creación de la entidad).
Acceso a la Herramienta de Diseño
Desde la tabla de sets de participaciones, se encuentran las acciones rápidas.
Se hace clic en la opción Diseño (solo visible para entidades si la administración lo permite).
Selección del Método de Diseño
El sistema ofrece dos opciones:
Diseño por plantillas
Diseño personalizado
Diseño por Plantillas
Se muestran tres tipos de plantillas predefinidas: SIPART - TIKET GESTIÓN - ASG
Las plantillas incluyen un diseño preestablecido, al igual que dimensiones de la participación, márgenes, sangres y matriz, con textos y fondos fijos.
Solo ciertos elementos son personalizables: logotipo y algunos textos.


También pueden elegir la opción de seleccionar una plantilla para la parte trasera (si no se selecciona, se usará la plantilla predeterminada de SIPART).
Diseño Personalizado
Se accede al editor avanzado con herramientas de diseño.
Opciones disponibles:
Definir dimensiones de la participación, márgenes y sangres.
Seleccionar un fondo desde una galería precargada o subir uno propio.
Añadir y organizar múltiples imágenes.
Insertar campos de texto personalizables con estilo y posición ajustables.
Elementos obligatorios que no pueden eliminarse ni quedar ocultos:
Texto legal e informativo - Precio y depositario - Números jugados - Código QR y código de numeración (para papeletas físicas) - Numeración de control. (Estos elementos si pueden editarse, en escala, posición y tipografía)
Guardado y Finalización
Una vez diseñado, se guarda la participación.
El icono de Diseño en la tabla de sets se sustituye por Editar.
Se habilitan dos nuevas opciones: Descargar e Imprimir.

















Exportación de Participaciones en el Panel de Control
El apartado Exportación de Participaciones permite a los gestores de las entidades descargar e imprimir las participaciones físicas una vez han sido diseñadas y guardadas. Este proceso organiza las participaciones en tacos y genera los documentos necesarios para su correcta gestión y control.

Acceso a la Exportación de Participaciones
Una vez realizada la reserva de lotería, creados los sets de participaciones y diseñadas las participaciones, el gestor de la entidad accede a la sección Participaciones en el panel de control.
En la tabla de sets de participaciones, el gestor puede visualizar:
Sets pendientes de diseño.
Sets ya diseñados y listos para la exportación.
Descarga de Participaciones
En las acciones rápidas, el gestor selecciona la opción Descargar Participaciones (solo disponible si el diseño ha sido guardado y si las participaciones son físicas).
Asignación de Tacos (Solo en la primera descarga del set)
Si es la primera vez que se descargan, el sistema muestra un formulario donde el gestor debe:
Definir el número de tacos a generar. Especificar la cantidad de participaciones por taco (50-25)(Puede que dependiendo de la reserva realizada y el set generado el último taco tenga un número inferior al elegido). 
Confirmación y Descarga
Una vez asignados los tacos, el gestor confirma la exportación y elige la ubicación para guardar el set.
El sistema genera tres tipos de documentos en PDF:
Resumen de Exportación:
Detalla las participaciones exportadas.
Especifica el número de tacos generados y su cantidad de participaciones.
Incluye fecha y hora de la exportación.
Portadas de Tacos:
Generadas con un diseño base de SIPART.
Contienen el nombre y logo de la entidad (si aplica).
Información del taco: número de taco, cantidad de participaciones, rango de numeración de control.
Participaciones Diseñadas:
Incluye todas las participaciones con el diseño final aprobado.
Registro de Exportaciones y Descargas Repetidas
Tras la primera exportación, en la sección Participaciones, se habilita una nueva acción rápida (lupa) que permite:
Consultar todas las exportaciones realizadas de ese set.
Ver qué gestor realizó la descarga y en qué fecha y hora.
Si el gestor vuelve a descargar el archivo, el sistema no pedirá nuevamente la configuración de los tacos.
En caso de errores en la configuración de los tacos, el gestor deberá contactar con la administración para realizar modificaciones.
Detalle de los tacos generados
Una vez exportados los documentos aparece un botón de acción rápida en el propio set, denominado tacos, en los cuales si presionamos, inmediatamente debajo de nuestro set se despliega una tabla con todos los tacos generados, con el número de taco, cantidad de participaciones de cada taco, de que número a qué número de participación incluye el taco, el estado en que se encuentra, disponible, en venta, vendido, devuelto, anulado o eliminado, también aparecerá el nombre de que vendedor lo tiene adjudicado que si pinchamos en él nos lleva a su ficha.







Proceso de Generación de Web de Venta en SIPART

Acceso y Configuración del TPV

La administración accede a la sección específica dentro de su panel de control en SIPART.

Introduce los datos necesarios en un formulario para configurar un TPV para la entidad.

Opcionalmente, añade un logotipo de la entidad y una imagen para la cabecera del iframe, personalizando su apariencia.

El sistema comprueba que no haya una solicitud pendiente de la misma entidad.

Almacenamiento y Notificación de la Solicitud
	
La información se almacena en la base de datos de SIPART.
El sistema envía una notificación al panel de control de SIPART indicando que hay una nueva solicitud pendiente.
	
Revisión por parte del Administrador de SIPART
	
El administrador accede a la sección Iframes dentro del sistema.

Encuentra una tabla con todas las solicitudes pendientes.

Selecciona la solicitud a procesar.

Revisa que toda la información esté correctamente ingresada.

Descarga los archivos adjuntos (logotipo e imagen de cabecera) para revisarlos.

Si es necesario, edita los archivos para ajustarlos a las medidas y características del iframe.

Generación del Iframe y Notificación a la Entidad
	
Una vez revisado, el administrador confirma la solicitud.
El sistema genera automáticamente el código del iframe.
Se notifica a la entidad correspondiente que el iframe está listo para usarse.

Se envía un correo electrónico con el código del iframe y una breve guía de instrucciones para insertarlo en su sitio web.
Proceso de Generación de Iframes de Pago en SIPART
El sistema SIPART permite la generación automática de iframes de pago, adaptados a la configuración de cada administración. Estos iframes ofrecen a los usuarios opciones de pago, consulta de premios, donaciones y generación de códigos de recarga.
Acceso y Configuración
El gestor de SIPART configura el perfil de una administración.
Según los datos proporcionados, el sistema genera un iframe personalizado adaptado a las necesidades de la entidad.
Generación de Iframe Básico (Cuando solo se proporciona una dirección web)
Se genera un iframe con las funciones esenciales de pago.
El usuario podrá:
Registrarse en SIPART si aún no lo ha hecho.
Iniciar sesión si ya tiene cuenta.
Consultar sus participaciones digitales, incluyendo premios.
Acceder a opciones de pago, donación o generación de código de recarga.
Si posee participaciones físicas, podrá ingresar manualmente los 20 dígitos de control para registrarlas en el sistema.
Al finalizar, el usuario podrá cerrar sesión y continuar navegando en la web de la administración.
Generación de Iframe Completo (Cuando se configuran parámetros de conexión a la API de la administración)
El iframe incluirá integración avanzada con la web de la administración.
Funcionamiento del acceso:
Verificación de sesión:
Si el usuario accede al iframe desde la web de la administración, el sistema hace una llamada a la API de la administración.
Se comprueba si el usuario tiene la sesión iniciada en la web.
Se verifica si su email está registrado en SIPART.
Opciones según estado del usuario:
Si no está registrado en SIPART, se le ofrecerá la opción de registrarse con las credenciales de la web, facilitando el proceso.
Si ya está registrado, el sistema iniciará sesión automáticamente.
Acceso a funcionalidades:
Se mantienen las opciones del iframe básico (consulta de participaciones, pagos, donaciones y generación de códigos de recarga).
Nueva funcionalidad para códigos de recarga:
Si el código pertenece a la administración de la web, se ofrecerá la opción de hacerlo efectivo en ese mismo momento o guardarlo para más tarde.
Si se elige hacerlo efectivo al instante, el sistema carga el código mediante la API de la administración y realiza el ingreso automáticamente en la cuenta del usuario dentro de la web de la administración.












Acción “Enviar a Diseñar” en SIPART
La funcionalidad “Enviar a Diseñar” dentro del apartado de Participaciones en SIPART permite a la entidad y a la administración delegar el diseño de un set de participaciones a una persona externa. Esta opción aparece junto a la selección del método de diseño, ya sea por medio de plantillas o mediante un diseño libre.
Proceso de Envío a Diseñar
Selección de la Opción
El gestor accede al apartado de Participaciones y selecciona la acción rápida de Diseño.
Además de las opciones habituales de diseño con plantillas o diseño libre, se muestra la opción “Enviar a Diseñar”.
Confirmación del Envío
Al hacer clic en “Enviar a Diseñar”, el sistema muestra un aviso indicando que se enviará un correo con el enlace y credenciales de acceso para el panel de diseño de SIPART.
El gestor introduce el correo electrónico de la persona externa que se encargará del diseño.
Generación y Envío de la Invitación
El sistema envía un correo electrónico automático a la persona invitada con la siguiente información:
Nombre de la entidad que realiza la invitación.
Enlace directo al panel de diseño de SIPART.
Usuario y contraseña generados automáticamente para acceder al sistema.

Acceso y Restricciones del Diseñador Externo
Acceso al Panel de Diseño
Al hacer clic en el enlace, el diseñador invitado es dirigido a un login exclusivo, donde deberá ingresar las credenciales proporcionadas.
Una vez dentro, accede a un panel de control limitado, donde solo podrá ver el set de participaciones asignado.
Funcionalidades Disponibles
El diseñador tendrá acceso a todas las herramientas de diseño habituales de SIPART, con la única excepción de que no podrá invitar a terceros.
No podrá modificar ni visualizar otros sets de participaciones fuera del asignado.
Limitaciones y Seguridad del Acceso
Acceso Temporal: La cuenta generada será válida por un máximo de 4 semanas.
Expiración de Credenciales: Transcurrido este tiempo, las credenciales serán eliminadas del sistema y el acceso quedará bloqueado.
Invitación Única: Solo una persona puede ser invitada a diseñar un set de participaciones a la vez. Si se requiere otra persona, el gestor deberá revocar la invitación anterior y enviar una nueva.





























Proceso de Envío a Imprimir en SIPART

El sistema SIPART permite la impresión de sets de participaciones a través de imprentas colaboradoras. 

Disponibilidad de la Opción “Enviar a Imprimir”
La opción “Enviar a Imprimir” solo aparecerá en las acciones rápidas de los sets de participaciones si hay una imprenta asociada configurada.
Si no hay ninguna imprenta vinculada, la opción no estará disponible.
Proceso de Envío a Imprimir
Envío desde la Administración
Cuando la Administración selecciona la opción “Enviar a Imprimir”, el sistema:
(Asignación de Tacos 
El sistema muestra un formulario donde el administrador debe:
Definir el número de tacos a generar. Especificar la cantidad de participaciones por taco (50-25)(Puede que dependiendo de la reserva realizada y el set generado el último taco tenga un número inferior al elegido). 
Una vez aceptado.)
Muestra un resumen con las características del set de participaciones esperando confirmación.
Una vez aceptado el sistema muestra un mensaje de confirmación de acción realizada, o
Ha ocurrido un error.
Envía automáticamente el set al panel de control de la imprenta para su procesamiento.
Envío desde la Entidad
Cuando una Entidad selecciona la opción “Enviar a Imprimir”, el sistema:
(Asignación de Tacos 
El sistema muestra un formulario donde el gestor debe:
Definir el número de tacos a generar. Especificar la cantidad de participaciones por taco (50-25)(Puede que dependiendo de la reserva realizada y el set generado el último taco tenga un número inferior al elegido). 
Una vez aceptado.)
Muestra un resumen con las características del set de participaciones.
Indica el importe a abonar para proceder con la impresión.
Tras el pago, el sistema confirma si:
La petición ha sido realizada con éxito, o
Ha ocurrido un error en el proceso de pago.
Bloqueo de Acciones tras el Envío a Imprimir
Una vez el set ha sido enviado a imprimir:
Se deshabilitan las siguientes opciones:
Botón de impresión
Botón de descarga
Edición
(solo se activarán si la imprenta rechaza el trabajo por algún motivo o si la administración o sipart lo permiten). 
Se mantienen activas:
Informe de estado.
Seguimiento del Estado de Impresión
El informe de estado mostrará información detallada sobre la impresión del set:
Fecha y hora en que se envió el diseño a imprimir.
Estado actual del proceso, que puede ser:
En revisión (la imprenta está validando el diseño).
En producción (la impresión está en curso).
Enviado (las participaciones han sido despachadas a la entidad o administración).
Detalle de los tacos generados
Una vez mandado a imprimir aparece un botón de acción rápida en el propio set, denominado tacos, en los cuales si presionamos, inmediatamente debajo de nuestro set se despliega una tabla con todos los tacos generados, con el número de taco, cantidad de participaciones de cada taco, de que número a qué número de participación incluye el taco, el estado en que se encuentra, disponible, en venta, vendido, devuelto, anulado o eliminado, también aparecerá el nombre de que vendedor lo tiene adjudicado que si pinchamos en él nos lleva a su ficha. También dispondrá de una acción rápida, si el taco no se encuentra asignado a nadie desde esa misma sección puede ser adjudicado a un vendedor 



















Proceso de Configuración de Sorteos de Lotería en SIPART
El sistema SIPART permite a los superadministradores gestionar sorteos de lotería desde el panel de control. Se pueden crear nuevos sorteos o editar sorteos existentes
Creación de un Nuevo Sorteo
Desde la sección Sorteos, dentro del panel de control del superadministrador, se puede crear un nuevo sorteo.
Formulario de Configuración
Al seleccionar la opción “Nuevo Sorteo”, el sistema muestra un formulario donde se deben completar los siguientes datos:
Imagen del Sorteo (Opcional): Se puede subir una imagen representativa del sorteo.
Número del Sorteo: Código único (Anual) que identifica el sorteo en el sistema. [14/25 es la numeración en el décimo físico nosotros indicamos en el formulario 14 el sistema ya sabe que es del año 25 y al año siguiente el sorteo 14 será el 14/26]
Fecha del Sorteo: Día en el que se llevará a cabo el sorteo.
Fecha Límite de Gestión: Última fecha en la que se podrán realizar acciones relacionadas con el sorteo, como: Hacer reservas de décimos. Crear y diseñar sets de participaciones. Otras gestiones dentro del sistema.
Nombre del Sorteo: Se puede asignar un nombre personalizado para facilitar su identificación.
Tipo de Sorteo: Se selecciona la modalidad del sorteo (Previamente configurada en sección Tipo de Sorteo). El sistema asigna automáticamente el precio del décimo según el tipo de sorteo elegido.
Gestión y Edición de Sorteos
Una vez creado el sorteo, este aparecerá en la tabla resumen dentro de la sección de sorteos.
Opciones Disponibles
Desde la tabla de sorteos, el superadministrador puede:
Consultar la información del sorteo configurado
Modificar los parámetros del sorteo si es necesario.
Consultar su estado actual, el cual puede ser: Activo: El sorteo está disponible y en proceso. Caducado: Ha pasado la fecha límite de gestión y ya no permite modificaciones. Sorteado: El sorteo ha finalizado y se han determinado los premios.
















Proceso de Creación y Edición de Tipos de Sorteo en SIPART
El sistema SIPART permite a los superadministradores gestionar los tipos de sorteo disponibles. Desde la sección Sorteos, se puede acceder a la configuración de los diferentes tipos de sorteos, pudiendo crear nuevos tipos, editarlos o eliminarlos según las necesidades 
Acceso a la Configuración de Tipos de Sorteo
Dentro de la sección Sorteos, el superadministrador encontrará el botón “Tipos de Sorteo”.
Al hacer clic en este botón, se accede a la sección de Tipos de Sorteo, donde se muestra una tabla resumen con todos los tipos de sorteo ya configurados.
Creación de un Nuevo Tipo de Sorteo
Para crear un nuevo tipo de sorteo, se debe seleccionar la opción “Crear”, lo que abrirá un formulario con los siguientes parámetros:
Nombre del Tipo de Sorteo: Permite asignar un nombre identificativo para este tipo de sorteo.
Precio de Venta del Décimo: Define el coste unitario de cada décimo dentro de este tipo de sorteo.
Premio a la Serie y Fracción: Opción para indicar si el sorteo cuenta con premios adicionales a la serie y/o fracción.
Nota: Estos valores determinarán las características de los sorteos creados posteriormente en el sistema.
La definición de Premio a la serie y a la fracción definirá si se ha de identificar en las participación la serie y fracción correspondiente al décimo jugado.
Edición y Eliminación de Tipos de Sorteo
Desde la tabla de Tipos de Sorteo, el superadministrador puede:
Editar los parámetros de un tipo de sorteo existente.
Eliminar tipos de sorteo si ya no son necesarios.
Restricción: No se podrá eliminar un tipo de sorteo si hay sorteos activos que lo estén utilizando.

Proceso de Asignación de Premios en SIPART
El sistema SIPART permite la asignación automática de premios a los sorteos en función de los datos proporcionados por Loterías y Apuestas del Estado. Esta funcionalidad garantiza una gestión precisa y eficiente de los premios, asegurando su correcta adjudicación y consulta.
Proceso de Obtención y Almacenamiento de Premios
Importación de Datos Oficiales
El sistema obtiene los siguientes datos desde los medios de comunicación oficiales de Loterías y Apuestas del Estado:
Números premiados - Categorías de premios - Importes asignados
Una vez recibidos, estos datos se almacenan en la base de datos y se vinculan automáticamente al sorteo correspondiente.
Consulta de Premios en la Sección Escrutinio
Ubicación:
Los premios adjudicados a cada sorteo estarán disponibles en la sección Escrutinio dentro del panel de administración.
Visualización en Tabla:
Se muestra un listado con todos los premios adjudicados.
Cada fila incluye la información detallada del premio (número, categoría, importe).
Se permite filtrar y buscar premios específicos dentro de un sorteo.
Modificación de Parámetros
 Edición de Premios:
El superadministrador puede modificar los parámetros de los premios adjudicados si fuera necesario, pero nunca podrá eliminarlos para garantizar la integridad de los resultados.
Acceso a Listas Oficiales y Comprobaciones
Desde los detalles de los premios, se proporciona acceso a las listas oficiales de cada sorteo.
 Esto permite realizar comprobaciones en el escrutinio, asegurando la transparencia y exactitud de la información.






















Proceso de Devolución de Participaciones a Vendedores (Opción 1 - Participaciones)
El sistema SIPART permite realizar devoluciones de participaciones a los vendedores desde diferentes secciones del panel de control, con distintas implicaciones según la sección en la que se realice la acción.
Acceso a la Sección de Participaciones
El gestor accede a la sección Participaciones dentro del panel de control de SIPART.
Métodos de Devolución
Dependiendo de la sección desde la cual se acceda a la devolución, el sistema realizará la acción de manera diferente:
Desde el acceso rápido en el set de participaciones:
Devolución del set completo: Las devoluciones afectarán a todas las participaciones del set sin hacer distinción por vendedor.
Proceso: Se podrá devolver cualquier participación dentro del set, sin especificar un vendedor.
Desde el detalle del set y la selección de un taco:
Devolución de un taco específico: Si se accede a la devolución desde esta opción, el sistema solo permitirá devolver el taco seleccionado.
Acción rápida: Al seleccionar un taco, las devoluciones se limitan a ese taco, afectando solo a las participaciones dentro del mismo.
Si el taco pertenece a una entrega de participaciones a un vendedor:
El sistema permitirá devolver participaciones de los tacos asignados al vendedor.
Se podrá realizar la devolución de participaciones de un vendedor específico.
Opciones de Devolución
Devolución por participaciones sueltas: Se puede seleccionar cada participación de forma individual para ser devuelta.
Devolución por rango: Se puede devolver un rango de participaciones especificando un rango “desde” y “hasta”.
Confirmación y Cálculo de Importe
El sistema mostrará el importe que debe abonar el vendedor por las participaciones no devueltas.
Nota: Las participaciones no devueltas se considerarán como vendidas a efectos informativos, ya que el vendedor podrá seguir devolviendo participaciones hasta la fecha límite o hasta que se realice la transmisión de la devolución a la Administración de Loterías.
Devoluciones No Definitivas
Las devoluciones no son definitivas ni vinculantes hasta que se haya realizado la transmisión a la Administración de Loterías.
Hasta ese momento, las devoluciones pueden ser modificadas o rectificadas por el gestor.
Proceso de rectificación: Las devoluciones pueden ser cambiadas dentro del sistema hasta que se transmitan oficialmente.

























Proceso de Devolución de Participaciones a Vendedores (Opción 2 - Vendedores)
El sistema SIPART permite realizar devoluciones de participaciones a los vendedores desde la sección de vendedores, lo que facilita la gestión de las participaciones asignadas a cada uno de ellos.
Se puede localizar al vendedor a través de:
Tabla de vendedores - Buscador - Filtros disponibles en la tabla
Una vez localizado el vendedor, el gestor accede a su perfil y utiliza la acción rápida de “Devolución”.
Métodos de Devolución
Desde esta opción, la devolución afecta exclusivamente a las participaciones asignadas al vendedor seleccionado.
El sistema permite realizar la devolución de dos formas:
Devolución por participaciones sueltas: Seleccionando manualmente las participaciones a devolver.
Devolución por rango: Definiendo un intervalo de participaciones (desde-hasta).
El vendedor solo podrá devolver participaciones dentro de los tacos que le han sido asignados.
IMPORTANTE:
Si el vendedor tiene varias entregas de participaciones asignadas, el sistema permitirá elegir de qué entrega se desea devolver las participaciones.
Confirmación y Cálculo de Importe
Al realizar una devolución, el sistema calculará automáticamente el importe correspondiente.
Las participaciones no devueltas se consideran como vendidas a efectos informativos.
Devoluciones No Definitivas
Las devoluciones no serán definitivas ni vinculantes hasta que se realice la transmisión a la Administración de Loterías.
Hasta ese momento, el vendedor podrá seguir devolviendo participaciones o modificar la devolución.

















Proceso de Transmisión de Devolución en SIPART
La transmisión de devolución es el proceso final donde el gestor de la entidad valida y confirma la devolución de participaciones antes de su transmisión definitiva a la Administración de Loterías.
 Este proceso garantiza que solo se devuelvan participaciones correctamente registradas y que no existan errores en el estado de las mismas.
Acceso a la Transmisión de Devolución
El gestor accede al panel de control de SIPART y entra en la sección Participaciones.
Desde aquí puede seleccionar la opción Transmisión de Devolución, que abre una tabla con los sets de participaciones disponibles para devolución.
Filtros Disponibles
El sistema permite filtrar la transmisión de devoluciones de dos maneras:
Filtro Tacos
Filtro por Participaciones
Dependiendo del filtro seleccionado, el gestor verá diferentes opciones de devolución.
Filtro por Tacos
Se mostrará una lista con todos los tacos que componen el set.
Cada taco incluirá la siguiente información:
Número de taco.
Rango de participaciones que contiene.
Número total de participaciones.
Cantidad de participaciones con estado “disponible” o “disponible-dv”.
Acción sobre los tacos:
El gestor puede hacer clic sobre un taco para desplegar el desglose de participaciones, viendo detalles como:
Número de control de la participación.
Números jugados.
Importe y donativo (si aplica).
Estado actual: Vendida - Entregada - Disponible-dv - Disponible
Restricción importante:
Si alguna participación tiene el estado “Entregada”, significa que el vendedor aún no realizó la devolución. El sistema no permitirá devolver estas participaciones hasta que el vendedor las devuelva primero.
Opciones de devolución en este filtro:
Devolver el taco completo desde la acción rápida.
Devolver participaciones sueltas.
Devolver por un rango de participaciones (desde-hasta).
Filtro por Participaciones
En este modo, el sistema muestra directamente todas las participaciones con estado “disponible” o “disponible-dv”.
Opciones de devolución en este filtro:
Devolver participaciones sueltas.
Devolver por un rango de participaciones.
En este filtro no está disponible la opción de devolución de tacos completos.
Proceso de Transmisión
Confirmación y transmisión
Una vez realizadas las devoluciones, el gestor pulsa “Transmitir”.
El sistema revisa si existen participaciones en estado incorrecto para la devolución (ejemplo: “Entregada”).
Si hay incidencias, el sistema las señalará y bloqueará la transmisión hasta que sean corregidas.
Si no hay incidencias, el sistema muestra un resumen con:
Número total de Participaciones que componen el set.
Número total de participaciones vendidas.
Número total de participaciones devueltas.
Importe total a pagar a la administración.
Importe total recaudado de Donativo.
Confirmación final:
Antes de transmitir definitivamente, el gestor deberá confirmar la operación.
Búsqueda Específica de Participaciones
Durante todo el proceso, el gestor puede buscar una participación en concreto utilizando el buscador.
Detalles disponibles en la búsqueda:
Número de participación.
Precio y donativo.
Persona o vendedor al que fue entregada.
Fecha de devolución del vendedor.
Estado actual de la participación.
Comunicación
El sistema enviará una notificación al panel de la administración, un email con el resumen de la devolución y un listado de todas las participaciones devueltas.
Este mismo informe será enviado a la dirección de email de la entidad.


