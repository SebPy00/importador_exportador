<?php

function obtenerBD(){

    $server="10.19.150.101";
    $nombre_base_de_datos = "sigesa";
    $usuario = "postgres";
    $contraseña = "Sistema2175128";
    try {

        $bd = new PDO('pgsql:host='.$server.';dbname=' . $nombre_base_de_datos, $usuario, $contraseña);
        // $base_de_datos->query("set names utf8;");
        // $base_de_datos->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // $base_de_datos->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        // echo 'exito';
        return $bd;

      
    } catch (Exception $e) {
        echo "error";
        # Nota: ¡en la vida real no imprimas errores!
        exit("Error obteniendo BD: " . $e->getMessage());
        // return null;
    }
 }
