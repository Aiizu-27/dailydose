INSERT INTO CATEGORIAS (ID_CATEGORIA, NOMBRE_CATEGORIA) VALUES
(1, 'Bakery artesanal'),
(2, 'Brunch & Salado'),
(3, 'Cafés con leche'),
(4, 'Cafés de autor'),
(5, 'Cafés fríos'),
(6, 'Espresso Bar'),
(7, 'Extras'),
(8, 'Leches alternativas'),
(9, 'Pastelería de autor'),
(10, 'Té y otras bebidas');


INSERT INTO PROVEEDORES (ID_PROVEEDOR, NOMBRE, TELEFONO, EMAIL, DIRECCION) VALUES
(1, 'Nomad Coffee Roasters', '611223344', 'pedidos@nomadcoffee.es', 'Calle de la Abundancia 12, Poblenou, Barcelona'),
(2, 'Obrador San Francisco', '655443322', 'logistica@obradorsf.com', 'Plaza de San Francisco 4, La Latina, Madrid'),
(3, 'Cerámicas del Sur', '633998877', 'info@ceramicasdelsur.es', 'Polígono Industrial Torrecilla, Nave 4B, Córdoba');


INSERT INTO ESPECIALIDAD_ACTUAL (ID_ESPECIALIDAD, ORIGEN_GRANO, NOTAS_CATA, TUESTE, METODO_FILTRO, DESCRIPCION_FILTRO, SEASONAL_NOMBRE, SEASONAL_DESCRIPCION, FECHA_INICIO, FECHA_FIN) VALUES
(1, 'Etiopía Yirgacheffe', 'Notas florales muy pronunciadas, jazmín, lima cítrica, té negro y cuerpo ligero.', 'Ligero (Light)', 'V60 / Chemex / Aeropress', 'Proceso lavado tradicional con clasificación manual exhaustiva y fermentación controlada en tanques de agua durante 24 horas.', 'Spring Blossom', 'Nuestra selección exclusiva para primavera, buscando un perfil en taza extremadamente limpio, floral, fresco y con una acidez brillante.', '2026-03-01', '2026-06-01'),
(2, 'Colombia Finca El Paraíso', 'Notas intensas y exóticas a maracuyá, yogur de fresa, gominola y chocolate blanco.', 'Medio (Omniroast)', 'Espresso / Flujo Libre', 'Proceso innovador de doble fermentación anaeróbica con levaduras específicas, seguido de un proceso de choque térmico para fijar los sabores.', 'Tropical Punch', 'Una explosión de sabores frutales y dulces diseñada para destacar tanto en espresso solo como combinada con leches tradicionales o alternativas.', '2026-05-01', '2026-08-01');


INSERT INTO PRODUCTOS (ID_PRODUCTO, NOMBRE, ID_CATEGORIA, ID_ESPECIALIDAD, PRECIO, STOCK, ID_PROVEEDOR, RUTA_IMAGEN) VALUES
(1, 'Cruasán de Mantequilla', 1, NULL, 2.50, 25, 2, 'assets/img/productos/croissant_mantequilla.png'),
(2, 'Cinnamon Roll de Canela', 1, NULL, 3.80, 4, 2, 'assets/img/productos/'),
(3, 'Tostada de Aguacate y Feta', 2, NULL, 6.50, 15, 2, 'assets/img/productos/brunch_aguacate.jpg'),
(4, 'Sandwich de Pastrami Premium', 2, NULL, 8.90, 10, 2, 'assets/img/productos/brunch_pastrami.jpg'),
(5, 'Flat White', 3, 2, 3.20, 450, 1, 'assets/img/productos/cafe_flatwhite.jpg'),
(6, 'Café Latte Tradicional', 3, 2, 3.00, 500, 1, 'assets/img/productos/cafe_latte.jpg'),
(7, 'Cappuccino Cremoso', 3, 2, 3.20, 400, 1, 'assets/img/productos/cafe_cappuccino.jpg'),
(8, 'Rose Latte Edición Especial', 4, 2, 4.20, 80, 1, 'assets/img/productos/autor_rose.jpg'),
(9, 'Espresso Tonic de Lavanda', 4, 2, 4.50, 60, 1, 'assets/img/productos/autor_tonic.jpg'),
(10, 'Iced Latte Supremo', 5, 2, 3.50, 300, 1, 'assets/img/productos/frios_icedlatte.jpg'),
(11, 'Cold Brew Macerado 12h', 5, 1, 4.00, 40, 1, 'assets/img/productos/frios_coldbrew.jpg'),
(12, 'Espresso Solo', 6, 2, 1.80, 600, 1, 'assets/img/productos/espresso_solo.jpg'),
(13, 'Espresso Doble (Doppio)', 6, 2, 2.20, 600, 1, 'assets/img/productos/espresso_doble.jpg'),
(14, 'Espresso Macchiato', 6, 2, 2.00, 550, 1, 'assets/img/productos/espresso_macchiato.jpg'),
(15, 'Café Filtro V60 de Origen', 6, 1, 4.50, 60, 1, 'assets/img/productos/espresso_v60.jpg'),
(16, 'Extra Shot Espresso', 7, 2, 0.80, 999, 1, 'assets/img/productos/extra_shot.jpg'),
(17, 'Sirope de Vainilla Madagascar', 7, NULL, 0.50, 100, 1, 'assets/img/productos/extra_sirope.jpg'),
(18, 'Suplemento Bebida de Avena', 8, NULL, 0.40, 200, 1, 'assets/img/productos/leche_avena.jpg'),
(19, 'Suplemento Bebida de Almendra', 8, NULL, 0.40, 150, 1, 'assets/img/productos/leche_almendra.jpg'),
(20, 'Tarta de Queso con Pistacho', 9, NULL, 5.50, 8, 2, 'assets/img/productos/postre_cheesecake.jpg'),
(21, 'Cookie de Chocolate y Sal Maldón', 9, NULL, 2.90, 30, 2, 'assets/img/productos/postre_cookie.jpg'),
(22, 'Té Matcha Latte Japonés', 10, NULL, 4.20, 120, 1, 'assets/img/productos/bebidas_matcha.jpg'),
(23, 'Kombucha de Jengibre Natural', 10, NULL, 3.90, 45, 1, 'assets/img/productos/bebidas_kombucha.jpg');


