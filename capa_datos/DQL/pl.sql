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
DELIMITER $$
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


DELIMITER $$

CREATE PROCEDURE sp_obtener_turnos_semanas(IN p_fecha_inicio DATE)
BEGIN
    -- Devuelve 4 semanas desde la fecha dada
    SELECT 
        t.ID_TURNO,
        t.DIA,
        t.TURNO,
        e.ID_EMPLEADO,
        u.NOMBRE,
        u.APELLIDOS
    FROM TURNOS t
    JOIN EMPLEADOS e ON t.ID_EMPLEADO = e.ID_EMPLEADO
    JOIN USUARIOS u  ON e.ID_USUARIO  = u.ID_USUARIO
    WHERE t.DIA BETWEEN p_fecha_inicio AND DATE_ADD(p_fecha_inicio, INTERVAL 27 DAY)
    ORDER BY t.DIA ASC, u.NOMBRE ASC;
END$$


CREATE PROCEDURE sp_asignar_turno(
    IN p_id_empleado INT,
    IN p_dia         DATE,
    IN p_turno       ENUM('MAÑANA','TARDE')
)
BEGIN
    INSERT INTO TURNOS (ID_EMPLEADO, DIA, TURNO)
    VALUES (p_id_empleado, p_dia, p_turno)
    ON DUPLICATE KEY UPDATE TURNO = p_turno;

    SELECT ROW_COUNT() AS filas_afectadas;
END$$


CREATE PROCEDURE sp_eliminar_turno(IN p_id_turno INT)
BEGIN
    DELETE FROM TURNOS WHERE ID_TURNO = p_id_turno;
    SELECT ROW_COUNT() AS filas_afectadas;
END$$

DELIMITER ;

-- =====================================================
-- SP DASHBOARD TRABAJADOR — DAILY DOSE
-- =====================================================

-- Obtener todas las mesas
DELIMITER $$
CREATE PROCEDURE sp_obtener_mesas()
BEGIN
    SELECT ID_MESA, NUMERO_MESA, CAPACIDAD, UBICACION, ESTADO
    FROM MESAS
    ORDER BY NUMERO_MESA ASC;
END$$
DELIMITER ;

-- Contar pedidos entregados hoy
DELIMITER $$
CREATE PROCEDURE sp_contar_pedidos_hoy()
BEGIN
    SELECT COUNT(*) AS TOTAL
    FROM PEDIDOS
    WHERE DATE(FECHA) = CURDATE()
      AND ESTADO = 'ENTREGADO';
END$$
DELIMITER ;

DELIMITER //

/* =====================================================================
   1. ESTADÍSTICAS DEL DÍA
   Calcula ingresos netos de hoy, total de comandas y el ticket medio.
   ===================================================================== */
DROP PROCEDURE IF EXISTS sp_admin_obtener_estadisticas_hoy //
CREATE PROCEDURE sp_admin_obtener_estadisticas_hoy()
BEGIN
    SELECT 
        IFNULL(SUM(TOTAL), 0.00) AS INGRESOS_HOY,
        COUNT(ID_PEDIDO) AS PEDIDOS_HOY,
        IFNULL(ROUND(AVG(TOTAL), 2), 0.00) AS TICKET_MEDIO
    FROM PEDIDOS 
    WHERE DATE(FECHA) = CURDATE() AND ESTADO != 'CANCELADO';
END //

/* =====================================================================
   2. GESTIÓN DE USUARIOS
   Trae la lista de todas las cuentas registradas en el sistema para la tabla.
   ===================================================================== */
DROP PROCEDURE IF EXISTS sp_admin_obtener_usuarios //
CREATE PROCEDURE sp_admin_obtener_usuarios()
BEGIN
    SELECT ID_USUARIO, NOMBRE, APELLIDOS, EMAIL, ROL 
    FROM USUARIOS 
    ORDER BY ID_USUARIO ASC;
END //

/* =====================================================================
   3. AUDITORÍA DE PEDIDOS
   Muestra los pedidos del día con la hora, el número de mesa y el cliente.
   ===================================================================== */
DROP PROCEDURE IF EXISTS sp_admin_obtener_pedidos_hoy //
CREATE PROCEDURE sp_admin_obtener_pedidos_hoy()
BEGIN
    SELECT 
        p.ID_PEDIDO, 
        p.FECHA, 
        m.NUMERO_MESA, 
        p.TOTAL, 
        p.ESTADO, 
        u.NOMBRE AS CLIENTE_NOMBRE
    FROM PEDIDOS p
    LEFT JOIN CLIENTES c ON p.ID_CLIENTE = c.ID_CLIENTE
    LEFT JOIN USUARIOS u ON c.ID_USUARIO = u.ID_USUARIO
    LEFT JOIN MESAS m ON p.ID_MESA = m.ID_MESA
    WHERE DATE(p.FECHA) = CURDATE()
    ORDER BY p.ID_PEDIDO DESC;
END //

/* =====================================================================
   4. ALERTAS DE INVENTARIO (STOCK BAJO)
   Detecta insumos críticos con 10 o menos unidades disponibles en el almacén.
   ===================================================================== */
DROP PROCEDURE IF EXISTS sp_admin_obtener_stock_bajo //
CREATE PROCEDURE sp_admin_obtener_stock_bajo()
BEGIN
    SELECT 
        p.ID_PRODUCTO, 
        p.NOMBRE, 
        c.NOMBRE_CATEGORIA AS CATEGORIA, 
        p.STOCK 
    FROM PRODUCTOS p
    LEFT JOIN CATEGORIAS c ON p.ID_CATEGORIA = c.ID_CATEGORIA
    WHERE p.STOCK <= 10
    ORDER BY p.STOCK ASC;
END //

/* =====================================================================
   5. CATÁLOGO COMPLETO DE LA CARTA
   Lista todos los productos unificados con el nombre de su categoría de texto.
   ===================================================================== */
DROP PROCEDURE IF EXISTS sp_admin_obtener_todos_productos //
CREATE PROCEDURE sp_admin_obtener_todos_productos()
BEGIN
    SELECT 
        p.ID_PRODUCTO, 
        p.NOMBRE, 
        c.NOMBRE_CATEGORIA AS CATEGORIA, 
        p.PRECIO, 
        p.STOCK 
    FROM PRODUCTOS p
    LEFT JOIN CATEGORIAS c ON p.ID_CATEGORIA = c.ID_CATEGORIA
    ORDER BY c.NOMBRE_CATEGORIA ASC, p.NOMBRE ASC;
END //

/* =====================================================================
   6. LISTAR BARISTAS PARA TURNOS
   Une EMPLEADOS con USUARIOS para pintar los nombres en el selector de turnos.
   ===================================================================== */
DROP PROCEDURE IF EXISTS sp_obtener_empleados //
CREATE PROCEDURE sp_obtener_empleados()
BEGIN
    SELECT 
        e.ID_EMPLEADO, 
        u.NOMBRE, 
        u.APELLIDOS, 
        e.PUESTO 
    FROM EMPLEADOS e
    JOIN USUARIOS u ON e.ID_USUARIO = u.ID_USUARIO
    ORDER BY u.NOMBRE ASC;
END //

DELIMITER ;