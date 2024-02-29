<?php
#importarCuentas.php
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
$contactList = []; // Inicializar el arreglo de contactos
if (strtoupper($fileExtension) == 'CSV') {
    foreach ($fileContacts as $contact) {
        if ($firstRow) {
            // Saltar la primera fila (encabezados)
            $firstRow = false;
            continue;
        }
        $cleanedData = mb_convert_encoding($contact, 'UTF-8', 'ISO-8859-1');
        $contactFields = explode(";", $cleanedData);

        // Convertir números científicos a cadena de caracteres si es necesario
        foreach ($contactFields as &$field) {
            if (is_numeric($field) && strpos($field, 'E') !== false) {
                // Convertir el número científico a cadena de caracteres
                $field = sprintf('%f', $field);
            }
        }

        $contactList[] = $contactFields;
    }
} else {
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
	case 'sudameris':
		insertSudameris();
		break;
	default:
		echo "Cliente no soportado";
		break;
}


function insertLux(){
	global $contactList, $data, $connect, $selectDate;
	
	$insertados = 0;
	$noinsertados = 0;
	$recorrido = 0;
	$query = getQueryLux();

	foreach ($contactList as $contactData) {

		$recorrido = $recorrido + 1;
	
		empty(trim($contactData[0])) ?	$sucursal = null : 	$sucursal = trim($contactData[0]);
		empty(trim($contactData[1])) ?	$cliente = null : 	$cliente = trim($contactData[1]);
		empty(trim($contactData[2])) ?	$nombre = null : 	$nombre = str_replace(['Ñ', "'", '!'], ['N', '', ''], trim($contactData[2]));
		empty(trim($contactData[3])) ?	$ruc = null : 	$ruc = trim($contactData[3]);
		empty(trim($contactData[4])) ?	$ci = null : 	$ci = trim($contactData[4]);
		empty(trim($contactData[5])) ?	$codigo_interno = null : 	$codigo_interno = trim($contactData[5]);
		empty(trim($contactData[6])) ?	$nro_documento = null : 	$nro_documento = trim($contactData[6]);
		empty(trim($contactData[7])) ?	$pago_nro = null : 	$pago_nro = trim($contactData[7]);
		empty(trim($contactData[8])) ?	$fecha = '1900/01/01' : 	$fecha = trim($contactData[8]);
		empty(trim($contactData[9])) ?	$moneda = null : 	$moneda = trim($contactData[9]);
		empty(trim($contactData[10])) ?	$nro_cuota = null : 	$nro_cuota = trim($contactData[10]);
		empty(trim($contactData[11])) ?	$vencimiento = '1900/01/01' : 	$vencimiento = trim($contactData[11]);
		empty(trim($contactData[12])) ?	$importe_cuota = 0 : 	$importe_cuota = str_replace(".", "", trim($contactData[12]));
		empty(trim($contactData[13])) ?	$saldo_cuota = 0 : 	$saldo_cuota = str_replace(".", "", trim($contactData[13]));
		empty(trim($contactData[14])) ?	$fecha_pago = '1900/01/01' : 	$fecha_pago = trim($contactData[14]);
		empty(trim($contactData[15])) ?	$importe_pago = 0 : 	$importe_pago = str_replace(".", "", trim($contactData[15]));
		empty(trim($contactData[16])) ?	$intereses = 0 : 	$intereses = str_replace(",", ".", trim($contactData[16]));
		empty(trim($contactData[17])) ?	$cod_cobrador = null : 	$cod_cobrador = trim($contactData[17]);
		empty(trim($contactData[18])) ?	$cobrador_recibo = null : 	$cobrador_recibo = (trim($contactData[18]));
		empty(trim($contactData[19])) ?	$cod_cobrador_cliente = null : 	$cod_cobrador_cliente = trim($contactData[19]);
		empty(trim($contactData[20])) ?	$cobrador_cliente = 0 : 	$cobrador_cliente = trim($contactData[20]);
		empty(trim($contactData[21])) ?	$cod_vendedor = 0 : 	$cod_vendedor = $contactData[21];
		empty(trim($contactData[22])) ?	$vendedor = 0 : 	$vendedor = (trim($contactData[22]));
		empty(trim($contactData[23])) ?	$cod_grupo_vendedor = 0 : 	$cod_grupo_vendedor = trim($contactData[23]);
		empty(trim($contactData[24])) ?	$grupo_vendedor = null : 	$grupo_vendedor = trim($contactData[24]);
		empty(trim($contactData[25])) ?	$dias_atraso = 0 : 	$dias_atraso = trim($contactData[25]);
		empty(trim($contactData[26])) ?	$nro_pagare = null : 	$nro_pagare = trim($contactData[26]);
		empty(trim($contactData[27])) ?	$localidad = null : 	$localidad = (trim($contactData[27]));
		empty(trim($contactData[28])) ?	$pagare_unico = null : 	$pagare_unico = trim($contactData[28]);
		empty(trim($contactData[29])) ?	$nro_solicitud = null : 	$nro_solicitud = trim($contactData[29]); 
		empty(trim($contactData[30])) ?	$barrio = null : 	$barrio = (trim($contactData[30]));
		empty(trim($contactData[31])) ?	$direccion = null : 	$direccion = str_replace(['Ñ', "'", '!'], ['N', '', ''], trim($contactData[31]));
		empty(trim($contactData[32])) ?	$telefono = null : 	$telefono = trim($contactData[32]);
		empty(trim($contactData[33])) ?	$celular = null : $celular = trim($contactData[33]);
		empty(trim($contactData[32])) ?	$ubicacion = null : $ubicacion = (trim($contactData[32]));

		$cod_ubicacion = null ;

		$created_by = 409;
		//$created_by = 1155;
	
		$sql = $connect->prepare($query);
		$sql->bindParam(':sucursal', $sucursal);
		$sql->bindParam(':cliente', $cliente);
		$sql->bindParam(':nombre', $nombre);
		$sql->bindParam(':ruc', $ruc);
		$sql->bindParam(':ci', $ci);
		$sql->bindParam(':codigo_interno', $codigo_interno);
		$sql->bindParam(':nro_documento', $nro_documento);
		$sql->bindParam(':pago_nro', $pago_nro);
		$sql->bindParam(':fecha', $fecha);
		$sql->bindParam(':moneda', $moneda);
		$sql->bindParam(':nro_cuota', $nro_cuota);
		$sql->bindParam(':vencimiento', $vencimiento);
		$sql->bindParam(':importe_cuota', $importe_cuota);
		$sql->bindParam(':saldo_cuota', $saldo_cuota);
		$sql->bindParam(':fecha_pago', $fecha_pago);
		$sql->bindParam(':importe_pago', $importe_pago);
		$sql->bindParam(':intereses', $intereses);
		$sql->bindParam(':cod_cobrador', $cod_cobrador);
		$sql->bindParam(':cobrador_recibo', $cobrador_recibo);
		$sql->bindParam(':cod_cobrador_cliente', $cod_cobrador_cliente);
		$sql->bindParam(':cobrador_cliente', $cobrador_cliente);
		$sql->bindParam(':cod_vendedor', $cod_vendedor);
		$sql->bindParam(':vendedor', $vendedor);
		$sql->bindParam(':cod_grupo_vendedor', $cod_grupo_vendedor);
		$sql->bindParam(':grupo_vendedor', $grupo_vendedor);
		$sql->bindParam(':dias_atraso', $dias_atraso);
		$sql->bindParam(':nro_pagare', $nro_pagare);
		$sql->bindParam(':localidad', $localidad);
		$sql->bindParam(':pagare_unico', $pagare_unico);
		$sql->bindParam(':nro_solicitud', $nro_solicitud);
		$sql->bindParam(':barrio', $barrio);
		$sql->bindParam(':direccion', $direccion);
		$sql->bindParam(':telefono', $telefono);
		$sql->bindParam(':cod_ubicacion', $cod_ubicacion);
		$sql->bindParam(':ubicacion', $ubicacion);
		$sql->bindParam(':celular', $celular);
		$sql->bindParam(':created_by', $created_by);
		$sql->bindParam(':cedula', $ci);
		$sql->bindParam(':fecha_insert', $selectDate);
	
		$exec = $sql->execute();
		if ($exec) {
			$insertados = $insertados + 1;
		} else {
			$noinsertados = $noinsertados + 1;
			$data = [
				"cliente" => $contactData[1],
				"operacion" => $contactData[8],
				"mensaje" => $sql->errorInfo()
			];
		}
	}
	showResults($recorrido, $insertados, $noinsertados);
}

