<?php
#importarCobros.php
include "bdpostgres.php";

// Made by Seb

// Se remplazan estos caracteres especiales ñ, Ñ, ', !

$selectCliente = $_POST['selectedCliente'];
$selectTipo = $_POST['selectedTipo'];
$selectDate = $_POST['selectedDate'];
$fileContacts = $_FILES['fileContacts'];
$fileExtension = pathinfo($fileContacts['name'], PATHINFO_EXTENSION); // Obtener la extensión del archivo
$fileContacts = file_get_contents($fileContacts['tmp_name']);
if ($fileContacts === false) {
    die('Error al leer el archivo.');
}
$fileContacts = explode("\n", $fileContacts);
$fileContacts = array_filter($fileContacts);

// preparar contactos (convertirlos en array)
$firstRow = true;
if (strtoupper($fileExtension) == 'CSV') {
	foreach ($fileContacts as $contact) {
		if ($firstRow) {
			// Saltar la primera fila (encabezados)
			$firstRow = false;
			continue;
		}
		$cleanedData = mb_convert_encoding($contact, 'UTF-8', 'ISO-8859-1');
		$contactList[] = explode(";", $cleanedData);
	}
}else {
	die('Error: El archivo es de un formato no admitido, debe ser csv. La extension es: '. $fileExtension);
}

$data = array();

switch ($selectCliente){
	case 'interfisa':
		insertInterfisa();
		break;
	case 'bancop':
		insertBancop();
		break;
	case 'lux':
		insertLux();
		break;
	case 'vinanzas':
		insertVinanzas();
		break;
	default:
		echo "Cliente no soportado";
		break;
}

function insertInterfisa(){
	global $contactList, $data, $connect, $selectDate;
	
	$insertados = 0;
	$noinsertados = 0;
	$recorrido = 0;
	$query = getQueryInterfisa();

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
	
		$sql = $connect->prepare($query);
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
		$sql->bindParam(':fecha_insert', $selectDate);
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
	showResults($recorrido, $insertados, $noinsertados);
}

