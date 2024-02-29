<?php

# Nuestra base de datos
require_once "bdpostgres.php";
ini_set('memory_limit', '2048M');

require 'vendor/autoload.php';

// use PDO;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

# Obtener base de datos
# Escribir encabezado de los productos
// $encabezado = ["Código de barras", "Descripción", "Precio de compra", "Precio de venta", "Existencia"];

$bd = obtenerBD();
$selectCliente = $_REQUEST['exportCliente'];
$selectTipo = $_REQUEST['exportTipo'];
$fecha_insert = $_REQUEST['exportDate'];
 
$spreadsheet = new Spreadsheet();

$spreadsheet
    ->getProperties()
    ->setCreator("Seb")
    ->setLastModifiedBy('Seb')
    ->setTitle('Excel creado con PhpSpreadSheet')
    ->setSubject('Excel para exportar registros de clientes de servicios')
    ->setDescription('Excel generado')
    ->setKeywords('PHPSpreadsheet')
    ->setCategory('Servicios');

$hoja = $spreadsheet->getActiveSheet();
$hoja->setTitle('Archivo Export');
switch($selectCliente){
    case 'lux':
        $encabezado = getHeaderLux();
        $consulta = getQueryLux();
        break;
    case 'interfisa':
        $encabezado = getHeaderIntefisa();
        $consulta = getQueryInterfisa();
        break;
    case 'vinanzas':
        $encabezado = getHeaderVinanzas();
        $consulta = getQueryVinanzas();
        break;
    case 'bancop':
        $encabezado = getHeaderBancop();
        $consulta = getQueryBancop();
        break;    
    default:
		echo "Cliente no soportado";
		break;
}   

# El último argumento es por defecto A1 pero lo pongo para que se explique mejor
$hoja->fromArray($encabezado, null, 'A1');

$sentencia = $bd->prepare($consulta, [
    PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL,
]);

$sentencia->execute();