function insertInterfisa(){
	global $contactList, $data, $connect, $selectTipo, $selectDate;
	
	$insertados = 0;
	$noinsertados = 0;
	$recorrido = 0;
	$query = getQueryInterfisa();

	foreach ($contactList as $contactData) {

		$recorrido = $recorrido + 1;
		# Preparar sentencia de productos
	
		empty(trim($contactData[0])) ?	$cod_cliente = null : 	$cod_cliente = trim($contactData[0]);
		empty(trim($contactData[1])) ?	$nro_documento = null : $nro_documento = trim($contactData[1]);
		empty(trim($contactData[2])) ?  $nombre_cliente = null : $nombre_cliente = str_replace(['Ñ', "'", '!'], ['N', '', ''], trim($contactData[2]));
		$telLaboral = trim($contactData[3]);
		if (is_numeric($telLaboral) && strpos($telLaboral, 'E') !== false) {
			// Convertimos el número científico a cadena de caracteres
			$ope = convertirNumeroCientifico($telLaboral);
		}
		empty(trim($contactData[4])) ?	$telefono_particular = null : 	$telefono_particular = trim($contactData[4]);
		empty(trim($contactData[5])) ?	$celular_alternativo = null : 	$celular_alternativo = trim($contactData[5]);
		empty(trim($contactData[6])) ?	$celular_laboral = null : 	$celular_laboral = trim($contactData[6]);
		empty(trim($contactData[7])) ?	$celular_particular = null : 	$celular_particular = trim($contactData[7]);
		$ope = trim($contactData[8]);
		if (is_numeric($ope) && strpos($ope, 'E') !== false) {
			// Convertimos el número científico a cadena de caracteres
			$ope = convertirNumeroCientifico($ope);
		}
		empty($ope) ?	$operacion = null : 	$operacion = $ope;
		empty(trim($contactData[9])) ?	$segmento = null : 	$segmento = trim($contactData[9]);
		empty(trim($contactData[10])) ?	$producto = null : 	$producto = trim($contactData[10]);
		empty(trim($contactData[11])) ?	$tipo_credito_tarjeta = null : 	$tipo_credito_tarjeta = htmlspecialchars(trim($contactData[11]));
		empty(trim($contactData[12])) ?	$tipo_operacion = null : 	$tipo_operacion = trim($contactData[12]);
		empty(trim($contactData[13])) ?	$tasa = 0 : 	$tasa = trim($contactData[13]);
		empty(trim($contactData[14])) ?	$saldo_capital = 0 : 	$saldo_capital = str_replace(",", ".", trim($contactData[14]));
		empty(trim($contactData[15])) ?	$saldo_interes = 0 : 	$saldo_interes = str_replace(",", ".", trim($contactData[15]));
		empty(trim($contactData[16])) ?	$tipo_cambio = 0 : 	$tipo_cambio = str_replace(",", ".", trim($contactData[16]));
		$dias_mora = trim($contactData[17]) ? trim($contactData[17]) : 1;
		$numero_cuota = trim($contactData[18]) ? trim($contactData[18]) : 0;
		$total_cuotas = trim($contactData[19]) ? trim($contactData[19]) : 0;
		empty(trim($contactData[20])) ?	$monto_cuota = 0 : 	$monto_cuota = str_replace(",", ".", trim($contactData[20]));
		empty(trim($contactData[21])) ?	$monto_mora = 0 : 	$monto_mora =  str_replace(",", ".", trim($contactData[21]));
		empty(trim($contactData[22])) ?	$cuotas_pagadas = 0 : 	$cuotas_pagadas = trim($contactData[22]);
		empty(trim($contactData[23])) ?	$cuotas_pendientes = 0 : 	$cuotas_pendientes = trim($contactData[23]);
		empty(trim($contactData[24])) ?	$fecha_vto_cuota = "1900/01/01" : 	$fecha_vto_cuota = trim($contactData[24]);
		empty(trim($contactData[25])) ?	$ult_fecha_pago = "1900/01/01" : 	$ult_fecha_pago = trim($contactData[25]);
		empty(trim($contactData[26])) ?	$total_deuda = 0 : 	$total_deuda = str_replace(",", ".", trim($contactData[26]));
		empty(trim($contactData[27])) ?	$estado = null : 	$estado = trim($contactData[27]);
		empty(trim($contactData[28])) ?	$fecha_apertura = "1900/01/01" : 	$fecha_apertura = trim($contactData[28]);
		empty(trim($contactData[29])) ?	$riesgo = 0 : 	$riesgo = trim($contactData[29]); //edit by Ydy Martinez
		
		$usu_insert = 1155;
		$refuno = "";
		$refdos =  "";
		$reftres =  "";
		$refcuatro = "";
		
		$sql = $connect->prepare($query);
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
		$sql->bindParam(':fecha_insert', $selectDate);
		$sql->bindParam(':usu_insert', $usu_insert);
		$exec = $sql->execute();
	
		if ($exec) {
			$insertados = $insertados + 1;
		} else {
			$noinsertados = $noinsertados + 1;
			$data = [
				"cliente" => $contactData[1],
				"operacion" => $contactData[8],
				"mensaje" => $sql->errorInfo()
			];
		}
	}
	showResults($recorrido, $insertados, $noinsertados);
}

