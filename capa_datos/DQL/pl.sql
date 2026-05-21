DELIMITER //

CREATE PROCEDURE sp_agregar_item_pedido(
    IN p_id_pedido INT,
    IN p_id_producto INT,
    IN p_cantidad INT
)
BEGIN
    DECLARE v_precio_unitario DECIMAL(6,2);
    DECLARE v_subtotal DECIMAL(6,2);
    
    -- 1. Buscamos el precio real del producto en el catálogo
    SELECT PRECIO INTO v_precio_unitario FROM PRODUCTOS WHERE ID_PRODUCTO = p_id_producto;
    
    -- 2. Calculamos el subtotal de la línea
    SET v_subtotal = v_precio_unitario * p_cantidad;
    
    -- 3. Insertamos el desglose en el detalle del pedido
    INSERT INTO DETALLE_PEDIDO (ID_PEDIDO, ID_PRODUCTO, CANTIDAD, PRECIO_UNITARIO, SUBTOTAL)
    VALUES (p_id_pedido, p_id_producto, p_cantidad, v_precio_unitario, v_subtotal);
    
    -- 4. Actualizamos el total acumulado en la cabecera del PEDIDO
    UPDATE PEDIDOS 
    SET TOTAL = (SELECT SUM(SUBTOTAL) FROM DETALLE_PEDIDO WHERE ID_PEDIDO = p_id_pedido)
    WHERE ID_PEDIDO = p_id_pedido;
END //

DELIMITER ;


DELIMITER //

CREATE PROCEDURE sp_ejecutar_canje_recompensa(
    IN p_id_cliente INT,
    IN p_id_recompensa INT
)
BEGIN
    DECLARE v_puntos_actuales INT;
    DECLARE v_coste_premio INT;
    DECLARE v_stock_premio INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
    
    -- Obtenemos el saldo del cliente y las condiciones del premio
    SELECT PUNTOS INTO v_puntos_actuales FROM CLIENTES WHERE ID_CLIENTE = p_id_cliente;
    SELECT COSTE_PUNTOS, STOCK INTO v_coste_premio, v_stock_premio FROM RECOMPENSAS WHERE ID_RECOMPENSA = p_id_recompensa;
    
    -- Control de seguridad perimetral de datos
    IF v_stock_premio <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR_STOCK: Recompensa agotada.';
    ELSEIF v_puntos_actuales < v_coste_premio THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR_PUNTOS: Saldo insuficiente.';
    ELSE
        -- 1. Restamos los puntos de la cartera del cliente
        UPDATE CLIENTES SET PUNTOS = PUNTOS - v_coste_premio WHERE ID_CLIENTE = p_id_cliente;
        
        -- 2. Consolidamos el intercambio en la tabla CANJES
        INSERT INTO CANJES (ID_CLIENTE, ID_RECOMPENSA, FECHA_CANJE, PUNTOS_GASTADOS)
        VALUES (p_id_cliente, p_id_recompensa, NOW(), v_coste_premio);
        
        -- 3. Guardamos el registro de la resta en el historial de puntos
        INSERT INTO HISTORIAL_PUNTOS (ID_CLIENTE, TIPO_MOVIMIENTO, CANTIDAD, MOTIVO, FECHA)
        VALUES (p_id_cliente, 'RESTA', v_coste_premio, 'Canje de beneficio en catálogo web', NOW());
        
        -- 4. Restamos una unidad del stock del premio (¡Saltará nuestro trigger de notificación!)
        UPDATE RECOMPENSAS SET STOCK = STOCK - 1 WHERE ID_RECOMPENSA = p_id_recompensa;
        
        COMMIT;
    END IF;
END //

DELIMITER ;


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