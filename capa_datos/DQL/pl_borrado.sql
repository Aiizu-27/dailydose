DROP PROCEDURE IF EXISTS sp_obtener_usuario_login;
DROP PROCEDURE IF EXISTS sp_registrar_cliente;
DROP PROCEDURE IF EXISTS sp_cambiar_password;
DROP PROCEDURE IF EXISTS sp_auth_verificar_email;
DROP PROCEDURE IF EXISTS sp_auth_obtener_password_hash;

DROP PROCEDURE IF EXISTS sp_obtener_producto_carrito;
DROP PROCEDURE IF EXISTS sp_procesar_pedido;
DROP PROCEDURE IF EXISTS sp_insertar_detalle_pedido;
DROP PROCEDURE IF EXISTS sp_cambiar_estado_pedido;

DROP PROCEDURE IF EXISTS sp_obtener_empleados;
DROP PROCEDURE IF EXISTS sp_obtener_pedidos_activos;
DROP PROCEDURE IF EXISTS sp_obtener_detalle_pedido;
DROP PROCEDURE IF EXISTS sp_obtener_turnos_semanas;
DROP PROCEDURE IF EXISTS sp_asignar_turno;
DROP PROCEDURE IF EXISTS sp_eliminar_turno;
DROP PROCEDURE IF EXISTS sp_obtener_mesas;
DROP PROCEDURE IF EXISTS sp_contar_pedidos_hoy;

DROP PROCEDURE IF EXISTS sp_admin_obtener_estadisticas_hoy;
DROP PROCEDURE IF EXISTS sp_admin_obtener_usuarios;
DROP PROCEDURE IF EXISTS sp_admin_obtener_pedidos_hoy;
DROP PROCEDURE IF EXISTS sp_admin_obtener_stock_bajo;
DROP PROCEDURE IF EXISTS sp_admin_obtener_todos_productos;

DROP PROCEDURE IF EXISTS sp_carta_obtener_categories;
DROP PROCEDURE IF EXISTS sp_carta_obtener_productos;
DROP PROCEDURE IF EXISTS sp_carta_obtener_especialidad;

DROP PROCEDURE IF EXISTS sp_promos_obtener_puntos_cliente;
DROP PROCEDURE IF EXISTS sp_promos_obtener_recompensas;

DROP PROCEDURE IF EXISTS sp_cliente_obtener_perfil;
DROP PROCEDURE IF EXISTS sp_cliente_obtener_ultimos_pedidos;
DROP PROCEDURE IF EXISTS sp_cliente_obtener_favoritos;
DROP PROCEDURE IF EXISTS sp_obtener_pedidos_cliente;