// echo var_dump($sentencia->fetchObject());
# Comenzamos en la 2 porque la 1 es del encabezado
$numeroDeFila = 2;
if($selectCliente == 'interfisa' and $selectTipo == 'cuentas'){
    while ($reg = $sentencia->fetchObject()) {
        $datos = array(
            'cod_cliente' => $reg->cod_cliente,
            'nro_documento' => $reg->nro_documento,
            'nombre_cliente' => $reg->nombre_cliente,
            'telefono_laboral' => $reg->telefono_laboral,
            'telefono_particular' => $reg->telefono_particular,
            'celular_alternativo' => $reg->celular_alternativo,
            'celular_laboral' => $reg->celular_laboral,
            'celular_particular' => $reg->celular_particular,
            'operacion' => $reg->operacion,
            'segmento' => $reg->segmento,
            'producto' => $reg->producto,
            'tipo_credito_tarjeta' => $reg->tipo_credito_tarjeta,
            'tipo_operacion' => $reg->tipo_operacion,
            'tasa' => $reg->tasa,
            'saldo_capital' => $reg->saldo_capital,
            'saldo_interes' => $reg->saldo_interes,
            'tipo_cambio' => $reg->tipo_cambio,
            'dias_mora' => $reg->dias_mora,
            'numero_cuota' => $reg->numero_cuota,
            'total_cuotas' => $reg->total_cuotas,
            'monto_cuota' => $reg->monto_cuota,
            'monto_mora' => $reg->monto_mora,
            'cuotas_pagadas' => $reg->cuotas_pagadas,
            'cuotas_pendientes' => $reg->cuotas_pendientes,
            'fecha_vto_cuota' => $reg->fecha_vto_cuota,
            'ult_fecha_pago' => $reg->ult_fecha_pago,
            'total_deuda' => $reg->total_deuda,
            'estado' => $reg->estado,
            'fecha_apertura' => $reg->fecha_apertura,
            'riesgo' => $reg->riesgo,
            'prioridad' => $reg->prioridad,
            'codigo_agente' => $reg->codigo_agente
        );
        # Escribirlos en el documento  
        // Número de fila actual
        $columnIndex = 1;
        // Bucle para recorrer el array y asignar valores a las celdas
        foreach ($datos as $columna => &$valor) {
            if (is_numeric($valor) && strpos($valor, 'E') !== false) {
                // Convertir el número científico a cadena de caracteres
                $valor = sprintf('%f', $valor);
            }
            $hoja->setCellValueByColumnAndRow($columnIndex, $numeroDeFila, $valor);
            $columnIndex++;
        }
        unset($valor); // Liberar la referencia al último elemento del array
        $numeroDeFila++;
    }
}else if($selectCliente == 'interfisa' and $selectTipo == 'pagos'){
    
    while ($reg = $sentencia->fetchObject()) {
        $datos = array(
            'cod_cliente' => $reg->cod_cliente,
            'cod_cliente_det3' => $reg->cod_cliente_det3,
            'cliente_det3' => $reg->cliente_det3,
            'operacion_det3' => $reg->operacion_det3,
            'estado_det3' => $reg->estado_det3,
            'tramos_det3' => $reg->tramos_det3,
            'cartera_det3' => $reg->cartera_det3,
            'saldo_det3' => $reg->saldo_det3,
            'fecha_pago_det3' => $reg->fecha_pago_det3,
            'monto_pagado_det3' => $reg->monto_pagado_det3,
            'numero_cuota_det3' => $reg->numero_cuota_det3,
            'tipo_operacion_det3' => $reg->tipo_operacion_det3,
            'nro_documento_det3' => $reg->nro_documento_det3,
            'producto_det3' => $reg->producto_det3,
            'tipo_cambio_det3' => $reg->tipo_cambio_det3
        );
        
        # Escribirlos en el documento  
        // Número de fila actual
        $columnIndex = 1;
        // Bucle para recorrer el array y asignar valores a las celdas
        foreach ($datos as $columna => $valor) {
            $hoja->setCellValueByColumnAndRow($columnIndex, $numeroDeFila, $valor);
            $columnIndex++;
        }
        $numeroDeFila++;
    }

}else if($selectCliente == 'lux' and $selectTipo == 'cuentas'){
    while ($reg = $sentencia->fetchObject()) {
        # Obtener los datos de la base de datos
        $datos = array(
            'operacion' => $reg->operacion,
            'ci' => $reg->ci,
            'gestor' => $reg->gestor,
            'cuenta' => $reg->cuenta,
            'cliente' => $reg->cliente,
            'telef' => $reg->telef,
            'celular' => $reg->celular,
            'laboral' => $reg->laboral,
            'dir' => $reg->dir,
            'telaboral' => $reg->telaboral,
            'referencias_personales' => $reg->referencias_personales,
            'fecha_venc' => $reg->fecha_venc,
            'diastraso' => $reg->diastraso,
            'nro_cuota' => $reg->nro_cuota,
            'saldo_cuota' => $reg->saldo_cuota,
            'mora' => $reg->mora,
            'punitorio' => $reg->punitorio,
            'gastos' => $reg->gastos,
            'iva' => $reg->iva,
            'montomora' => $reg->montomora,
            'g1nroci' => $reg->g1nroci,
            'nom_vendedor' => $reg->nom_vendedor,
            'estado' => $reg->estado,
            'sucursal' => $reg->sucursal,
            'ruc' => $reg->ruc,
            'nro_documento' => $reg->nro_documento,
            'pago_nro' => $reg->pago_nro,
            'fecha' => $reg->fecha,
            'moneda' => $reg->moneda,
            'cod_cobrador' => $reg->cod_cobrador,
            'cobrador_recibo' => $reg->cobrador_recibo,
            'cod_vendedor' => $reg->cod_vendedor,
            'vendedor' => $reg->vendedor,
            'cod_grupo_vendedor' => $reg->cod_grupo_vendedor,
            'grupo_vendedor' => $reg->grupo_vendedor,
            'nro_pagare' => $reg->nro_pagare,
            'localidad' => $reg->localidad,
            'pagare_unico' => $reg->pagare_unico,
            'nro_solicitud' => $reg->nro_solicitud,
            'barrio' => $reg->barrio,
            'cod_ubicacion' => $reg->cod_ubicacion,
            'ubicacion' => $reg->ubicacion,
            'importe_cuota' => $reg->importe_cuota
        );
        # Escribirlos en el documento
        $columnIndex = 1;
        foreach ($datos as $columna => $valor) {
            $hoja->setCellValueByColumnAndRow($columnIndex, $numeroDeFila, $valor);
            $columnIndex++;
        }
        $numeroDeFila++;
    }
}else if($selectCliente == 'lux' and $selectTipo == 'pagos'){
    //Agregar para esta logica.
}else if($selectCliente == 'vinanzas' and $selectTipo == 'cuentas'){
    while ($reg = $sentencia->fetchObject()) {
        $datos = array(
            'COD_PERSONA' => $reg->cod_persona,
            'NRO_DOCUMENTO' => $reg->nro_documento,
            'NOM_COMPLETO' => $reg->nom_completo,
            'DIR_PRINCIPAL' => $reg->dir_principal,
            'BARRIO' => $reg->barrio,
            'CIUDAD' => $reg->ciudad,
            'TELEFONO1' => $reg->telefono1,
            'TELEFONO2' => $reg->telefono2,
            'LUG_TRABAJO' => $reg->lug_trabajo,
            'DIRECCION_LABORAL' => $reg->direccion_laboral,
            'TELEFONO_LABORAL' => $reg->telefono_laboral,
            'MAYOR_ATR_CLI_INICIAL' => $reg->mayor_atr_cli_inicial,
            'TRAMO_CLI_INICIAL' => $reg->tramo_cli_inicial,
            'COD_EQUIPO' => $reg->cod_equipo,
            'MONEDA' => $reg->moneda,
            'NRO_OPERACION' => $reg->nro_operacion,
            'NRO_OPE_ORIGINAL' => $reg->nro_ope_original,
            'ORIGEN_OPERACION' => $reg->origen_operacion,
            'TIPO_OPERACION' => $reg->tipo_operacion,
            'DIA_ATR_OPE_ACTUAL' => $reg->dia_atr_ope_actual,
            'TRAMO_CUENTA' => $reg->tramo_cuenta,
            'SEGMENTACION' => $reg->segmentacion,
            'COD_MODALIDAD' => $reg->cod_modalidad,
            'MODALIDAD' => $reg->modalidad,
            'MTO_DEU_TOTAL_ACTUAL' => $reg->mto_deu_total_actual,
            'MTO_DEU_VENCIDA_INICIAL' => $reg->mto_deu_vencida_inicial,
            'MTO_DEU_VENCIDA_ACTUAL' => $reg->mto_deu_vencida_actual,
            'MTO_DEU_VENCER_INICIAL' => $reg->mto_deu_vencer_inicial,
            'MTO_DEU_VENCER_ACTUAL' => $reg->mto_deu_vencer_actual,
            'MTO_COBRADO' => $reg->mto_cobrado,
            'MTO_COBRADO_NETO' => $reg->mto_cobrado_neto,
            'JUDICIAL' => $reg->judicial,
            'CARTERA_STE' => $reg->cartera_ste,
            'FEC_ULT_PAGO_OPERACION' => $reg->fec_ult_pago,
            'MODALIDAD_ORIGINAL' => $reg->modalidad_original,
            'NRO_DOC_LIBRADOR' => $reg->nro_doc_librador,
            'NOM_LIBRADOR' => $reg->nom_librador,
            'NRO_CUOTA' => $reg->nro_cuota,
            'FEC_VENCIMIENTO' => $reg->fec_vencimiento,
            'FEC_ULT_PAGO' => $reg->fec_ult_pago_1,
            'SAL_CAPITAL' => $reg->sal_capital,
            'SAL_INTERES' => $reg->sal_interes,
            'MTO_INT_MORA' => $reg->mto_int_mora,
            'MTO_INT_PUNITORIO' => $reg->mto_int_punitorio,
            'MTO_GAS_COBRANZAS' => $reg->mto_gas_cobranzas,
            'MTO_IMPUESTO' => $reg->mto_impuesto,
            'COD_GESTOR' => '',
        );
        $columnIndex = 1;
        // Bucle para recorrer el array y asignar valores a las celdas
        foreach ($datos as $columna => $valor) {
            $hoja->setCellValueByColumnAndRow($columnIndex, $numeroDeFila, $valor);
            $columnIndex++;
        }
        $numeroDeFila++;
    }
}else if($selectCliente == 'vinanzas' and $selectTipo == 'pagos'){
    while ($reg = $sentencia->fetchObject()) {
        $datos = array(
            "Nro Documento" => $reg->nro_documento,
            "Tipo Cambio DET3" => $reg->tipo_cambio_det3,
            "Producto DET3" => $reg->producto_det3,
            "Nro Documento DET3" => $reg->nro_documento_det3,
            "Tipo Operacion DET3" => $reg->tipo_operacion_det3,
            "Numero Cuota DET3" => $reg->numero_cuota_det3,
            "Monto Pagado DET3" => $reg->monto_pagado_det3,
            "Fecha Pago DET3" => $reg->fecha_pago_det3,
            "Saldo DET3" => $reg->saldo_det3,
            "Cartera DET3" => $reg->cartera_det3,
            "Tramo DET3" => $reg->tramo_det3,
            "Estado DET3" => $reg->estado_det3,
            "Operacion DET3" => $reg->operacion_det3,
            "Cliente DET3" => $reg->cliente_det3,
            "Cod Cliente DET3" => $reg->cod_cliente_det3,
            "NRO_OPE_ORIGINAL" => $reg->nro_ope_original,
            "ES_REFINANCIADO" => $reg->es_refinanciado,
            "ORIGEN_OPERACION" => $reg->origen_operacion,
            "COD_EQUIPO" => $reg->cod_equipo
        );
        
        // Número de fila actual
        $columnIndex = 1;
        // Bucle para recorrer el array y asignar valores a las celdas
        foreach ($datos as $columna => $valor) {
            $hoja->setCellValueByColumnAndRow($columnIndex, $numeroDeFila, $valor);
            $columnIndex++;
        }
        $numeroDeFila++;
    }
}


