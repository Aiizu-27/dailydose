El diseño de la base de datos de DailyDose se fundamenta en un modelo relacional normalizado que garantiza la integridad de los datos y la escalabilidad del sistema. A continuación, se detallan las entidades que componen la arquitectura:
I. Módulo de Identidad y Control de Acceso
1. Tabla: USUARIOS
Descripción técnica: Constituye el núcleo del sistema de autenticación. Almacena las credenciales de acceso y el nivel de privilegios mediante un campo de roles.
Campos destacados: CONTRASENA (almacenamiento de hash para seguridad) y ROL (segregación de funciones entre Administrador, Empleado y Cliente).
Justificación: Centraliza la seguridad lógica, permitiendo implementar un control de acceso basado en roles (RBAC) que protege los datos sensibles de la empresa.
2. Tabla: CLIENTES
Descripción técnica: Entidad de extensión vinculada a los usuarios con rol de consumidor. Almacena el perfil de fidelización y datos de contacto.
Campos destacados: PUNTOS (saldo neto actual) y ID_USUARIO (relación 1:1 con la tabla maestra).
Justificación: Cumple con la normalización al separar atributos de fidelización de la lógica de acceso, permitiendo que el sistema de puntos sea independiente de las credenciales.
3. Tabla: EMPLEADOS
Descripción técnica: Gestiona la información administrativa y operativa del personal de DailyDose.
Campos destacados: PUESTO, SALARIO y TURNO.
Justificación: Permite la gestión de recursos humanos y la trazabilidad operativa. Al compartir el ID_USUARIO, facilita que un trabajador opere como cliente sin duplicar registros en la base de datos.
4. Tabla: NOTIFICACIONES
Descripción técnica: Motor de mensajería asíncrona para la comunicación con los usuarios.
Campos destacados: TIPO (Pedido, Sistema, Promo) y LEIDO (booleano para control de visualización).
Justificación: Mejora la eficiencia operativa al alertar en tiempo real sobre eventos críticos, manteniendo un registro persistente que el usuario puede consultar al iniciar sesión.

II. Módulo de Catálogo y Logística
5. Tabla: CATEGORIAS
Descripción técnica: Diccionario de clasificación para la organización del menú y productos.
Justificación: Evita la redundancia de datos y facilita la escalabilidad; añadir una nueva familia de productos no requiere alterar la estructura de la base de datos.
6. Tabla: PRODUCTOS
Descripción técnica: Almacena el catálogo maestro de artículos disponibles para la venta.
Campos destacados: PRECIO, STOCK y RUTA_IMAGEN.
Justificación: Centraliza la información comercial y se relaciona con categorías y proveedores para ofrecer una visión 360º del inventario.
7. Tabla: ESPECIALIDAD_ACTUAL
Descripción técnica: Tabla de extensión que almacena la ficha técnica del café de origen estacional.
Campos destacados: NOTAS_CATA, ORIGEN_GRANO y TUESTE.
Justificación: Sigue el principio de especialización de atributos; evita campos nulos en productos genéricos (como bollería) al externalizar los datos técnicos exclusivos del café de especialidad.
8. Tabla: INVENTARIO
Descripción técnica: Registro de auditoría para el control de existencias en almacén.
Justificación: Proporciona trazabilidad sobre las mermas y entradas de mercancía, esencial para la auditoría contable y la prevención de pérdidas.

III. Módulo Operativo y Ventas
9. Tabla: MESAS
Descripción técnica: Representación lógica de la disposición física del local.
Campos destacados: ESTADO (Libre, Ocupada, Reservada).
Justificación: Optimiza el flujo de trabajo en sala, permitiendo al backend gestionar la disponibilidad de espacios en tiempo real.
10. Tabla: PEDIDOS
Descripción técnica: Cabecera transaccional que gestiona el ciclo de vida de cada venta.
Campos destacados: ESTADO (Pendiente, En Preparación, Entregado, etc.).
Justificación: Implementa el flujo de trabajo dinámico necesario para coordinar la cocina y la barra, vinculando al cliente y al empleado responsable.
11. Tabla: DETALLE_PEDIDO
Descripción técnica: Desglose de artículos por orden (resolución de relación N:M).
Justificación: Protege la integridad histórica de las ventas. Al registrar el PRECIO_UNITARIO en el momento del pedido, el balance financiero no se ve afectado por cambios futuros en el catálogo.
12. Tabla: PAGOS
Descripción técnica: Registro de la liquidación económica de las transacciones.
Campos destacados: TIPO_PAGO (Bizum, Tarjeta, Efectivo).
Justificación: Garantiza el cuadre de caja y permite analizar los métodos de pago preferidos por los clientes para futuras decisiones estratégicas.

IV. Módulo de Fidelización (Recompensas)
13. Tabla: RECOMPENSAS
Descripción técnica: Catálogo maestro de beneficios y premios.
Campos destacados: COSTE_PUNTOS y SOLO_EMPLEADOS.
Justificación: Separa la naturaleza del premio de la transacción, facilitando la creación de campañas de fidelización dinámicas.
14. Tabla: CANJES
Descripción técnica: Registro histórico del intercambio de puntos por recompensas.
Justificación: Asegura la integridad referencial; vincula a clientes reales con premios activos, registrando el valor del canje de forma inalterable.
15. Tabla: HISTORIAL_PUNTOS
Descripción técnica: Libro contable de la moneda virtual (puntos) de la aplicación.
Justificación: Esencial para la auditoría y seguridad lógica. Permite reconstruir cualquier saldo y detectar anomalías en la acumulación de puntos por compras.
