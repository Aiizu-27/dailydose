# Daily Dose - Plataforma Transaccional Web y Persistencia Relacional a Medida

## Descripción del Proyecto

**Daily Dose** es una solución de software a medida diseñada para la transformación digital y gestión operativa integral de una pyme del sector hostelero. A diferencia de las arquitecturas comerciales rígidas, este sistema implementa un entorno desacoplado y transaccional que administra flujos críticos de negocio: autenticación centralizada, control de comandas y mesas en sala, optimización de inventario, pasarela de pagos simulada y un motor de fidelización de clientes basado en recompensas acumulativas (*Daily Points*).

Este proyecto constituye el núcleo de desarrollo técnico y persistencia de datos del Trabajo Fin de Ciclo (TFC).

<p align="center">
  <a href="https://daily-dose.es" target="_blank">
    <img src="https://img.shields.io/badge/DailyDose-Abrir%20Web-f42b1d?style=for-the-badge&logo=googlechrome&logoColor=white" />
  </a>
</p>

---

## 🛠️ Stack Tecnológico (Arquitectura LEMP)

La plataforma se despliega sobre una infraestructura distribuida en tres capas independientes bajo un stack de alto rendimiento de código abierto:

* **Capa de Presentación (Frontend):** Interfaz nativa construida bajo el estándar **Mobile-First**. Implementa manipulación dinámica del DOM mediante **Vanilla JavaScript** y diseño adaptativo con hojas de estilo avanzadas (estética *Glassmorphism* y conmutación reactiva mediante variables globales CSS para **Modo Claro / Modo Oscuro**).
* **Capa de Aplicación (Backend):** Lógica de negocio modular programada en **PHP**, delegando la ejecución de scripts dinámicos al entorno aislado **PHP-FPM** mediante sockets FastCGI sobre un servidor web **Nginx**.
* **Capa de Datos (Persistencia):** Motor relacional **MySQL** encargado de la consistencia estructural del ecosistema.

---

## Características Técnicas y Bastionado

### 1. Ingeniería de Datos y Normalización (3FN)
La base de datos relacional ha sido modelada e implementada desde cero, aplicando un riguroso proceso de normalización hasta la **Tercera Forma Normal (3FN)** para eliminar redundancias y dependencias anómalas. La integridad referencial del negocio está blindada a nivel de motor mediante restricciones explícitas de claves ajenas (*Foreign Keys*) asociadas a directivas automatizadas como `ON DELETE CASCADE` y `ON DELETE SET NULL`.

### 2. Abstracción de Lógica mediante Stored Procedures
Para maximizar la eficiencia y liberar carga de cómputo en el servidor de aplicaciones, la lógica pesada transaccional se ha encapsulado directamente en el motor relacional mediante **Procedimientos Almacenados** (ej. `sp_carta_obtener_productos`, `sp_admin_obtener_pedidos_hoy`). Las excepciones operativas se gestionan de manera interna lanzando alertas nativas mediante la directiva `SIGNAL SQLSTATE`.

### 3. Mitigación de Vectores de Ataque (OWASP Top 10)
* **Inyección SQL:** Toda la comunicación e intercambio de parámetros entre PHP y MySQL se realiza mediante **sentencias preparadas con la extensión MySQLi** (`prepare()` y `bind_param()`), forzando la precompilación sintáctica y aislando por completo los datos de la lógica de ejecución.
* **Criptografía y Sesiones:** Las credenciales de acceso se protegen mediante hashing criptográfico irreversible utilizando el algoritmo **Bcrypt** (`password_hash()`). Además, se implementa un control de accesos basado en roles lógicos (**RBAC**), y el archivo maestro de configuración de base de datos se almacena de forma segura fuera del directorio raíz público (`public_html`) de Nginx.

### 4. Consumo Asíncrono de Datos (Fetch API)
El intercambio de información entre el frontend y el backend se realiza en segundo plano de manera asíncrona mediante la **Fetch API**, transmitiendo objetos estructurados en formato **JSON**. Esto evita recargas completas de la interfaz, reduce drásticamente la latencia y minimiza el consumo de ancho de banda de la red local segmentada del establecimiento.

---

## Estructura del Modelo Relacional

El esquema de persistencia se compone de tablas interconectadas que segmentan las operaciones del negocio:

* **Módulo de Accesos:** `USUARIOS`, `CLIENTES`, `EMPLEADOS` (Seguridad RBAC).
* **Módulo de Ventas y Sala:** `MESAS`, `PEDIDOS`, `DETALLE_PEDIDO`, `PAGOS`.
* **Módulo de Logística:** `PRODUCTOS`, `PROVEEDORES`, `INVENTARIO`.
* **Módulo de Fidelización:** `PROMOCIONES`, `LOYALTY_TRANSACTIONS`, `CANJES`.

---

## Despliegue e Infraestructura Cloud

La aplicación está optimizada para su explotación en producción dentro de un entorno virtualizado seguro:
* **Alojamiento:** Servidor Privado Virtual (VPS) con sistema operativo Linux Ubuntu Server alojado en **AWS Lightsail**.
* **Gestión:** Acceso y administración remota mediante protocolo SSH seguro por clave pública/privada y control de versiones integrado con Git.
