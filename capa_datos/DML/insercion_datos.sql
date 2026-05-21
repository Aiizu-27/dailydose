-- =============================================================================
-- POBLADO MAESTRO DE PERSONAL: USUARIOS Y EMPLEADOS (MÓDULO DE ACCESO)
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. INSERCIÓN EN TABLA MAESTRA: USUARIOS
-- Campos: ID_USUARIO, NOMBRE, APELLIDOS, EMAIL, CONTRASENA, CAMBIAR_PASSWORD, FECHA_REGISTRO
-- -----------------------------------------------------------------------------
INSERT INTO USUARIOS (ID_USUARIO, NOMBRE, APELLIDOS, EMAIL, CONTRASENA, CAMBIAR_PASSWORD, FECHA_REGISTRO) VALUES
-- Usuario 1: Administrador Global del Sistema
(1, 'Ana', 'Gobernadora Pérez', 'admin@daily-dose.es', '$2y$10$Z3U6R3o0eU1tcm8uU2VjdWVybyBwYXNzd29yZCBhZG1pbg==', FALSE, '2026-01-01 08:00:00'),

-- Usuarios 2 al 5: Los cuatro empleados operativos de la cafetería
(2, 'Carlos', 'Martínez Gómez', 'carlos.camarero@daily-dose.es', '$2y$10$U3RhZmZDYW1hcmVybzIwMjYgcGFzc3dvcmQgc3RhZmY=', FALSE, '2026-01-15 10:00:00'),
(3, 'Javier', 'Ruiz Estévez', 'javier.barista@daily-dose.es', '$2y$10$QmFyaXN0YUphdmkyMDI2bWFzdGVyY29mZmVlYmFzaXN0YQ==', FALSE, '2026-02-01 08:30:00'),
(4, 'María', 'López Santillana', 'maria.cocina@daily-dose.es', '$2y$10$Q29jaW5hTWFyaWEyMDI2Y2hlZmZzcGVjaWFsaWRhZA==', FALSE, '2026-03-01 07:00:00'),
(5, 'Laura', 'Castro Villanueva', 'laura.sala@daily-dose.es', '$2y$10$U2FsYUxhdXJhMjAyNnRwdmNvbWFuZGFzZmx1am93ZWI=', FALSE, '2026-05-10 11:15:00');

-- -----------------------------------------------------------------------------
-- 2. INSERCIÓN EN TABLA DE ESPECIALIZACIÓN: EMPLEADOS (Relación 1:1 con USUARIOS)
-- Campos: ID_EMPLEADO, ID_USUARIO, PUESTO, SALARIO, TURNO, FECHA_CONTRATACION
-- -----------------------------------------------------------------------------
INSERT INTO EMPLEADOS (ID_EMPLEADO, ID_USUARIO, PUESTO, SALARIO, TURNO, FECHA_CONTRATACION) VALUES
-- Vínculo del Usuario 1 como Administrador con privilegios totales
(1, 1, 'ADMIN', 2200.00, 'Completo', '2026-01-01'),

-- Vínculos de los Usuarios 2 al 5 como personal técnico de sala y barra
(2, 2, 'EMPLEADO', 1450.00, 'Tarde', '2026-01-15'),  -- Personal de Sala / Camarero
(3, 3, 'EMPLEADO', 1500.00, 'Mañana', '2026-02-01'), -- Barista especialista en café
(4, 4, 'EMPLEADO', 1600.00, 'Mañana', '2026-03-01'), -- Encargada de cocina y repostería
(5, 5, 'EMPLEADO', 1450.00, 'Tarde', '2026-05-10');  -- Refuerzo operativo de sala


-- =============================================================================
-- ACTUALIZACIÓN MAESTRA DE CATEGORÍAS (SEGÚN INTERFAZ DE USUARIO)
-- =============================================================================

-- Inyección del catálogo real de la barra lateral de DailyDose
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



-- =============================================================================
-- POBLADO MAESTRO DE LA TABLA: PRODUCTOS (SEGÚN CATEGORÍAS REALES)
-- =============================================================================

