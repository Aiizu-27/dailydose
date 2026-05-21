/*Procedimiento para obtener usuario*/

DELIMITER $$

CREATE PROCEDURE sp_obtener_usuario_login(IN p_correo VARCHAR(255))
BEGIN
    SELECT u.ID_USUARIO, u.NOMBRE, u.APELLIDOS, u.EMAIL, u.CONTRASENA, u.ROL,u.CAMBIAR_PASSWORD
    FROM USUARIOS u 
    WHERE u.EMAIL = p_correo;
END$$

DELIMITER ;


/*pl registro*/
DELIMITER $$

CREATE PROCEDURE sp_registrar_cliente(
    IN p_nombre    VARCHAR(50),
    IN p_apellidos VARCHAR(50),
    IN p_correo    VARCHAR(50),
    IN p_pass_hash VARCHAR(255),
    IN p_telefono  VARCHAR(15)
)
BEGIN
    DECLARE v_id_usuario INT;

    -- Iniciamos la transacción dentro del SP
    START TRANSACTION;

    INSERT INTO USUARIOS (NOMBRE, APELLIDOS, EMAIL, CONTRASENA, ROL, CAMBIAR_PASSWORD)
    VALUES (p_nombre, p_apellidos, p_correo, p_pass_hash, 'CLIENTE', FALSE);

    SET v_id_usuario = LAST_INSERT_ID();

    INSERT INTO CLIENTES (ID_USUARIO, TELEFONO, PUNTOS)
    VALUES (v_id_usuario, p_telefono, 0);

    COMMIT;

    -- Devolvemos el ID por si lo necesitamos en PHP
    SELECT v_id_usuario AS ID_USUARIO;

END$$

DELIMITER ;

/*auth_cambiar_pass.php*/
DELIMITER $$

CREATE PROCEDURE sp_cambiar_password(
    IN p_id_usuario INT,
    IN p_pass_hash  VARCHAR(255)
)
BEGIN
    UPDATE USUARIOS 
    SET CONTRASENA = p_pass_hash, CAMBIAR_PASSWORD = FALSE 
    WHERE ID_USUARIO = p_id_usuario;

    -- Devolvemos filas afectadas para confirmar que se actualizó
    SELECT ROW_COUNT() AS filas_afectadas;
END$$

DELIMITER ;


/*Procedimiento para obtener producto en el carrito*/
DELIMITER $$

CREATE PROCEDURE sp_obtener_producto_carrito(IN p_id_producto INT)
BEGIN
    SELECT NOMBRE, PRECIO, STOCK 
    FROM PRODUCTOS 
    WHERE ID_PRODUCTO = p_id_producto;
END$$

DELIMITER ;


/*Procedimiento para procesar pedido*/
DELIMITER $$

CREATE PROCEDURE sp_procesar_pedido(
    IN p_id_cliente  INT,
    IN p_id_mesa     INT,
    IN p_total       DECIMAL(6,2),
    IN p_puntos      INT
)
BEGIN
    DECLARE v_id_pedido INT;

    -- Insertar pedido
    INSERT INTO PEDIDOS (FECHA, TOTAL, ID_CLIENTE, ID_MESA, ESTADO)
    VALUES (NOW(), p_total, p_id_cliente, p_id_mesa, 'PENDIENTE');

    SET v_id_pedido = LAST_INSERT_ID();

    -- Sumar puntos al cliente
    UPDATE CLIENTES 
    SET PUNTOS = PUNTOS + p_puntos 
    WHERE ID_CLIENTE = p_id_cliente;

    -- Registrar en historial de puntos
    INSERT INTO HISTORIAL_PUNTOS (ID_CLIENTE, TIPO_MOVIMIENTO, CANTIDAD, MOTIVO)
    VALUES (p_id_cliente, 'SUMA', p_puntos, CONCAT('Pedido #', v_id_pedido));

    SELECT v_id_pedido AS ID_PEDIDO;
END$$