function eliminarAcentos($string) {
    // Eliminar caracteres especiales y acentos
    $string = preg_replace('/[^\x20-\x7E]/','', $string);
    return $string;
}

function insertVinanzas(){
	global $contactList, $data, $connect, $selectDate;
	
	$insertados = 0;
	$noinsertados = 0;
	$recorrido = 0;
	$query = getQueryVinanzas();
	//FRANCISCA LOMBARDO NUÃ‘EZ  Este viene en el CSV
	//FRANCISCA LOMBARDO NUEZ   Este es sin aplicar utf8 al insertar en BD
	//FRANCISCA LOMBARDO NUÂEZ Este es aplicando utf8 al insertar en BD
	foreach ($contactList as $contactData) {

		$recorrido = $recorrido + 1;
		# Preparar sentencia de productos
	
		empty(trim($contactData[0])) ? $cod_persona = 0 : $cod_persona = trim($contactData[0]);
		empty(trim($contactData[1])) ? $nro_documento = null : $nro_documento = trim($contactData[1]);
		empty(trim($contactData[2])) ? $nom_completo = null : $nom_completo = str_replace(['Ñ', 'Ã', "'", '!'], ['N', '', '', ''], utf8_encode(trim($contactData[2])));
		empty(trim($contactData[3])) ? $dir_principal = null : $dir_principal = eliminarAcentos(utf8_encode(trim($contactData[3])));
		empty(trim($contactData[4])) ? $barrio = null : $barrio = str_replace(['Ñ', "'", '!'], ['N', '', ''], trim($contactData[4]));
		empty(trim($contactData[5])) ? $ciudad = null : $ciudad = str_replace(['Ñ', "'", '!'], ['N', '', ''], trim($contactData[5]));
		empty(trim($contactData[6])) ? $telefono1 = null : $telefono1 = trim($contactData[6]);
		empty(trim($contactData[7])) ? $telefono2 = null : $telefono2 = trim($contactData[7]);
		empty(trim($contactData[8])) ? $lug_trabajo = null : $lug_trabajo = str_replace(['Ñ', 'Ã', "'", '!'], ['N', '', '', ''], utf8_encode(trim($contactData[8])));
		empty(trim($contactData[9])) ? $direccion_laboral = null : $direccion_laboral = str_replace(['Ñ',"Ã", "'", '!'], ['N', '', '', ''], utf8_encode(trim($contactData[9])));
		empty(trim($contactData[10])) ? $telefono_laboral = null : $telefono_laboral = trim($contactData[10]);
		empty(trim($contactData[11])) ? $mayor_atr_cli_inicial = 0 : $mayor_atr_cli_inicial = trim($contactData[11]);
		empty(trim($contactData[12])) ? $tramo_cli_inicial = 0 : $tramo_cli_inicial = trim($contactData[12]);
		empty(trim($contactData[13])) ? $cod_equipo = 0 : $cod_equipo = trim($contactData[13]);
		empty(trim($contactData[14])) ? $moneda = null : $moneda = trim($contactData[14]);
		$ope = trim($contactData[15]);
		if (is_numeric($ope) && strpos($ope, 'E') !== false) {
			// Convertimos el número científico a cadena de caracteres
			$ope = convertirNumeroCientifico($ope);
		}
		empty(trim($ope)) ? $nro_operacion = null : $nro_operacion = trim($ope);
		$ope_ori = trim($contactData[16]);
		if (is_numeric($ope_ori) && strpos($ope_ori, 'E') !== false) {
			// Convertimos el número científico a cadena de caracteres
			$ope_ori = convertirNumeroCientifico($ope_ori);
		}
		empty(trim($ope_ori)) ? $nro_ope_original = null : $nro_ope_original = trim($ope_ori);
		empty(trim($contactData[17])) ? $origen_operacion = null : $origen_operacion = str_replace(['Ñ', "'", '!'], ['N', '', ''], trim($contactData[17]));
		empty(trim($contactData[18])) ? $tipo_operacion = null : $tipo_operacion = trim($contactData[18]);
		empty(trim($contactData[19])) ? $dia_atr_ope_actual = 0 : $dia_atr_ope_actual = trim($contactData[19]);
		empty(trim($contactData[20])) ? $tramo_cuenta = 0 : $tramo_cuenta = trim($contactData[20]);
		empty(trim($contactData[21])) ? $segmentacion = null : $segmentacion = trim($contactData[21]);
		empty(trim($contactData[22])) ? $cod_modalidad = 0 : $cod_modalidad = trim($contactData[22]);
		empty(trim($contactData[23])) ? $modalidad = null : $modalidad = trim($contactData[23]);
		empty(trim($contactData[24])) ? $mto_deu_total_actual = 0 : $mto_deu_total_actual = trim($contactData[24]);
		empty(trim($contactData[25])) ? $mto_deu_vencida_inicial = 0 : $mto_deu_vencida_inicial = trim($contactData[25]);
		empty(trim($contactData[26])) ? $mto_deu_vencida_actual = 0 : $mto_deu_vencida_actual = trim($contactData[26]);
		empty(trim($contactData[27])) ? $mto_deu_vencer_inicial = 0 : $mto_deu_vencer_inicial = trim($contactData[27]);
		empty(trim($contactData[28])) ? $mto_deu_vencer_actual = 0 : $mto_deu_vencer_actual = trim($contactData[28]);
		empty(trim($contactData[29])) ? $mto_cobrado = 0 : $mto_cobrado = trim($contactData[29]);
		empty(trim($contactData[30])) ? $mto_cobrado_neto = 0 : $mto_cobrado_neto = trim($contactData[30]);
		empty(trim($contactData[31])) ? $judicial = null : $judicial = trim($contactData[31]);
		empty(trim($contactData[32])) ? $cartera_ste = null : $cartera_ste = trim($contactData[32]);
		empty(trim($contactData[33])) ? $fec_ult_pago = "1900/01/01" : $fec_ult_pago = trim($contactData[33]);
		empty(trim($contactData[34])) ? $modalidad_original = null : $modalidad_original = trim($contactData[34]);
		empty(trim($contactData[35])) ? $nro_doc_librador = null : $nro_doc_librador = trim($contactData[35]);
		empty(trim($contactData[36])) ? $nom_librador = null : $nom_librador = trim($contactData[36]);
		empty(trim($contactData[37])) ? $nro_cuota = 0 : $nro_cuota = trim($contactData[37]);
		empty(trim($contactData[38])) ? $fec_vencimiento = null : $fec_vencimiento = trim($contactData[38]);
		empty(trim($contactData[39])) ? $fec_ult_pago_1 = null : $fec_ult_pago_1 = trim($contactData[39]);
		empty(trim($contactData[40])) ? $sal_capital = 0 : $sal_capital = str_replace(",", ".", trim($contactData[40]));
		empty(trim($contactData[41])) ? $sal_interes = 0 : $sal_interes = str_replace(",", ".", trim($contactData[41]));
		empty(trim($contactData[42])) ? $mto_int_mora = 0 : $mto_int_mora = str_replace(",", ".", trim($contactData[42]));
		empty(trim($contactData[43])) ? $mto_int_punitorio = 0 : $mto_int_punitorio = str_replace(",", ".", trim($contactData[43]));
		empty(trim($contactData[44])) ? $mto_gas_cobranzas = 0 : $mto_gas_cobranzas = str_replace(",", ".", trim($contactData[44]));
		empty(trim($contactData[45])) ? $mto_impuesto = 0 : $mto_impuesto = str_replace(",", ".", trim($contactData[45]));

		$sql = $connect->prepare($query);
		$sql->bindParam(':cod_persona', $cod_persona);
		$sql->bindParam(':nro_documento', $nro_documento);
		$sql->bindParam(':nom_completo', $nom_completo);
		$sql->bindParam(':dir_principal', $dir_principal);
		$sql->bindParam(':barrio', $barrio);
		$sql->bindParam(':ciudad', $ciudad);
		$sql->bindParam(':telefono1', $telefono1);
		$sql->bindParam(':telefono2', $telefono2);
		$sql->bindParam(':lug_trabajo', $lug_trabajo);
		$sql->bindParam(':direccion_laboral', $direccion_laboral);
		$sql->bindParam(':telefono_laboral', $telefono_laboral);
		$sql->bindParam(':mayor_atr_cli_inicial', $mayor_atr_cli_inicial);
		$sql->bindParam(':tramo_cli_inicial', $tramo_cli_inicial);
		$sql->bindParam(':cod_equipo', $cod_equipo);
		$sql->bindParam(':moneda', $moneda);
		$sql->bindParam(':nro_operacion', $nro_operacion);
		$sql->bindParam(':nro_ope_original', $nro_ope_original);
		$sql->bindParam(':origen_operacion', $origen_operacion);
		$sql->bindParam(':tipo_operacion', $tipo_operacion);
		$sql->bindParam(':dia_atr_ope_actual', $dia_atr_ope_actual);
		$sql->bindParam(':tramo_cuenta', $tramo_cuenta);
		$sql->bindParam(':segmentacion', $segmentacion);
		$sql->bindParam(':cod_modalidad', $cod_modalidad);
		$sql->bindParam(':modalidad', $modalidad);
		$sql->bindParam(':mto_deu_total_actual', $mto_deu_total_actual);
		$sql->bindParam(':mto_deu_vencida_inicial', $mto_deu_vencida_inicial);
		$sql->bindParam(':mto_deu_vencida_actual', $mto_deu_vencida_actual);
		$sql->bindParam(':mto_deu_vencer_inicial', $mto_deu_vencer_inicial);
		$sql->bindParam(':mto_deu_vencer_actual', $mto_deu_vencer_actual);
		$sql->bindParam(':mto_cobrado', $mto_cobrado);
		$sql->bindParam(':mto_cobrado_neto', $mto_cobrado_neto);
		$sql->bindParam(':judicial', $judicial);
		$sql->bindParam(':cartera_ste', $cartera_ste);
		$sql->bindParam(':fec_ult_pago', $fec_ult_pago);
		$sql->bindParam(':modalidad_original', $modalidad_original);
		$sql->bindParam(':nro_doc_librador', $nro_doc_librador);
		$sql->bindParam(':nom_librador', $nom_librador);
		$sql->bindParam(':nro_cuota', $nro_cuota);
		$sql->bindParam(':fec_vencimiento', $fec_vencimiento);
		$sql->bindParam(':fec_ult_pago_1', $fec_ult_pago_1);
		$sql->bindParam(':sal_capital', $sal_capital);
		$sql->bindParam(':sal_interes', $sal_interes);
		$sql->bindParam(':mto_int_mora', $mto_int_mora);
		$sql->bindParam(':mto_int_punitorio', $mto_int_punitorio);
		$sql->bindParam(':mto_gas_cobranzas', $mto_gas_cobranzas);
		$sql->bindParam(':mto_impuesto', $mto_impuesto);
		$sql->bindParam(':fecha_insert', $selectDate);
		
		$exec = $sql->execute();
	
		if ($exec) {
			$insertados = $insertados + 1;
		} else {
			$noinsertados = $noinsertados + 1;
			$data = [
				"cliente" => $contactData[1],
				"operacion" => $contactData[8],
				"mensaje" => $sql->errorInfo()
			];
		}
	}
	showResults($recorrido, $insertados, $noinsertados);
}

