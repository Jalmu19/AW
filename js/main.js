
$(document).ready(function(){

    $(".btn_aumentar_producto").click(function(){
       modificarValor($(this), "+");
    });

    $(".btn_disminuir_producto").click(function(){
        modificarValor($(this), "-");
    });
});


function modificarValor(boton, operacion){
    let cantidad = boton.siblings(".cantidad_producto");
    let inputOculto = boton.siblings(".input_cantidad");
    let nombreProducto = boton.siblings(".nombreProducto").val();
  
    //Obtenemos el valor actual (usando .text()) y lo pasamos a número
    let valorActual = parseInt(inputOculto.val());

    let nuevoValor = valorActual;
    //Modificamos el valor y lo volvemos a escribir en el HTML
    if(operacion === "+") nuevoValor = nuevoValor + 1;
    else if(valorActual > 0) nuevoValor = nuevoValor - 1;

    if( nuevoValor === 0)
        eliminar_producto_carrito(nombreProducto);
    else{
        cantidad.text(nuevoValor);
        inputOculto.val(nuevoValor);

        // Enviamos 'origen=carrito' para que el PHP sepa que debe sobreescribir la cantidad
        $.get("includes/clases/pedidos/anyadir_carrito.php", {
            id: nombreProducto,
            cantidad: nuevoValor,
            origen: 'carrito' 
        });
    }
   
    modificarPrecio();
}



$(document).ready(function(){
    $(".borrar_prod_carrito").click(function(){
        let fila = $(this).closest("tr"); //buscamos la celda vecina
        let nombreProducto = fila.find(".nombreProducto").val();

         
        eliminar_producto_carrito(nombreProducto);             
    });
});


function eliminar_producto_carrito(nombreProducto){

    if(confirm("¿Desea borrar este producto del carrito?")){         
        $.post("includes/clases/pedidos/eliminar_producto_pedido.php", { id:nombreProducto}, function() {
            //se ejecuta solo cuando el PHP termina
            //si había alguna oferta activa, se recalcula
            $.post("includes/clases/ofertas/aplicar_oferta.php", function() {
                window.location.href = "carrito.php"; //recarga la página para ver los cambios
            });
        });
    }  
}



function modificarPrecio() {
    let totalGeneral = 0;

    $(".input_cantidad").each(function() {
        let precioUnid = parseFloat($(this).siblings(".precio_unidad").val());
        let cantidad = parseInt($(this).val());

        totalGeneral += precioUnid * cantidad;      
    });

    $("#subtotal_carrito").text(totalGeneral.toFixed(2));
    $("#total_carrito").text(totalGeneral.toFixed(2));
}


//para actualizar la cantidad en el formulario
$(document).ready(function() {
    // Escuchamos el evento de envío del formulario de finalizar
    $('#formFinalizar').submit(function() {
        let formulario = $(this);

        // Buscamos todos los inputs de cantidad que generó la TablaPedidos
        $('.input_cantidad').each(function() {
            let nombre = $(this).attr('name'); 
            let valor = $(this).val();

            // Los clonamos como campos ocultos dentro del formulario de envío
            $('<input>').attr({
                type: 'hidden',
                name: nombre,
                value: valor
            }).appendTo(formulario);
        });

        return true; 
    });
});



function calculoDescuento() {
    let precio = parseFloat($("#precio_total").text()) || 0;
    let precio_reducido = parseFloat($("#precio_reducido").val());

    // Si no hay nada escrito, descuento 0
    if(isNaN(precio_reducido)) {
        $("#descuento-input").val(0);
        return;
    }

    let descuento = 0;
    if (precio > 0) {
        descuento = (100 * (precio - precio_reducido)) / precio;
    }
    
    $("#descuento-input").val(Math.round(descuento));
}