DELIMITER ;


/*Procesar carrito*/
CREATE PROCEDURE sp_insertar_detalle_pedido(
    IN p_id_pedido       INT,
    IN p_id_producto     INT,
    IN p_cantidad        INT,
    IN p_precio_unitario DECIMAL(6,2),
    IN p_subtotal        DECIMAL(6,2)
)
BEGIN
    INSERT INTO DETALLE_PEDIDO (ID_PEDIDO, ID_PRODUCTO, CANTIDAD, PRECIO_UNITARIO, SUBTOTAL)
    VALUES (p_id_pedido, p_id_producto, p_cantidad, p_precio_unitario, p_subtotal);

    -- Actualizamos stock a la vez
    UPDATE PRODUCTOS 
    SET STOCK = STOCK - p_cantidad 
    WHERE ID_PRODUCTO = p_id_producto;
END$$

DELIMITER ;


/*pl estados de pedido*/
DELIMITER $$

CREATE PROCEDURE sp_cambiar_estado_pedido(
    IN p_id_pedido   INT,
    IN p_nuevo_estado ENUM('PENDIENTE','EN_PREPARACION','LISTO','ENTREGADO','CANCELADO'),
    IN p_id_empleado INT
)
BEGIN
    IF p_nuevo_estado = 'EN_PREPARACION' AND p_id_empleado IS NOT NULL THEN
        UPDATE PEDIDOS 
        SET ESTADO = p_nuevo_estado, ID_EMPLEADO = p_id_empleado 
        WHERE ID_PEDIDO = p_id_pedido;
    ELSE
        UPDATE PEDIDOS 
        SET ESTADO = p_nuevo_estado
        WHERE ID_PEDIDO = p_id_pedido;
    END IF;

    SELECT ROW_COUNT() AS filas_afectadas;
END$$

DELIMITER ;



/*dashboard_trabajador*/
DELIMITER $$

CREATE PROCEDURE sp_obtener_empleados()
BEGIN
    SELECT e.ID_EMPLEADO, u.NOMBRE 
    FROM EMPLEADOS e 
    JOIN USUARIOS u ON e.ID_USUARIO = u.ID_USUARIO
    ORDER BY u.NOMBRE ASC;
END$$


CREATE PROCEDURE sp_obtener_pedidos_activos()
BEGIN
    SELECT 
        p.ID_PEDIDO,
        p.FECHA,
        p.TOTAL,
        p.ESTADO,
        p.ID_EMPLEADO,
        m.NUMERO_MESA,
        u.NOMBRE        AS CLIENTE_NOMBRE,
        emp_u.NOMBRE    AS BARISTA
    FROM PEDIDOS p
    LEFT JOIN CLIENTES c    ON p.ID_CLIENTE   = c.ID_CLIENTE
    LEFT JOIN USUARIOS u    ON c.ID_USUARIO   = u.ID_USUARIO
    LEFT JOIN EMPLEADOS e   ON p.ID_EMPLEADO  = e.ID_EMPLEADO
    LEFT JOIN USUARIOS emp_u ON e.ID_USUARIO  = emp_u.ID_USUARIO
    LEFT JOIN MESAS m       ON p.ID_MESA      = m.ID_MESA
    WHERE p.ESTADO IN ('PENDIENTE', 'EN_PREPARACION', 'LISTO')
    ORDER BY p.FECHA ASC;
END$$


CREATE PROCEDURE sp_obtener_detalle_pedido(IN p_id_pedido INT)
BEGIN
    SELECT 
        pr.NOMBRE       AS PRODUCTO,
        dp.CANTIDAD,
        dp.PRECIO_UNITARIO,
        dp.SUBTOTAL
    FROM DETALLE_PEDIDO dp
    JOIN PRODUCTOS pr ON dp.ID_PRODUCTO = pr.ID_PRODUCTO
    WHERE dp.ID_PEDIDO = p_id_pedido;
END$$

DELIMITER ;