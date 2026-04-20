# DailyDose - Aplicación Web y Base de Datos

## Descripción del proyecto

DailyDose es una aplicación web diseñada para la gestión integral de un entorno empresarial. El sistema permite administrar usuarios, clientes, empleados, productos, pedidos, pagos e inventario, así como un sistema de fidelización basado en puntos.

Este proyecto forma parte de un Trabajo de Fin de Ciclo (TFC) y se centra en el desarrollo de la aplicación web y el diseño de la base de datos relacional.

<p align="center">
  <a href="https://daily-dose.es" target="_blank">
    <img src="https://img.shields.io/badge/DailyDose-Abrir%20Web-f42b1d?style=for-the-badge&logo=googlechrome&logoColor=white" />
  </a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/En%20desarrollo-C7E3D4?style=for-the-badge" />
</p>

---

## Objetivo

El objetivo del proyecto es desarrollar una aplicación web funcional que permita:

- Gestión de usuarios con distintos roles (cliente, empleado, administrador)
- Administración de productos y proveedores
- Gestión de pedidos y detalle de pedidos
- Control de inventario
- Sistema de pagos
- Sistema de puntos y fidelización de clientes

---

## Arquitectura de la aplicación

La aplicación sigue una arquitectura cliente-servidor:

- **Frontend:** Interfaz web para la interacción del usuario
- **Backend:** Lógica de negocio y gestión de datos
- **Base de datos:** Sistema relacional MySQL

Flujo de la aplicación:

Usuario → Interfaz Web → Backend → Base de Datos

---

## Base de datos

La base de datos está diseñada bajo un modelo relacional e incluye las siguientes entidades principales:

- USUARIOS
- CLIENTES
- EMPLEADOS
- PRODUCTOS
- PROVEEDORES
- PEDIDOS
- DETALLE_PEDIDO
- PAGOS
- INVENTARIO
- PROMOCIONES
- LOYALTY_TRANSACTIONS
- CANJES

El sistema garantiza la integridad de los datos mediante claves primarias y foráneas.

---

## Funcionalidades principales

- Registro e inicio de sesión de usuarios
- Gestión de perfiles según rol
- Catálogo de productos
- Creación y gestión de pedidos
- Cálculo de totales y detalle de compra
- Registro de pagos
- Sistema de puntos de fidelización
- Historial de transacciones

---

## Modelo de datos

La base de datos está normalizada y estructurada para evitar redundancia y asegurar la integridad de los datos.

Relaciones principales:

- Usuarios → Clientes / Empleados
- Clientes → Pedidos
- Pedidos → Detalle de pedidos
- Productos → Proveedores
- Clientes → Transacciones de fidelización

---

## Autor

Proyecto desarrollado como parte del Trabajo de Fin de Ciclo (TFC), centrado en el desarrollo de una aplicación web con base de datos relacional.

---

## Nota

Este proyecto tiene un enfoque académico y demuestra competencias en:

- Desarrollo web
- Diseño de bases de datos relacionales
- Modelado de sistemas empresariales
