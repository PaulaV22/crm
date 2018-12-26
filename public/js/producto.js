function justNumbers(e) {
    var keynum = window.event ? window.event.keyCode : e.which;
    if ((keynum == 8) || (keynum == 46))
        return true;

    return /\d/.test(String.fromCharCode(keynum));
}

// Precio Venta
function calculaCostoTotalCompra() {
    // Precio Total de Compra
    $total = parseFloat($('#precio_compra').val());
    $total = $total + parseFloat($('#costos_directos').val());
    $total = ($total + parseFloat($('#gastos_directos').val())).toFixed(2);
    if ($total) {
        $("#precio_compra_total").val($total);
    } else {
        $("#precio_compra_total").val("0");
    }
    // Precio Venta
    calculaCMPorcentual();
    // Precio Final
    calculaDescuentoIVA();
}

// Contribucion Marginal Valor
function calculaCMValor() {
    $("#cm_valor").removeClass("prod-imput-gray");
    // Precio de Venta
    $c_total = parseFloat($('#precio_compra_total').val());
    $cm_valor = parseFloat($('#cm_valor').val());
    $p_venta = ($c_total + $cm_valor).toFixed(2);
    if ($p_venta) {
        $("#precio_venta").val($p_venta);
        $("#precio_venta").addClass("prod-imput-gray");
    } else {
        $("#precio_venta").val("0");
    }
    // Contribucion Marginal Porcentual
    $cm_porcentual = (($cm_valor * 100) / $c_total).toFixed(2);
    if ($cm_porcentual) {
        $("#cm_porcentual").val($cm_porcentual);
        $("#cm_porcentual").addClass("prod-imput-gray");
    } else {
        $("#cm_porcentual").val("0");
    }
    // Precio Final
    calculaDescuentoIVA();
}

// Contribucion Marginal Porcentual
function calculaCMPorcentual() {
    $("#cm_porcentual").removeClass("prod-imput-gray");
    // Precio de Venta
    $c_total = parseFloat($('#precio_compra_total').val());
    $cm_porcentual = parseFloat($('#cm_porcentual').val()) / 100;
    $p_venta = (($c_total * $cm_porcentual) + $c_total).toFixed(2);
    if ($p_venta) {
        $("#precio_venta").val($p_venta);
        $("#precio_venta").addClass("prod-imput-gray");
    } else {
        $("#precio_venta").val("0");
    }
    // Contribucion Marginal Valor
    $cm_valor = ($cm_porcentual * $c_total).toFixed(2);
    if ($cm_valor) {
        $("#cm_valor").val($cm_valor);
        $("#cm_valor").addClass("prod-imput-gray");
    } else {
        $("#cm_valor").val("0");
    }
    // Precio Final
    calculaDescuentoIVA();
}

// Precio de Venta
function calculaPrecioVenta() {
    $("#precio_venta").removeClass("prod-imput-gray");
    $c_total = parseFloat($('#precio_compra_total').val());
    $p_venta = parseFloat($('#precio_venta').val());
    // Contribucion Marginal Valor
    $cm_valor = ($p_venta - $c_total).toFixed(2);
    if ($cm_valor) {
        $("#cm_valor").val($cm_valor);
        $("#cm_valor").addClass("prod-imput-gray");
    } else {
        $("#cm_valor").val("0");
    }
    // Contribucion Marginal Porcentual
    $cm_porcentual = (($cm_valor * 100) / $c_total).toFixed(2);
    if ($cm_porcentual) {
        $("#cm_porcentual").val($cm_porcentual);
        $("#cm_porcentual").addClass("prod-imput-gray");
    } else {
        $("#cm_porcentual").val("0");
    }
    // Precio Final
    calculaDescuentoIVA();
}

// Descuento y IVA
function calculaDescuentoIVA() {
    $p_venta = parseFloat($('#precio_venta').val());
    $descuento = parseFloat($('#descuento').val()) / 100;
    $iva = parseFloat($('#iva').val()) / 100;
    // Precio Venta con Dto.
    $p_venta_dto = ($p_venta - ($p_venta * $descuento)).toFixed(2);
    if ($p_venta_dto) {
        $("#precio_venta_dto").val($p_venta_dto);
    } else {
        $("#precio_venta_dto").val("0");
    }
    // Total IVA.
    $iva_gravado = ($p_venta * $iva).toFixed(2);
    if ($p_venta_dto) {
        $("#iva_gravado").val($iva_gravado);
    } else {
        $("#iva_gravado").val("0");
    }
    // Precio Publico + IVA
    $p_final_iva = ($p_venta + ($p_venta * $iva)).toFixed(2);
    if ($p_final_iva) {
        $("#precio_final_iva").val($p_final_iva);
    } else {
        $("#precio_final_iva").val("0");
    }
    // Precio Publico + IVA + Dto.
    $p_final_iva_dto = (($p_venta - ($p_venta * $descuento)) + (($p_venta - ($p_venta * $descuento)) * $iva)).toFixed(2);
    if ($p_final_iva_dto) {
        $("#precio_final_iva_dto").val($p_final_iva_dto);
    } else {
        $("#precio_final_iva_dto").val("0");
    }
}