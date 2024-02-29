<?php
include('conexion.php');

echo 'se va a ejecutar';
$consulexisteope = pg_query($conexion, "SELECT * from opemov where ope= 117042 order by nro");

$numrows = pg_num_rows($consulexisteope);
echo $numrows;
//SQLEXEC(s_bd,"select * from opemov where ope=?v_ope order by nro","c_opemov")''

$v_sal = 0;
$v_vto = Date('d-m-Y');

//v_sal=0
//v_vto=thisform.v_fec.Value

//SELECT c_opemov

// DO WHILE EOF()=.f.
while ($regg = pg_fetch_object($consulexisteope)) {

	$v_nro = $regg->nro;
	$v_vto = $regg->vto;
	echo "l";
	// DO WHILE nro=v_nro AND EOF()=.f.
	while ($reg = pg_fetch_object($consulexisteope)) {

		if ($reg->col == 1) {
			$v_sal = $v_sal + $reg->imp; //$contactData[5];

			echo "\nvalor sumado de v_sal es" . $v_sal;
		} else {
			$v_sal = $v_sal - $reg->imp; //$contactData[5];
			echo "\n \tvalor restado de v_sal es" . $v_sal;
		}

		// SKIP
		continue;
	}
	if ($v_sal > 0) {
		// EXIT;
		echo "v_sal es ".$v_sal;
		echo "\nsaldo es mayor a cero";
		exit;
	}
}

echo $v_nro;
// DO WHILE EOF()=.f.
// 	v_nro=nro
// 	v_vto=vto
// 	DO WHILE nro=v_nro AND EOF()=.f.
	
// 		IF col=1
// 			v_sal=v_sal+imp
// 		ELSE
// 			v_sal=v_sal-imp
// 		endif
	
// 		SKIP
// 	ENDDO
// 	IF v_sal>0
// 		EXIT
		
// 	endif
// enddo



// insertar registros
// foreach ($contactList as $contactData) {
// 	$consulexisteope = pg_query($conexion, "SELECT * from ope where ope = {$contactData[5]}  ");
// 	$existeope = pg_num_rows($consulexisteope);

// 	if ($existeope == 0) {

// 		$cantnoexisteope = $cantnoexisteope + 1;
// 		$openoexiste = $contactData[5];
		

// 	} else {

// 		$cantexisteope = $cantexisteope+1;

// 		$consulta = pg_query($conexion, "SELECT * from juicios where ope = {$contactData[5]}  ");
// 		$existe = pg_num_rows($consulta);

// 		if ($existe == 0) {
// 			$query = pg_query($conexion, "INSERT INTO juicios (fecha_inicio_juicio,fecha_escrito,fecha_carga,titular,nroci,ope,cod_abogado,ruta,id_estado) 
// 		values ('{$contactData[0]}','{$contactData[1]}','{$contactData[2]}','{$contactData[3]}','{$contactData[4]}',{$contactData[5]},{$contactData[6]}
// 		,'{$contactData[7]}',{$contactData[8]}) ");

// 			$insertados = $insertados + 1;
// 		} else {

// 			// $query = pg_query($conexion, "UPDATE juicios SET fecha_inicio_juicio = '{$contactData[0]}',fecha_escrito = '{$contactData[1]}',fecha_carga = '{$contactData[2]}',titular='{$contactData[3]}'

// 			$query = pg_query($conexion, "UPDATE juicios SET fecha_inicio_juicio = '{$contactData[0]}',fecha_escrito = '{$contactData[1]}',titular='{$contactData[3]}'
// 		,nroci='{$contactData[4]}',cod_abogado= {$contactData[6]},ruta= '{$contactData[7]}',id_estado= {$contactData[8]},fecha_ult_actualizacion = 'now()' WHERE ope = {$contactData[5]} ");

// 			$update = $update + 1;
// 		}
// 	}
// }
