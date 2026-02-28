INSERT INTO Producto VALUES('pasta', 10.0, true, 21, true, 'macarrones con salsa boloñesa','../img/productos/pasta.png','hidratos de carbono');
INSERT INTO Producto VALUES('salmorejo', 10.0, true, 21, true, 'salmorejo cordobés','../img/productos/salmorejo.png','verdura');
INSERT INTO Producto VALUES('salmón', 10.0, true, 21, true, 'salmón al horno','../img/productos/salmon.png','pescado');
INSERT INTO Producto VALUES('pollo', 10.0, true, 21, true, 'pollo con patatas','../img/productos/pollo.png','carne');
INSERT INTO Producto VALUES('tiramisu', 10.0, true, 21, true, 'tiramisu italiano','../img/productos/tiramisu.png','postre');
INSERT INTO Producto VALUES('brownie', 10.0, true, 21, true, 'brownie de chocolate','../img/productos/brownie.png', 'postre');
INSERT INTO Producto VALUES('coca-cola', 2.5, true, 21, true, 'coca-cola','../img/productos/coca_cola.png', 'bebida');

INSERT INTO Usuario VALUES('Marta', 'Pérez Gómez', 'marta@gmail.com', '', 'cliente', 'martita')
INSERT INTO Usuario VALUES('Jose', 'Sánchez López', 'jose@gmail.com', '', 'cliente', 'jose')
INSERT INTO Usuario VALUES('Ana', 'Fernández Martín', 'ana@gmail.com', '', 'camarero', 'ana')
INSERT INTO Usuario VALUES('Juan', 'Pérez Ruiz', 'juan@gmail.com', '', 'camarero', 'juan')
INSERT INTO Usuario VALUES('Silvia', 'Díaz Moreno', 'silvia@gmail.com', '', 'cocinero', 'silvia')
INSERT INTO Usuario VALUES('Marcos', 'Alonso Gutierrez', 'marcos@gmail.com', '', 'cocinero', 'marcos')
INSERT INTO Usuario VALUES('Emma', 'Ortiz Gómez', 'emma@gmail.com', '', 'gerente', 'emma')

INSERT INTO Categoria VALUES('hidratos de carbono', 'macronutriente esencial', '../img/categorias/hidratos_de_carbono.png', 'emma')
INSERT INTO Categoria VALUES('verdura', 'fuente de vitaminas', '../img/categorias/verdura.png', 'emma')
INSERT INTO Categoria VALUES('pescado', 'fuente de proteínas, omega-3 y otros', '../img/categorias/pescado.png', 'emma')
INSERT INTO Categoria VALUES('carne', 'fuente de proteínas, grasas y minerales', '../img/categorias/carne.png', 'emma')
INSERT INTO Categoria VALUES('postre', 'plato dulce/salado', '../img/categorias/postres.png', 'emma')
INSERT INTO Categoria VALUES('bebida', 'liquido para refrescar', '../img/categorias/bebidas.png', 'emma')

INSERT INTO Pedido VALUES('12-02-2025 22:00', 1, , 22.50, 'Cocinando', 'martita', 'ana', 'silvia')
INSERT INTO Pedido VALUES('13-02-2025 15:02', 2, , 12.25, 'Cocinando', 'martita', 'ana', 'marcos')
INSERT INTO Pedido VALUES('15-02-2025 12:24', 3, , 30.20, 'Cocinando', 'martita', 'juan', 'marcos')
INSERT INTO Pedido VALUES('16-02-2025 20:46', 4, , 7.40, 'Cocinando', 'jose', 'juan', 'silvia')
INSERT INTO Pedido VALUES('17-02-2025 19:37', 5, , 10.50, 'Cocinando', 'jose', 'ana', 'silvia')

INSERT INTO Pedido-Producto VALUES('pasta', '12-02-2025 22:00', 1)
INSERT INTO Pedido-Producto VALUES('pollo', '13-02-2025 15:02', 2)
INSERT INTO Pedido-Producto VALUES('brownie', '15-02-2025 12:24', 3)
INSERT INTO Pedido-Producto VALUES('pollo', '16-02-2025 20:46', 4)
INSERT INTO Pedido-Producto VALUES('salmon', '17-02-2025 19:37', 5)

INSERT INTO Cocinero-Producto VALUES('silvia', 'pasta')
INSERT INTO Cocinero-Producto VALUES('silvia', 'pollo')
INSERT INTO Cocinero-Producto VALUES('silvia', 'salmon')
INSERT INTO Cocinero-Producto VALUES('marcos', 'pollo')
INSERT INTO Cocinero-Producto VALUES('marcos', 'brownie')




