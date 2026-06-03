DELIMITER //

CREATE TRIGGER tg_descontar_stock_pedido
AFTER INSERT ON DETALLE_PEDIDO
FOR EACH ROW
BEGIN
    UPDATE PRODUCTOS 
    SET STOCK = STOCK - NEW.CANTIDAD
    WHERE ID_PRODUCTO = NEW.ID_PRODUCTO;
END //

DELIMITER ;


DELIMITER //

CREATE TRIGGER tg_ocupar_mesa_automatica
AFTER INSERT ON PEDIDOS
FOR EACH ROW
BEGIN
    IF NEW.ESTADO IN ('PENDIENTE', 'EN PREPARACION') THEN
        UPDATE MESAS 
        SET ESTADO = 'OCUPADA'
        WHERE ID_MESA = NEW.ID_MESA;
    END IF;
END //

DELIMITER ;


DELIMITER //

CREATE TRIGGER tg_restablecer_stock_eliminar_item
AFTER DELETE ON DETALLE_PEDIDO
FOR EACH ROW
BEGIN
    UPDATE PRODUCTOS 
    SET STOCK = STOCK + OLD.CANTIDAD
    WHERE ID_PRODUCTO = OLD.ID_PRODUCTO;
END //

DELIMITER ;


DELIMITER //

CREATE TRIGGER tg_notificar_nuevo_pedido
AFTER INSERT ON PEDIDOS
FOR EACH ROW
BEGIN
    DECLARE v_id_usuario_cliente INT;

    IF NEW.ID_CLIENTE IS NOT NULL THEN

        SELECT ID_USUARIO INTO v_id_usuario_cliente 
        FROM CLIENTES 
        WHERE ID_CLIENTE = NEW.ID_CLIENTE;

        INSERT INTO NOTIFICACIONES (ID_USUARIO, MENSAJE, TIPO, LEIDO, FECHA_CREACION)
        VALUES (
            v_id_usuario_cliente, 
            CONCAT('¡Tu pedido #', NEW.ID_PEDIDO, ' ha sido registrado con éxito! Estado: ', NEW.ESTADO, '. Total: ', NEW.TOTAL, '€'), 
            'Pedido', 
            FALSE, 
            NOW()
        );
        
    END IF;
END //

DELIMITER ;


DELIMITER //

CREATE TRIGGER tg_alerta_stock_critico
AFTER UPDATE ON PRODUCTOS
FOR EACH ROW
BEGIN
    DECLARE v_id_admin INT;

    IF NEW.STOCK <= 5 AND OLD.STOCK > 5 THEN
        
        SELECT ID_USUARIO INTO v_id_admin 
        FROM EMPLEADOS 
        WHERE PUESTO = 'ADMIN' 
        LIMIT 1;
        
        IF v_id_admin IS NOT NULL THEN
            INSERT INTO NOTIFICACIONES (ID_USUARIO, MENSAJE, TIPO, LEIDO, FECHA_CREACION)
            VALUES (
                v_id_admin, 
                CONCAT('¡ALERTA DE STOCK! El producto "', NEW.NOMBRE, '" se está agotando. Quedan solo ', NEW.STOCK, ' unidades.'), 
                'Almacén', 
                FALSE, 
                NOW()
            );
        END IF;
        
    END IF;
END //

DELIMITER ;


DELIMITER //

CREATE TRIGGER tg_notificar_canje
AFTER INSERT ON CANJES
FOR EACH ROW
BEGIN
    DECLARE v_id_usuario_cliente INT;
    DECLARE v_nombre_premio VARCHAR(100);
    
    SELECT ID_USUARIO INTO v_id_usuario_cliente FROM CLIENTES WHERE ID_CLIENTE = NEW.ID_CLIENTE;
    
    SELECT NOMBRE INTO v_nombre_premio FROM RECOMPENSAS WHERE ID_RECOMPENSA = NEW.ID_RECOMPENSA;
    
    IF v_id_usuario_cliente IS NOT NULL THEN
        INSERT INTO NOTIFICACIONES (ID_USUARIO, MENSAJE, TIPO, LEIDO, FECHA_CREACION)
        VALUES (
            v_id_usuario_cliente,
            CONCAT('¡Canje realizado! Has adquirido: "', v_nombre_premio, '" por un coste de ', NEW.PUNTOS_GASTADOS, ' puntos. Muestra este aviso en barra.'),
            'Recompensa',
            FALSE,
            NOW()
        );
    END IF;
END //

DELIMITER ;


DELIMITER //

DROP TRIGGER IF EXISTS tg_notificar_nuevo_pedido; //

CREATE TRIGGER tg_notificar_pedido_a_empleados
AFTER INSERT ON PEDIDOS
FOR EACH ROW
BEGIN

    INSERT INTO NOTIFICACIONES (ID_USUARIO, MENSAJE, TIPO, LEIDO, FECHA_CREACION)
    SELECT 
        e.ID_USUARIO, 
        CONCAT('¡Nuevo pedido recibido! Mesa #', NEW.ID_MESA, ' (Pedido #', NEW.ID_PEDIDO, '). Total comanda: ', NEW.TOTAL, '€'), 
        'Pedido', 
        FALSE, 
        NOW()
    FROM EMPLEADOS e;
END //

DELIMITER ;