function insertVinanzas(){
	global $contactList, $data, $connect, $selectDate;
	
	$insertados = 0;
	$noinsertados = 0;
	$recorrido = 0;
	$query = getQueryVinanzas();

	foreach ($contactList as $contactData) {

		$recorrido = $recorrido + 1;

		empty(trim($contactData[0])) ? $cod_persona = null : $cod_persona = trim($contactData[0]);
		empty(trim($contactData[1])) ? $nro_documento = null : $nro_documento = trim($contactData[1]);
		empty(trim($contactData[2])) ? $nro_operacion = null : $nro_operacion = trim($contactData[2]);
		empty(trim($contactData[3])) ? $nro_ope_original = null : $nro_ope_original = trim($contactData[3]);
		empty(trim($contactData[4])) ? $numero_cuota = null : $numero_cuota = trim($contactData[4]);
		empty(trim($contactData[5])) ? $fecha_pago = "1900/01/01" : $fecha_pago = trim($contactData[5]);
		empty(trim($contactData[6])) ? $monto_pagado = null : $monto_pagado = str_replace(",", ".", trim($contactData[6]));
		empty(trim($contactData[7])) ? $es_refinanciado = null : $es_refinanciado = trim($contactData[7]);
		empty(trim($contactData[8])) ? $origen_operacion = null : $origen_operacion = trim($contactData[8]);
		empty(trim($contactData[9])) ? $cod_equipo = null : $cod_equipo = trim($contactData[9]);
	
		$usu_insert = 1155;

		$sql = $connect->prepare($query);
		$sql->bindParam(':cod_persona', $cod_persona);
		$sql->bindParam(':nro_documento', $nro_documento);
		$sql->bindParam(':nro_operacion', $nro_operacion);
		$sql->bindParam(':nro_ope_original', $nro_ope_original);
		$sql->bindParam(':nro_cuota', $numero_cuota);
		$sql->bindParam(':fecha_pago', $fecha_pago);
		$sql->bindParam(':monto_pagado', $monto_pagado);
		$sql->bindParam(':es_refinanciado', $es_refinanciado);
		$sql->bindParam(':origen_operacion', $origen_operacion);
		$sql->bindParam(':cod_equipo', $cod_equipo);
		$sql->bindParam(':fecha_insert', $selectDate);
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
	showResults($recorrido, $insertados, $noinsertados);
}

function insertBancop(){
	global $contactList, $data, $connect, $selectDate;
	
	$insertados = 0;
	$noinsertados = 0;
	$recorrido = 0;
	$query = getQueryBancop();

	foreach ($contactList as $contactData) {

		$recorrido = $recorrido + 1;

		empty(trim($contactData[0])) ? $fec_cobro = "1900/01/01" : $fec_cobro = trim($contactData[0]);
		empty(trim($contactData[1])) ? $cod_cliente = null : $cod_cliente = trim($contactData[1]);
		empty(trim($contactData[2])) ? $nro_operacion = null : $nro_operacion = trim($contactData[2]);
		empty(trim($contactData[3])) ? $nro_cuota = null : $nro_cuota = trim($contactData[3]);
		empty(trim($contactData[4])) ? $producto = null : $producto = trim($contactData[4]);
		empty(trim($contactData[5])) ? $tip_credito = null : $tip_credito = trim($contactData[5]);
		empty(trim($contactData[6])) ? $cod_moneda = null : $cod_moneda =  trim($contactData[6]);
		empty(trim($contactData[7])) ? $tot_cobrado = 0 : $tot_cobrado = trim($contactData[7]);
		empty(trim($contactData[8])) ? $cotizacion = null : $cotizacion = trim($contactData[8]);
		empty(trim($contactData[9])) ? $tot_cobrado_gs = null : $tot_cobrado_gs = trim($contactData[9]);
	
		$usu_insert = 1155;

		$sql = $connect->prepare($query);
		$sql->bindParam(':cod_cliente', $cod_cliente);
		$sql->bindParam(':producto', $producto);
		$sql->bindParam(':nro_operacion', $nro_operacion);
		$sql->bindParam(':nro_cuota', $nro_cuota);
		$sql->bindParam(':tot_cobrado_gs', $tot_cobrado_gs);
		$sql->bindParam(':fecha_cobro', $fec_cobro);
		$sql->bindParam(':fecha_insert', $selectDate);
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
	showResults($recorrido, $insertados, $noinsertados);
}

function getQueryVinanzas(){
	$query = "INSERT INTO vinanzas_cobranza.pagos(COD_PERSONA, NRO_DOCUMENTO, NRO_OPERACION, NRO_OPE_ORIGINAL, 
	NUMERO_CUOTA, FECHA_PAGO, MONTO_PAGADO, ES_REFINANCIADO, ORIGEN_OPERACION, COD_EQUIPO, fecha_insert)
    VALUES(:cod_persona, :nro_documento, :nro_operacion, :nro_ope_original, :nro_cuota, :fecha_pago, 
	:monto_pagado, :es_refinanciado, :origen_operacion, :cod_equipo, :fecha_insert)";

	return $query;
}

function getQueryInterfisa(){
	$query = "INSERT INTO pagos_servicios (cod_cliente,nro_documento,nombre_cliente,operacion,producto,estado, tramo, cartera,tipo_cambio,saldo,fecha_pago,
	monto_pagado,numero_cuota,tipo_operacion, fecha_insert)
	VALUES(:cod_cliente,:nro_documento, :nombre_cliente, :operacion,:producto,:estado, :tramo, :cartera,:tipo_cambio,:saldo,:fecha_pago,:monto_pagado,
	:numero_cuota,:tipo_operacion, :fecha_insert);";

	return $query;
}

function getQueryBancop(){
	$query = "INSERT INTO bancop.pagos(
		doc,nombre_completo,cuenta,modalidad,deuda_total,pago_minimo,dias,pago,fecha_pago,observacion,corte,tramo,fecha_insert)
	SELECT 
		nro_documento,nom_cliente,'',:producto,0,:tot_cobrado_gs,dia_mora,:tot_cobrado_gs,:fecha_cobro,'','',1,:fecha_insert
	FROM 
		bancop.base_prestamo 
	WHERE 
		cod_cliente = :cod_cliente AND nro_operacion = :nro_operacion AND nro_cuota = :nro_cuota 
	ORDER BY fecha_insert::date DESC LIMIT 1;
	";

	return $query;
}

function showResults($recorrido, $insertados, $noinsertados){
	global $selectDate;
	echo  "Registros recorridos: " . $recorrido . ' ;' . "\n";
	echo  "Insertardos: " . $insertados . ' ;' . "\n";
	echo  "No insertardos: " . $noinsertados . ' ;' . "\n";
	echo  "Fecha seleccionada: " . $selectDate . ' ;' . "\n";

	if($noinsertados > 0){
		global $data;
		echo var_dump($data);
	}
}