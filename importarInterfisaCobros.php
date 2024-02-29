<?php
include "bdpostgres.php";

// $bd = obtenerBD();
// Se remplazan estos caracteres especiales ñ, Ñ, ', !
// $bd->beginTransaction();

// # Preparar sentencia de productos
// $sentencia = $bd->prepare("INSERT INTO  public.base_interfisa (cod_cliente, nro_documento, nombre_cliente, telefono_laboral, telefono_particular, celular_alternativo,
// celular_laboral, celular_particular, operacion, segmento, producto, tipo_credito_tarjeta, tipo_operacion, tasa, saldo_capital, saldo_interes,tipo_cambio,
// dias_mora, numero_cuota, total_cuotas, monto_cuota, monto_mora, cuotas_pagadas, cuotas_pendientes, fecha_vto_cuota, ult_fecha_pago, total_deuda, estado, fecha_apertura) 
// VALUES(?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ?,?);");


$fileContacts = $_FILES['fileContacts2'];
$fileContacts = file_get_contents($fileContacts['tmp_name']);

$fileContacts = explode("\n", $fileContacts);
//quitar espacio al final de cada registro
$fileContacts = array_filter($fileContacts);

// preparar contactos (convertirlos en array)
$firstRow = true;
foreach ($fileContacts as $contact) {
    if ($firstRow) {
        // Saltar la primera fila (encabezados)
        $firstRow = false;
        continue;
    }

    $cleanedData = mb_convert_encoding($contact, 'UTF-8', 'ISO-8859-1');
    $contactList[] = explode(";", $cleanedData);
}

$insertados = 0;
$noinsertados = 0;
$recorrido = 0;
$data = array();
// $bd->beginTransaction();

// $sentencia = $bd->prepare("INSERT INTO  public.base_interfisa (cod_cliente, nro_documento, nombre_cliente, telefono_laboral, telefono_particular, celular_alternativo,
// celular_laboral, celular_particular, operacion, segmento, producto, tipo_credito_tarjeta, tipo_operacion, tasa, saldo_capital, saldo_interes,tipo_cambio,
// dias_mora, numero_cuota, total_cuotas, monto_cuota, monto_mora, cuotas_pagadas, cuotas_pendientes, fecha_vto_cuota, ult_fecha_pago, total_deuda, estado, fecha_apertura) 
// VALUES(?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ?,?);");