function insertSudameris(){
	global $contactList, $data, $connect, $selectDate;
	
	$insertados = 0;
	$noinsertados = 0;
	$recorrido = 0;
	$query = getQuerySudameris();

	foreach ($contactList as $contactData){

		$recorrido = $recorrido + 1;
		
		$fecha = empty(trim($contactData[0])) ? "1900/01/01" : trim($contactData[0]);
		$nrocuenta = empty(trim($contactData[1])) ? null : trim($contactData[1]);
		$cod_pais = empty(trim($contactData[2])) ? 0 : trim($contactData[2]);
		$tipo_doc = empty(trim($contactData[3])) ? 0 : trim($contactData[3]);
		$nro_documento = empty(trim($contactData[4])) ? null : trim($contactData[4]);
		$nombre_cuenta = empty(trim($contactData[5])) ? null : trim($contactData[5]);
		$id_operacion = empty(trim($contactData[6])) ? null : trim($contactData[6]);
		$segmento = empty(trim($contactData[7])) ? null : trim($contactData[7]);
		$producto = empty(trim($contactData[8])) ? null : trim($contactData[8]);
		$tipo_producto = empty(trim($contactData[9])) ? null : trim($contactData[9]);
		$moneda = empty(trim($contactData[10])) ? 0 : trim($contactData[10]);
		$tasa = empty(trim($contactData[11])) ? 0 : trim($contactData[11]);
		$saldo_capital = empty(trim($contactData[12])) ? 0 : trim($contactData[12]);
		$saldo_interes = empty(trim($contactData[13])) ? 0 : trim($contactData[13]);
		$dias_mora = empty(trim($contactData[14])) ? 0 : trim($contactData[14]);
		$cantidad_cuotas = empty(trim($contactData[15])) ? 0 : trim($contactData[15]);
		$cuotas_pagadas = empty(trim($contactData[16])) ? 0 : trim($contactData[16]);
		$cuotas_pendientes = empty(trim($contactData[17])) ? 0 : trim($contactData[17]);
		$importe_total_vencido = empty(trim($contactData[18])) ? 0 : trim($contactData[18]);
		$fechavtomasantiguo = empty(trim($contactData[19])) ? null : trim($contactData[19]);
		$fechaultimopago = empty(trim($contactData[20])) ? null : trim($contactData[20]);
		$saldoriesgoop = empty(trim($contactData[21])) ? null : trim($contactData[21]);
		$fechavalorop = empty(trim($contactData[22])) ? null : trim($contactData[22]);
		$crcbanco = empty(trim($contactData[23])) ? 0 : trim($contactData[23]);
		$ejecutivocuenta = empty(trim($contactData[24])) ? null : trim($contactData[24]);
		$cuentaclientecash = empty(trim($contactData[25])) ? null : trim($contactData[25]);
		$empresapayroll = empty(trim($contactData[26])) ? null : trim($contactData[26]);
		$fechaultimocobro = empty(trim($contactData[27])) ? null : trim($contactData[27]);
		$montoultimocobro = empty(trim($contactData[28])) ? null : trim($contactData[28]);
		$fechanacimiento = empty(trim($contactData[29])) ? null : trim($contactData[29]);
		$estadocivil = empty(trim($contactData[30])) ? null : trim($contactData[30]);
		$separacionbienes = empty(trim($contactData[31])) ? null : trim($contactData[31]);
		$codpaiscony = empty(trim($contactData[32])) ? 0 : trim($contactData[32]);
		$tipodocconyuge = empty(trim($contactData[33])) ? 0 : trim($contactData[33]);
		$documentoconyuge = empty(trim($contactData[34])) ? null : trim($contactData[34]);
		$nombreconyuge = empty(trim($contactData[35])) ? null : trim($contactData[35]);
		$crcsistema = empty(trim($contactData[36])) ? 0 : trim($contactData[36]);
		$deudasistema = empty(trim($contactData[37])) ? null : trim($contactData[37]);
		$importeoriginal = empty(trim($contactData[38])) ? null : trim($contactData[38]);
		$fechavto = empty(trim($contactData[39])) ? null : trim($contactData[39]);
		$interesvencido = empty(trim($contactData[40])) ? null : trim($contactData[40]);
		$interes_suspendido = empty(trim($contactData[41])) ? null : trim($contactData[41]);
		$otroscargos = empty(trim($contactData[42])) ? null : trim($contactData[42]);
		$cuentascobro = empty(trim($contactData[43])) ? null : trim($contactData[43]);
		$vinvulo_op = empty(trim($contactData[44])) ? null : trim($contactData[44]);
		$codpaisvinc = empty(trim($contactData[45])) ? 0 : trim($contactData[45]);
		$tipodocvinc = empty(trim($contactData[46])) ? 0 : trim($contactData[46]);
		$documentocodeudor = empty(trim($contactData[47])) ? null : trim($contactData[47]);
		$nombrecodeudor = empty(trim($contactData[48])) ? null : trim($contactData[48]);
		$tarjetanro = empty(trim($contactData[49])) ? null : trim($contactData[49]);
		$formabloqueo = empty(trim($contactData[50])) ? null : trim($contactData[50]);
		$fechaultcierre = empty(trim($contactData[51])) ? null : trim($contactData[51]);
		$cargos_suspendidos = empty(trim($contactData[52])) ? null : trim($contactData[52]);
		$tramomora = empty(trim($contactData[53])) ? null : trim($contactData[53]);
		$modulo = empty(trim($contactData[54])) ? null : trim($contactData[54]);
		$codagente = empty(trim($contactData[55])) ? null : trim($contactData[55]);
		$importecapitalvencido = empty(trim($contactData[56])) ? null : trim($contactData[56]);

		$fecha_insert = $selectDate;
		
		$sql = $connect->prepare($query);
		$sql->bindParam(':fecha', $fecha);
		$sql->bindParam(':nrocuenta', $nrocuenta);
		$sql->bindParam(':cod_pais', $cod_pais);
		$sql->bindParam(':tipo_doc', $tipo_doc);
		$sql->bindParam(':nro_documento', $nro_documento);
		$sql->bindParam(':nombre_cuenta', $nombre_cuenta);
		$sql->bindParam(':id_operacion', $id_operacion);
		$sql->bindParam(':segmento', $segmento);
		$sql->bindParam(':producto', $producto);
		$sql->bindParam(':tipo_producto', $tipo_producto);
		$sql->bindParam(':moneda', $moneda);
		$sql->bindParam(':tasa', $tasa);
		$sql->bindParam(':saldo_capital', $saldo_capital);
		$sql->bindParam(':saldo_interes', $saldo_interes);
		$sql->bindParam(':dias_mora', $dias_mora);
		$sql->bindParam(':cantidad_cuotas', $cantidad_cuotas);
		$sql->bindParam(':cuotas_pagadas', $cuotas_pagadas);
		$sql->bindParam(':cuotas_pendientes', $cuotas_pendientes);
		$sql->bindParam(':importe_total_vencido', $importe_total_vencido);
		$sql->bindParam(':fechavtomasantiguo', $fechavtomasantiguo);
		$sql->bindParam(':fechaultimopago', $fechaultimopago);
		$sql->bindParam(':saldoriesgoop', $saldoriesgoop);
		$sql->bindParam(':fechavalorop', $fechavalorop);
		$sql->bindParam(':crcbanco', $crcbanco);
		$sql->bindParam(':ejecutivocuenta', $ejecutivocuenta);
		$sql->bindParam(':cuentaclientecash', $cuentaclientecash);
		$sql->bindParam(':empresapayroll', $empresapayroll);
		$sql->bindParam(':fechaultimocobro', $fechaultimocobro);
		$sql->bindParam(':montoultimocobro', $montoultimocobro);
		$sql->bindParam(':fechanacimiento', $fechanacimiento);
		$sql->bindParam(':estadocivil', $estadocivil);
		$sql->bindParam(':separacionbienes', $separacionbienes);
		$sql->bindParam(':nombreconyuge', $nombreconyuge);
		$sql->bindParam(':codpaiscony', $codpaiscony);
		$sql->bindParam(':tipodocconyuge', $tipodocconyuge);
		$sql->bindParam(':documentoconyuge', $documentoconyuge);
		$sql->bindParam(':crcsistema', $crcsistema);
		$sql->bindParam(':deudasistema', $deudasistema);
		$sql->bindParam(':importeoriginal', $importeoriginal);
		$sql->bindParam(':fechavto', $fechavto);
		$sql->bindParam(':interesvencido', $interesvencido);
		$sql->bindParam(':interes_suspendido', $interes_suspendido);
		$sql->bindParam(':otroscargos', $otroscargos);
		$sql->bindParam(':cuentascobro', $cuentascobro);
		$sql->bindParam(':vinvulo_op', $vinvulo_op);
		$sql->bindParam(':codpaisvinc', $codpaisvinc);
		$sql->bindParam(':tipodocvinc', $tipodocvinc);
		$sql->bindParam(':documentocodeudor', $documentocodeudor);
		$sql->bindParam(':nombrecodeudor', $nombrecodeudor);
		$sql->bindParam(':tarjetanro', $tarjetanro);
		$sql->bindParam(':formabloqueo', $formabloqueo);
		$sql->bindParam(':fechaultcierre', $fechaultcierre);
		$sql->bindParam(':cargos_suspendidos', $cargos_suspendidos);
		$sql->bindParam(':tramomora', $tramomora);
		$sql->bindParam(':modulo', $modulo);
		$sql->bindParam(':codagente', $codagente);
		$sql->bindParam(':fecha_insert', $fecha_insert);
		$sql->bindParam(':importecapitalvencido', $importecapitalvencido);

		$exec = $sql->execute();
		//echo $sql;
		if ($exec) {
				$insertados = $insertados + 1;
		} else {
			$noinsertados = $noinsertados + 1;
			$data = [
				"cliente" => $contactData[1],
				"operacion" => $contactData[8],
				"mensaje" => $sql->errorInfo()
			];
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

	foreach ($contactList as $contactData){

		$recorrido = $recorrido + 1;
		
		empty(trim($contactData[0])) ?	$fecha_proceso = "1900/01/01" : 	$fecha_proceso = trim($contactData[0]);
		empty(trim($contactData[1])) ?	$cod_cliente = null : 	$cod_cliente = trim($contactData[1]);
		empty(trim($contactData[2])) ?	$nro_documento = null : $nro_documento = trim($contactData[2]);
		empty(trim($contactData[3])) ?  $nombre_cliente = null : $nombre_cliente = str_replace(['Ñ', "'", '!'], ['N', '', ''], trim($contactData[3]));
		empty(trim($contactData[4])) ?	$telefono_laboral = null : 	$telefono_laboral = trim($contactData[4]);
		empty(trim($contactData[5])) ?	$telefono_particular = null : 	$telefono_particular = trim($contactData[5]);
		empty(trim($contactData[6])) ?	$celular_particular = null : 	$celular_particular = trim($contactData[6]);
		empty(trim($contactData[7])) ?	$operacion = null : 	$operacion = trim($contactData[7]);
		empty(trim($contactData[8])) ?	$numero_cuota = '1' : 	$numero_cuota = trim($contactData[8]);
		empty(trim($contactData[9])) ?	$producto = null : 	$producto = trim($contactData[9]);
		empty(trim($contactData[10])) ?	$tipo_credito = null : 	$tipo_credito = htmlspecialchars(trim($contactData[10]));
		empty(trim($contactData[11])) ?	$cod_moneda = '1' : 	$cod_moneda = trim($contactData[11]);
		empty(trim($contactData[12])) ?	$tasa = 0 : 	$tasa = trim($contactData[12]);
		$saldo_capital = str_replace(",", ".", trim($contactData[13]));
		$saldo_interes =  str_replace(",", ".", trim($contactData[14]));
		$total_cuotas = trim($contactData[15]);
		empty(trim($contactData[16])) ?	$monto_mora = 0 : 	$monto_mora =  str_replace(",", ".", trim($contactData[16]));
		empty(trim($contactData[17])) ?	$cotizacion = '' : 	$cotizacion =  trim($contactData[17]);
		$saldo_capital_gs = str_replace(",", ".", trim($contactData[18]));
		$saldo_interes_gs =  str_replace(",", ".", trim($contactData[19]));
		$total_cuotas_gs = trim($contactData[20]);
		empty(trim($contactData[21])) ?	$monto_mora = 0 : 	$monto_mora =  str_replace(",", ".", trim($contactData[21]));
		$dias_mora = trim($contactData[22]);
		empty(trim($contactData[23])) ?	$total_nro_cuotas = 0 : 	$total_nro_cuotas = trim($contactData[23]);
		empty(trim($contactData[24])) ? $cuotas_pagadas = 0 : $cuotas_pagadas = floatval(trim($contactData[24]));
		empty(trim($contactData[25])) ?	$cuotas_pendientes = 0 : 	$cuotas_pendientes = trim($contactData[25]);
		empty(trim($contactData[26])) ?	$fecha_vto_cuota = "1900/01/01" : 	$fecha_vto_cuota = trim($contactData[26]);
		empty(trim($contactData[27])) ?	$ult_fecha_pago = "" : 	$ult_fecha_pago = trim($contactData[27]);
		empty(trim($contactData[28])) ?	$estado = null : 	$estado = trim($contactData[28]);
		empty(trim($contactData[29])) ?	$fecha_apertura = "1900/01/01" : 	$fecha_apertura = trim($contactData[29]);
		empty(trim($contactData[30])) ?	$cod_gestor = '' : 	$cod_gestor = trim($contactData[30]); 
		
		$sql = $connect->prepare($query);
		$sql->bindParam(':fec_proceso', $fecha_proceso);
		$sql->bindParam(':cod_cliente', $cod_cliente);
		$sql->bindParam(':nro_documento', $nro_documento);
		$sql->bindParam(':nom_cliente', $nombre_cliente);
		$sql->bindParam(':tel_laboral', $telefono_laboral);
		$sql->bindParam(':tel_particular', $telefono_particular);
		$sql->bindParam(':celular', $celular_particular);
		$sql->bindParam(':nro_operacion', $operacion);
		$sql->bindParam(':nro_cuota', $numero_cuota);
		$sql->bindParam(':producto', $producto);
		$sql->bindParam(':tip_credito', $tipo_credito);
		$sql->bindParam(':cod_moneda', $cod_moneda);
		$sql->bindParam(':tasa', $tasa);
		$sql->bindParam(':sal_capital', $saldo_capital);
		$sql->bindParam(':sal_interes', $saldo_interes);
		$sql->bindParam(':tot_cuota', $total_cuotas);
		$sql->bindParam(':mto_mora', $monto_mora);
		$sql->bindParam(':cotizacion', $cotizacion);
		$sql->bindParam(':sal_capital_gs', $saldo_capital_gs);
		$sql->bindParam(':sal_interes_gs', $saldo_interes_gs);
		$sql->bindParam(':tot_cuota_gs', $total_cuotas_gs);
		$sql->bindParam(':mto_mora_gs', $monto_mora_gs);
		$sql->bindParam(':dia_mora', $dias_mora);
		$sql->bindParam(':tot_nro_cuota', $total_nro_cuotas);
		$sql->bindParam(':can_cuo_pagadas', $cuotas_pagadas);
		$sql->bindParam(':can_cuo_pendientes', $cuotas_pendientes);
		$sql->bindParam(':fec_vencimiento', $fecha_vto_cuota);
		$sql->bindParam(':fec_ult_pago', $ult_fecha_pago);
		$sql->bindParam(':est_cuota', $estado);
		$sql->bindParam(':fec_apertura', $fecha_apertura);
		$sql->bindParam(':cod_gestor', $cod_gestor);
		$sql->bindParam(':fecha_insert', $selectDate);

		$exec = $sql->execute();
		//echo $sql;
		if ($exec) {
				$insertados = $insertados + 1;
		} else {
			$noinsertados = $noinsertados + 1;
			$data = [
				"cliente" => $contactData[1],
				"operacion" => $contactData[8],
				"mensaje" => $sql->errorInfo()
			];
		}
	}
	showResults($recorrido, $insertados, $noinsertados);

}

function getQueryLux(){
	$query = "INSERT INTO lux.base (sucursal, cliente, nombre, ruc, ci, codigo_interno, nro_documento, pago_nro, fecha, moneda, nro_cuota, vencimiento,
	importe_cuota, saldo_cuota, fecha_pago, importe_pago, intereses, cod_cobrador, cobrador_recibo, cod_cobrador_cliente, cobrador_cliente, cod_vendedor, 
	vendedor, cod_grupo_vendedor, grupo_vendedor, dias_atraso, nro_pagare, localidad, pagare_unico, nro_solicitud, barrio, direccion, telefono, cod_ubicacion,
	ubicacion, celular,created_by, tramo, fecha_insert)
	VALUES(:sucursal,:cliente,:nombre,:ruc,:ci,:codigo_interno,:nro_documento,:pago_nro,:fecha,:moneda,:nro_cuota,:vencimiento,:importe_cuota,:saldo_cuota,
	:fecha_pago,:importe_pago,:intereses,:cod_cobrador,:cobrador_recibo,:cod_cobrador_cliente,:cobrador_cliente,:cod_vendedor,:vendedor,:cod_grupo_vendedor,
	:grupo_vendedor,:dias_atraso,:nro_pagare,:localidad,:pagare_unico,:nro_solicitud,:barrio,:direccion,:telefono,:cod_ubicacion,:ubicacion,:celular,:created_by,
	coalesce((Select tramo from lux.tramo where ci = :cedula), ''), :fecha_insert);";
	return $query;	
}

function getQueryInterfisa(){
	$query = "INSERT INTO base_interfisa (cod_cliente, nro_documento, nombre_cliente, telefono_laboral, telefono_particular, celular_alternativo,
	celular_laboral, celular_particular, operacion, segmento, producto, tipo_credito_tarjeta, tipo_operacion, tasa, saldo_capital, saldo_interes,tipo_cambio,
	dias_mora, numero_cuota, total_cuotas, monto_cuota, monto_mora, cuotas_pagadas, cuotas_pendientes, fecha_vto_cuota, ult_fecha_pago, total_deuda, estado, 
	fecha_apertura,riesgo, fecha_insert, usu_insert)
	VALUES(:cod_cliente, :nro_documento, :nombre_cliente, :telefono_laboral, :telefono_particular, :celular_alternativo,
	:celular_laboral, :celular_particular, :operacion, :segmento, :producto, :tipo_credito_tarjeta, :tipo_operacion, :tasa, :saldo_capital, :saldo_interes,:tipo_cambio,
	:dias_mora, :numero_cuota, :total_cuotas, :monto_cuota, :monto_mora, :cuotas_pagadas, :cuotas_pendientes, :fecha_vto_cuota, :ult_fecha_pago, :total_deuda, :estado, 
	:fecha_apertura,:riesgo,:fecha_insert::timestamp, :usu_insert)";
	
	return $query;
}

