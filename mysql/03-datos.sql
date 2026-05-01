

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
INSERT INTO Producto VALUES('agua', 1.5, true, 21, true, 'botella de agua','agua.png', false,'bebida');


INSERT INTO Pedido VALUES('2026-04-22 22:00:00', 1, 'en local', 10.0, 10.0, 0.0, 'Cocinando', 'martita', 'ana', 'silvia');
INSERT INTO Pedido VALUES('2026-04-23 15:02:00', 2, 'en local', 20.0, 20.0, 0.0, 'Cocinando', 'martita', 'ana', 'marcos');
INSERT INTO Pedido VALUES('2026-04-25 12:24:00', 3,'a domicilio' , 4.50, 4.50, 0.0, 'Cocinando', 'martita', 'juan', 'marcos');
INSERT INTO Pedido VALUES('2026-04-26 20:46:00', 4,'a domicilio' , 10.0, 10.0, 0.0, 'Cocinando', 'jose', 'juan', 'silvia');
INSERT INTO Pedido VALUES('2026-04-27 19:37:00', 5, 'a domicilio', 13.50, 13.50, 0.0, 'Cocinando', 'jose', 'ana', 'silvia');

INSERT INTO Pedido VALUES('2026-04-28 14:30:00', 6, 'en local', 6.53, 7.25, 10.0, 'Recibido', 'jose', 'juan', 'marcos');
INSERT INTO Pedido VALUES('2026-04-20 15:00:00', 7, 'en local', 21.6, 27, 20.0, 'Recibido', 'jose', 'juan', 'silvia');
INSERT INTO Pedido VALUES('2026-04-21 9:00:00', 8, 'en local', 6.53, 7.25, 10.0, 'Recibido', 'martita', 'juan', 'silvia');
INSERT INTO Pedido VALUES('2024-04-22 9:15:00', 9, 'a domicilio', 6.53, 7.25, 10.0, 'Recibido', 'martita', 'juan', 'marcos');


INSERT INTO Pedido_Producto VALUES('pasta', 1, '2026-02-22 22:00:00', 1, false);
INSERT INTO Pedido_Producto VALUES('pollo', 2, '2026-02-23 15:02:00', 2, false);
INSERT INTO Pedido_Producto VALUES('brownie', 1, '2026-02-25 12:24:00', 3, false);
INSERT INTO Pedido_Producto VALUES('pollo', 1, '2026-02-26 20:46:00', 4, false);
INSERT INTO Pedido_Producto VALUES('salmon', 1, '2026-02-27 19:37:00', 5, false);
INSERT INTO Pedido_Producto VALUES('coca-cola', 1, '2026-02-27 19:37:00', 5, false);

INSERT INTO Pedido_Producto VALUES('cafe con leche', 1, '2026-04-28 14:30:00', 6, false);
INSERT INTO Pedido_Producto VALUES('brownie', 1, '2026-04-28 14:30:00', 6, false);
INSERT INTO Pedido_Producto VALUES('tiramisu', 1, '2026-04-20 15:00:00', 7, false);
INSERT INTO Pedido_Producto VALUES('brownie', 2, '2026-04-20 15:00:00', 7, false);
INSERT INTO Pedido_Producto VALUES('cafe con leche', 1, '2026-04-21 9:00:00', 8, false);
INSERT INTO Pedido_Producto VALUES('brownie', 1, '2026-04-21 9:00:00', 8, false);
INSERT INTO Pedido_Producto VALUES('cafe con leche', 1, '2024-04-22 9:15:00', 9, false);
INSERT INTO Pedido_Producto VALUES('brownie', 1, '2024-04-22 9:15:00', 9, false);


INSERT INTO Oferta(`nombre`, `descripcion`, `fecha_ini`, `fecha_fin`, `descuento`) VALUES('Despertar dulce', 'Desayuna lo mejor del dia', '2026-03-18 9:00:00', '2026-12-18 9:00:00', 10.0);
INSERT INTO Oferta(`nombre`, `descripcion`, `fecha_ini`, `fecha_fin`, `descuento`) VALUES('Puente de mayo', 'Preparate para el verano', '2026-03-20 9:00:00', '2026-05-20 9:00:00', 20.0);
INSERT INTO Oferta(`nombre`, `descripcion`, `fecha_ini`, `fecha_fin`, `descuento`) VALUES('Veraneo', 'Refresca tu cuerpo', '2026-05-20 9:00:00', '2026-09-20 9:00:00', 20.0);
INSERT INTO Oferta(`nombre`, `descripcion`, `fecha_ini`, `fecha_fin`, `descuento`) VALUES('Doble tentacion', 'Endulzate la vida', '2026-03-20 9:00:00', '2026-05-20 9:00:00', 20.0);

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

INSERT INTO Pedido_Ofertas (id_oferta, fecha_hora, num_pedido, cantidad_aplicada) VALUES (1, '2026-04-28 14:30:00', 6, 1);
INSERT INTO Pedido_Ofertas (id_oferta, fecha_hora, num_pedido, cantidad_aplicada) VALUES (4, '2026-04-20 15:00:00', 7, 2);
INSERT INTO Pedido_Ofertas (id_oferta, fecha_hora, num_pedido, cantidad_aplicada) VALUES (1, '2024-04-21 09:00:00', 8, 1);
INSERT INTO Pedido_Ofertas (id_oferta, fecha_hora, num_pedido, cantidad_aplicada) VALUES (1, '2024-04-22 09:15:00', 9, 1);