-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3305
-- Tiempo de generación: 04-06-2025 a las 18:44:59
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestion_incidentes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `base_de_conocimiento`
--

CREATE TABLE `base_de_conocimiento` (
  `id_entry` int(11) NOT NULL,
  `error` text NOT NULL,
  `solucion` text NOT NULL,
  `id_tecnico` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `base_de_conocimiento`
--

INSERT INTO `base_de_conocimiento` (`id_entry`, `error`, `solucion`, `id_tecnico`, `fecha_registro`) VALUES
(1, 'Pantalla azul al iniciar el equipo', 'Se reemplazó la memoria RAM dañada.', 2, '2025-06-03 10:00:00'),
(2, 'El sistema no arranca', 'Se reinstaló el sistema operativo y se actualizó el BIOS.', 1, '2025-06-02 14:35:00'),
(3, 'La impresora no responde', 'Se reinstaló el driver y se configuró como predeterminada.', 4, '2025-06-01 09:50:00'),
(4, 'Sobrecalentamiento del CPU', 'Se limpió el disipador y se aplicó nueva pasta térmica.', 2, '2025-06-03 12:15:00'),
(5, 'Red no disponible', 'Se cambió el cable de red y se reinició el switch.', 3, '2025-06-02 16:10:00'),
(11, 'Error al acceder a base da datos', 'Se realizó un diagnóstico en el servidor de base de datos BD-01 debido a un error de conexión reportado. Al revisar el estado del servicio, se detectó que el servidor MySQL se encontraba detenido. Se procedió a iniciar manualmente el servicio MySQL y se confirmó su arranque exitoso. Posteriormente, se verificó que el puerto 3306 estuviera habilitado en el firewall, asegurando así que no hubiera bloqueos de red que impidieran la conexión.', 7, '2025-06-03 22:56:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogo_recursos`
--

CREATE TABLE `catalogo_recursos` (
  `id_recurso` int(11) NOT NULL,
  `nombre_recurso` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad_disponible` int(11) DEFAULT 0,
  `unidad` varchar(50) DEFAULT 'pieza',
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `catalogo_recursos`
--

INSERT INTO `catalogo_recursos` (`id_recurso`, `nombre_recurso`, `descripcion`, `cantidad_disponible`, `unidad`, `estado`) VALUES
(1, 'Cable Ethernet Cat6', 'Cable de red de alta velocidad para conexiones LAN', 48, 'pieza', 'activo'),
(2, 'Pantalla LCD 15.6\'\'', 'Pantalla de reemplazo para laptops de oficina', 15, 'pieza', 'activo'),
(3, 'Fuente de Poder 500W', 'Fuente de poder estándar para computadoras de escritorio', 20, 'pieza', 'activo'),
(4, 'Cuerda para elíptica', 'Cuerda de tracción para equipos elípticos', 10, 'pieza', 'activo'),
(5, 'Tarjeta de red PCIe', 'Adaptador de red para computadoras sin tarjeta integrada', 25, 'pieza', 'activo'),
(6, 'Altavoces internos para laptop', 'Altavoces compatibles con laptops Dell/HP', 30, 'pieza', 'activo'),
(7, 'Monitor LED 21\'\'', 'Monitor LED para estaciones administrativas', 12, 'pieza', 'activo'),
(8, 'Adaptador corriente DC 90W', 'Adaptador de corriente para proyectores y routers', 18, 'pieza', 'activo'),
(9, 'Kit limpieza polvo', 'Kit para limpieza interna de equipos de cómputo', 40, 'pieza', 'activo'),
(10, 'Controlador Display LCD', 'Controlador de reemplazo para pantallas de gimnasio', 8, 'pieza', 'activo'),
(11, 'Cable Ethernet Cat6', 'Cable de red de alta velocidad para conexiones LAN', 1, 'pieza', 'activo'),
(12, 'Controlador Display LCD', 'Controlador de reemplazo para pantallas de gimnasio', 1, 'pieza', 'activo'),
(13, 'Cable Ethernet Cat6', 'Cable de red de alta velocidad para conexiones LAN', 1, 'pieza', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogo_servicios`
--

CREATE TABLE `catalogo_servicios` (
  `id_servicio` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `tiempo_estimado_minutos` int(11) DEFAULT NULL,
  `estado` enum('activo','pendiente_aprobacion','rechazado') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `catalogo_servicios`
--

INSERT INTO `catalogo_servicios` (`id_servicio`, `nombre_servicio`, `descripcion`, `costo`, `tiempo_estimado_minutos`, `estado`) VALUES
(1, 'Configuracion de IPV4 y IPV6.', 'Configurar las IPs del equipo de computo en cuestion para su funcionamiento en la red.', 300.00, 40, 'activo'),
(2, 'Reemplazo de pantalla LCD', 'Sustitución de display dañado en equipo de gimnasio o computadora.', 450.00, 60, 'activo'),
(3, 'Optimización de red inalámbrica', 'Revisión y ajuste de configuración de red Wi-Fi para mejorar velocidad.', 350.00, 45, 'activo'),
(4, 'Cambio de cableado Ethernet', 'Sustitución de cables Ethernet defectuosos o dañados.', 200.00, 30, 'activo'),
(5, 'Instalación de software administrativo', 'Instalación de paquetes como Office, TeamViewer y Teams.', 400.00, 50, 'activo'),
(6, 'Revisión de altavoces', 'Diagnóstico y reparación de audio en laptops o PCs.', 300.00, 40, 'activo'),
(7, 'Mantenimiento preventivo a equipos de gimnasio', 'Ajuste y revisión de sistemas mecánicos y electrónicos en máquinas.', 500.00, 75, 'activo'),
(8, 'Ajuste de conexiones de video', 'Verificación y corrección de cables o conectores de pantallas.', 250.00, 35, 'activo'),
(9, 'Instalación de drivers generales', 'Instalación y configuración de drivers para funcionamiento óptimo del equipo.', 280.00, 45, 'activo'),
(10, 'Configuracion de Puertos en Base de datos', 'Configurar los puertos correspondientes de la base de datos para dar acceso al usuario', 1500.00, 120, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ci_items`
--

CREATE TABLE `ci_items` (
  `id_ci` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `responsable` varchar(100) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `procesador` varchar(100) DEFAULT 'Sin Componente',
  `ram` varchar(50) DEFAULT 'Sin Componente',
  `display` varchar(50) DEFAULT 'Sin Componente',
  `power_source` varchar(100) DEFAULT 'Sin Componente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ci_items`
--

INSERT INTO `ci_items` (`id_ci`, `nombre`, `tipo`, `descripcion`, `ubicacion`, `responsable`, `estado`, `procesador`, `ram`, `display`, `power_source`) VALUES
(1, 'Router Principal', 'Infraestructura', 'Router Cisco principal', 'Sucursal A', 'Equipo TI', 'Activo', 'ARM Dual-Core', '512 MB', 'Web Interface', 'Adaptador DC'),
(2, 'Servidor de Base de Datos', 'Infraestructura', 'Servidor Dell para base de datos', 'Sucursal B', 'Equipo TI', 'Activo', 'Intel Xeon E5', '32 GB', 'N/A', 'Fuente 650W'),
(3, 'Bicicleta Estática 01', 'Equipamiento', 'Bicicleta conectada con sensores', 'Sucursal A', 'Mantenimiento', 'Activo', 'CPU interno', 'N/A', 'Pantalla LCD integrada', 'Conexión directa'),
(4, 'Cinta de correr', 'Gimnasio', 'Cinta de correr para ejercicios cardiovasculares', 'Sucursal A', 'Mantenimiento', 'Activo', 'CPU interno', 'N/A', 'Pantalla LCD integrada', 'Conexión directa'),
(5, 'Bicicleta estática', 'Gimnasio', 'Bicicleta estática para entrenamiento de piernas', 'Sucursal A', 'Mantenimiento', 'Activo', 'CPU interno', 'N/A', 'Pantalla LCD integrada', 'Conexión directa'),
(6, 'PC de Oficina', 'Computo', 'Computadora de escritorio de uso administrativo', 'Sucursal A', 'Equipo TI', 'Activo', 'Intel Core i5', '8 GB', 'Monitor 21\"', 'Fuente 500W'),
(7, 'Router Wi-Fi', 'Red', 'Router para conexión inalámbrica en la sucursal', 'Sucursal A', 'Equipo TI', 'Activo', 'ARM Cortex', '1 GB', 'LED Status', 'Adaptador DC'),
(8, 'Proyector Multimedia', 'Equipamiento', 'Proyector multimedia para presentaciones', 'Sucursal A', 'Mantenimiento', 'En mantenimiento', 'N/A', 'N/A', 'Proyección óptica', 'Adaptador 90W'),
(9, 'Máquina de pesas', 'Gimnasio', 'Equipo de pesas para entrenamiento de fuerza', 'Sucursal B', 'Mantenimiento', 'Activo', 'CPU interno', 'N/A', 'Pantalla LCD integrada', 'Conexión directa'),
(10, 'Elíptica', 'Gimnasio', 'Elíptica para ejercicios cardiovasculares', 'Sucursal B', 'Mantenimiento', 'Dañado', 'CPU interno', 'N/A', 'Pantalla LCD integrada', 'Conexión directa'),
(11, 'Laptop de oficina', 'Computo', 'Laptop para uso administrativo', 'Sucursal B', 'Equipo TI', 'Activo', 'Intel Core i7', '16 GB', 'Pantalla 15.6\"', 'Batería interna'),
(12, 'Switch de red', 'Red', 'Switch para distribución de red en la sucursal', 'Sucursal B', 'Equipo TI', 'Activo', 'ARM Cortex-A53', '2 GB', 'N/A', 'Corriente directa'),
(13, 'Pantalla LED', 'Equipamiento', 'Pantalla LED de 50\" para presentaciones', 'Sucursal B', 'Mantenimiento', 'Activo', 'N/A', 'N/A', 'Panel LED', 'Corriente AC'),
(14, 'Switch Cisco', 'Red', 'switch de red de 24 puertos ethernet', 'Sucursal B', 'EquipoTi', 'Activo', 'ARM Dual-Core', '512 MB', 'Web Interface', 'Adaptador DC');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidentes`
--

CREATE TABLE `incidentes` (
  `id_incidente` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `id_ci_afectado` int(11) NOT NULL,
  `estado` enum('enviado','en proceso','terminado','liberado','evaluado','rechazada') DEFAULT 'enviado',
  `prioridad` enum('Baja','Media','Alta','Crítica') DEFAULT 'Media',
  `fecha_reporte` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_resolucion` timestamp NULL DEFAULT NULL,
  `fecha_limite` date DEFAULT NULL,
  `responsable` varchar(100) NOT NULL,
  `id_tecnico_asignado` int(11) DEFAULT NULL,
  `resolucion` text DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `estrellas` tinyint(3) UNSIGNED DEFAULT NULL,
  `comentario_encargado` text DEFAULT NULL,
  `tiempo_estimado_total` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `incidentes`
--

INSERT INTO `incidentes` (`id_incidente`, `titulo`, `descripcion`, `id_ci_afectado`, `estado`, `prioridad`, `fecha_reporte`, `fecha_resolucion`, `fecha_limite`, `responsable`, `id_tecnico_asignado`, `resolucion`, `diagnostico`, `estrellas`, `comentario_encargado`, `tiempo_estimado_total`) VALUES
(1, 'Falla en Router', 'El router principal no responde.', 1, 'en proceso', 'Crítica', '2025-03-20 22:17:23', NULL, '2025-06-03', 'Juan Pérez', 12, NULL, NULL, NULL, NULL, 0),
(7, 'Daño en polea de eliptica', 'cambio de cuerda de eliptica', 10, 'enviado', 'Baja', '2025-03-21 07:03:55', NULL, NULL, 'carlos rodriguez', 9, NULL, NULL, NULL, NULL, 0),
(8, 'Daño en display de cinta de correr', 'la pantalla se distorsiona', 4, 'en proceso', 'Media', '2025-05-27 02:09:04', NULL, NULL, 'Juan Pérez', 2, NULL, NULL, NULL, NULL, 0),
(9, 'Instalacion de Office', 'Instalar paquete office, teamviewer y teams', 6, 'terminado', 'Media', '2025-05-27 03:23:30', NULL, '2025-05-28', 'Juan Pérez', 6, NULL, NULL, NULL, NULL, 0),
(10, 'Internet Lento', 'El internet tarda mucho en dar respuesta', 7, 'en proceso', 'Media', '2025-05-27 04:54:20', NULL, '2025-06-06', 'Juan Pérez', 4, NULL, NULL, NULL, NULL, 0),
(11, 'Cambio de cable ethernet', 'cambiar cables ethernet dañados', 1, 'enviado', 'Media', '2025-05-27 06:18:48', NULL, NULL, 'Juan Pérez', 4, NULL, NULL, NULL, NULL, 0),
(12, 'No se escuchan los altavoces de la laptop', 'la latop de oficina no se escuchan los altavoces para nada', 11, 'evaluado', 'Alta', '2025-06-02 03:43:06', '2025-06-04 05:34:56', '2025-06-04', 'zeth jimenez', 1, 'Se instalaron los drivers de Realtek Audio para posteriormente hacer pruebas con el usuario.', 'el equipo no tiene los drivers de audio correctamente instalados.', 5, 'Muy bien :)', 95),
(13, 'prueba1', 'prueba1', 6, 'en proceso', 'Baja', '2025-06-03 02:47:39', NULL, '2025-06-06', 'Juan Pérez', 3, NULL, NULL, NULL, NULL, 0),
(14, 'Error al acceder a base da datos', 'Al cceder ala base de datos se presenta el error: Can\'t connect to MySQL server on \'localhost\' (10061)', 2, 'evaluado', 'Crítica', '2025-06-04 05:42:13', '2025-06-04 05:56:20', '2025-06-04', 'zeth jimenez', 7, 'Se realizó un diagnóstico en el servidor de base de datos BD-01 debido a un error de conexión reportado. Al revisar el estado del servicio, se detectó que el servidor MySQL se encontraba detenido. Se procedió a iniciar manualmente el servicio MySQL y se confirmó su arranque exitoso. Posteriormente, se verificó que el puerto 3306 estuviera habilitado en el firewall, asegurando así que no hubiera bloqueos de red que impidieran la conexión.', 'El servicio de MySQL no está activo o el puerto de conexión (por defecto 3306) está bloqueado por el firewall.', 4, 'Excelente servicio por parte del personal de ayuda, solo falto mas cordialidad', 120);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos_incidente`
--

CREATE TABLE `recursos_incidente` (
  `id_recurso_incidente` int(11) NOT NULL,
  `id_incidente` int(11) DEFAULT NULL,
  `id_recurso` int(11) DEFAULT NULL,
  `cantidad_utilizada` int(11) DEFAULT 1,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recursos_incidente`
--

INSERT INTO `recursos_incidente` (`id_recurso_incidente`, `id_incidente`, `id_recurso`, `cantidad_utilizada`, `fecha_registro`) VALUES
(5, 14, 13, 1, '2025-06-03 22:50:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_incidente`
--

CREATE TABLE `servicios_incidente` (
  `id_servicio_incidente` int(11) NOT NULL,
  `id_incidente` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios_incidente`
--

INSERT INTO `servicios_incidente` (`id_servicio_incidente`, `id_incidente`, `id_servicio`, `fecha_registro`) VALUES
(12, 12, 9, '2025-06-03 18:13:07'),
(13, 12, 5, '2025-06-03 21:35:39'),
(14, 14, 10, '2025-06-03 22:49:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_bc`
--

CREATE TABLE `solicitudes_bc` (
  `id_solicitud` int(11) NOT NULL,
  `id_tecnico` int(11) NOT NULL,
  `id_incidente` int(11) NOT NULL,
  `error` text NOT NULL,
  `solucion` text NOT NULL,
  `estado` enum('pendiente','aceptado','rechazado') DEFAULT 'pendiente',
  `fecha_solicitud` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_bc`
--

INSERT INTO `solicitudes_bc` (`id_solicitud`, `id_tecnico`, `id_incidente`, `error`, `solucion`, `estado`, `fecha_solicitud`) VALUES
(4, 7, 14, 'Error al acceder a base da datos', 'Se realizó un diagnóstico en el servidor de base de datos BD-01 debido a un error de conexión reportado. Al revisar el estado del servicio, se detectó que el servidor MySQL se encontraba detenido. Se procedió a iniciar manualmente el servicio MySQL y se confirmó su arranque exitoso. Posteriormente, se verificó que el puerto 3306 estuviera habilitado en el firewall, asegurando así que no hubiera bloqueos de red que impidieran la conexión.', '', '2025-06-03 22:56:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_nuevo_servicio`
--

CREATE TABLE `solicitudes_nuevo_servicio` (
  `id_solicitud` int(11) NOT NULL,
  `id_tecnico` int(11) DEFAULT NULL,
  `nombre_servicio` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `costo_sugerido` decimal(10,2) DEFAULT NULL,
  `tiempo_estimado_minutos` int(11) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `fecha_solicitud` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_nuevo_servicio`
--

INSERT INTO `solicitudes_nuevo_servicio` (`id_solicitud`, `id_tecnico`, `nombre_servicio`, `descripcion`, `costo_sugerido`, `tiempo_estimado_minutos`, `estado`, `fecha_solicitud`) VALUES
(1, 1, 'Configuracion de IPV4 y IPV6.', 'Configurar las IPs del equipo de computo en cuestion para su funcionamiento en la red.', 300.00, 40, 'aprobado', '2025-06-03 15:31:24'),
(2, 7, 'Configuracion de Puertos en Base de datos', 'Configurar los puertos correspondientes de la base de datos para dar acceso al usuario', 1500.00, 120, 'aprobado', '2025-06-03 22:48:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_recursos`
--

CREATE TABLE `solicitudes_recursos` (
  `id_solicitud` int(11) NOT NULL,
  `id_tecnico` int(11) DEFAULT NULL,
  `id_incidente` int(11) DEFAULT NULL,
  `id_recurso` int(11) DEFAULT NULL,
  `cantidad_solicitada` int(11) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `fecha_solicitud` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_recursos`
--

INSERT INTO `solicitudes_recursos` (`id_solicitud`, `id_tecnico`, `id_incidente`, `id_recurso`, `cantidad_solicitada`, `estado`, `fecha_solicitud`) VALUES
(1, 1, 12, 1, 1, 'aprobado', '2025-06-03 18:56:26'),
(2, 1, 12, 10, 1, 'aprobado', '2025-06-03 19:08:10'),
(3, 7, 14, 1, 1, 'aprobado', '2025-06-03 22:49:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tecnicos`
--

CREATE TABLE `tecnicos` (
  `id_tecnico` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `especialidad` varchar(100) NOT NULL,
  `disponibilidad` enum('Disponible','En servicio') NOT NULL DEFAULT 'Disponible',
  `rfc` char(13) NOT NULL DEFAULT '',
  `desempeno` decimal(3,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tecnicos`
--

INSERT INTO `tecnicos` (`id_tecnico`, `nombre`, `especialidad`, `disponibilidad`, `rfc`, `desempeno`) VALUES
(1, 'José Pérez', 'Técnico en redes', 'En servicio', 'PERJ920101ABD', 5.00),
(2, 'Manuel Ruiz', 'Técnico en mantenimiento de equipo de gym', 'En servicio', 'RUIM910305XYZ', 0.00),
(3, 'Carlos López', 'Técnico en redes', 'En servicio', 'LOPC900823DEF', 0.00),
(4, 'Ana Martínez', 'Técnico en infraestructura', 'En servicio', 'MAAA880712LMN', 0.00),
(5, 'Luis Gómez', 'Técnico en equipos de gimnasio', 'En servicio', 'GOML850419TUV', 0.00),
(6, 'Pedro Fernández', 'Técnico en mantenimiento de computadoras', 'Disponible', 'FERP930615JKL', 0.00),
(7, 'Sofía Sánchez', 'Técnico en mantenimiento de servidores', 'En servicio', 'SANS970201ZXC', 4.00),
(8, 'Miguel Díaz', 'Técnico en mantenimiento de redes', 'En servicio', 'DIAM890725HJK', 0.00),
(9, 'Claudia Rodríguez', 'Técnico en equipos de gimnasio', 'Disponible', 'RODC940318BNM', 0.00),
(10, 'Fernando Torres', 'Técnico en equipos de red', 'Disponible', 'TORF960522POI', 0.00),
(11, 'Javier Pérez', 'Técnico en mantenimiento de servidores', 'En servicio', 'PERJ930811QWE', 0.00),
(12, 'Raúl Jiménez', 'Técnico en infraestructura y redes', 'En servicio', 'JIMR920920ASD', 0.00),
(13, 'Mario Bros', 'Ingeniero de Redes', 'Disponible', 'PLRJ920101ABZ', 0.00),
(14, 'Joaquin Lopez', 'Técnico en redes', 'Disponible', 'PERP930104ABK', 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo` enum('encargado','admin') NOT NULL,
  `sucursal` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `password`, `tipo`, `sucursal`) VALUES
(4, 'Admin General', 'admin@smartfit.com', '$2y$10$zjPCboblqWCUkS4gRm4UlOZ3e601si/pXobSQaKhF.6S8I/r4h/NC', 'admin', NULL),
(5, 'Juan Pérez', 'juan@smartfit.com', '$2y$10$8KD.gPZxomAk.81DL79TPelvMhzCIXDRVLsS52P616P8fu3lC79jG', 'encargado', 'Sucursal A'),
(8, 'carlos rodriguez', 'carlillos@outlook.com', '$2y$10$XIgARkF/8hmSqpWD9Qwoz.tyqU.EVwniXF00FhUK.EvePr3/8Rx3i', 'encargado', 'Sucursal B'),
(9, 'zeth jimenez', 'zeth@gmail.com', '$2y$10$t4OfNsx1cuB/MNNrzcIn6Or/vjkAqxLQ2ayk1V4CVEC2496x12.qa', 'encargado', 'Sucursal B'),
(11, 'alexis guillen', 'guillen@gmail.com', '$2y$10$Ek/Nq.ZVoWHHwb8stpTzIOtaTE836p.LYUse3CBIcxHOwbojOFmz6', 'encargado', 'Sucursal B'),
(12, 'Ismael Rodriguez', 'mayel14@hotmail.com', '$2y$10$qdgJl7nsBJm69ZWuXMSwpO/I4eCLNgz7ittY5J/MVznSt9zbj4u4C', 'encargado', 'Sucursal B'),
(13, 'Alonso Terraza', 'alont3@gmail.com', '$2y$10$uXO2M56iv/a2CfGAB/R9eOdvczq66OlYC/7FLDFg.E1TulDH.jAuG', 'encargado', 'Sucursal A');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `base_de_conocimiento`
--
ALTER TABLE `base_de_conocimiento`
  ADD PRIMARY KEY (`id_entry`),
  ADD KEY `id_tecnico` (`id_tecnico`);

--
-- Indices de la tabla `catalogo_recursos`
--
ALTER TABLE `catalogo_recursos`
  ADD PRIMARY KEY (`id_recurso`);

--
-- Indices de la tabla `catalogo_servicios`
--
ALTER TABLE `catalogo_servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `ci_items`
--
ALTER TABLE `ci_items`
  ADD PRIMARY KEY (`id_ci`);

--
-- Indices de la tabla `incidentes`
--
ALTER TABLE `incidentes`
  ADD PRIMARY KEY (`id_incidente`),
  ADD KEY `id_ci_afectado` (`id_ci_afectado`),
  ADD KEY `id_tecnico_asignado` (`id_tecnico_asignado`);

--
-- Indices de la tabla `recursos_incidente`
--
ALTER TABLE `recursos_incidente`
  ADD PRIMARY KEY (`id_recurso_incidente`),
  ADD KEY `id_incidente` (`id_incidente`),
  ADD KEY `id_recurso` (`id_recurso`);

--
-- Indices de la tabla `servicios_incidente`
--
ALTER TABLE `servicios_incidente`
  ADD PRIMARY KEY (`id_servicio_incidente`),
  ADD KEY `id_incidente` (`id_incidente`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `solicitudes_bc`
--
ALTER TABLE `solicitudes_bc`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `id_tecnico` (`id_tecnico`),
  ADD KEY `id_incidente` (`id_incidente`);

--
-- Indices de la tabla `solicitudes_nuevo_servicio`
--
ALTER TABLE `solicitudes_nuevo_servicio`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `id_tecnico` (`id_tecnico`);

--
-- Indices de la tabla `solicitudes_recursos`
--
ALTER TABLE `solicitudes_recursos`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `id_tecnico` (`id_tecnico`),
  ADD KEY `fk_incidente_solicitud` (`id_incidente`),
  ADD KEY `fk_recurso_solicitud` (`id_recurso`);

--
-- Indices de la tabla `tecnicos`
--
ALTER TABLE `tecnicos`
  ADD PRIMARY KEY (`id_tecnico`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `base_de_conocimiento`
--
ALTER TABLE `base_de_conocimiento`
  MODIFY `id_entry` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `catalogo_recursos`
--
ALTER TABLE `catalogo_recursos`
  MODIFY `id_recurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `catalogo_servicios`
--
ALTER TABLE `catalogo_servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `ci_items`
--
ALTER TABLE `ci_items`
  MODIFY `id_ci` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `incidentes`
--
ALTER TABLE `incidentes`
  MODIFY `id_incidente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `recursos_incidente`
--
ALTER TABLE `recursos_incidente`
  MODIFY `id_recurso_incidente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `servicios_incidente`
--
ALTER TABLE `servicios_incidente`
  MODIFY `id_servicio_incidente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `solicitudes_bc`
--
ALTER TABLE `solicitudes_bc`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudes_nuevo_servicio`
--
ALTER TABLE `solicitudes_nuevo_servicio`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `solicitudes_recursos`
--
ALTER TABLE `solicitudes_recursos`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tecnicos`
--
ALTER TABLE `tecnicos`
  MODIFY `id_tecnico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `base_de_conocimiento`
--
ALTER TABLE `base_de_conocimiento`
  ADD CONSTRAINT `base_de_conocimiento_ibfk_1` FOREIGN KEY (`id_tecnico`) REFERENCES `tecnicos` (`id_tecnico`);

--
-- Filtros para la tabla `incidentes`
--
ALTER TABLE `incidentes`
  ADD CONSTRAINT `incidentes_ibfk_1` FOREIGN KEY (`id_ci_afectado`) REFERENCES `ci_items` (`id_ci`),
  ADD CONSTRAINT `incidentes_ibfk_2` FOREIGN KEY (`id_tecnico_asignado`) REFERENCES `tecnicos` (`id_tecnico`);

--
-- Filtros para la tabla `recursos_incidente`
--
ALTER TABLE `recursos_incidente`
  ADD CONSTRAINT `recursos_incidente_ibfk_1` FOREIGN KEY (`id_incidente`) REFERENCES `incidentes` (`id_incidente`),
  ADD CONSTRAINT `recursos_incidente_ibfk_2` FOREIGN KEY (`id_recurso`) REFERENCES `catalogo_recursos` (`id_recurso`);

--
-- Filtros para la tabla `servicios_incidente`
--
ALTER TABLE `servicios_incidente`
  ADD CONSTRAINT `servicios_incidente_ibfk_1` FOREIGN KEY (`id_incidente`) REFERENCES `incidentes` (`id_incidente`),
  ADD CONSTRAINT `servicios_incidente_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `catalogo_servicios` (`id_servicio`);

--
-- Filtros para la tabla `solicitudes_bc`
--
ALTER TABLE `solicitudes_bc`
  ADD CONSTRAINT `solicitudes_bc_ibfk_1` FOREIGN KEY (`id_tecnico`) REFERENCES `tecnicos` (`id_tecnico`),
  ADD CONSTRAINT `solicitudes_bc_ibfk_2` FOREIGN KEY (`id_incidente`) REFERENCES `incidentes` (`id_incidente`);

--
-- Filtros para la tabla `solicitudes_nuevo_servicio`
--
ALTER TABLE `solicitudes_nuevo_servicio`
  ADD CONSTRAINT `solicitudes_nuevo_servicio_ibfk_1` FOREIGN KEY (`id_tecnico`) REFERENCES `tecnicos` (`id_tecnico`);

--
-- Filtros para la tabla `solicitudes_recursos`
--
ALTER TABLE `solicitudes_recursos`
  ADD CONSTRAINT `fk_incidente_solicitud` FOREIGN KEY (`id_incidente`) REFERENCES `incidentes` (`id_incidente`),
  ADD CONSTRAINT `fk_recurso_solicitud` FOREIGN KEY (`id_recurso`) REFERENCES `catalogo_recursos` (`id_recurso`),
  ADD CONSTRAINT `solicitudes_recursos_ibfk_1` FOREIGN KEY (`id_tecnico`) REFERENCES `tecnicos` (`id_tecnico`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