INSERT INTO INVENTARIO (ID_MOVIMIENTO, ID_PRODUCTO, CANTIDAD, FECHA, TIPO_MOVIMIENTO) VALUES
(1, 1, 30, '2026-05-19 08:00:00', 'ENTRADA'),
(2, 2, 12, '2026-05-19 08:00:00', 'ENTRADA'),
(3, 2, 8, '2026-05-19 21:30:00', 'AJUSTE'),
(4, 4, 15, '2026-05-20 08:15:00', 'ENTRADA'),
(5, 4, 2, '2026-05-20 14:00:00', 'AJUSTE'),
(6, 20, 10, '2026-05-20 09:00:00', 'ENTRADA'),
(7, 20, 1, '2026-05-20 17:45:00', 'AJUSTE'),
(8, 23, 24, '2026-05-20 11:00:00', 'ENTRADA');


INSERT INTO MESAS (ID_MESA, NUMERO_MESA, CAPACIDAD, UBICACION, ESTADO) VALUES
(1, 1, 2, 'SALA - Ventanal QR-M1', 'LIBRE'),
(2, 2, 2, 'SALA - Ventanal QR-M2', 'LIBRE'),
(3, 3, 4, 'SALA - Centro USB-M3', 'LIBRE'),
(4, 4, 4, 'SALA - Centro M4', 'LIBRE'),
(5, 5, 2, 'SALA - Lateral M5', 'LIBRE'),
(6, 6, 4, 'SALA - Espacio Lounge S6', 'LIBRE'),
(7, 7, 8, 'SALA DE REUNIONES - Mesa Presidencial', 'LIBRE');


INSERT INTO RECOMPENSAS (ID_RECOMPENSA, NOMBRE, DESCRIPCION, COSTE_PUNTOS, TIPO, STOCK, SOLO_EMPLEADOS) VALUES
(1, 'Espresso o Macchiato Gratis', 'Disfruta de un café solo o cortado de nuestro Espresso Bar sin coste.', 200, 'PRODUCTO', 150, FALSE),
(2, 'Cookie de Chocolate y Sal', 'Canjeable por una de nuestras cookies artesanal de autor recién horneadas.', 300, 'PRODUCTO', 40, FALSE),
(3, 'Flat White o Cappuccino Gratis', 'Elige tu café con leche de especialidad favorito en tamaño estándar.', 350, 'PRODUCTO', 100, FALSE),
(4, 'Cinnamon Roll de Regalo', 'Endulza tu día con nuestro famoso rollo de canela artesanal.', 400, 'PRODUCTO', 8, FALSE),
(5, 'Espresso Tonic de Lavanda', 'Prueba uno de nuestros cafés de autor fríos más exclusivos.', 450, 'PRODUCTO', 25, FALSE),
(6, 'Taza Cerámica Oficial', 'Taza de cerámica de edición limitada DailyDose hecha a mano.', 1200, 'PRODUCTO', 5, FALSE),
(7, 'Sudadera Staff DailyDose', 'Incentivo exclusivo de marca para el personal de sala y barra.', 2000, 'PRODUCTO', 10, TRUE);