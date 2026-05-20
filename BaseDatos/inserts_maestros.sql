-- ========================================================
-- 1. TABLA: CATEGORIAS
-- ========================================================
INSERT INTO CATEGORIAS (ID_CATEGORIA, NOMBRE_CATEGORIA, DESCRIPCION) VALUES
(1, 'Café de Especialidad', 'Bebidas de café con puntuación SCA > 80, espresso y filtros.'),
(2, 'Bollería Artesanal', 'Repostería y panadería horneada a diario en nuestro obrador.'),
(3, 'Bebidas Frías', 'Refrescos naturales, iced lattes, tés fríos y kombuchas.'),
(4, 'Merchandising y Grano', 'Bolsas de café de origen en grano o molido y tazas oficiales.');

-- ========================================================
-- 2. TABLA: PROVEEDORES
-- ========================================================
INSERT INTO PROVEEDORES (ID_PROVEEDOR, NOMBRE, CONTACTO, TELEFONO, EMAIL) VALUES
(1, 'Nomad Coffee Roasters', 'Alejandro M.', '611223344', 'pedidos@nomadcoffee.es'),
(2, 'Obrador San Francisco', 'Sofía G.', '655443322', 'logistica@obradorsf.com'),
(3, 'Cerámicas del Sur', 'Pablo R.', '633998877', 'info@ceramicasdelsur.es');

-- ========================================================
-- 3. TABLA: ESPECIALIDAD_ACTUAL (Metadatos de café stacional)
-- ========================================================
INSERT INTO ESPECIALIDAD_ACTUAL (ID_ESPECIALIDAD, ORIGEN_GRANO, NOTAS_CATA, TUESTE, METODO_FILTRO, SEASONAL_NAME) VALUES
(1, 'Etiopía Yirgacheffe', 'Notas florales, jazmín, lima cítrica y cuerpo ligero.', 'Ligero', 'V60 / Chemex', 'Spring Blossom'),
(2, 'Colombia Finca El Paraíso', 'Notas intensas a maracuyá, yogur de fresa y chocolate blanco.', 'Medio', 'Espresso / Aeropress', 'Tropical Punch');

-- ========================================================
-- 4. TABLA: PRODUCTOS (Catálogo con stock real)
-- ========================================================
INSERT INTO PRODUCTOS (ID_PRODUCTO, ID_CATEGORIA, ID_PROVEEDOR, ID_ESPECIALIDAD, NOMBRE, PRECIO, STOCK, RUTA_IMAGEN) VALUES
(1, 1, 1, 2, 'Espresso Doble', 2.20, 500, 'assets/img/productos/espresso.jpg'),
(2, 1, 1, 2, 'Flat White', 3.20, 450, 'assets/img/productos/flatwhite.jpg'),
(3, 1, 1, 1, 'Café de Filtro V60', 4.50, 60, 'assets/img/productos/v60.jpg'),
(4, 2, 2, NULL, 'Cruasán de Mantequilla', 2.50, 25, 'assets/img/productos/cruasan.jpg'),
(5, 2, 2, NULL, 'Cinnamon Roll de Canela', 3.80, 4, 'assets/img/productos/cinnamon.jpg'), -- Stock bajo para saltar alertas
(6, 3, 1, NULL, 'Iced Latte Supremo', 3.50, 200, 'assets/img/productos/icedlatte.jpg'),
(7, 4, 1, 1, 'Bolsa Etiopía Grano 250g', 14.50, 12, 'assets/img/productos/bolsa_etiopia.jpg'),
(8, 4, 3, NULL, 'Taza Cerámica DailyDose', '12.00', 3, 'assets/img/productos/taza.jpg'); -- Stock bajo para saltar alertas

-- ========================================================
-- 5. TABLA: RECOMPENSAS (Premios del catálogo de puntos)
-- ========================================================
INSERT INTO RECOMPENSAS (ID_RECOMPENSA, NOMBRE, DESCRIPCION, COSTE_PUNTOS, TIPO, STOCK, SOLO_EMPLEADOS) VALUES
(1, 'Café Clásico Gratis', 'Canjeable por un Espresso, Macchiato o Cappuccino.', 15, 'Consumición', 150, FALSE),
(2, 'Cinnamon Roll de Regalo', 'Endulza tu día con nuestro famoso rollo de canela.', 35, 'Consumición', 8, FALSE),
(3, 'Taza Oficial Exclusiva', 'Taza de cerámica de edición limitada para coleccionistas.', 120, 'Regalo', 4, FALSE);

-- ========================================================
-- 6. TABLA: MESAS (Mapa físico de la sala)
-- ========================================================
INSERT INTO MESAS (ID_MESA, NUMERO_MESA, CAPACIDAD, UBICACION, ESTADO) VALUES
(1, 1, 2, 'Ventanal Principal Izquierda', 'LIBRE'),
(2, 2, 2, 'Ventanal Principal Derecha', 'LIBRE'),
(3, 3, 4, 'Sofá Confort Púrpura', 'LIBRE'),
(4, 4, 4, 'Centro de Sala Principal', 'LIBRE'),
(5, 5, 1, 'Barra Puesto Alto 1', 'LIBRE');

