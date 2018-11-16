var eventos = [];
// obtengo ID de eventos
function seleccionarFila(e) {
    elementId = e.target.id;
    $('#' + elementId).toggleClass('table-seleccion');
    if ($('#' + elementId).hasClass('table-seleccion')) {
        // Guardo id de evento
        eventos.push(elementId);
        console.log(eventos);
    } else {
        // Elimino id de evento
        eventos.splice($.inArray(elementId, eventos), 1);
        console.log(eventos);
    }
}
;

// funcion para eliminar los eventos guardados
function eliminaEventos() {
    // para cada elemento, envio id a la Action eliminaEvento
    for (i = 0; i < eventos.length; i++) {
        id_evento = eventos[i];
        $.ajax({
            "dataType": "text",
            "type": "POST",
            "data": "temp",
            "url": '/clientes/ajax/eliminaEventos/' + id_evento,

            "success": function (msg) {
                document.location.reload();
            },
            "error": function (msg) {
                console.log("1-error!");
            }

        }).done(function (msg) {
            console.log("1-done!");
        })
    }
}

//toma el pais al cambiar la opcion, lo envia a la accion getPovincias que devuelve la lista de provincias para elegir
function actualizarDatosProvincias(id_pais)
{
    $.post('/clientes/ajax/getProvincias/' + id_pais, function (data) {
        $('#div_provincias').html(data);
    });
}

