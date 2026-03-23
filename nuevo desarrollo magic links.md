1. Alta de Administración de Lotería
El proceso para las administraciones se centra en la identidad de la oficina y una
gestión de acceso controlada por el Super Administrador.
• Gestor Informativo: Los datos del gestor de la administración se registran
únicamente para fines de contacto y soporte. Estos datos no se integran
en la base de datos de usuarios de la plataforma, permitiendo que dicha
persona física pueda registrarse posteriormente como usuario
independiente, si lo desea.
• Identificación de Usuario Único: El sistema genera un usuario inalterable
basado en el tipo de punto de venta:
o Administración de Lotería: Número de receptor + los 3 últimos
dígitos del número de administración.
o Punto de Venta Mixto: Únicamente el número de receptor.
2. Flujo de Activación y Reseteo (Superadministrador)
El Superadministrador es el único que puede disparar el envío de credenciales
mediante un botón específico en la ficha de la administración.
• Envío Manual: El sistema no envía nada de forma automática al crear el
alta. El Superadministrador debe pulsar el botón de envío para iniciar la
comunicación con el mail de la administración.
• Magic Link: El correo electrónico enviado contiene el Usuario y un Enlace
Magic. Al pulsar este enlace, la administración es dirigida a la plataforma
para establecer la contraseña que desee.
• Disponibilidad Permanente: Este botón está siempre activo en el panel
del Superadministrador. Se utiliza tanto para el primer acceso como para
casos de olvido de credenciales, funcionando como un sistema de reseteo
de contraseña.
3. Seguridad y Privacidad
• Encriptación: Aunque el Superadministrador puede forzar el cambio de
contraseña de forma unilateral, la plataforma nunca muestra la clave
actual, ya que se almacena encriptada en la base de datos.
• Autogestión: Una vez que la administración accede con su usuario y la
contraseña definida a través del Magic Link, tiene la potestad de cambiar
su contraseña desde su propio panel de configuración.
• Dentro de la ficha de la administración (vista del superadministrador) debe
haber el botón con el disparador, un campo con el usuario que no se puede
cambiar y otro campo con la contraseña (encriptada). Ese campo si puede
cambiarlo el superadministrador.
• Dentro del panel de la administración tendrá, en la sección datos, una zona
con el usuario (que no puede cambiar) y otro para poder cambiar la
contraseña poniendo la contraseña anterior y marcando la nueva y
volviendo a marca la nueva para asegurar que sea la correcta.

// DESDE AQUI
2. Alta de Entidad y Gestión de Responsables
El flujo para las entidades se basa en la figura del usuario registrado y una
estructura jerárquica de permisos.
• Gestor Responsable Obligatorio: Cada entidad debe tener siempre un
Gestor Responsable con todos los permisos y la etiqueta de
"responsable".
• Vinculación con Usuarios Reales: A diferencia de las administraciones, el
gestor de la entidad debe ser un usuario registrado en PARTILOT para
aceptar formalmente las condiciones legales.
o Si no es usuario: Recibe un email que le informa de que tiene que
registrarse con ese mail al que se le a enviado el mensaje y que
cuando se haya registrado le saldrá la notificación de que le han
agregado como gestor responsable y que tiene que aceptar. Si no
aceptase quedaría como denegado y la entidad no podría seguir con
los procesos de impresión, asignación de participaciones… ya que
tiene que haber si o si un gestor responsable.
o
o Si ya es usuario: Recibe una notificación push en la APP para
aceptar su nuevo rol. Si no aceptase quedaría el estado como
denegado y la entidad no podría seguir con los procesos de
impresión, asignación de participaciones… ya que tiene que haber si
o si un gestor responsable.
• Jerarquía de Gestores: El gestor responsable puede crear otros gestores
adicionales y asignarles los permisos que considere oportunos.
• Intercambio de Responsabilidad: Para cambiar al gestor responsable,
debe existir otro gestor previamente registrado por el cual se intercambiará
el rol. El nuevo responsable debe aceptar explícitamente el cargo tras
recibir un correo informativo. Si no se hace de esta manera, no se puede
cambiar el gestor y quería el antiguo mientras no se acepta el cambio.
• Limitaciones de Rol: Ciertas acciones críticas, como la transmisión de la
devolución a la administración, son competencia exclusiva del gestor
responsable.