DROP TABLE IF EXISTS `Oferta_Producto`;
DROP TABLE IF EXISTS `Oferta`;
DROP TABLE IF EXISTS `Pedido_Producto`;
DROP TABLE IF EXISTS `Pedido`;
DROP TABLE IF EXISTS `Producto`;
DROP TABLE IF EXISTS `Categoria`;
DROP TABLE IF EXISTS `Usuarios`;


CREATE TABLE `Usuarios` (
    `nombreUsuario` varchar(20) NOT NULL,
    `nombre` varchar(40) NOT NULL,
    `apellidos` varchar(50) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    `rol` int NOT NULL, 
    `avatar` varchar(100) NOT NULL,
    PRIMARY KEY (`nombreUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Pedido` (
    `fecha_hora` datetime NOT NULL,
    `num_pedido` int NOT NULL,
    `tipo` varchar(30) NOT NULL,
    `total` float DEFAULT 0.0,
    `subtotal` float DEFAULT 0.0,
    `descuento` float DEFAULT 0.0,
    `estado` varchar(25) NOT NULL,
    `cliente` varchar(20) NOT NULL,
    `camarero` varchar(20) DEFAULT NULL,
    `cocinero` varchar(20) DEFAULT NULL,
    PRIMARY KEY (`fecha_hora`, `num_pedido`),
    FOREIGN KEY (`cliente`) REFERENCES `Usuarios`(`nombreUsuario`),
    FOREIGN KEY (`camarero`) REFERENCES `Usuarios`(`nombreUsuario`),
    FOREIGN KEY (`cocinero`) REFERENCES `Usuarios`(`nombreUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Categoria` (
    `nombre` varchar(30) NOT NULL,
    `descripcion` varchar(100) NOT NULL,
    PRIMARY KEY (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Producto` (
    `nombre` varchar(30) NOT NULL,
    `precio` float NOT NULL,
    `disponibilidad` boolean NOT NULL DEFAULT 1,
    `iva` float NOT NULL DEFAULT 21,
    `ofertado` boolean NOT NULL DEFAULT 0,
    `descripcion` varchar(100) NOT NULL,
    `imagen` varchar(100) NOT NULL,
    `cocinable` boolean NOT NULL,
    `categoria` varchar(30) NOT NULL,
    PRIMARY KEY (`nombre`),
    FOREIGN KEY (`categoria`) REFERENCES `Categoria`(`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Pedido_Producto` (
    `nombre` varchar(30) NOT NULL,
    `cantidad` int NOT NULL DEFAULT 1,
    `fecha_hora` datetime NOT NULL,
    `num_pedido` int NOT NULL,
    `preparado` boolean NOT NULL DEFAULT 0,
    PRIMARY KEY (`nombre`, `fecha_hora`, `num_pedido`),
    FOREIGN KEY (`nombre`) REFERENCES `Producto` (`nombre`),
    FOREIGN KEY (`fecha_hora`, `num_pedido`) REFERENCES `Pedido` (`fecha_hora`, `num_pedido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Oferta` (
    `id_oferta` int not null auto_increment,
    `nombre` varchar(50) NOT NULL,
    `descripcion` varchar(100) NOT NULL,
    `fecha_ini` datetime NOT NULL,
    `fecha_fin` datetime NOT NULL,
    `descuento` decimal(5,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (`id_oferta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Oferta_Producto` (
    `nombre_producto` varchar(30) NOT NULL,
    `id_oferta` int NOT NULL,
    `cantidad` int NOT NULL,
    PRIMARY KEY (`nombre_producto`, `id_oferta`),
    FOREIGN KEY (`nombre_producto`) REFERENCES `Producto` (`nombre`),
    FOREIGN KEY (`id_oferta`) REFERENCES `Oferta` (`id_oferta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Pedido_Ofertas` (
    `id_oferta` int NOT NULL,
    `fecha_hora` datetime NOT NULL,
    `num_pedido` int NOT NULL,
    `cantidad_aplicada` int NOT NULL DEFAULT 1,
    PRIMARY KEY (`id_oferta`, `fecha_hora`, `num_pedido`),
    FOREIGN KEY (`id_oferta`) REFERENCES `Oferta` (`id_oferta`),
    FOREIGN KEY (`fecha_hora`, `num_pedido`) REFERENCES `Pedido` (`fecha_hora`, `num_pedido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

