En la práctica el caso que tenías (un solo número reservado, premios por categoría sin reintegro, digitales ya vendidas) debería ir bien. Para una batería de pruebas razonable, estos son los escenarios donde sí podría haber sorpresas:

Lo que ya cubre bien el fix
Un número por reserva (tu caso: 87488, 87490…) → físicas y digitales vendidas.
Cualquier categoría: centenas, anterior/posterior, extracciones, reintegro, etc.
Varios sets sobre el mismo número (digital + físico) → el desglose debería sumar ambos.
Casos donde conviene probar expresamente
Caso	Por qué
Reserva con varios números (ej. 87100 + 87488)	El mapeo participación → número usa participation_number como índice. En sets digitales ese número es global (1, 2, 3… pero puede empezar en otro valor), no siempre coincide con el índice del número reservado. Riesgo real si la reserva tiene 2+ números.
Venta digital pendiente (reserva_venta_digital)	No cuenta hasta que el comprador se registra o vincula el código → pasa a vendida.
Solo premio de reintegro / extracción	Antes ya funcionaba; útil como regresión para confirmar que no se ha roto.
Primer o segundo premio exacto	Comprobar que el importe y los décimos cuadran con la simulación del modal.
Varias entidades en la misma administración	Verificar que cada una ve solo sus participaciones vendidas y su premio.
Procesar y guardar el escrutinio	La pantalla previa ya está alineada; tras Procesar conviene revisar que los totales guardados coinen con lo visto antes.
Prueba mínima recomendada (5 minutos)
87488 (o similar): solo centenas/anterior, 5 digitales vendidas → badge verde + desglose sin vender físicas.
87490: reintegro + otras categorías → confirmar que sigue igual que antes.
Una venta digital pendiente (sin registrar) → no debe sumar en “Vendidas premiadas”.
Si tienes alguna reserva multi-número, vende en el segundo número y comprueba el premio.
Resumen
Reserva de 1 número (lo más habitual): no deberías tener problemas con ningún tipo de premio.
El punto más débil que queda en el código es reservas con varios números + sets digitales, por el tema del participation_number.
Lo demás son estados de venta (vendida vs pendiente) o comprobaciones de regresión.