-- ========================================================
-- 7. TABLA: USUARIOS (Censo único con credenciales)
-- ========================================================
-- Nota: Las contraseñas puestas son simuladas, en PHP usarás password_hash()
INSERT INTO USUARIOS (ID_USUARIO, NOMBRE, APELLIDOS, EMAIL, CONTRASENA, CAMBIAR_PASSWORD, FECHA_REGISTRO) VALUES
(1, 'Ana', 'Gobernadora (Admin)', 'admin@daily-dose.es', '$2y$10$Z3U6R3o0eU1tcm8uU2VjdWVybyBwYXNzd29yZCBhZG1pbg==', FALSE, '2026-01-01 08:00:00'),
(2, 'Carlos', 'Martínez (Camarero)', 'carlos@daily-dose.es', '$2y$10$U3RhZmZDYW1hcmVybzIwMjYgcGFzc3dvcmQgc3RhZmY=', FALSE, '2026-01-15 10:00:00'),
(3, 'Lucía', 'Fernández (Cliente)', 'lucia.fer@gmail.com', '$2y$10$Q2xpZW50ZUx1Y2lhMjAyNiBwYXNzd29yZCBjbGllbnRl', FALSE, '2026-02-01 16:30:00'),
(4, 'Diego', 'Sánchez (Cliente)', 'diego.sanz@outlook.com', '$2y$10$RGllZ29TYW5jaGV6OTkgcGFzc3dvcmQgY2xpZW50ZTI=', FALSE, '2026-03-10 11:20:00'),
(5, 'Marta', 'Gómez (Cliente)', 'marta.gomez@yahoo.com', '$2y$10$TWFydGFHb21lejg4IHBhc3N3b3JkIGNsaWVudGUz', FALSE, '2026-04-05 19:15:00');

-- ========================================================
-- 8. TABLA: EMPLEADOS
-- ========================================================
INSERT INTO EMPLEADOS (ID_EMPLEADO, ID_USUARIO, PUESTO, SALARIO, TURNO, FECHA_CONTRATACION) VALUES
(1, 1, 'ADMIN', 2100.00, 'Completo', '2026-01-01'),
(2, 2, 'EMPLEADO', 1400.00, 'Tarde', '2026-01-15');

-- ========================================================
-- 9. TABLA: CLIENTES (Carteras de puntos fidelizados)
-- ========================================================
INSERT INTO CLIENTES (ID_CLIENTE, ID_USUARIO, TELEFONO, PUNTOS) VALUES
(1, 3, '600112233', 165), -- Lucía es cliente habitual (Tiene bastantes puntos)
(2, 4, '677889900', 42),  -- Diego consume de vez en cuando
(3, 5, '655443322', 8);   -- Marta es nueva en el club

-- ========================================================
-- 10. TABLA: HISTORIAL_PUNTOS (Auditoría de lealtad inicial)
-- ========================================================
INSERT INTO HISTORIAL_PUNTOS (ID_HISTORIAL, ID_CLIENTE, TIPO_MOVIMIENTO, CANTIDAD, MOTIVO, FECHA) VALUES
(1, 1, 'SUMA', 100, 'Bono de bienvenida por registro en app', '2026-02-01 16:31:00'),
(2, 1, 'SUMA', 65, 'Puntos acumulados en visitas de febrero y marzo', '2026-03-15 18:20:00'),
(3, 2, 'SUMA', 42, 'Puntos acumulados en consumos previos de barra', '2026-03-10 11:25:00'),
(4, 3, 'SUMA', 8, 'Puntos acumulados por compra de un Flat White', '2026-04-05 19:22:00');

-- ========================================================
-- 11. TABLA: PEDIDOS (Historial de tickets facturados)
-- ========================================================
-- Ticket 1: Cerrado por completo el mes pasado
INSERT INTO PEDIDOS (ID_PEDIDO, ID_MESA, ID_CLIENTE, ID_EMPLEADO, TOTAL, ESTADO, FECHA) VALUES
(1, 4, 1, 2, 9.20, 'ENTREGADO', '2026-04-10 17:15:00');

-- Ticket 2: Cerrado por completo la semana pasada
INSERT INTO PEDIDOS (ID_PEDIDO, ID_MESA, ID_CLIENTE, ID_EMPLEADO, TOTAL, ESTADO, FECHA) VALUES
(2, 2, 2, 2, 18.30, 'ENTREGADO', '2026-05-12 11:00:00');

-- ========================================================
-- 12. TABLA: DETALLE_PEDIDO (Líneas de los tickets anteriores)
-- ========================================================
-- Desglose del Ticket 1 (Total: 9.20)
INSERT INTO DETALLE_PEDIDO (ID_DETALLE, ID_PEDIDO, ID_PRODUCTO, CANTIDAD, PRECIO_UNITARIO, SUBTOTAL) VALUES
(1, 1, 2, 2, 3.20, 6.40), -- 2 Flat Whites
(2, 1, 4, 1, 2.50, 2.50); -- 1 Cruasán

-- Desglose del Ticket 2 (Total: 18.30)
INSERT INTO DETALLE_PEDIDO (ID_DETALLE, ID_PEDIDO, ID_PRODUCTO, CANTIDAD, PRECIO_UNITARIO, SUBTOTAL) VALUES
(3, 2, 3, 1, 4.50, 4.50), -- 1 Café de Filtro
(4, 2, 5, 1, 3.80, 3.80), -- 1 Cinnamon Roll
(5, 2, 7, 1, 14.50, 14.50); -- 1 Bolsa de café en grano

-- ========================================================
-- 13. TABLA: PAGOS (Arqueo contable de caja)
-- ========================================================
INSERT INTO PAGOS (ID_PAGO, ID_PEDIDO, TIPO_PAGO, MONTO, FECHA) VALUES
(1, 1, 'Tarjeta', 9.20, '2026-04-10 17:30:00'),
(2, 2, 'Efectivo', 18.30, '2026-05-12 11:22:00');
