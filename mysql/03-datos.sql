

INSERT INTO Usuarios VALUES('martita', 'Marta', 'Pérez Gómez', 'marta@gmail.com', '$2y$10$G4jdtAR1AibJm0/PGgI7cunmySeB0vbPaDV5.G/LddxoOzZ8ySbYm', 1, 'avatar24.png');
INSERT INTO Usuarios VALUES('jose', 'Jose', 'Sánchez López', 'jose@gmail.com', '$2y$10$G4jdtAR1AibJm0/PGgI7cunmySeB0vbPaDV5.G/LddxoOzZ8ySbYm',1,'avatar11.png');
INSERT INTO Usuarios VALUES('ana', 'Ana', 'Fernández Martín', 'ana@gmail.com', '$2y$10$G4jdtAR1AibJm0/PGgI7cunmySeB0vbPaDV5.G/LddxoOzZ8ySbYm',2,'avatar22.png');
INSERT INTO Usuarios VALUES('juan', 'Juan', 'Pérez Ruiz', 'juan@gmail.com', '$2y$10$G4jdtAR1AibJm0/PGgI7cunmySeB0vbPaDV5.G/LddxoOzZ8ySbYm', 2, 'avatar13.png');
INSERT INTO Usuarios VALUES('silvia','Silvia', 'Díaz Moreno', 'silvia@gmail.com', '$2y$10$G4jdtAR1AibJm0/PGgI7cunmySeB0vbPaDV5.G/LddxoOzZ8ySbYm', 3, 'avatar2.png');
INSERT INTO Usuarios VALUES('marcos','Marcos', 'Alonso Gutierrez', 'marcos@gmail.com', '$2y$10$G4jdtAR1AibJm0/PGgI7cunmySeB0vbPaDV5.G/LddxoOzZ8ySbYm', 3, 'avatar7.png');
INSERT INTO Usuarios VALUES('emma', 'Emma', 'Ortiz Gómez', 'emma@gmail.com', '$2y$10$G4jdtAR1AibJm0/PGgI7cunmySeB0vbPaDV5.G/LddxoOzZ8ySbYm', 4, 'avatar4.png');

INSERT INTO Categoria VALUES('entrante', 'entrantes');
INSERT INTO Categoria VALUES('primer plato', 'primeros platos');
INSERT INTO Categoria VALUES('segundo plato', 'segundos platos');
INSERT INTO Categoria VALUES('postre', 'postres caseros');
INSERT INTO Categoria VALUES('bebida', 'bebidas');

INSERT INTO Producto VALUES('croquetas', 7.0, true, 21, true, 'croquetas de jamón','croquetas.png',true,'entrante');
INSERT INTO Producto VALUES('pasta', 10.0, true, 21, true, 'macarrones con salsa boloñesa','pasta.png',true,'primer plato');
INSERT INTO Producto VALUES('salmorejo', 6.0, true, 21, true, 'salmorejo cordobés','salmorejo.png',true,'primer plato');
INSERT INTO Producto VALUES('salmon', 11.0, true, 21, true, 'salmón al horno','salmon.png',true,'segundo plato');
INSERT INTO Producto VALUES('pollo', 10.0, true, 21, true, 'pollo con patatas','pollo.png',true,'segundo plato');
INSERT INTO Producto VALUES('tiramisu', 4.50, true, 21, true, 'tiramisu italiano','tiramisu.png',true,'postre');
INSERT INTO Producto VALUES('brownie',4.50, true, 21, true, 'brownie de chocolate','brownie.png',true, 'postre');
INSERT INTO Producto VALUES('coca-cola', 2.5, true, 21, true, 'coca-cola','coca_cola.png', false,'bebida');
INSERT INTO Producto VALUES('cafe solo', 2.5, true, 21, true, 'cafe solo','cafe_solo.png', false,'bebida');
INSERT INTO Producto VALUES('cafe con leche', 2.75, true, 21, true, 'cafe con leche','cafe_con_leche.png', false,'bebida');

INSERT INTO Pedido VALUES('2025-02-12 22:00:00', 1, 'en local', 22.50, 'Cocinando', 'martita', 'ana', 'silvia');
INSERT INTO Pedido VALUES('2025-02-13 15:02:00', 2, 'en local', 12.25, 'Cocinando', 'martita', 'ana', 'marcos');
INSERT INTO Pedido VALUES('2025-02-15 12:24:00', 3,'a domicilio' , 30.20, 'Cocinando', 'martita', 'juan', 'marcos');
INSERT INTO Pedido VALUES('2025-02-16 20:46:00', 4,'a domicilio' , 7.40, 'Cocinando', 'jose', 'juan', 'silvia');
INSERT INTO Pedido VALUES('2025-02-17 19:37:00', 5, 'a domicilio', 10.50, 'Cocinando', 'jose', 'ana', 'silvia');

INSERT INTO Pedido_Producto VALUES('pasta', 1, '12-02-2025 22:00:00', 1, false);
INSERT INTO Pedido_Producto VALUES('pollo', 2, '13-02-2025 15:02:00', 2, false);
INSERT INTO Pedido_Producto VALUES('brownie', 1, '15-02-2025 12:24:00', 3, false);
INSERT INTO Pedido_Producto VALUES('pollo', 1, '16-02-2025 20:46:00', 4, false);
INSERT INTO Pedido_Producto VALUES('salmon', 1, '17-02-2025 19:37:00', 5, false);
INSERT INTO Pedido_Producto VALUES('coca-cola', 1, '17-02-2025 19:37:00', 5, false);

INSERT INTO Oferta(`nombre`, `descripcion`, `fecha_ini`, `fecha_fin`, `descuento`) VALUES('Despertar dulce', 'Desayuna lo mejor del dia', '2026-04-18 9:00:00', '2026-12-18 9:00:00', 10.0);
INSERT INTO Oferta(`nombre`, `descripcion`, `fecha_ini`, `fecha_fin`, `descuento`) VALUES('Puente de mayo', 'Preparate para el verano', '2026-04-20 9:00:00', '2026-05-20 9:00:00', 20.0);
INSERT INTO Oferta(`nombre`, `descripcion`, `fecha_ini`, `fecha_fin`, `descuento`) VALUES('Veraneo', 'Refresca tu cuerpo', '2026-05-20 9:00:00', '2026-09-20 9:00:00', 20.0);
INSERT INTO Oferta(`nombre`, `descripcion`, `fecha_ini`, `fecha_fin`, `descuento`) VALUES('Doble tentacion', 'Endulzate la vida', '2026-04-20 9:00:00', '2026-05-20 9:00:00', 20.0);


INSERT INTO Oferta_Producto VALUES('cafe con leche', 1, 1);
INSERT INTO Oferta_Producto VALUES('brownie', 1, 1);
INSERT INTO Oferta_Producto VALUES('salmorejo', 2, 1);
INSERT INTO Oferta_Producto VALUES('pollo', 2, 1);
INSERT INTO Oferta_Producto VALUES('coca-cola', 2, 1);
INSERT INTO Oferta_Producto VALUES('salmorejo', 3, 1);
INSERT INTO Oferta_Producto VALUES('salmon', 3, 1);
INSERT INTO Oferta_Producto VALUES('agua', 3, 1);
INSERT INTO Oferta_Producto VALUES('tiramisu', 4, 1);
INSERT INTO Oferta_Producto VALUES('brownie', 4, 2);
