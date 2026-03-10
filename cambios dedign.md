hola, tal como está el sistema hay que hacer unos cuantos cambios en la parte de diseño e impresion:

1. al momento de generar el thumbnail del diseño para las participaciones, se debera capturar unicamente la parte sin la matriz, ya que sería la seccion de la participacion que se le mostraria al usuario, la parte de la matriz es solo para la entidad que genera el diseño no hace falta mostrarla al usuario.

2. en las participaciones fisicas, se diseña la portada y trasera, en la portada debe estar un indicador de qr, tal como está en la participacion, ya que ahi es donde se colocará el qr para la lectura y asignacion de taco completo.

3. cuando entro a diseñar una participacion de un set digital, no debo ver la matriz, no me debe permitir diseñar ni la portada, ni la parte trasera, solo la participacion, y la parte final de configurar salida tampoco, la participacion debe ser la misma que se muestra en el thumbnail, sin la matriz. Al ser una participación digital, y no tener ni portada ni trasera no me deberá permitir imprimir nada, solo una opción para descargar en buena calidad una imagen de la imagen de la participación generada, esto con el fin de que puedan publicarla donde les parezca mejor, la imagen se debe generar en el momento utilizando la información guardada, para no generar carga en el disco duro.

4. en ambos casos, al estar diseñando, los elementos no deben poder salirse el margen superior, inferior, izquierdo o derecho que se configure, debe mantenerse dentro de ese cuadrado, tal como funciona en la parte trasera de la participacion el borde al lado derecho del cual no se puede pasar, pero en este caso tambien en los margenes (participacion, portada y trasera deben tener esa restriccion de margenes).

5. al diseñar la trasera, como tenemos ese borde derecho del cual no se puede pasar ni colocar nada encima, debe funcionar tambien para la imagen de fondo, la imagen de fondo solo se debe colocar en la parte "utilizable", teniendo en cuenta que ese borde es la parte trasera de las participaciones y coincide exactamente con la matriz (el tamaño de este margen cambia segun la configuracion de la matriz).

6. los botones de la parte de arriba (el de agregar texto, imagen, fondo, etc.) hay que centrarlos que todos queden alineados en el centro de la pantalla, y que no se salgan del borde de la pantalla. y en cada paso debe salir el tamaño que tiene segun la configuracion inicial: tipo de formato por ejemplo

- A3 (297 x 420) apaisado 3 x 2
- Margenes de la pagina
- Sangres
- Tamaño de la matriz (si aplica)
- Margenes por pagina, etc...

7. cuando genero un diseño de un set, si voy a realizar el diseño de otro set de la misma reserva, no se debe perder el diseño que ya tengo, debe guardarse y poder seguir trabajando en el mismo diseño. por ejemplo, si tengo la reserve_id = 1 y el set_id = 1, y voy a realizar el diseño de otro set de la misma reserva, no se debe perder el diseño que ya tenia de la anterior participacion, debe guardarse y poder seguir trabajando en el mismo diseño, ya que sería el mismo numero reservado. si es de otra reserva si debe ser un diseño nuevo, si ya tengo el diseño creado si me debe permitir editarlo.

8. con respecto a lo anterior, deberia tener a la mano una especia de listado de diseños que ya tengo, de la misma entidad, que me permita seleccionar el diseño que ya tengo y seguir trabajando en el mismo, sin tener que crear uno nuevo. cambiando unicamente el numero de reserva, que se coloque el de la reserva actual.

Esto de abajo te lo tengo que enviar con capturas para que te quedes mejor con la idea.
9. un cambio mas grande, antes de pasar a diseñar, debo poder elegir si es un "diseño", lo que me permite seguir con el flujo actual, o diseño e impresion externo, lo que me permite primeramente añadir un comentario en la parte superior, debajo una carga de archivos (dividido en 2 la pantalla, del lado izquierdo la carga de archivos, y del lado derecho el listado de archivos cargados), y despues el boton de siguiente, aqui tengo que colocar un correo para que a ese correo le llegue un enlace donde poder entrar a diseñar, el diseño externo es porque no lo quiero hacer yo sino que lo voy a delegar a otra persona, que es la persona que me va a hacer el diseño e impresion. esa persona al recibir el enlace (con token de seguridad) le debe permitir entrar a diseñar (con el mismo estilo que tenemos actualmente en diseño e impresión), y cuando termine ya me aparecerá el diseño creado y puedo imprimirlo. mientras la otra persona no haya guardado el diseño, no se debe poder imprimir, debe aparecer un mensaje de que el diseño está pendiente de ser creado.
Nota: Los archivos enviados no se adjuntaran por correo, al momento de que la persona entre al enlace proporcionado (enviado por email) tendrá la opción de descargar los archivos suministrados.