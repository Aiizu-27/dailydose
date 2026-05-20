CREATE EVENT ev_auto_liberar_mesas_60min
ON SCHEDULE EVERY 5 MINUTE
DO
    UPDATE MESAS m
    JOIN PEDIDOS p ON m.ID_MESA = p.ID_MESA
    SET m.ESTADO = 'LIBRE'
    WHERE m.ESTADO = 'OCUPADA' 
      AND p.ESTADO = 'ENTREGADO' -- Usamos el estado real de tu tabla PEDIDOS
      AND TIMESTAMPDIFF(MINUTE, p.FECHA, NOW()) >= 60;
