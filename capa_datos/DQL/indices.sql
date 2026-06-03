CREATE UNIQUE INDEX idx_usuarios_email ON USUARIOS(EMAIL);

CREATE INDEX idx_pedidos_mesa_estado ON PEDIDOS(ID_MESA, ESTADO);

CREATE INDEX idx_pedidos_fecha ON PEDIDOS(FECHA);

CREATE INDEX idx_productos_categoria ON PRODUCTOS(ID_CATEGORIA);

CREATE INDEX idx_detalle_pedido_padre ON DETALLE_PEDIDO(ID_PEDIDO);

CREATE INDEX idx_notificaciones_usuario_leido 
ON NOTIFICACIONES(ID_USUARIO, LEIDO);

CREATE INDEX idx_historial_puntos_cliente_fecha 
ON HISTORIAL_PUNTOS(ID_CLIENTE, FECHA DESC);

CREATE INDEX idx_canjes_cliente_fecha 
ON CANJES(ID_CLIENTE, FECHA_CANJE DESC);