$nueva_fecha = date("dmY", strtotime($fecha_insert));

$filename = strtoupper($selectCliente).$selectTipo."Exportado".$nueva_fecha;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename . '.csv');
$writer = new Csv($spreadsheet);
$writer->setDelimiter(';'); //SELECCIONA EL DELIMITADOR
$writer->setEnclosure(''); //QUITAR COMILLAS DOBLES	
$writer->save('php://output');
// return $filename;
exit;

function getHeaderLux(){
    $encabezado = [
        "operacion", "ci", "gestor", "cuenta", "cliente", "telef", "celular", "laboral", "dir", "telaboral", "referencias_personales",
        "fecha_venc", "diastraso", "nro_cuota", "saldo_cuota", "mora", "punitorio", "gastos", "iva", "montomora", "g1nroci", "nom_vendedor", "estado",
        "sucursal", "ruc", "nro_documento", "pago_nro", "fecha", "moneda", "cod_cobrador", "cobrador_recibo", "cod_vendedor", "vendedor", "cod_grupo_vendedor",
        "grupo_vendedor", "nro_pagare", "localidad", "pagare_unico", "nro_solicitud", "barrio", "cod_ubicacion", "ubicacion", "importe_cuota",
    
    ];

    return $encabezado;
}

function getHeaderVinanzas(){
    global $selectTipo;
    if($selectTipo == 'cuentas'){
        $encabezado = [
            "COD_PERSONA", "NRO_DOCUMENTO", "NOM_COMPLETO", "DIR_PRINCIPAL", "BARRIO", "CIUDAD", "TELEFONO1", 
            "TELEFONO2", "LUG_TRABAJO", "DIRECCION_LABORAL", "TELEFONO_LABORAL", "MAYOR_ATR_CLI_INICIAL", 
            "TRAMO_CLI_INICIAL", "COD_EQUIPO", "MONEDA", "NRO_OPERACION", "NRO_OPE_ORIGINAL", "ORIGEN_OPERACION", 
            "TIPO_OPERACION", "DIA_ATR_OPE_ACTUAL", "TRAMO_CUENTA", "SEGMENTACION", "COD_MODALIDAD", "MODALIDAD", 
            "MTO_DEU_TOTAL_ACTUAL", "MTO_DEU_VENCIDA_INICIAL", "MTO_DEU_VENCIDA_ACTUAL", 
            "MTO_DEU_VENCER_INICIAL", "MTO_DEU_VENCER_ACTUAL", "MTO_COBRADO", "MTO_COBRADO_NETO", "JUDICIAL", 
            "CARTERA_STE", "FEC_ULT_PAGO_OPERACION", "MODALIDAD_ORIGINAL", "NRO_DOC_LIBRADOR", "NOM_LIBRADOR", 
            "NRO_CUOTA", "FEC_VENCIMIENTO", "FEC_ULT_PAGO", "SAL_CAPITAL", "SAL_INTERES", "MTO_INT_MORA", 
            "MTO_INT_PUNITORIO", "MTO_GAS_COBRANZAS", "MTO_IMPUESTO", "COD_GESTOR",
        ];
    }else if($selectTipo == 'pagos'){
        $encabezado = [
            "Nro Documento", "Tipo Cambio DET3", "Producto DET3", "Nro Documento DET3", "Tipo Operacion DET3", 
            "Numero Cuota DET3", "Monto Pagado DET3", "Fecha Pago DET3", "Saldo DET3", "Cartera DET3", 
            "Tramo DET3", "Estado DET3", "Operacion DET3", "Cliente DET3", "Cod Cliente DET3", 
            "NRO_OPE_ORIGINAL", "ES_REFINANCIADO", "ORIGEN_OPERACION", "COD_EQUIPO",
        ];
    }
    return $encabezado;

}

