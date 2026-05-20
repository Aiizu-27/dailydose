-- 1. Optimización del Login (para sp_obtener_usuario_login)
-- Hace que la búsqueda por correo en el inicio de sesión sea instantánea.
CREATE UNIQUE INDEX idx_usuarios_email ON USUARIOS(EMAIL);

-- 2. Optimización del TPV y la Sala (para sp_obtener_pedido_active_mesa)
-- Acelera la carga del mapa de mesas al buscar comandas que no estén cerradas.
CREATE INDEX idx_pedidos_mesa_estado ON PEDIDOS(ID_MESA, ESTADO);

-- 3. Optimización del Resumen de Caja y Estadísticas
-- Agrupa los pedidos por fecha para sacar la facturación diaria o mensual sin esfuerzo.
CREATE INDEX idx_pedidos_fecha ON PEDIDOS(FECHA);

-- 4. Optimización de la Carta / Menú Dinámico
-- PHP filtrará los platos por categoría ('Café', 'Bollería') al pintar el frontend.
CREATE INDEX idx_productos_categoria ON PRODUCTOS(ID_CATEGORIA);

-- 5. Optimización de la Carga del Carrito (para sp_obtener_lineas_pedido)
-- Vincula a toda velocidad las líneas del detalle con su cabecera de pedido.
CREATE INDEX idx_detalle_pedido_padre ON DETALLE_PEDIDO(ID_PEDIDO);

-- 6. EL MÁS CRÍTICO: Optimización del bucle de la Campana de Alertas
-- Como el JavaScript va a consultar la tabla cada 10 segundos buscando "mis notis sin leer",
-- este índice compuesto evita que MySQL tenga que revisar toda la tabla en cada consulta.
CREATE INDEX idx_notificaciones_usuario_leido 
ON NOTIFICACIONES(ID_USUARIO, LEIDO);

-- 7. Optimización del Historial de Puntos (para sp_obtener_historial_puntos_cliente)
-- Al usar un índice compuesto por el cliente y la fecha, MySQL le devuelve al cliente 
-- su extracto de puntos ordenado de golpe, sin tener que hacer un ordenamiento en memoria (Filesort).
CREATE INDEX idx_historial_puntos_cliente_fecha 
ON HISTORIAL_PUNTOS(ID_CLIENTE, FECHA DESC);

-- 8. Optimización de la consulta de Premios Reclamados (para sp_obtener_canjes_cliente)
-- Agiliza la carga de los "vouchers" o regalos que el cliente ya ha canjeado para enseñarlos en la barra.
CREATE INDEX idx_canjes_cliente_fecha 
ON CANJES(ID_CLIENTE, FECHA_CANJE DESC);
