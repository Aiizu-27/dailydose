-- =====================================================================
-- INSERCIÓN DE USUARIOS DE PRUEBA GENÉRICOS (PASSWORD: 123456)
-- =====================================================================

-- 1. CONFIGURACIÓN: ADMINISTRADOR
INSERT INTO USUARIOS (NOMBRE, APELLIDOS, EMAIL, CONTRASENA, ROL, CAMBIAR_PASSWORD)
VALUES (
    'Admin', 
    'Admin', 
    'admin@dailydose.com', 
    '$2y$10$8C7bVvx8W432B/y5Q9hIbe0y2N6oMv1uRjP6pM0U2xV5W1S0g7g2.', 
    'ADMIN', 
    FALSE
);


-- 2. CONFIGURACIÓN: EMPLEADO (BARISTA)
INSERT INTO USUARIOS (NOMBRE, APELLIDOS, EMAIL, CONTRASENA, ROL, CAMBIAR_PASSWORD)
VALUES (
    'Empleado', 
    'Empleado', 
    'empleado@dailydose.com', 
    '$2y$10$8C7bVvx8W432B/y5Q9hIbe0y2N6oMv1uRjP6pM0U2xV5W1S0g7g2.', 
    'EMPLEADO', 
    FALSE
);

INSERT INTO EMPLEADOS (ID_USUARIO, PUESTO)
VALUES (LAST_INSERT_ID(), 'BARISTA');


-- 3. CONFIGURACIÓN: CLIENTE CLUB
INSERT INTO USUARIOS (NOMBRE, APELLIDOS, EMAIL, CONTRASENA, ROL, CAMBIAR_PASSWORD)
VALUES (
    'Cliente', 
    'Cliente', 
    'cliente@dailydose.com', 
    '$2y$10$8C7bVvx8W432B/y5Q9hIbe0y2N6oMv1uRjP6pM0U2xV5W1S0g7g2.', 
    'CLIENTE', 
    FALSE
);

INSERT INTO CLIENTES (ID_USUARIO, TELEFONO, PUNTOS)
VALUES (LAST_INSERT_ID(), '600123456', 150);

-- =====================================================================
-- FIN DEL SCRIPT
-- =====================================================================