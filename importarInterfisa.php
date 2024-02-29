<?php
include "bdpostgres.php";

// $bd = obtenerBD();

// $bd->beginTransaction();
// Se remplazan estos caracteres especiales ñ, Ñ, ', !
// # Preparar sentencia de productos
// $sentencia = $bd->prepare("INSERT INTO  public.base_interfisa (cod_cliente, nro_documento, nombre_cliente, telefono_laboral, telefono_particular, celular_alternativo,
// celular_laboral, celular_particular, operacion, segmento, producto, tipo_credito_tarjeta, tipo_operacion, tasa, saldo_capital, saldo_interes,tipo_cambio,
// dias_mora, numero_cuota, total_cuotas, monto_cuota, monto_mora, cuotas_pagadas, cuotas_pendientes, fecha_vto_cuota, ult_fecha_pago, total_deuda, estado, fecha_apertura) 
// VALUES(?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ?,?);");

$fileContacts = $_FILES['fileContacts'];
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

// insertar registros
foreach ($contactList as $contactData) {

	$recorrido = $recorrido + 1;

	# Preparar sentencia de productos


	empty(trim($contactData[0])) ?	$cod_cliente = null : 	$cod_cliente = trim($contactData[0]);
	empty(trim($contactData[1])) ?	$nro_documento = null : $nro_documento = trim($contactData[1]);
	empty(trim($contactData[2])) ?  $nombre_cliente = null : $nombre_cliente = str_replace(['Ñ', "'", '!'], ['N', '', ''], trim($contactData[2]));
	empty(trim($contactData[3])) ?	$telefono_laboral = null : 	$telefono_laboral = trim($contactData[3]);
	empty(trim($contactData[4])) ?	$telefono_particular = null : 	$telefono_particular = trim($contactData[4]);
	empty(trim($contactData[5])) ?	$celular_alternativo = null : 	$celular_alternativo = trim($contactData[5]);
	empty(trim($contactData[6])) ?	$celular_laboral = null : 	$celular_laboral = trim($contactData[6]);
	empty(trim($contactData[7])) ?	$celular_particular = null : 	$celular_particular = trim($contactData[7]);
	empty(trim($contactData[8])) ?	$operacion = null : 	$operacion = trim($contactData[8]);
	empty(trim($contactData[9])) ?	$segmento = null : 	$segmento = trim($contactData[9]);
	empty(trim($contactData[10])) ?	$producto = null : 	$producto = trim($contactData[10]);
	empty(trim($contactData[11])) ?	$tipo_credito_tarjeta = null : 	$tipo_credito_tarjeta = htmlspecialchars(trim($contactData[11]));
	empty(trim($contactData[12])) ?	$tipo_operacion = null : 	$tipo_operacion = trim($contactData[12]);
	empty(trim($contactData[13])) ?	$tasa = 0 : 	$tasa = trim($contactData[13]);
	$saldo_capital = str_replace(",", ".", trim($contactData[14]));
	$saldo_interes =  str_replace(",", ".", trim($contactData[15]));
	empty(trim($contactData[16])) ?	$tipo_cambio = 0 : 	$tipo_cambio = str_replace(",", ".", trim($contactData[16]));
	// empty(trim($contactData[17])) ?	$dias_mora = 0 : 	$dias_mora = $contactData[17];
	$dias_mora = trim($contactData[17]);
	// empty(trim($contactData[18])) ?	$numero_cuota = null : 	$numero_cuota = $contactData[18];
	$numero_cuota = trim($contactData[18]);
	$total_cuotas = trim($contactData[19]);
	$monto_cuota =   str_replace(",", ".", trim($contactData[20]));
	empty(trim($contactData[21])) ?	$monto_mora = 0 : 	$monto_mora =  str_replace(",", ".", trim($contactData[21]));
	empty(trim($contactData[22])) ?	$cuotas_pagadas = 0 : 	$cuotas_pagadas = trim($contactData[22]);
	empty(trim($contactData[23])) ?	$cuotas_pendientes = 0 : 	$cuotas_pendientes = trim($contactData[23]);
	empty(trim($contactData[24])) ?	$fecha_vto_cuota = "1900/01/01" : 	$fecha_vto_cuota = trim($contactData[24]);
	empty(trim($contactData[25])) ?	$ult_fecha_pago = "1900/01/01" : 	$ult_fecha_pago = trim($contactData[25]);
	$total_deuda =  str_replace(",", ".", trim($contactData[26]));
	empty(trim($contactData[27])) ?	$estado = null : 	$estado = trim($contactData[27]);
	empty(trim($contactData[28])) ?	$fecha_apertura = "1900/01/01" : 	$fecha_apertura = trim($contactData[28]);
	empty(trim($contactData[29])) ?	$riesgo = null : 	$riesgo = trim($contactData[29]); //edit by Ydy Martinez
	/*trim($contactData[30]) ?	$refuno = "" : 	$refuno = trim($contactData[30]);
	trim($contactData[31]) ?	$refdos = "" : 	$refdos = trim($contactData[31]);
	trim($contactData[32]) ?	$reftres = "" : 	$reftres = trim($contactData[32]);
	trim($contactData[33]) ?	$refcuatro = "" : $refcuatro = trim($contactData[33]);*/
	$refuno = "";
	$refdos =  "";
	$reftres =  "";
	$refcuatro = "";
	//ediy by Ydy Martinez se agrega el campo riesgo
	$sql = "INSERT INTO base_interfisa (cod_cliente, nro_documento, nombre_cliente, telefono_laboral, telefono_particular, celular_alternativo,
	 celular_laboral, celular_particular, operacion, segmento, producto, tipo_credito_tarjeta, tipo_operacion, tasa, saldo_capital, saldo_interes,tipo_cambio,
	 dias_mora, numero_cuota, total_cuotas, monto_cuota, monto_mora, cuotas_pagadas, cuotas_pendientes, fecha_vto_cuota, ult_fecha_pago, total_deuda, estado, fecha_apertura,riesgo,referencia_uno,referencia_dos,referencia_tres,referencia_cuatro)
	 VALUES(:cod_cliente, :nro_documento, :nombre_cliente, :telefono_laboral, :telefono_particular, :celular_alternativo,
	 :celular_laboral, :celular_particular, :operacion, :segmento, :producto, :tipo_credito_tarjeta, :tipo_operacion, :tasa, :saldo_capital, :saldo_interes,:tipo_cambio,
	 :dias_mora, :numero_cuota, :total_cuotas, :monto_cuota, :monto_mora, :cuotas_pagadas, :cuotas_pendientes, :fecha_vto_cuota, :ult_fecha_pago, :total_deuda, :estado, :fecha_apertura,:riesgo,
	 :referencia_uno,:referencia_dos,:referencia_tres,:referencia_cuatro);";

	$sql = $connect->prepare($sql);
	$sql->bindParam(':cod_cliente', $cod_cliente);
	$sql->bindParam(':nro_documento', $nro_documento);
	$sql->bindParam(':nombre_cliente', $nombre_cliente);
	$sql->bindParam(':telefono_laboral', $telefono_laboral);
	$sql->bindParam(':telefono_particular', $telefono_particular);
	$sql->bindParam(':celular_alternativo', $celular_alternativo);
	$sql->bindParam(':celular_laboral', $celular_laboral);
	$sql->bindParam(':celular_particular', $celular_particular);
	$sql->bindParam(':operacion', $operacion);
	$sql->bindParam(':segmento', $segmento);
	$sql->bindParam(':producto', $producto);
	$sql->bindParam(':tipo_credito_tarjeta', $tipo_credito_tarjeta);
	$sql->bindParam(':tipo_operacion', $tipo_operacion);
	$sql->bindParam(':tasa', $tasa);
	$sql->bindParam(':saldo_capital', $saldo_capital);
	$sql->bindParam(':saldo_interes', $saldo_interes);
	$sql->bindParam(':tipo_cambio', $tipo_cambio);
	$sql->bindParam(':dias_mora', $dias_mora);
	$sql->bindParam(':numero_cuota', $numero_cuota);
	$sql->bindParam(':total_cuotas', $total_cuotas);
	$sql->bindParam(':monto_cuota', $monto_cuota);
	$sql->bindParam(':monto_mora', $monto_mora);
	$sql->bindParam(':cuotas_pagadas', $cuotas_pagadas);
	$sql->bindParam(':cuotas_pendientes', $cuotas_pendientes);
	$sql->bindParam(':fecha_vto_cuota', $fecha_vto_cuota);
	$sql->bindParam(':ult_fecha_pago', $ult_fecha_pago);
	$sql->bindParam(':total_deuda', $total_deuda);
	$sql->bindParam(':estado', $estado);
	$sql->bindParam(':fecha_apertura', $fecha_apertura);
	$sql->bindParam(':riesgo', $riesgo);
	$sql->bindParam(':referencia_uno', $refuno);
	$sql->bindParam(':referencia_dos', $refdos);
	$sql->bindParam(':referencia_tres', $reftres);
	$sql->bindParam(':referencia_cuatro', $refcuatro);
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

echo  "Registros recorridos: " . $recorrido . ' ;' . "\n";
echo  "Insertardos: " . $insertados . ' ;' . "\n";
echo  "No insertardos: " . $noinsertados . ' ;' . "\n";

if($noinsertados > 0){
	echo var_dump($data);
}