function getHeaderIntefisa(){
    global $selectTipo;
    if($selectTipo == 'cuentas'){
        $encabezado = [
            "cod_cliente", "nro_documento", "nombre_cliente", "telefono_laboral", "telefono_particular", 
            "celular_alternativo", "celular_laboral", "celular_particular", "operacion", "segmento",
            "producto", "tipo_credito_tarjeta", "tipo_operacion", "tasa", "saldo_capital", "saldo_interes",
            "tipo_cambio", "dias_mora", "numero_cuota", "total_cuotas", "monto_cuota", "monto_mora", 
            "cuotas_pagadas", "cuotas_pendientes", "fecha_vto_cuota", "ult_fecha_pago", "total_deuda", "estado",
            "fecha_apertura", "riesgo", "prioridad", "cod_agente", 
        ];
    }else if($selectTipo == 'pagos'){
        $encabezado = [
            "Cod Cliente", "Cod Cliente DET3", "Cliente DET3", "Operacion DET3", "Estado DET3", 
            "Tramo DET3", "Cartera DET3", "Saldo DET3", "Fecha Pago DET3", "Monto Pagado DET3",
            "Numero Cuota DET3", "Tipo Operacion DET3", "Nro Documento DET3", "Producto DET3", 
            "Tipo Cambio DET3",
        ];
    }
    return $encabezado;
}