function getQuerySudameris(){
	$query = "INSERT INTO sudameris.base (
		fecha, nrocuenta, cod_pais, tipo_doc, nrodocumento, nombrecuenta, idoperacion, segmento, producto, tipoproducto,
		moneda, tasa, saldocapital, saldointeres, diasmora, cantidadcuotas, cuotaspagadas, cuotaspendientes, importetotalvencido,
		fechavtomasantiguo, fechaultimopago, saldoriesgoop, fechavalorop, crcbanco, ejecutivocuenta, cuentaclientecash, empresapayroll,
		fechaultimocobro, montoultimocobro, fechanacimiento, estadocivil, separacionbienes, codpaiscony, tipodoccony,
		documentoconyuge, nombreconyuge, crcsistema, deudasistema, importeoriginal, fechavto, interesvencido, interes_suspendido, otroscargos, cuentascobro,
		vinvulo_op, codpaisvinc, tipodocvinc, documentocodeudor, nombrecodeudor, tarjetanro, formabloqueo, fechaultcierre, cargos_suspendidos,
		tramomora, modulo, codagente, fecha_insert, importecapitalvencido
	) VALUES (
		:fecha, :nrocuenta, :cod_pais, :tipo_doc, :nro_documento, :nombre_cuenta, :id_operacion, :segmento, :producto, :tipo_producto,
		:moneda, :tasa, :saldo_capital, :saldo_interes, :dias_mora, :cantidad_cuotas, :cuotas_pagadas, :cuotas_pendientes, :importe_total_vencido,
		:fechavtomasantiguo, :fechaultimopago, :saldoriesgoop, :fechavalorop, :crcbanco, :ejecutivocuenta, :cuentaclientecash, :empresapayroll,
		:fechaultimocobro, :montoultimocobro, :fechanacimiento, :estadocivil, :separacionbienes, :codpaiscony, :tipodocconyuge,
		:documentoconyuge, :nombreconyuge, :crcsistema, :deudasistema, :importeoriginal, :fechavto, :interesvencido, :interes_suspendido, :otroscargos, :cuentascobro,
		:vinvulo_op, :codpaisvinc, :tipodocvinc, :documentocodeudor, :nombrecodeudor, :tarjetanro, :formabloqueo, :fechaultcierre, :cargos_suspendidos,
		:tramomora, :modulo, :codagente, :fecha_insert, :importecapitalvencido
	);
	";
	
	return $query;
}

