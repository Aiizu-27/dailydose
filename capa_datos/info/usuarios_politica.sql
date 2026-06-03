-- =============================================================================
-- CONTROL DE ACCESOS Y ROLES DE BASE DE DATOS (RBAC) - DAILYDOSE
-- =============================================================================
-- Este archivo define la política de seguridad perimetral para daily-dose.es.
-- Se aplica el "Principio del Menor Privilegio", garantizando que cada entidad
-- tenga acceso únicamente a los recursos estrictamente necesarios para su función.
-- =============================================================================

USE dailydose_db;

-- =============================================================================
-- USUARIO 1: EL ADMINISTRADOR DEL SISTEMA
-- =============================================================================
-- WHO: admin_DD@localhost
-- ¿QUIÉN ES?: El dueño de la infraestructura (Sysadmin).
-- ROLES Y RESPONSABILIDADES:
--   - Diseñar, alterar y destruir la estructura de la base de datos (DDL).
--   - Gestionar las copias de seguridad (Backups) y restauraciones del sistema.
--   - Crear y auditar los accesos de los demás usuarios.
-- SEGURIDAD: NO se usa nunca dentro del código PHP para evitar que un bug o una
-- inyección SQL exponga el control total del servidor de base de datos.
-- =============================================================================


-- =============================================================================
-- USUARIO 2: EL MOTOR DE LA APLICACIÓN (El Backend en PHP)
-- =============================================================================
-- WHO: daily_app@localhost
-- ¿QUIÉN ES?: El usuario que usará tu código PHP para hacer rular la web.
-- ROLES Y RESPONSABILIDADES:
--   - Es el "trabajador pesado" del bar. Interactúa con los datos en tiempo real (DML).
--   - Inserta los pedidos de los clientes, modifica el stock y lee el catálogo.
--   - Tiene permisos para ejecutar todos los Procedimientos Almacenados (PL).
-- RESTRICCIONES: No puede borrar bases de datos, ni alterar tablas, ni crear usuarios.
-- =============================================================================

DROP USER IF EXISTS 'daily_app'@'localhost';

CREATE USER 'daily_app'@'localhost' IDENTIFIED BY 'D4ily_D0s3_M4st3r';

GRANT SELECT, INSERT, UPDATE, DELETE ON dailydose_db.* TO 'daily_app'@'localhost';

GRANT EXECUTE ON dailydose_db.* TO 'daily_app'@'localhost';


-- =============================================================================
-- USUARIO 3: EL AUDITOR RESTRINGIDO (El Tribunal / Gráficos externos)
-- =============================================================================
-- WHO: daily_reader@localhost
-- ¿QUIÉN ES?: El evaluador del TFC, un tutor de ASIR o un software de analíticas.
-- ROLES Y RESPONSABILIDADES:
--   - Consultar el estado del sistema en modo "Solo Lectura" (Read-Only / DQL).
--   - Verificar que las tablas tienen datos y que los índices funcionan correctamente.
-- RESTRICCIONES CRÍTICAS: 
--   - No puede meter filas, ni cambiar precios, ni cobrar pedidos (`INSERT`/`UPDATE` prohibidos).
--   - Solo puede ejecutar los Procedimientos Almacenados que devuelven listas,
--     teniendo CAPADO el acceso a los que modifican el estado de la caja o del local.
-- =============================================================================

DROP USER IF EXISTS 'daily_reader'@'localhost';

CREATE USER 'daily_reader'@'localhost' IDENTIFIED BY 'D4ily_R34d_0nly';

GRANT SELECT, SHOW VIEW ON dailydose_db.* TO 'daily_reader'@'localhost';

GRANT EXECUTE ON PROCEDURE dailydose_db.sp_obtener_usuario_login TO 'daily_reader'@'localhost';
GRANT EXECUTE ON PROCEDURE dailydose_db.sp_obtener_rol_sistema TO 'daily_reader'@'localhost';
GRANT EXECUTE ON PROCEDURE dailydose_db.sp_obtener_pedido_activo_mesa TO 'daily_reader'@'localhost';
GRANT EXECUTE ON PROCEDURE dailydose_db.sp_obtener_lineas_pedido TO 'daily_reader'@'localhost';
GRANT EXECUTE ON PROCEDURE dailydose_db.sp_obtener_historial_puntos_cliente TO 'daily_reader'@'localhost';
GRANT EXECUTE ON PROCEDURE dailydose_db.sp_obtener_canjes_cliente TO 'daily_reader'@'localhost';


FLUSH PRIVILEGES;