function getHeaderBancop(){
    global $selectTipo;
    if($selectTipo == 'cuentas'){
        $encabezado = [
            "cod_cliente", "nro_documento", "nombre_cliente", "telefono_laboral", "telefono_particular", 
            "celular_alternativo", "celular_laboral", "celular_particular", "operacion", "segmento",
            "producto", "tipo_credito_tarjeta", "tipo_operacion", "tasa", "saldo_capital", "saldo_interes",
            "tipo_cambio", "dias_mora", "numero_cuota", "total_cuotas", "monto_cuota", "monto_mora", 
            "cuotas_pagadas", "cuotas_pendientes", "fecha_vto_cuota", "ult_fecha_pago", "total_deuda", "estado",
            "fecha_apertura", "riesgo", "prioridad", "cod_agente", 
        ];
    }else if($selectTipo == 'pagos'){
        $encabezado = [
            "Cod Cliente", "Cod Cliente DET3", "Cliente DET3", "Operacion DET3", "Estado DET3", 
            "Tramo DET3", "Cartera DET3", "Saldo DET3", "Fecha Pago DET3", "Monto Pagado DET3",
            "Numero Cuota DET3", "Tipo Operacion DET3", "Nro Documento DET3", "Producto DET3", 
            "Tipo Cambio DET3",
        ];
    }
    return $encabezado;
}