function getQueryVinanzas(){
	$query = "INSERT INTO vinanzas_cobranza.base(cod_persona, nro_documento, nom_completo, dir_principal, 
	barrio, ciudad, telefono1, telefono2, lug_trabajo, direccion_laboral, telefono_laboral, 
	mayor_atr_cli_inicial, tramo_cli_inicial, cod_equipo, moneda, nro_operacion, nro_ope_original, 
	origen_operacion, tipo_operacion, dia_atr_ope_actual, tramo_cuenta, segmentacion, cod_modalidad, modalidad, 
	mto_deu_total_actual, mto_deu_vencida_inicial, mto_deu_vencida_actual, mto_deu_vencer_inicial, 
	mto_deu_vencer_actual, mto_cobrado, mto_cobrado_neto, judicial, cartera_ste, fec_ult_pago, 
	modalidad_original, nro_doc_librador, nom_librador, nro_cuota, fec_vencimiento, fec_ult_pago_1, sal_capital, 
	sal_interes, mto_int_mora, mto_int_punitorio, mto_gas_cobranzas, mto_impuesto, fecha_insert)
	VALUES(:cod_persona, :nro_documento, :nom_completo, :dir_principal, :barrio, :ciudad, :telefono1, :telefono2, 
	:lug_trabajo, :direccion_laboral, :telefono_laboral, :mayor_atr_cli_inicial, :tramo_cli_inicial, :cod_equipo, 
	:moneda, :nro_operacion, :nro_ope_original, :origen_operacion, :tipo_operacion, :dia_atr_ope_actual, :tramo_cuenta, 
	:segmentacion, :cod_modalidad, :modalidad, :mto_deu_total_actual, :mto_deu_vencida_inicial, :mto_deu_vencida_actual, 
	:mto_deu_vencer_inicial, :mto_deu_vencer_actual, :mto_cobrado, :mto_cobrado_neto, :judicial, :cartera_ste, :fec_ult_pago, 
	:modalidad_original, :nro_doc_librador, :nom_librador, :nro_cuota, :fec_vencimiento, :fec_ult_pago_1, :sal_capital, 
	:sal_interes, :mto_int_mora, :mto_int_punitorio, :mto_gas_cobranzas, :mto_impuesto, :fecha_insert)";
	
	return $query;
}

