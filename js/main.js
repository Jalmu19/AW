
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

    cantidad.text(nuevoValor);
    inputOculto.val(nuevoValor);

    modificarPrecio();


    // Enviamos 'origen=carrito' para que el PHP sepa que debe sobreescribir la cantidad
    $.get("includes/clases/pedidos/añadir_carrito.php", {
        id: nombreProducto,
        cantidad: nuevoValor,
        origen: 'carrito' 
    });
}


$(document).ready(function(){
    $(".borrar_prod_carrito").click(function(){
        if(confirm("¿Desea borrar este producto del carrito?")){ 
            modificarPrecio(); //esto no funciona
        }        
    });
});



function modificarPrecio() {
    let totalGeneral = 0;

    $(".input_cantidad").each(function() {
        let precioUnid = parseFloat($(this).siblings(".precio_unidad").val());
        let cantidad = parseInt($(this).val());

        totalGeneral += precioUnid * cantidad;      
    });

    $("#total_carrito").text(totalGeneral + "€");
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



function calculoDescuento(){
    let precio = $("#precio_total").val();
    let precio_reducido = $("#precio_reducido").val();
    return (100*(precio-precio_reducido))/precio;
}




