<?php
include('conexion.php');

$fileContacts = $_FILES['fileContacts'];
$fileContacts = file_get_contents($fileContacts['tmp_name']);

$fileContacts = explode("\n", $fileContacts);
//quitar espacio al final de cada registro
$fileContacts = array_filter($fileContacts);

// preparar contactos (convertirlos en array)
foreach ($fileContacts as $contact) {
	$contactList[] = explode(";", $contact);
}
$insertados = 0;
$update = 0;
$cantnoexisteope = 0;
$openoexiste = 0;
$cantexisteope = 0;
$data = array();

// insertar registros
foreach ($contactList as $contactData) {

	empty(trim($contactData[0])) ?	$cod_cliente = NULL : 	$cod_cliente = $contactData[0];
	empty(trim($contactData[1])) ?	$nro_documento = NULL : 	$nro_documento = $contactData[1];
	empty(trim($contactData[2])) ?	$nombre_cliente = NULL : 	$nombre_cliente = $contactData[2];
	empty(trim($contactData[3])) ?	$telefono_laboral = NULL : 	$telefono_laboral = $contactData[3];
	empty(trim($contactData[4])) ?	$telefono_particular = NULL : 	$telefono_particular = $contactData[4];
	empty(trim($contactData[5])) ?	$celular_alternativo = NULL : 	$celular_alternativo = $contactData[5];
	empty(trim($contactData[6])) ?	$celular_laboral = NULL : 	$celular_laboral = $contactData[6];
	empty(trim($contactData[7])) ?	$celular_particular = NULL : 	$celular_particular = $contactData[7];
	empty(trim($contactData[8])) ?	$operacion = NULL : 	$operacion = $contactData[8];
	empty(trim($contactData[9])) ?	$segmento = NULL : 	$segmento = $contactData[9];
	empty(trim($contactData[10])) ?	$producto = NULL : 	$producto = $contactData[10];
	empty(trim($contactData[11])) ?	$tipo_credito_tarjeta = NULL : 	$tipo_credito_tarjeta = $contactData[11];
	empty(trim($contactData[12])) ?	$tipo_operacion = NULL : 	$tipo_operacion = $contactData[12];
	empty(trim($contactData[13])) ?	$tasa = NULL : 	$tasa = $contactData[13];
	empty(trim($contactData[14])) ?	$saldo_capital = NULL : 	$saldo_capital = intval($contactData[14]);
	empty(trim($contactData[15])) ?	$saldo_interes = NULL : 	$saldo_interes = intval($contactData[15]);
	empty(trim($contactData[16])) ?	$tipo_cambio = NULL : 	$tipo_cambio = $contactData[16];
	empty(trim($contactData[17])) ?	$dias_mora = NULL : 	$dias_mora = $contactData[17];
	empty(trim($contactData[18])) ?	$numero_cuota = NULL : 	$numero_cuota = $contactData[18];
	empty(trim($contactData[19])) ?	$total_cuotas = NULL : 	$total_cuotas = $contactData[19];
	empty(trim($contactData[20])) ?	$monto_cuota = NULL : 	$monto_cuota = intval($contactData[20]);
	empty(trim($contactData[21])) ?	$monto_mora = NULL : 	$monto_mora = intval($contactData[21]);
	empty(trim($contactData[22])) ?	$cuotas_pagadas = NULL : 	$cuotas_pagadas = $contactData[22];
	empty(trim($contactData[23])) ?	$cuotas_pendientes = NULL : 	$cuotas_pendientes = $contactData[23];
	empty(trim($contactData[24])) ?	$fecha_vto_cuota = "1900/01/01" : 	$fecha_vto_cuota = $contactData[24];
	empty(trim($contactData[25])) ?	$ult_fecha_pago = "1900/01/01" : 	$ult_fecha_pago = $contactData[25];
	empty(trim($contactData[26])) ?	$total_deuda = NULL : 	$total_deuda = intval($contactData[26]);
	empty(trim($contactData[27])) ?	$estado = NULL : 	$estado = $contactData[27];
	empty(trim($contactData[28])) ?	$fecha_apertura = "1900/01/01" : 	$fecha_apertura = $contactData[28];


	$query = pg_query($conexion, "INSERT INTO base_interfisa(cod_cliente, nro_documento, nombre_cliente, telefono_laboral, telefono_particular, celular_alternativo, 
	celular_laboral, celular_particular, operacion, segmento, producto, tipo_credito_tarjeta, tipo_operacion, tasa, saldo_capital, saldo_interes, 
	tipo_cambio, dias_mora, numero_cuota, total_cuotas, monto_cuota, monto_mora, cuotas_pagadas, cuotas_pendientes, fecha_vto_cuota, ult_fecha_pago, total_deuda, estado, fecha_apertura)
	VALUES($cod_cliente, '$nro_documento', '$nombre_cliente', '$telefono_laboral', '$telefono_particular', '$celular_alternativo', '$celular_laboral', '$celular_particular', '$operacion', 
	'$segmento', '$producto', '$tipo_credito_tarjeta', '$tipo_operacion', '$tasa', $saldo_capital, $saldo_interes,
	$tipo_cambio, $dias_mora, $numero_cuota, $total_cuotas, $monto_cuota, $monto_mora, $cuotas_pagadas, $cuotas_pendientes, '$fecha_vto_cuota', '$ult_fecha_pago', $total_deuda, '$estado', '$fecha_apertura')");

	if ($query) {

		// echo 'insertado '. $contactData[8];
		$insertados = $insertados + 1;
	} else {
		$data[] = array(
			"0" => $contactData[8],
		);
	}
}
echo json_encode($data);

echo  "<br> insertardos" . $insertados . "<br>Actualizados: ";