function getQueryLux(){
    global $fecha_insert;

    $query = "SELECT codigo_interno as operacion, ci ,  '' as gestor,  cliente as cuenta,  nombre as cliente, 
        telefono as telef,  celular as celular, '' as laboral,  direccion as dir, tramo as telaboral, 
        '' as referencias_personales,  vencimiento as fecha_venc, dias_atraso as diastraso,  nro_cuota,  
        saldo_cuota, 
        (select intereses as mora  from lux.base l where l.nro_cuota  = bl.nro_cuota and l.ci = bl.ci 
        AND l.codigo_interno = bl.codigo_interno order by created_at DESC, fecha_pago desc limit 1) 
        AS mora,  
        '' as punitorio,  '' as gastos,  '' as iva,  
        (saldo_cuota + coalesce((select intereses from lux.base ll where ll.nro_cuota  = bl.nro_cuota 
        and ll.ci = bl.ci AND ll.codigo_interno = bl.codigo_interno 
        order by created_at desc, fecha_pago desc limit 1),0)) as montoMora, 
        cod_cobrador_cliente  as g1NroCI,  cobrador_cliente as nom_vendedor, '' as estado, sucursal,  
        ruc,  nro_documento,  pago_nro,  fecha,  moneda, cod_cobrador,  cobrador_recibo,  cod_vendedor,  
        vendedor,  cod_grupo_vendedor, grupo_vendedor,  nro_pagare,  localidad,  pagare_unico,  
        nro_solicitud,  barrio,  cod_ubicacion, ubicacion, importe_cuota  
        from lux.base   bl where saldo_cuota  <> 0 AND to_char( created_at,'yyyy-mm-dd')= '$fecha_insert'";

    return $query;
}

function getQueryBancop(){
    global $fecha_insert, $selectTipo;
    if($selectTipo == 'cuentas'){
        $query = "SELECT bi.cod_cliente,bi.nro_documento,bi.nombre_cliente,bi.telefono_laboral,	
        bi.telefono_particular,bi.celular_alternativo,bi.celular_laboral,bi.celular_particular,
        bi.operacion,segmento, bi.producto,bi.tipo_credito_tarjeta,	bi.tipo_operacion, bi.tasa,
        bi.saldo_capital, bi.saldo_interes,bi.tipo_cambio,bi.dias_mora, bi.numero_cuota,
        bi.total_cuotas  ,bi.monto_cuota,bi.monto_mora, bi.cuotas_pagadas,bi.cuotas_pendientes,
        bi.fecha_vto_cuota::date, bi.ult_fecha_pago::date, bi.total_deuda, bi.estado,bi.fecha_apertura::date, 
        bi.riesgo, 
        coalesce((select prioridad from prioridades_interfisa pi where trim(pi.ci) = trim(bi.nro_documento) limit 1), '6')as prioridad,
        coalesce((select agente from asignaciones_interfisa ai where trim(ai.ci) = trim(bi.nro_documento) limit 1), '1712')as codigo_agente
        FROM base_interfisa bi WHERE create_at::date = '$fecha_insert'";

    }else if($selectTipo == 'pagos'){
        $query = "select cod_cliente, cod_cliente as cod_cliente_det3, nombre_cliente as cliente_det3, 
        operacion as operacion_det3, estado as estado_det3, tramo as tramos_det3, cartera as cartera_det3, 
        saldo as saldo_det3, fecha_pago as fecha_pago_det3, monto_pagado as monto_pagado_det3,
        numero_cuota as numero_cuota_det3, tipo_operacion as tipo_operacion_det3, nro_documento as nro_documento_det3,
        producto as producto_det3, tipo_cambio as tipo_cambio_det3 
        from pagos_servicios where fecha_insert::date = '$fecha_insert'";
    
    }
    return $query;
}