// insertar registros
foreach ($contactList as $contactData) {

	$recorrido = $recorrido + 1;

	# Preparar sentencia de productos
	empty(trim($contactData[0])) ?	$cod_cliente = null : 	$cod_cliente = trim($contactData[0]); //edit ydy martinez
	//empty(trim($contactData[1])) ?	$nro_documento = null : 	$nro_documento = trim($contactData[1]);
	empty(trim($contactData[1])) ?  $nombre_cliente = null : $nombre_cliente = str_replace(['Ñ', "'", '!'], ['N', '', ''], trim($contactData[1]));
	empty(trim($contactData[2])) ?	$operacion = null : 	$operacion = trim($contactData[2]); //edit ydy martinez
	empty(trim($contactData[3])) ?	$estado = null : 	$estado =  mb_convert_encoding(trim($contactData[3]), 'UTF-8');
	empty(trim($contactData[4])) ?	$tramo = null : 	$tramo =  mb_convert_encoding(trim($contactData[4]), 'UTF-8'); 
	empty(trim($contactData[5])) ?	$cartera = null : $cartera =  mb_convert_encoding(TRIM($contactData[5]), 'UTF-8'); 
	$saldo = str_replace(",", ".", trim($contactData[6])); //edit ydy martinez
	empty(trim($contactData[7])) ?	$fecha_pago = "1900/01/01" : 	$fecha_pago = trim($contactData[7]); //edit ydy martinez
	$monto_pagado = str_replace(",", ".", trim($contactData[8])); //edit ydy martinez
	$numero_cuota = trim($contactData[9]); //edit ydy martinez
	empty(trim($contactData[10])) ?	$tipo_operacion = null : 	$tipo_operacion = trim($contactData[10]); //edit ydy martinez

	empty(trim($contactData[11])) ?	$producto = null : 	$producto =  mb_convert_encoding(TRIM($contactData[11]), 'UTF-8');
	empty(trim($contactData[12])) ?	$segmento = null : 	$segmento =  mb_convert_encoding(TRIM( $contactData[12]), 'UTF-8');
	empty(trim($contactData[13])) ?	$nro_documento = null : 	$nro_documento = trim($contactData[13]);
	empty(trim($contactData[14])) ?	$cotizacion = null : 	$cotizacion = str_replace(",", ".", trim($contactData[14])); //edit ydy martinez
	empty(trim($contactData[15])) ?	$diasmora = null : 	$diasmora = trim($contactData[15]);

	// $data = [
	// 	$cod_cliente, $nro_documento, $nombre_cliente, $telefono_laboral, $telefono_particular, $celular_alternativo, $celular_laboral, $celular_particular, $operacion,
	// 	$segmento, $producto, $tipo_credito_tarjeta, $tipo_operacion, $tasa, $saldo_capital, $saldo_interes,
	// 	$tipo_cambio, $dias_mora, $numero_cuota, $total_cuotas, $monto_cuota, $monto_mora, $cuotas_pagadas, $cuotas_pendientes, $fecha_vto_cuota, $ult_fecha_pago, $total_deuda, $estado, $fecha_apertura
	// ];

	//edit by Ydy Martinez se agrega el campo riesgo
	$sql = "INSERT INTO pagos_servicios (cod_cliente,nro_documento,nombre_cliente,operacion,producto,estado, tramo, cartera,tipo_cambio,saldo,fecha_pago,
	monto_pagado,numero_cuota,tipo_operacion)
	 VALUES(:cod_cliente,:nro_documento, :nombre_cliente, :operacion,:producto,:estado, :tramo, :cartera,:tipo_cambio,:saldo,:fecha_pago,:monto_pagado,
	 :numero_cuota,:tipo_operacion);";

	$sql = $connect->prepare($sql);
	// $sql->bindParam(':cod_cliente', $nombres);
	$sql->bindParam(':cod_cliente', $cod_cliente);
	$sql->bindParam(':nro_documento', $nro_documento);
	$sql->bindParam(':nombre_cliente', $nombre_cliente);
	$sql->bindParam(':operacion', $operacion);
	$sql->bindParam(':producto', $producto);
	$sql->bindParam(':estado', $estado);
	$sql->bindParam(':tramo', $tramo);
	$sql->bindParam(':cartera', $cartera);
	$sql->bindParam(':tipo_cambio', $cotizacion);
	$sql->bindParam(':saldo', $saldo);
	$sql->bindParam(':fecha_pago', $fecha_pago);
	$sql->bindParam(':monto_pagado', $monto_pagado);
	$sql->bindParam(':numero_cuota', $numero_cuota);
	$sql->bindParam(':tipo_operacion', $tipo_operacion);
	$exec = $sql->execute();

	if ($recorrido == 1) {

		continue;
	} else {
		// $lastInsertId = $connect->lastInsertId();
		if ($exec) {
			$insertados = $insertados + 1;
		} else {
			$noinsertados = $noinsertados + 1;
			$data = [
				"cliente" => $contactData[1],
				"operacion" => $contactData[8],
				"mensaje" => $sql->errorInfo()
			];
			// print_r($sql->errorInfo());
		}
	}
}

echo  "Registros recorridos: " . $recorrido . " ;" . "\n";
echo  "Insertardos: " . $insertados . " ;" . "\n";
echo  "No insertardos: " . $noinsertados . " ;" . "\n";

if($noinsertados > 0){
	echo var_dump($data);
}