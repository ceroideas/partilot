12. IMPRESIÓN
Punto 1: Gestión de Permisos y Origen del Diseño
1.1. Determinación de la Responsabilidad Operativa (Switch de Gestión)
El proceso comienza tras la reserva de lotería y la creación de los sets. La
Administración de Lotería es el primer actor y debe configurar un switch de
responsabilidad que define el comportamiento del sistema:
• Habilitación de Diseño: La administración decide si ella realizará el diseño
e impresión o si habilita a la Entidad para hacerlo.
• Gestión Financiera: Este switch identifica automáticamente a quién se le
debe cobrar por la utilización de la plataforma y, en su caso, por los
servicios de la imprenta de PARTILOT. (2 switch diferentes)
• Cobro de Gestión: En este punto es donde el sistema debe procesar el
cobro por la utilización de la plataforma PARTILOT.
1.2. Persistencia y Recuperación de Trabajo (Auto-save)
Para garantizar una experiencia de usuario sin pérdidas de información, el sistema
implementa un mecanismo de guardado dinámico:
• Guardado Automático: A medida que la entidad o administración edita un
set, el sistema almacena el progreso.
• Continuidad: Si la sesión se interrumpe, el usuario puede retomar el
diseño exactamente donde lo dejó o decidir empezar de cero con la
información básica ya completada (numero, importe participación,
donativo, QR, numero secuencial…)
1.3. Reutilización y Clonación de Diseños
Para optimizar el tiempo, especialmente en ampliaciones de reservas dentro de
un mismo sorteo, el sistema ofrece una biblioteca de diseños previos:
• Histórico de Diseños: Al entrar en un set nuevo, el sistema muestra
diseños creados anteriormente por la misma entidad/administración.
• Metadatos de Referencia: Cada diseño sugerido incluye su fecha de
creación y el set al que pertenece, participaciones generadas, y por quien
fue realizado (imprenta partilot, diseño propio), teniendo una etiqueta que
lo indique (propio, imprenta, papel), originalmente para facilitar su
identificación.
• Condición de Edición: Un diseño recuperado solo será editable si fue
realizado originalmente por la entidad o administración, no por una
imprenta externa (en este caso solo mostraría el jpg de la imagen)
• Inyección de Datos Variables: Al elegir un diseño antiguo, el sistema
inyecta automáticamente los datos del set actual (cantidad de
participaciones, lotería, donativo y número/s) sobre la base gráfica
seleccionada.
1.4. Definición de la Modalidad de Producción
En este paso inicial, el usuario debe marcar el camino que seguirá el set entre las
cuatro opciones disponibles:
• Modelo de Edición Propia: Diseño y generación de PDF dentro de la
plataforma para impresión local o en imprenta propia.
• Modelo de Imprenta PARTILOT (Delegación Total): Se elige una imprenta
de PARTILOT para que ellos realicen el diseño y la impresión tras recibir los
archivos y observaciones del cliente. El sistema recopila la información del
set para la imprenta (numero, precio, donativo), le solicita tamaño de
participación donde quiere que se imprima, tamaño de tacos , subida de
archivos que necesitara la imprenta para diseñar y un campo de
observaciones/textos que la imprenta debe utilizar para el diseño.
• Modelo de Impresión PARTILOT (Diseño Propio): El usuario diseña en
PARTILOT, pero bajo una interfaz limitada y acotada a los estándares
técnicos de la imprenta colaboradora para que la imprenta de PARTILOT
pueda imprimirlo.
• Modelos Precargados: Uso de papeles de empresas externas (Thyke
Gestión, ASG) con zonas de inserción predefinidas.
El sistema mostrara en el primer paso cuando se elija el set para diseñar, si es para
imprimir, 4 opciones:
• Diseño e impresión propios
• Diseño e impresión imprenta Partilot
• Impresión Imprenta Partilot
• Papeles preconfigurados (Thyke, ASG…)
Con esto conseguimos seleccionar los modelos ya configurados en función de las
necesidades del cliente, facilitando así el trabajo de la imprenta de Partilot.
1.5. Bloqueos de Seguridad y Visibilidad
El sistema protege la integridad de la lotería mediante reglas de estado:
• Estado de las Participaciones: El diseño solo es editable mientras no haya
ninguna participación asignada a un vendedor o vendida digitalmente.
• Visibilidad de Acciones: Los botones de "Diseño", "Descarga" e "Imprimir"
(acciones rápidas) solo están activos si la entidad tiene los permisos
otorgados por la administración y según el estado actual del set. (si hay
alguna participación asignada a algún vendedor o vendida, el sistema no
deja editar el diseño. Para ello deberían estar todas las participaciones en
el estado disponible o disponibles DV
• Diseños Externos: Si el diseño fue realizado por la imprenta de PARTILOT,
el cliente verá la imagen final (JPG), no habrá posibilidad de edición ya que
lo hizo la imprenta.
Punto 2: Configuración zona de trabajo de diseño
Una vez superado el filtro de permisos y modelo de producción, el sistema abre la
interfaz de configuración del soporte físico. El objetivo es calcular el área de
trabajo real y la distribución de las participaciones en el pliego.
2.1. Selección del Soporte de Impresión
El usuario debe definir el tamaño del papel final sobre el que se realizará la
producción (solo cuando sea el usuario el que diseña y va a enviar a su imprenta)
• Formatos Estándar: Se permite elegir entre A3 o A4 para que el sistema
calcule automáticamente las dimensiones del área de trabajo.
• Cálculo de Área: Basado en esta selección, el sistema delimita los bordes
físicos del papel en el editor.
2.2. Retícula, Repetición y Tamaños
Esta sección define cuántas participaciones cabrán por cada hoja, lo cual es
crítico para la eficiencia del material:
• Configuración de Filas y Columnas: El usuario introduce el número de
filas y columnas necesarias para definir el tamaño de la participación
individual dentro del pliego.
• Formatos para Imprenta PARTILOT: Si se ha elegido trabajar con la
imprenta colaboradora, el sistema restringe (capa) las opciones a dos
formatos estándar para garantizar la eficiencia:
o 6 participaciones por A3: Organizadas en una retícula de 3x2. El
sistema debe indicar el tamaño de la participación total y el tamaño
útil sin la matriz.
o 8 participaciones por A3: Organizadas en una retícula de 4x2.
Igualmente, se debe mostrar el desglose de medidas con y sin
matriz.
2.3. Parámetros de Corte y Seguridad de Imprenta
Para evitar errores en la fase de post-impresión (guillotinado), se deben configurar
los siguientes campos técnicos (ya definidos y no editables cuando se elija
trabajar con la imprenta de PARTILOT)
• Márgenes Generales: Definición del espacio en blanco de seguridad entre
el borde del papel y el inicio del diseño.
• Sangre: El sistema requiere configurar la sangre (normalmente 3mm) para
asegurar que el diseño sobresalga de la línea de corte, evitando filos
blancos tras el uso de la guillotina.
• Distancia de Matriz (Perforación): Se define el espacio exacto donde se
situará la línea de puntos (troquel) que separará la matriz (que se queda la
entidad) de la participación (que se lleva el cliente).
2.4. Organización Logística de Tacos
Antes de generar el PDF, es obligatorio definir la agrupación física de las
participaciones:
• Tamaño del Taco: Selección de unidades por bloque, siendo lo habitual 25,
30 o 50 participaciones.
• Impacto en la Salida: Esta configuración determinará cómo se organizará
el archivo PDF final para facilitar el grapado y la distribución secuencial. El
PDF tiene que organizarse en modo talonario para que el corte permita que
las participaciones queden organizadas de menor a mayor.
2.5. Modelos Precargados y Plantillas
Para usuarios que no desean realizar un diseño desde cero o que utilizan soportes
externos:
• Plantillas Externas: Acceso a modelos precargados de empresas como
Thyke Gestión o ASG.
• Zonas Predefinidas: Estas plantillas vienen con espacios fijos donde el
sistema insertará automáticamente los campos necesarios, los códigos
QR, logos y textos de la entidad.
Punto 3: Editor Gráfico y Capas de Seguridad
Este es el núcleo creativo y de seguridad de la plataforma. El editor debe equilibrar
la libertad de diseño con la protección absoluta de los datos que permiten la
validación y el cobro de las participaciones.
3.1. Capas del Sistema (Bloqueadas e Inamovibles)
Para evitar errores humanos, omisiones o intentos de fraude, el sistema inyecta
automáticamente una serie de elementos que no pueden ser borrados, movidos,
ocultados ni alterados por imágenes o textos superpuestos. Estos datos se
obtienen directamente del set configurado previamente.
• Código QR Único: Se genera un código individual para cada participación
basado en los dígitos de control creados al configurar el set. Este QR es el
vínculo esencial con la base de datos de PARTILOT para el escrutinio y
cobro.
• Datos del Set (Lotería): Se muestran de forma obligatoria los números
jugados, el importe destinado a lotería y el importe del donativo. El sistema
valida que el donativo no supere el 20% permitido por la normativa.
• Identificadores Dobles: El número de participación se genera en formato
secuencial (ej. Set-Consecutivo, como 1-00001). Este campo aparece dos
veces por diseño: una en la matriz (que conserva la entidad) y otra en la
participación física (que recibe el cliente).
• Campos Fijos para Imprenta: Si el diseño lo realiza la imprenta externa, el
sistema obliga a incluir el código QR, el número de referencia y los
contadores tanto en la matriz como en la participación.
• Numero de referencia: el numero que identifica cada participación de
manera única y que esta contenido también en el QR que incluye la
participación.
3.2. Herramientas de Edición Libre
Una vez garantizados los elementos de seguridad, la entidad o administración
dispone de un abanico de herramientas para personalizar la estética de la
participación:
• Control de Texto Avanzado: Permite modificar el tamaño, tipo de letra,
color y la orientación de los textos no bloqueados para ajustarlos a la
imagen corporativa.
• Gestión Multimedia: * Fondos: Subida de imágenes de fondo para el
frontal de la participación.
o Imágenes Sueltas: Posibilidad de insertar logotipos de la entidad,
de patrocinadores o iconos de redes sociales.
o Calidad de Imagen: El sistema advierte que los archivos deben
tener un mínimo de 150 ppp; de lo contrario, la imagen resultará
pixelada en la impresión física.
• Manipulación de Capas y Visualización:
o Ordenación: Capacidad de enviar elementos hacia adelante o hacia
atrás para gestionar superposiciones.
o Zoom de Precisión: Herramienta para acercar o alejar la vista y
trabajar en detalles minuciosos del diseño.
• Contadores Secuenciales Adicionales: Funcionalidad para incluir
contadores personalizados (ej. si el set es de 3.000 participaciones, se
pueden crear rangos del 1 al 1000, 1001 al 2000, etc.). Esto resulta útil para
gestionar sorteos paralelos, como cestas de regalo, vinculados a la
participación.
3.3. Persistencia y Reutilización del Diseño
• Guardado Automático: El sistema almacena el progreso de la edición de
forma dinámica. Si el usuario abandona la sesión, puede retomar el trabajo
desde el último punto guardado o empezar de nuevo sobre la base previa.
• Biblioteca de Diseños Propios: Al iniciar un nuevo set, el sistema permite
recuperar diseños anteriores de la misma entidad, mostrando la fecha de
creación y el set de origen. Esto es especialmente útil para ampliaciones de
reservas en un mismo sorteo.
• Integración de Datos: Al cargar un diseño antiguo en un set nuevo, el
sistema actualiza automáticamente los campos variables (números,
importes, cantidad de participaciones) manteniendo la estructura gráfica.
Punto 4: Diseño de Tacos (Tapas) y Reversos
Esta fase es crucial para la organización física de las participaciones. El sistema
automatiza la creación de elementos que facilitan tanto la entrega a vendedores
como la inclusión de información legal o publicitaria.
4.1. Diseño y Configuración de Tapas de Tacos
La tapa es la portada de cada bloque de participaciones (por ejemplo, de 50
unidades). Su diseño no es solo estético, sino funcional para la gestión interna de
la entidad.
• Edición Gráfica de la Tapa: Se permite realizar un diseño personalizado
para la portada del taco utilizando las mismas herramientas que en el
frontal (imágenes de fondo, logos y textos).
• Código BIDI de Gestión: El sistema genera e inserta automáticamente un
código BIDI único en la tapa que contiene la siguiente información:
o Identificación de la Entidad.
o Número de taco y total de tacos del set.
o Rango de participaciones: Indica exactamente de qué número a
qué número de participación contiene el taco (ej. del 01-00001 al
01-00050).
o Datos económicos: Precio de la participación, importe de lotería y
donativo.
o URL de pago: Enlace directo (www.sipart/pago_participaciones)
para facilitar el proceso. (leyendo el QR con un lector externo dirige a
la web de partilot para comprobar la participación).
• Utilidad con la App Partilot: Este código QR permite que el gestor de la
entidad asigne el taco completo a un vendedor de forma inmediata
escaneándolo con la App, evitando tener que registrar cada participación
individualmente o teclear rangos manuales.
4.2. Diseño del Reverso (Cara Posterior)
El sistema ofrece la posibilidad de configurar la parte trasera de la participación a
nivel gráfico para aprovechar todo el soporte físico.
• Herramientas Homogéneas: Se utilizan las mismas herramientas de
edición que en el frontal (control de texto, subida de imágenes y
manipulación de capas).
• Contenido Sugerido: Esta sección suele utilizarse para:
o Bases Legales: Texto informativo sobre el sorteo y la entidad
depositaria.
o Publicidad: Espacio para logotipos de patrocinadores que ayuden a
sufragar los costes.
o Información de Cobro: Textos personalizados sobre dónde y
cuándo cobrar premios de forma presencial.
• Respeto a la Matriz: El diseño del reverso se ajusta automáticamente para
respetar la zona de la matriz, asegurando que al separar la participación no
se pierda información crítica.
4.3. Validación del Conjunto
Antes de proceder a la generación de archivos, el sistema verifica que:
• El diseño de la tapa corresponda con el tamaño de taco definido en el
Punto 3 (ej. si se definieron tacos de 50, el rango en el OK se ajustará a esa
cifra).
• Si se ha incluido reverso, este mantenga la coherencia con las dimensiones
y sangres configuradas en la maquetación técnica.
Punto 5: Generación y Modelos de Salida
El sistema finaliza el flujo de impresión convirtiendo el diseño digital en archivos
de producción profesional que garantizan la integridad de los datos de SIPART.
5.1. Generación del Triple PDF
Una vez que el diseño es validado y guardado, el sistema genera de forma
automática tres documentos PDF independientes para facilitar la logística en la
imprenta:
• PDF de Participaciones (Anversos): Incluye la totalidad de las
participaciones generadas en modo talonario. Están organizadas de tal
manera que, tras el corte y apilado, el orden secuencial se mantenga
correctamente para su distribución.
• PDF de Tapas de Tacos: Genera las portadas de los bloques según la
cantidad de participaciones definida (ej. 25 o 50). Cada tapa incluye su
propio código BIDI de gestión y el rango específico de participaciones que
contiene el taco y el numero de taco del total de tacos.
• PDF de Imagen Trasera (Reversos): Si se configuró un reverso, se genera
un archivo independiente con el diseño de la parte posterior, ajustado para
coincidir perfectamente con la matriz del anverso.
5.2. Vías de Producción Disponibles
El usuario debe decidir cómo se materializarán físicamente estos archivos:
• Descarga Manual (Autogestión):
o La administración o entidad descarga los archivos directamente
desde el panel de control.
o Esta opción no tiene coste de gestión de impresión adicional en la
plataforma.
o Permite imprimir localmente o enviar los archivos a una imprenta de
confianza externa a la red de Partilot.
• Envío a Imprenta Partilot:
o El sistema envía automáticamente el set de archivos al panel de
control de la imprenta colaboradora.
o La imprenta se encarga del proceso integral: impresión, corte,
grapado de tacos y envío físico a la dirección indicada.
o Esta vía conlleva un coste de impresión que se calcula
dinámicamente y debe abonarse mediante TPV antes del envío.
5.3. Auditoría y Seguridad de Exportación (La Lupa)
Para proteger la propiedad intelectual y evitar duplicaciones no autorizadas, el
sistema implementa un estricto control de seguimiento:
• Registro de Actividad: En la sección "Participaciones", se habilita una
acción rápida (icono de lupa) que permite consultar qué gestor realizó la
descarga o envío, indicando la fecha y hora exacta.
• Bloqueo de Datos: Tras la primera exportación exitosa de los PDF, el
sistema bloquea cualquier cambio en los datos fundamentales del set.
Esto es vital para mantener la integridad de los códigos QR ya generados y
evitar que existan participaciones físicas con datos distintos a los
registrados en la base de datos.
5.4. Caducidad y Enlaces
• En el caso de envíos externos, el sistema genera enlaces con caducidad (4
semanas para el acceso a diseño delegado) para garantizar que la
información sensible no permanezca expuesta indefinidamente en
servidores externos.
Punto 6: Paneles de Gestión y Flujos de Funcionamiento
Este nivel de administración es exclusivo para el equipo de PARTILOT y no es
visible para las administraciones de lotería ni para las entidades. Su función es
definir las reglas de negocio que garantizan que el sistema sea rentable y
técnicamente viable.
6.1. Configuración Maestra de Variables y Costes
El Super Admin debe introducir los valores numéricos que alimentarán el motor de
cálculo automático de presupuestos:
• Gestión de Variables de Precio por Formato:
o Precio Base en Pliego A3 (6 participaciones): Importe fijo por cada
participación impresa en hoja A3 con retícula 3x2.
o Precio Base en Pliego A3 (8 participaciones): Importe fijo por cada
participación impresa en hoja A3 con retícula 4x2.
o Suplemento por Diseño: Coste adicional fijo aplicado cuando la
entidad delega el diseño a la imprenta de PARTILOT.
• Escalado de Tacos:
o Precio por Taco de 50: Tarifa estándar por el manipulado (corte y
grapado) de tacos de 50 unidades.
o Precio por Taco Especial (< 50): Tarifa diferenciada para tacos de 25
o 30 unidades, compensando el coste de manipulado para tiradas
menores. (con un mínimos de 10 participaciones por taco).
6.2. Algoritmo de Margen PARTILOT
El sistema calcula el precio final que se mostrará al cliente (Administración o
Entidad) integrando el beneficio de la plataforma:
• Campo de Porcentaje de Aplicación: Selector para definir el porcentaje
(ej. 15% o 20%) que se suma a los costes de la imprenta.
• Lógica de Cálculo: El sistema aplica la fórmula: Precio Final = (Coste
Imprenta + Coste Diseño) * (% Margen PARTILOT).
6.3. Parámetros Técnicos de Producción (Imprenta)
Para asegurar que el diseño sea compatible con la maquinaria física, el Super
Admin predefine los límites del editor gráfico:
• Márgenes de Seguridad: Distancia mínima en milímetros desde el borde
del papel hasta los elementos de diseño.
• Sangre : Exceso de imagen (habitualmente 3mm) para evitar bordes
blancos tras el corte.
• Distancia de Matriz (Perforación): Ubicación exacta de la línea de puntos
para separar la participación de su matriz física.
6.4. Panel de Auditoría y Facturación Global
Este repositorio centraliza toda la actividad económica y documental del módulo:
• Trazabilidad de Trabajos: Listado maestro con ID de pedido, fecha/hora,
entidad/administración solicitante e imprenta asignada.
• Repositorio de Facturas: Sistema donde se almacenan o enlazan las
facturas emitidas a la entidad y las facturas recibidas de la imprenta para el
control contable de PARTILOT.
6.5. El Flujo de Cobro por Gestión
Independientemente de si se imprime en PARTILOT o no, el sistema utiliza este
punto para ejecutar el cobro por el uso de la plataforma:
• Momento del Cobro: Se realiza una vez finalizada la edición del set, antes
de permitir la descarga de los PDF o el envío a imprenta.
• Identificación del Pagador: El sistema utiliza el "switch" definido en el
Punto 1 para lanzar el cargo por TPV a la Administración o a la Entidad.
Punto 7: Paneles de la Imprenta y Circuito de Aprobación
El sistema PARTILOT actúa como un puente técnico entre la entidad y la imprenta.
Para que este proceso sea eficaz, se restringen ciertas libertades creativas en
favor de la viabilidad técnica.
7.1. Interfaz y Acceso de la Imprenta
Cuando se delega un trabajo, el sistema genera un entorno de trabajo controlado
para el taller:
• Acceso Temporal: La imprenta recibe un enlace con credenciales
exclusivas válidas por un máximo de 4 semanas.
• Visualización de Datos Críticos: El panel muestra los datos del sorteo que
la imprenta debe integrar: números jugados, fecha y número del sorteo, e
importes de lotería y donativo.
• Control de Pedidos: La imprenta dispone de un listado con los trabajos
realizados, fechas, entidades clientes, cantidad de participaciones e
importes cobrados.
7.2. Proceso de Diseño delegado
Si la imprenta es la encargada de realizar el diseño, el flujo sigue estos pasos
estrictos:
• Recepción de Activos: El taller descarga los logotipos y archivos subidos
por la entidad (con el requisito de calidad de 150 ppp) y lee las
observaciones/textos indicadas por la entidad/administración (redes
sociales, teléfonos, etc.).
• Campos Fijos Obligatorios: Al diseñar, el sistema obliga a la imprenta a
mantener visibles y sin obstrucciones el código QR único, el número de
referencia y los contadores en matriz y participación.
• Publicación de Muestra: La imprenta sube una imagen del diseño final al
panel de gestión de la entidad para su revisión.
7.3. Circuito de "Luz Verde" y Aprobación
• Validación por el Cliente: La administración o entidad recibe una
notificación para revisar el diseño subido por la imprenta.
• Aprobación: Una vez que el cliente da su "luz verde", el sistema habilita a la
imprenta la generación de los tres PDF definitivos.
• Generación de Salida: La imprenta utiliza el panel para generar
automáticamente los archivos de producción (Anversos, Tapas con BIDI y
Reversos).
7.4. Flujo Financiero y Facturación
• Pago por TPV: El sistema procesa el cobro a través de la plataforma
(ejecutado por la entidad o administración según el switch de gestión) en el
momento de confirmar el envío a imprimir.
• Liquidación: PARTILOT recibe el pago del cliente y, posteriormente, la
imprenta factura directamente a PARTILOT por los trabajos realizados.
• Repositorio de Facturas: Tanto PARTILOT como el cliente tienen acceso a
los enlaces de descarga de las facturas correspondientes en sus
respectivos paneles.
Punto 8: Seguridad Operativa y Bloqueo Final
El objetivo de este punto es "congelar" el estado del diseño y la configuración del
set para evitar fraudes, errores en el cobro de premios o alteraciones en la imagen
de la participación una vez que el proceso de venta ha comenzado.
8.1. Bloqueo Automático por Actividad Operativa
El sistema monitoriza en tiempo real las acciones realizadas sobre el set de
participaciones. La edición del diseño se bloquea permanentemente en el
momento en que ocurre cualquiera de los siguientes hitos:
• Venta de Participación: En cuanto se registra la primera venta (física o
digital).
• Asignación a Vendedor: En el instante en que la disponibilidad total del set
se descuenta debido a la asignación de un taco o participación a un
vendedor a través de la App o el panel.
8.2. Condiciones para la Re-edición
Para que el sistema vuelva a habilitar las herramientas de edición sobre un set que
ya ha sido impreso o asignado, se deben cumplir condiciones de integridad total:
• Recopilación de Existencias: La totalidad de las participaciones físicas
que componen el set deben estar nuevamente en estado "Disponible" o
"Disponible DV" (devolución del vendedor).
• Gestión de Devoluciones: La entidad debe recopilar todas las
participaciones previamente adjudicadas a vendedores y registrarlas como
devueltas en el sistema. Solo cuando el contador de participaciones en
manos de terceros sea cero, se desbloqueará el panel de edición.
8.3. Restricción Irreversible para Participaciones Digitales
Existe una excepción técnica fundamental respecto a la naturaleza de la venta:
• Imposibilidad de Devolución: A diferencia de las físicas, las
participaciones digitales no admiten el proceso de "devolución definitiva" a
la entidad.
• Excepcion de devolución: En el caso de que se haya vendido
participaciones a un menor y al ir a registrase dicho menor, el sistema no
puede dejar que se registre y por tanto la participación no se asignaría. En
este caso, la plataforma comprobaría en los log, si alguien se ha intentado
registrar y no ha podido por ser menor de edad. Si corresponde a el usuario
que compro la participación, el sistema dejara que la entidad pueda
pasarla a disponible y anular el enlace para poder reintegrar el dinero de la
compra al menor.
• Bloqueo Permanente: Si el set contiene participaciones digitales y se ha
realizado una venta (por tanto ya hay alguna vendida), el diseño no se
podrá volver a editar en ninguna circunstancia, ya que no es posible
recuperar esas participaciones para garantizar que el nuevo diseño
coincida con lo ya vendido digitalmente.
8.4. Integridad del Sorteo y Escrutinio
Este bloqueo garantiza la seguridad en las fases posteriores al sorteo:
• Validación de Premios: Asegura que los datos impresos (números,
importes y QR) coincidan exactamente con la configuración del sorteo
procesada por PARTILOT.
• Protección del QR: Al impedir cambios post-venta, se asegura que el
código QR único siempre dirija a la información correcta del set y la reserva
original, evitando conflictos durante el cobro de premios.