INSERT INTO PRODUCTOS (ID_PRODUCTO, NOMBRE, ID_CATEGORIA, ID_ESPECIALIDAD, PRECIO, STOCK, ID_PROVEEDOR, RUTA_IMAGEN) VALUES
-- 1. Bakery artesanal (ID_CATEGORIA = 1) -> Proveedor: Obrador San Francisco (2)
(1, 'Cruasán de Mantequilla', 1, NULL, 2.50, 25, 2, 'assets/img/productos/bakery_cruasan.jpg'),
(2, 'Cinnamon Roll de Canela', 1, NULL, 3.80, 4, 2, 'assets/img/productos/bakery_cinnamon.jpg'), -- Stock crítico (<5) para disparar el trigger

-- 2. Brunch & Salado (ID_CATEGORIA = 2) -> Proveedor: Obrador San Francisco (2)
(3, 'Tostada de Aguacate y Feta', 2, NULL, 6.50, 15, 2, 'assets/img/productos/brunch_aguacate.jpg'),
(4, 'Sandwich de Pastrami Premium', 2, NULL, 8.90, 10, 2, 'assets/img/productos/brunch_pastrami.jpg'),

-- 3. Cafés con leche (ID_CATEGORIA = 3) -> Proveedor: Nomad Coffee (1) | Café Estacional Espresso (2)
(5, 'Flat White', 3, 2, 3.20, 450, 1, 'assets/img/productos/cafe_flatwhite.jpg'),
(6, 'Café Latte Tradicional', 3, 2, 3.00, 500, 1, 'assets/img/productos/cafe_latte.jpg'),
(7, 'Cappuccino Cremoso', 3, 2, 3.20, 400, 1, 'assets/img/productos/cafe_cappuccino.jpg'),

-- 4. Cafés de autor (ID_CATEGORIA = 4) -> Proveedor: Nomad Coffee (1) | Café Estacional Espresso (2)
(8, 'Rose Latte Edición Especial', 4, 2, 4.20, 80, 1, 'assets/img/productos/autor_rose.jpg'),
(9, 'Espresso Tonic de Lavanda', 4, 2, 4.50, 60, 1, 'assets/img/productos/autor_tonic.jpg'),

-- 5. Cafés fríos (ID_CATEGORIA = 5) -> Proveedor: Nomad Coffee (1)
(10, 'Iced Latte Supremo', 5, 2, 3.50, 300, 1, 'assets/img/productos/frios_icedlatte.jpg'),
(11, 'Cold Brew Macerado 12h', 5, 1, 4.00, 40, 1, 'assets/img/productos/frios_coldbrew.jpg'), -- Usa el grano de filtro (1)

-- 6. Espresso Bar (ID_CATEGORIA = 6) -> Proveedor: Nomad Coffee (1) | Café Estacional Espresso (2)
(12, 'Espresso Solo', 6, 2, 1.80, 600, 1, 'assets/img/productos/espresso_solo.jpg'),
(13, 'Espresso Doble (Doppio)', 6, 2, 2.20, 600, 1, 'assets/img/productos/espresso_doble.jpg'),
(14, 'Espresso Macchiato', 6, 2, 2.00, 550, 1, 'assets/img/productos/espresso_macchiato.jpg'),
(15, 'Café Filtro V60 de Origen', 6, 1, 4.50, 60, 1, 'assets/img/productos/espresso_v60.jpg'), -- Usa el grano de filtro (1)

-- 7. Extras (ID_CATEGORIA = 7) -> Proveedor: Nomad Coffee (1)
(16, 'Extra Shot Espresso', 7, 2, 0.80, 999, 1, 'assets/img/productos/extra_shot.jpg'),
(17, 'Sirope de Vainilla Madagascar', 7, NULL, 0.50, 100, 1, 'assets/img/productos/extra_sirope.jpg'),

-- 8. Leches alternativas (ID_CATEGORIA = 8) -> Proveedor: Nomad Coffee (1)
(18, 'Suplemento Bebida de Avena', 8, NULL, 0.40, 200, 1, 'assets/img/productos/leche_avena.jpg'),
(19, 'Suplemento Bebida de Almendra', 8, NULL, 0.40, 150, 1, 'assets/img/productos/leche_almendra.jpg'),

-- 9. Pastelería de autor (ID_CATEGORIA = 9) -> Proveedor: Obrador San Francisco (2)
(20, 'Tarta de Queso con Pistacho', 9, NULL, 5.50, 8, 2, 'assets/img/productos/postre_cheesecake.jpg'),
(21, 'Cookie de Chocolate y Sal Maldón', 9, NULL, 2.90, 30, 2, 'assets/img/productos/postre_cookie.jpg'),

