INSERT INTO Producto VALUES('croquetas', 7.0, true, 21, true, 'croquetas de jamón','../img/productos/croquetas.png',true,'entrante');
INSERT INTO Producto VALUES('pasta', 10.0, true, 21, true, 'macarrones con salsa boloñesa','../img/productos/pasta.png',true,'primer plato');
INSERT INTO Producto VALUES('salmorejo', 10.0, true, 21, true, 'salmorejo cordobés','../img/productos/salmorejo.png',true,'primer plato');
INSERT INTO Producto VALUES('salmon', 10.0, true, 21, true, 'salmón al horno','../img/productos/salmon.png',true,'segundo plato');
INSERT INTO Producto VALUES('pollo', 10.0, true, 21, true, 'pollo con patatas','../img/productos/pollo.png',true,'segundo plato');
INSERT INTO Producto VALUES('tiramisu', 10.0, true, 21, true, 'tiramisu italiano','../img/productos/tiramisu.png',true,'postre');
INSERT INTO Producto VALUES('brownie', 10.0, true, 21, true, 'brownie de chocolate','../img/productos/brownie.png',true, 'postre');
INSERT INTO Producto VALUES('coca-cola', 2.5, true, 21, true, 'coca-cola','../img/productos/coca_cola.png', false,'bebida');

INSERT INTO Usuarios VALUES('martita', 'Marta', 'Pérez Gómez', 'marta@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Cliente', '../img/avatar.png');
INSERT INTO Usuarios VALUES('jose', 'Jose', 'Sánchez López', 'jose@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Cliente','../img/avatar.png');
INSERT INTO Usuarios VALUES('ana', 'Ana', 'Fernández Martín', 'ana@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Camarero','../img/avatar.png');
INSERT INTO Usuarios VALUES('juan', 'Juan', 'Pérez Ruiz', 'juan@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Camarero', '../img/avatar.png');
INSERT INTO Usuarios VALUES('silvia','Silvia', 'Díaz Moreno', 'silvia@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Cocinero', '../img/avatar.png');
INSERT INTO Usuarios VALUES('marcos','Marcos', 'Alonso Gutierrez', 'marcos@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Cocinero', '../img/avatar.png');
INSERT INTO Usuarios VALUES('emma', 'Emma', 'Ortiz Gómez', 'emma@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Gerente', '../img/avatar.png');

INSERT INTO Categoria VALUES('entrante', 'entrantes', 'emma');
INSERT INTO Categoria VALUES('primer plato', 'primeros platos', 'emma');
INSERT INTO Categoria VALUES('segundo plato', 'segundos platos', 'emma');
INSERT INTO Categoria VALUES('postre', 'postres caseros', 'emma');
INSERT INTO Categoria VALUES('bebida', 'bebidas', 'emma');


INSERT INTO Pedido VALUES('12-02-2025 22:00:00', 1, 'cena', 22.50, 'Cocinando', 'martita', 'ana', 'silvia');
INSERT INTO Pedido VALUES('13-02-2025 15:02:00', 2, 'comida', 12.25, 'Cocinando', 'martita', 'ana', 'marcos');
INSERT INTO Pedido VALUES('15-02-2025 12:24:00', 3,'comida' , 30.20, 'Cocinando', 'martita', 'juan', 'marcos');
INSERT INTO Pedido VALUES('16-02-2025 20:46:00', 4,'cena' , 7.40, 'Cocinando', 'jose', 'juan', 'silvia');
INSERT INTO Pedido VALUES('17-02-2025 19:37:00', 5, 'cena', 10.50, 'Cocinando', 'jose', 'ana', 'silvia');

INSERT INTO Pedido_Producto VALUES('pasta', '12-02-2025 22:00:00', 1, false);
INSERT INTO Pedido_Producto VALUES('pollo', '13-02-2025 15:02:00', 2, false);
INSERT INTO Pedido_Producto VALUES('brownie', '15-02-2025 12:24:00', 3, false);
INSERT INTO Pedido_Producto VALUES('pollo', '16-02-2025 20:46:00', 4, false);
INSERT INTO Pedido_Producto VALUES('salmon', '17-02-2025 19:37:00', 5, false);

INSERT INTO Cocinero_Producto VALUES('silvia', 'pasta');
INSERT INTO Cocinero_Producto VALUES('silvia', 'pollo');
INSERT INTO Cocinero_Producto VALUES('silvia', 'salmon');
INSERT INTO Cocinero_Producto VALUES('marcos', 'pollo');
INSERT INTO Cocinero_Producto VALUES('marcos', 'brownie');




