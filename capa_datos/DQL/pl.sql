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