-- 10. Té y otras bebidas (ID_CATEGORIA = 10) -> Proveedor: Nomad Coffee (1)
(22, 'Té Matcha Latte Japonés', 10, NULL, 4.20, 120, 1, 'assets/img/productos/bebidas_matcha.jpg'),
(23, 'Kombucha de Jengibre Natural', 10, NULL, 3.90, 45, 1, 'assets/img/productos/bebidas_kombucha.jpg');



-- =============================================================================
-- POBLADO MAESTRO DE LA TABLA: INVENTARIO (AUDITORÍA DE ALMACÉN)
-- =============================================================================

-- Inyección de movimientos históricos (Entradas, Mermas e Incidencias)
INSERT INTO INVENTARIO (ID_MOVIMIENTO, ID_PRODUCTO, CANTIDAD, FECHA, TIPO_MOVIMIENTO) VALUES
-- Auditoría para Bakery artesanal (ID_PRODUCTO = 1 y 2)
(1, 1, 30, '2026-05-19 08:00:00', 'ENTRADA'), -- Recepción matutina de cruasanes
(2, 2, 12, '2026-05-19 08:00:00', 'ENTRADA'), -- Recepción inicial de Cinnamon Rolls
(3, 2, 8, '2026-05-19 21:30:00', 'MERMA'),   -- Retirada de producto no vendido al cierre

-- Auditoría para Brunch & Salado (ID_PRODUCTO = 4)
(4, 4, 15, '2026-05-20 08:15:00', 'ENTRADA'), -- Suministro diario de pan de pastrami
(5, 4, 2, '2026-05-20 14:00:00', 'INCIDENCIA'), -- Producto defectuoso o caída en cocina

-- Auditoría para Pastelería de autor (ID_PRODUCTO = 20)
(6, 20, 10, '2026-05-20 09:00:00', 'ENTRADA'), -- Entrada de tarta de queso con pistacho
(7, 20, 1, '2026-05-20 17:45:00', 'MERMA'),   -- Porción dañada durante el corte en barra

-- Auditoría para Bebidas y Grano (ID_PRODUCTO = 23)
(8, 23, 24, '2026-05-20 11:00:00', 'ENTRADA'); -- Reposición de botellas de Kombucha



-- =============================================================================
-- POBLADO MAESTRO DE LA TABLA: ESPECIALIDAD_ACTUAL (CAFÉ DE ORIGEN)
-- =============================================================================

-- Inyección de las fichas técnicas de los granos estacionales activos en 2026
INSERT INTO ESPECIALIDAD_ACTUAL (
    ID_ESPECIALIDAD, ORIGEN_GRANO, NOTAS_CATA, TUESTE, METODO_FILTRO, 
    DESCRIPCION_PROCESO, SEASONAL_NAME, SEASONAL_DESC, FECHA_INICIO, FECHA_FIN
) VALUES
(
    1, 
    'Etiopía Yirgacheffe', 
    'Notas florales muy pronunciadas, jazmín, lima cítrica, té negro y cuerpo ligero.', 
    'Ligero (Light)', 
    'V60 / Chemex / Aeropress', 
    'Proceso lavado tradicional con clasificación manual exhaustiva y fermentación controlada en tanques de agua durante 24 horas.', 
    'Spring Blossom', 
    'Nuestra selección exclusiva para primavera, buscando un perfil en taza extremadamente limpio, floral, fresco y con una acidez brillante.', 
    '2026-03-01', 
    '2026-06-01'
),
(
    2, 
    'Colombia Finca El Paraíso', 
    'Notas intensas y exóticas a maracuyá, yogur de fresa, gominola y chocolate blanco.', 
    'Medio (Omniroast)', 
    'Espresso / Flujo Libre', 
    'Proceso innovador de doble fermentación anaeróbica con levaduras específicas, seguido de un proceso de choque térmico para fijar los sabores.', 
    'Tropical Punch', 
    'Una explosión de sabores frutales y dulces diseñada para destacar tanto en espresso solo como combinada con leches tradicionales o alternativas.', 
    '2026-05-01', 
    '2026-08-01'
);


-- =============================================================================
-- POBLADO MAESTRO DE LA TABLA: PROVEEDORES (ABASTECIMIENTO DE MATERIA PRIMA)
-- =============================================================================

