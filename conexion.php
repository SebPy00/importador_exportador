<?php
//Nos conectamos con PostgreSQL produccion
	$conexion = pg_connect("host=10.19.150.101 dbname=sigesa user=postgres password=Sistema2175128")
    // $conexion = pg_connect("host=10.19.150.24 dbname=sigesa user=postgres password=2032893")
    or die('No se ha podido conectar: ' . pg_last_error());
	
/*try {
   $conexion = new PDO("mysql:host=$host;dbname=itic", $usuario, $contraseña);
   $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $conexion->exec("set names utf8");
    return$conexion;
    }
catch(PDOException $error)
    {
    echo "No se pudo conectar a la BD: " . $error->getMessage();
    }*/
?>