function getQueryBancop(){
	$query = 'INSERT INTO bancop.base_prestamo (fec_proceso, cod_cliente, nro_documento, nom_cliente, tel_laboral, tel_particular, celular,
	nro_operacion, nro_cuota, producto, tip_credito, cod_moneda, tasa, sal_capital, sal_interes, tot_cuota, mto_mora, cotizacion, 
	sal_capital_gs, sal_interes_gs, tot_cuota_gs, mto_mora_gs, dia_mora, tot_nro_cuotas, can_cuo_pagadas, can_cuo_pendientes, fec_vencimiento, 
	fec_ult_pago, est_cuota, fec_apertura, cod_gestor,fecha_insert)
	VALUES(:fec_proceso, :cod_cliente, :nro_documento, :nom_cliente, :tel_laboral, :tel_particular, :celular,
	:nro_operacion, :nro_cuota, :producto, :tip_credito, :cod_moneda, :tasa, :sal_capital, :sal_interes, :tot_cuota, :mto_mora, :cotizacion,
	:sal_capital_gs, :sal_interes_gs, :tot_cuota_gs, :mto_mora_gs, :dia_mora, :tot_nro_cuota, :can_cuo_pagadas, :can_cuo_pendientes,
	:fec_vencimiento, :fec_ult_pago, :est_cuota, :fec_apertura, :cod_gestor, :fecha_insert::timestamp)';
	
	return $query;
}

function convertirNumeroCientifico($numero_cientifico) {
    // Usamos la función sprintf() para formatear el número
    // con un formato específico que no sea científico
    return sprintf('%f', $numero_cientifico);
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