-- Inyección de los proveedores oficiales del catálogo de DailyDose
INSERT INTO PROVEEDORES (ID_PROVEEDOR, NOMBRE, TELEFONO, EMAIL, DIRECCION) VALUES
(
    1, 
    'Nomad Coffee Roasters', 
    '611223344', 
    'pedidos@nomadcoffee.es', 
    'Calle de la Abundancia 12, Poblenou, Barcelona'
),
(
    2, 
    'Obrador San Francisco', 
    '655443322', 
    'logistica@obradorsf.com', 
    'Plaza de San Francisco 4, La Latina, Madrid'
),
(
    3, 
    'Cerámicas del Sur', 
    '633998877', 
    'info@ceramicasdelsur.es', 
    'Polígono Industrial Torrecilla, Nave 4B, Córdoba'
);



-- =============================================================================
-- REPOBLADO DE LA TABLA: MESAS (NUEVA NOMENCLATURA POR ZONAS OPERATIVAS)
-- =============================================================================

-- Inyección de las 7 mesas oficiales de DailyDose
INSERT INTO MESAS (ID_MESA, NUMERO_MESA, CAPACIDAD, UBICACION, ESTADO) VALUES
-- Mesas con código QR en la zona inferior
(1, 1, 2, 'SALA - Ventanal QR-M1', 'LIBRE'),
(2, 2, 2, 'SALA - Ventanal QR-M2', 'LIBRE'),

-- Mesas de la zona central (Cargas USB y adyacentes)
(3, 3, 4, 'SALA - Centro USB-M3', 'LIBRE'),
(4, 4, 4, 'SALA - Centro M4', 'LIBRE'),
(5, 5, 2, 'SALA - Lateral M5', 'LIBRE'),

-- Mesa de la zona de confort / Sofás del fondo
(6, 6, 4, 'SALA - Espacio Lounge S6', 'LIBRE'),

-- Sala de Reuniones / Espacio exclusivo (Zona superior derecha del plano)
(7, 7, 8, 'SALA DE REUNIONES - Mesa Presidencial', 'LIBRE');


-- =============================================================================
-- POBLADO MAESTRO DE LA TABLA: RECOMPENSAS (CATÁLOGO DE FIDELIZACIÓN)
-- Regla de Negocio: 1€ de Gasto = 10 Puntos Acumulados
-- =============================================================================

-- Inyección de recompensas con costes adaptados al multiplicador x10
INSERT INTO RECOMPENSAS (ID_RECOMPENSA, NOMBRE, DESCRIPCION, COSTE_PUNTOS, TIPO, STOCK, SOLO_EMPLEADOS) VALUES
-- Premios de Consumición Básica (Equivalen a haber gastado entre 20€ y 35€ en el local)
(1, 'Espresso o Macchiato Gratis', 'Disfruta de un café solo o cortado de nuestro Espresso Bar sin coste.', 200, 'Consumición', 150, FALSE),
(2, 'Cookie de Chocolate y Sal', 'Canjeable por una de nuestras cookies artesanal de autor recién horneadas.', 300, 'Consumición', 40, FALSE),
(3, 'Flat White o Cappuccino Gratis', 'Elige tu café con leche de especialidad favorito en tamaño estándar.', 350, 'Consumición', 100, FALSE),

-- Premios de Consumición Premium o Merchandising Ligero (Equivalen a un gasto acumulado de 40€ a 60€)
(4, 'Cinnamon Roll de Regalo', 'Endulza tu día con nuestro famoso rollo de canela artesanal.', 400, 'Consumición', 8, FALSE),
(5, 'Espresso Tonic de Lavanda', 'Prueba uno de nuestros cafés de autor fríos más exclusivos.', 450, 'Consumición', 25, FALSE),

-- Premios de Merchandising Alto (Equivale a un gasto acumulado de 120€, ideal para clientes muy fieles)
(6, 'Taza Cerámica Oficial', 'Taza de cerámica de edición limitada DailyDose hecha a mano.', 1200, 'Merchandising', 5, FALSE),

-- Incentivos Internos Exclusivos (Filtro de seguridad SOLO_EMPLEADOS activado) 
(7, 'Sudadera Staff DailyDose', 'Incentivo exclusivo de marca para el personal de sala y barra.', 2000, 'Ropa', 10, TRUE);
