
var transacciones;

$(document).ready(function(){
    // LOAD MODAL PRESUPUESTOS
    // $("#btn_modal_presupuesto").click(function(){
    //     $("#table_bienes").dataTable().fnDestroy();
    //     jsonModalTransaccion = jsonPresupuestos;
    //     completarTransacciones(jsonModalTransaccion);
    // });
    
    // LOAD MODAL PEDIDOS
    // $("#btn_modal_pedido").click(function(){
    //     $("#table_bienes").dataTable().fnDestroy();
    //     jsonModalTransaccion = jsonPedidos;
    //     completarTransacciones(jsonModalTransaccion);
    // });
    
    $("#btn-modal").click(function(){
        optionSelected= $("#tipoTransaccion option:selected").val();
        console.log(optionSelected);
        jsonModalTransaccion=[];
        switch (optionSelected) {
            case 'Presupuesto':
                jsonModalTransaccion = jsonPresupuestos;
                break;
            case 'Pedido':
                jsonModalTransaccion = jsonPedidos;
                break;
            case 'Remito':
                jsonModalTransaccion = jsonRemitos;
                break;
            case 'Factura':
                jsonModalTransaccion = jsonPresupuestos;
                break;
            case 'NotaCredito':
                jsonModalTransaccion = jsonPresupuestos;
                break;
            case 'NotaDebito':
               
                jsonModalTransaccion = jsonPresupuestos;
                break;
            default:
                jsonModalTransaccion = jsonPresupuestos;
          }
        completarTransacciones(jsonModalTransaccion);
    });
    
});

function completarTransacciones(transacciones_anteriores){
    // $("#table_bienes").dataTable().fnDestroy();
    // $('#table_bienes').empty(); 
    transacciones= transacciones_anteriores;
    transaccion_ant = null;
    transaccion = null;
    $(document).ready(function () {
        $('#table_transacciones').DataTable({
            destroy: true,
            paging: false,
            searching: false,
            
            "order": [0, 'desc'],

            "language": {
                "url": "/json/spanish.json"
            }
        });
    });
    var divContainer = document.getElementById("div_table_transacciones");
    var table = document.createElement("table");
    table.setAttribute("id", "table_transacciones");
    table.setAttribute("class", "display");

    var thead = document.createElement("thead");
    var col = ["Numero", "Fecha Transaccion", "Monto", ""];

    var tr = thead.insertRow(-1);
    for (var i = 0; i < col.length; i++) {
        var th = document.createElement("th");
        th.innerHTML = col[i];
        tr.appendChild(th);
    }
    thead.appendChild(tr);
    table.appendChild(thead);

    var tbody = document.createElement("tbody");
    tbody.setAttribute("role", "button");
    var value = null;
    for (var i = 0; i < transacciones.length; i++) {
        var transac = transacciones[i];
        tr = tbody.insertRow(-1);
        // tr.onclick= selectItem(item["id"]);
        tr.setAttribute("id", i);
        tr.setAttribute("class", "click");
        //    tr.setAttribute("onclick","selectItem(event,id)");
        for (var j = 0; j < col.length-1; j++) {
            var tabCell = tr.insertCell(-1);
            tabCell.setAttribute("id", i);
            tabCell.setAttribute("class", "click");
            if (col[j]=="Numero"){
                value = transac["Numero Tipo Transaccion"];
            }
            else{
                value = transac[col[j]];
            }
            tabCell.innerHTML = value;
        }

        // Botones
        var btn = document.createElement('button');
        btn.setAttribute('type', 'button');

        if (transaccionPreviaNum!=null && transaccionPreviaNum==transacciones[i]["Numero Tipo Transaccion"]){
            console.log("lo pone como chequeado");
            btn.setAttribute('class', 'btn btn-default btn-sm  glyphicon glyphicon-check ');
            transaccion = i+"button";
        }
        else{
            btn.setAttribute('class', 'btn btn-default btn-sm  glyphicon glyphicon-unchecked ');
        }
        btn.setAttribute('id', i+"button");
        btn.setAttribute("onclick", "selectTransaccion(id)");
        var tabCell = tr.insertCell(-1);
        tabCell.setAttribute("class", "click");
        tabCell.appendChild(btn);
    }
    table.appendChild(tbody);

    divContainer.innerHTML = "";
    divContainer.appendChild(table);
}

function getTransaccion(buttonId){
    return  buttonId.substring(0, buttonId.indexOf('b'));
}

function selectTransaccion(id){
    if ($('#' + id).hasClass('glyphicon-unchecked')){
        console.log("uncheked");
        $('#' + id).removeClass('glyphicon-unchecked');
        $('#' + id).addClass('glyphicon-check');
        console.log("cheked");

        var indexTransaccion = getTransaccion(id);

        transaccion_ant = transaccion;
        if (transaccion_ant!=null) {
            $('#' + transaccion_ant).removeClass('glyphicon-check');
            $('#' + transaccion_ant).addClass('glyphicon-unchecked');
            console.log("uncheked");
        }
        //Guardo id de la fila seleccionada
        transaccion = id;
        completeItems(indexTransaccion);
    }
}

function processData(data){
    console.log(data);
}

function addTransaccionItems(){
    // removeItemsAnteriores();
    items= itemsTransaccion;
    // items = (items.concat(itemsTransaccion));
    addItems(items, tipoTransaccion, idPersona, precioActualizado);
}

function completeItems(id){

    if (id!=null){
        var transaccion_selected = transacciones[id];
        var idTransaccion = transaccion_selected["Id"];
        var numTransaccion = transaccion_selected["Numero Tipo Transaccion"];
        // alert(tipoTransaccion);
        // alert(idTransaccion);
        $('#transaccion_buscada').val(numTransaccion);
        $('#id_transaccion_previa').val(idTransaccion);
        $.post( '/' + tipoTransaccion + '/ajax/getItemsTransaccion/' + idTransaccion, function (data) {
            $('#div_transaccion').html(data);
            // Ejecuto Scripts desde la vista get-items-transaccion.phtml
        });
    }
}

function getItemsTransaccionPrevia(){
    // alert(tipoTransaccion);
    // alert(transaccionPreviaNum);
    $.post( '/' + tipoTransaccion + '/ajax/getItemsPrevios/' + transaccionPreviaNum, function (data) {
        $('#div_items_transaccion_previa').html(data);
    });
}
