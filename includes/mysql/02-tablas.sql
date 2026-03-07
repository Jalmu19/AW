DROP TABLE IF EXISTS `Usuario`;
DROP TABLE IF EXISTS `Categoria`;
DROP TABLE IF EXISTS `Producto`;
DROP TABLE IF EXISTS `Pedido`;
DROP TABLE IF EXISTS `Pedido_Producto`
DROP TABLE IF EXISTS `Cocinero_Producto`



CREATE TABLE IF NOT EXISTS `Usuario` (
    `nombre` varchar(15) NOT NULL,
    `apellidos` varchar(30)  NOT NULL,
    `email` varchar(30)  NOT NULL,
    `contraseña_hash` varchar(255) NOT NULL,
    `rol` ENUM('gerente', 'camarero', 'cocinero', 'cliente') NOT NULL,
    `nombre_usuario` varchar(30) NOT NULL,

    PRIMARY KEY(`nombre_usuario`)

) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `Pedido` (
    `fecha_hora` datetime NOT NULL,
    `num_pedido` int NOT NULL,
    `tipo` varchar(30) NOT NULL,
    `total` float,
    `estado` varchar(11) NOT NULL,

    `cliente` varchar(30) NOT NULL,
    `camarero` varchar(30) NOT NULL,
    `cocinero` varchar(30) NOT NULL,

    PRIMARY KEY(`fecha_hora`, `num_pedido`),
    KEY `num_pedido` (`num_pedido`), --para acelerar busquedas
    FOREIGN KEY (`cliente`) REFERENCES `Usuario`(`nombre_usuario`)
    FOREIGN KEY (`camarero`) REFERENCES `Usuario`(`nombre_usuario`)
    FOREIGN KEY (`cocinero`) REFERENCES `Usuario`(`nombre_usuario`)
    
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `Categoria` (
    `nombre` varchar(15) NOT NULL,
    `descripcion` varchar(50) NOT NULL,

    `gerente` varchar(30) NOT NULL,

    PRIMARY KEY(`nombre`),
    FOREIGN KEY (`gerente`) REFERENCES `Usuario`(`nombre_usuario`)
    
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `Producto` (
    `nombre` varchar(15) NOT NULL,
    `precio` float NOT NULL,
    `disponibilidad` boolean NOT NULL,
    `iva` float NOT NULL,
    `ofertado` boolean NOT NULL,
    `descripcion` varchar(50) NOT NULL,
    `imagen` varchar(50) NOT NULL,
    `cocinable` boolean NOT NULL,
    `categoria` varchar(30) NOT NULL,

    PRIMARY KEY(`nombre`),
    FOREIGN KEY (`categoria`) REFERENCES `Categoria`(`nombre`)
    
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `Pedido_Producto` (
    `nombre` varchar(15) NOT NULL,
    `fecha_hora` datetime NOT NULL,
    `num_pedido` int NOT NULL,
    `preparado` boolean NOT NULL,

    PRIMARY KEY(`nombre`, `fecha_hora`, `num_pedido`),
    FOREIGN KEY (`nombre`) REFERENCES `Producto`(`nombre`)
    FOREIGN KEY (`fecha_hora`,  `num_pedido`) REFERENCES  `Pedido`(`fecha_hora`, `num_pedido`)
    
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `Cocinero_Producto` (
    `cocinero` varchar(15) NOT NULL,
    `nombre_producto`varchar(15)  int NOT NULL,

    PRIMARY KEY(`cocinero`, `nombre_producto`),
    FOREIGN KEY (`cocinero`) REFERENCES `Usuario`(`nombre_usuario`)
    FOREIGN KEY (`nombre_producto`) REFERENCES `Producto`(`nombre`)   
    
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

