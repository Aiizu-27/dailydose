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