function getQueryInterfisa(){
    global $fecha_insert, $selectTipo;
    if($selectTipo == 'cuentas'){
        $query = "SELECT	bi.cod_cliente,bi.nro_documento,bi.nombre_cliente,bi.telefono_laboral,
        bi.telefono_particular,bi.celular_alternativo,bi.celular_laboral,bi.celular_particular,
        bi.operacion,segmento, bi.producto,bi.tipo_credito_tarjeta,	bi.tipo_operacion, bi.tasa,
        bi.saldo_capital, bi.saldo_interes,bi.tipo_cambio,bi.dias_mora, bi.numero_cuota,
        bi.total_cuotas  ,bi.monto_cuota,bi.monto_mora, bi.cuotas_pagadas,bi.cuotas_pendientes,
        bi.fecha_vto_cuota::date, bi.ult_fecha_pago::date, bi.total_deuda, bi.estado,bi.fecha_apertura::date, 
        bi.riesgo, 
        coalesce((select prioridad from prioridades_interfisa pi where trim(pi.ci) = trim(bi.nro_documento) limit 1), '6')as prioridad,
        coalesce((select agente from asignaciones_interfisa ai where trim(ai.ci) = trim(bi.nro_documento) 
				  and agente in (select usu from dblink(
        		'dbname=sigesa port=5432 host=10.19.150.101 user=postgres password=Sistema2175128',
        		'SELECT usu_externo FROM sysusu where grupo = 4 and sit = 1 '
    		) AS s(usu varchar) )
				  limit 1), '1712')as codigo_agente
        FROM base_interfisa bi WHERE fecha_insert::date = '$fecha_insert'";

    }else if($selectTipo == 'pagos'){
        $query = "select cod_cliente, cod_cliente as cod_cliente_det3, nombre_cliente as cliente_det3, 
        operacion as operacion_det3, estado as estado_det3, tramo as tramos_det3, cartera as cartera_det3, 
        saldo as saldo_det3, fecha_pago as fecha_pago_det3, monto_pagado as monto_pagado_det3,
        numero_cuota as numero_cuota_det3, tipo_operacion as tipo_operacion_det3, nro_documento as nro_documento_det3,
        producto as producto_det3, tipo_cambio as tipo_cambio_det3 
        from pagos_servicios where fecha_insert::date = '$fecha_insert'";
    
    }
    return $query;
}

function getQueryVinanzas(){
    global $fecha_insert, $selectTipo;
    if($selectTipo == 'cuentas'){
        $query = "SELECT cod_persona, nro_documento, nom_completo, dir_principal, barrio, ciudad, telefono1, 
        telefono2, lug_trabajo, direccion_laboral, telefono_laboral, mayor_atr_cli_inicial, tramo_cli_inicial, 
        cod_equipo, moneda, nro_operacion, nro_ope_original, origen_operacion, tipo_operacion, dia_atr_ope_actual, 
        tramo_cuenta, segmentacion, cod_modalidad, modalidad, mto_deu_total_actual, mto_deu_vencida_inicial, 
        mto_deu_vencida_actual, mto_deu_vencer_inicial, mto_deu_vencer_actual, mto_cobrado, mto_cobrado_neto, 
        judicial, cartera_ste, TO_CHAR(fec_ult_pago::date, 'DD-MM-YYYY') AS fec_ult_pago, modalidad_original, 
        nro_doc_librador, nom_librador, nro_cuota, TO_CHAR(fec_vencimiento::date, 'DD-MM-YYYY') AS fec_vencimiento, 
        TO_CHAR(fec_ult_pago_1::date, 'DD-MM-YYYY') AS fec_ult_pago_1, sal_capital, sal_interes, mto_int_mora, 
        mto_int_punitorio, mto_gas_cobranzas, mto_impuesto
        FROM vinanzas_cobranza.base 
        WHERE fecha_insert::date = '$fecha_insert'";

    }else if($selectTipo == 'pagos'){
        $query = "SELECT nro_documento, '' as tipo_cambio_det3, '' as producto_det3, '' as nro_documento_det3,
        '' as tipo_operacion_det3, numero_cuota as numero_cuota_det3, monto_pagado as monto_pagado_det3, 
        fecha_pago as fecha_pago_det3, '' as saldo_det3, '' as cartera_det3, '' as tramo_det3, '' as estado_det3,
        nro_operacion as operacion_det3, '' as cliente_det3, cod_persona as cod_cliente_det3, nro_ope_original,
        es_refinanciado, origen_operacion, cod_equipo
        FROM vinanzas_cobranza.pagos
        WHERE fecha_insert::date = '$fecha_insert'";
    
    }
    return $query;
}