//para los botones de crear ofertas
$(document).ready(function(){

    if ($("#productos_json").val() !== "") {
        $("#tabla-productos-oferta tbody tr").addClass("fila-oferta");
        modificarPrecioOferta(); 
    }

    $("#btn_add_pack").click(function(){
        addProductoPack();
    });

    $(document).on("click", ".borrar_prod_pack", function(){
        borrarProductoPack($(this));
    });

   $("#precio_reducido").on("input", function(){
        let inputValor = $(this).val();
        let precioFinal = parseFloat(inputValor);
        let totalPack = parseFloat($("#precio_total").text()) || 0;

        // Si la casilla está vacía
        if (inputValor === "" || isNaN(precioFinal)) {
            $("#descuento-input").val("0");
            return; 
        }

        // Si es menor que 0, forzamos a 0
        if (precioFinal < 0) {
            $(this).val(0);
        } 
        // Si supera el total del pack, forzamos al total máximo
        else if (precioFinal > totalPack) {
            $(this).val(totalPack.toFixed(2));
        }

        // Una vez comprobado y corregido, calculamos el descuento
        calculoDescuento();
    });

});


function addProductoPack() {
    let select = $("#select-prod-aux");
    let nombre = select.val();
    let precio = select.find(':selected').data('precio');
    let cantidadNueva = parseInt($("#cant-prod-aux").val());

    if (nombre === "") {
        alert("Selecciona un producto");
        return;
    }

    let productoEncontrado = false;

    // buscamos si ese prod ya está en la tabla para modificar la cantidad
    $(".fila-oferta").each(function() {
        let nombreExistente = $(this).find(".nom-prod").text().trim();
        
        if (nombreExistente === nombre) {
            let cantActual = parseInt($(this).find(".cant-prod").text());
            $(this).find(".cant-prod").text(cantActual + cantidadNueva);
            productoEncontrado = true;
            return false; 
        }
    });

    // si no está en la tabla ya 
    if (!productoEncontrado) {
        let nuevaFila = `
            <tr class="fila-oferta">
                <td class="nom-prod">${nombre}</td>
                <td class="cant-prod">${cantidadNueva}</td>
                <td>
                    <span class="precio-prod">${precio}</span>€
                </td>
                <td>
                    <button type="button" class="borrar_prod_pack">Borrar</button>
                </td>
            </tr>`;
        $("#tabla-productos-oferta tbody").append(nuevaFila);
    }

    modificarPrecioOferta();
}


function borrarProductoPack(boton) {
    if(confirm("¿Desea quitar este producto del pack?")){ 
        boton.closest("tr").remove();
        modificarPrecioOferta();
    }
}


function modificarPrecioOferta() {
    let totalPack = 0;
    let listaProductosJSON = [];

    if ($(".fila-oferta").length > 0) {
        $(".fila-oferta").each(function() {
            let nombre = $(this).find(".nom-prod").text().trim();
            let cantidad = parseInt($(this).find(".cant-prod").text().trim());
            let precioUnitario = parseFloat($(this).find(".precio-prod").text().trim());

            if (!isNaN(cantidad) && !isNaN(precioUnitario)) {
                totalPack += (precioUnitario * cantidad);
                listaProductosJSON.push({ nombre: nombre, cantidad: cantidad });
            }
        });
    } 
    
    // Actualizamos el input con lo que hay actualmente en la tabla
    $("#productos_json").val(JSON.stringify(listaProductosJSON));
    $("#precio_total").text(totalPack.toFixed(2));
    
    calculoDescuento();
}


function calculoPrecioTotal() {
    let total = 0;

    // Usamos jQuery para iterar por cada fila de la tabla
    $('table tbody tr').each(function() {

        let cant = $(this).find('td:nth-child(2)').text();
        let prec = $(this).find('td:nth-child(3)').text();
        
        //No estaba leyendo bien la , para los decimales
        prec = prec.replace(',', '.').replace("€", "");
       
        const cantidad = parseInt(cant, 10);
        const precio = parseFloat(prec);
        
        if (!isNaN(cantidad) && !isNaN(precio)) {
            total += cantidad * precio;
        }
        
    });

   $("#total_sin").text(total.toFixed(2));
}

function calculoPrecioFinal(descuento){

    let precioIni = parseFloat($("#total_sin").text())
    let precioFin = 0.0;
    let factor = descuento/100;
      
    precioFin = precioIni-(precioIni*factor);
    console.log(precioFin);

    $("#total_con").text(precioFin.toFixed(2));

}


$(document).ready(function(){
    $("#boton-desc").click(function(){
        aplicarOfertas();
    });
});




function aplicarOfertas(){
    $.post("includes/clases/ofertas/aplicar_oferta.php", function(data) {
        // recargamos para ver el subtotal/descuento actualizado
        location.reload(); 
        alert(data);
    });
     
}
