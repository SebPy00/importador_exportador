<?php

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />


    <title>Document</title>
</head>

<body>
    <?php
    /**
     * Ejemplo de cómo usar PDO y PHPSpreadSheet para
     * importar datos de Excel a MySQL de manera
     * fácil, rápida y segura
     *
     * @author parzibyte
     * @see https://parzibyte.me/blog/2019/02/14/leer-archivo-excel-php-phpspreadsheet/
     * @see https://parzibyte.me/blog/2018/02/12/mysql-php-pdo-crud/
     * @see https://parzibyte.me/blog/2019/02/16/php-pdo-parte-2-iterar-cursor-comprobar-si-elemento-existe/
     * @see https://parzibyte.me/blog/2018/11/08/crear-archivo-excel-php-phpspreadsheet/
     * @see https://parzibyte.me/blog/2018/10/11/sintaxis-corta-array-php/
     *
     */

    # Cargar clases instaladas por Composer
    require_once "vendor/autoload.php";

    # Nuestra base de datos
    require_once "bd.php";

    # Indicar que usaremos el IOFactory
    use PhpOffice\PhpSpreadsheet\IOFactory;

    # Obtener conexión o salir en caso de error, mira bd.php
    $bd = obtenerBD();

    # El archivo a importar
    # Recomiendo poner la ruta absoluta si no está junto al script
    // $rutaArchivo = "tebuscamos.xlsx";
    $rutaArchivo = "tebuscamosimportar.xlsx";
    $documento = IOFactory::load($rutaArchivo);

    # Se espera que en la primera hoja estén los productos
    $hojaDeProductos = $documento->getSheet(0);

    # Preparar base de datos para que los inserts sean rápidos
    $bd->beginTransaction();

    # Preparar sentencia de productos
    $sentencia = $bd->prepare("insert into tebuscamos
(ci, nombres, apellidos, sexo, edad, fecha_nac, lugar_nac, estado_civil, profesion, direccion, nacionalidad, emision_ci, vencimiento_ci, fallecido, telefonos, copaco, numero_alternativos, otra_direcion, ruc, ruc_dv, ruc_tipo, ruc_fecha_act, ips_tipo, ips_estado, ips_enrolado, ips_fecha_actualizacion, ips_detalle_patronal, ips_beneficiario, funcion_publica, bnf_datos, padron_tsje, registros_tit_mec, postulaciones_cv, profesion_gremios, correo_registrados, posible_doble_identidad, doble_identidad_confir, madre, padre, conyugue, conyugue2, hermanos_ci, posibles_hermanos, posible_hermanas_casadas, informes_comerciales, antec_policial, ordenes_captura, antec_judicial,fecha_subido,supervisor,cod_gestor) values
(?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ? ,?, ?,?, ?, ?,?,?,?)");

    # Calcular el máximo valor de la fila como entero, es decir, el
    # límite de nuestro ciclo
    $numeroMayorDeFila = $hojaDeProductos->getHighestRow(); // Numérico
    $letraMayorDeColumna = $hojaDeProductos->getHighestColumn(); // Letra
    # Convertir la letra al número de columna correspondiente
    $numeroMayorDeColumna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($letraMayorDeColumna);
    // codigo agregado
    $cont = 0;

    ?>
    <div class="card-body">
        <form id="frm" action="" method="POST">
            <!-- TABLA -->
            <div class="card-body table-responsive">
                <!-- <div class="panel-body table-responsive">  -->
                    <?php
                    echo $rutaArchivo?>
                <table id="tbllistado" class="table-responsive dataTable table table-striped table-bordered table-condensed table-hover table-sm">
                    <thead>
                        <th>#</th>
                        <th>CI</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Sexo</th>
                        <th>edad</th>
                        <th>fecha_nac</th>
                        <th>lugar nac</th>
                        <th>estado civil</th>
                        <th>profesion</th>
                        <th>direccion</th>
                        <th>nacionaliad</th>
                        <th>fecha_subido</th>
                        <th>Supervisor</th>

                    </thead>
                    <tbody>
                        <?php
                        // Recorrer filas; comenzar en la fila 2 porque omitimos el encabezado
                        for ($indiceFila = 2; $indiceFila <= $numeroMayorDeFila; $indiceFila++) {
                            $cont++;
                            # Las columnas están en este orden:
                            # Código de barras, Descripción, Precio de Compra, Precio de Venta, Existencia
                            $ci    = $hojaDeProductos->getCellByColumnAndRow(1, $indiceFila);
                            $nombres = $hojaDeProductos->getCellByColumnAndRow(2, $indiceFila);
                            $apellidos = $hojaDeProductos->getCellByColumnAndRow(3, $indiceFila);
                            $sexo = $hojaDeProductos->getCellByColumnAndRow(4, $indiceFila);
                            $edad = $hojaDeProductos->getCellByColumnAndRow(5, $indiceFila);
                            $fecha_nac = $hojaDeProductos->getCellByColumnAndRow(6, $indiceFila);
                            $lugar_nac = $hojaDeProductos->getCellByColumnAndRow(7, $indiceFila);
                            $estado_civil = $hojaDeProductos->getCellByColumnAndRow(8, $indiceFila);
                            $profesion = $hojaDeProductos->getCellByColumnAndRow(9, $indiceFila);
                            $direccion = $hojaDeProductos->getCellByColumnAndRow(10, $indiceFila);
                            $nacionalidad = $hojaDeProductos->getCellByColumnAndRow(11, $indiceFila);
                            $emision_ci = $hojaDeProductos->getCellByColumnAndRow(12, $indiceFila);
                            $vencimiento_ci = $hojaDeProductos->getCellByColumnAndRow(13, $indiceFila);
                            $fallecido = $hojaDeProductos->getCellByColumnAndRow(14, $indiceFila);
                            $telefonos = $hojaDeProductos->getCellByColumnAndRow(15, $indiceFila);
                            $copaco = $hojaDeProductos->getCellByColumnAndRow(16, $indiceFila);
                            $numero_alternativos = $hojaDeProductos->getCellByColumnAndRow(17, $indiceFila);
                            $otra_direcion = $hojaDeProductos->getCellByColumnAndRow(18, $indiceFila);
                            $ruc = $hojaDeProductos->getCellByColumnAndRow(19, $indiceFila);
                            $ruc_dv = $hojaDeProductos->getCellByColumnAndRow(20, $indiceFila);
                            $ruc_tipo = $hojaDeProductos->getCellByColumnAndRow(21, $indiceFila);
                            $ruc_fecha_act = $hojaDeProductos->getCellByColumnAndRow(22, $indiceFila);
                            $ips_tipo = $hojaDeProductos->getCellByColumnAndRow(23, $indiceFila);
                            $ips_estado = $hojaDeProductos->getCellByColumnAndRow(24, $indiceFila);
                            $ips_enrolado = $hojaDeProductos->getCellByColumnAndRow(25, $indiceFila);
                            $ips_fecha_actualizacion = $hojaDeProductos->getCellByColumnAndRow(26, $indiceFila);
                            $ips_detalle_patronal = $hojaDeProductos->getCellByColumnAndRow(27, $indiceFila);
                            $ips_beneficiario = $hojaDeProductos->getCellByColumnAndRow(28, $indiceFila);
                            $funcion_publica = $hojaDeProductos->getCellByColumnAndRow(29, $indiceFila);
                            $bnf_datos = $hojaDeProductos->getCellByColumnAndRow(30, $indiceFila);
                            $padron_tsje = $hojaDeProductos->getCellByColumnAndRow(31, $indiceFila);
                            $registros_tit_mec = $hojaDeProductos->getCellByColumnAndRow(32, $indiceFila);
                            $postulaciones_cv = $hojaDeProductos->getCellByColumnAndRow(33, $indiceFila);
                            $profesion_gremios = $hojaDeProductos->getCellByColumnAndRow(34, $indiceFila);
                            $correo_registrados = $hojaDeProductos->getCellByColumnAndRow(35, $indiceFila);
                            $posible_doble_identidad = $hojaDeProductos->getCellByColumnAndRow(36, $indiceFila);
                            $doble_identidad_confir = $hojaDeProductos->getCellByColumnAndRow(37, $indiceFila);
                            $madre = $hojaDeProductos->getCellByColumnAndRow(38, $indiceFila);
                            $padre = $hojaDeProductos->getCellByColumnAndRow(39, $indiceFila);
                            $conyugue = $hojaDeProductos->getCellByColumnAndRow(40, $indiceFila);
                            $conyugue2 = $hojaDeProductos->getCellByColumnAndRow(41, $indiceFila);
                            $hermanos_ci = $hojaDeProductos->getCellByColumnAndRow(42, $indiceFila);
                            $posibles_hermanos = $hojaDeProductos->getCellByColumnAndRow(43, $indiceFila);
                            $posible_hermanas_casadas = $hojaDeProductos->getCellByColumnAndRow(44, $indiceFila);
                            $informes_comerciales = $hojaDeProductos->getCellByColumnAndRow(45, $indiceFila);
                            $antec_policial = $hojaDeProductos->getCellByColumnAndRow(46, $indiceFila);
                            $ordenes_captura = $hojaDeProductos->getCellByColumnAndRow(47, $indiceFila);
                            $antec_judicial = $hojaDeProductos->getCellByColumnAndRow(48, $indiceFila);
                            $fecha_subido = $hojaDeProductos->getCellByColumnAndRow(49, $indiceFila);
                            $supervisor = $hojaDeProductos->getCellByColumnAndRow(50, $indiceFila);
                            $cogestor = $hojaDeProductos->getCellByColumnAndRow(51, $indiceFila);
                            /////////
                            // $codigoDeBarras = $hojaDeProductos->getCellByColumnAndRow(1, $indiceFila);
                            // $descripcion = $hojaDeProductos->getCellByColumnAndRow(2, $indiceFila);
                            // $precioCompra = $hojaDeProductos->getCellByColumnAndRow(3, $indiceFila);
                            // $precioVenta = $hojaDeProductos->getCellByColumnAndRow(4, $indiceFila);
                            // $existencia = $hojaDeProductos->getCellByColumnAndRow(5, $indiceFila);
                            $sentencia->execute([
                                $ci, $nombres, $apellidos, $sexo, $edad, $fecha_nac, $lugar_nac, $estado_civil, $profesion, $direccion, $nacionalidad, $emision_ci, $vencimiento_ci, $fallecido, $telefonos, $copaco, $numero_alternativos, $otra_direcion, $ruc, $ruc_dv,
                                $ruc_tipo, $ruc_fecha_act, $ips_tipo, $ips_estado, $ips_enrolado, $ips_fecha_actualizacion, $ips_detalle_patronal, $ips_beneficiario, $funcion_publica, $bnf_datos, $padron_tsje, $registros_tit_mec, $postulaciones_cv, $profesion_gremios, $correo_registrados,
                                $posible_doble_identidad, $doble_identidad_confir, $madre, $padre, $conyugue, $conyugue2, $hermanos_ci, $posibles_hermanos, $posible_hermanas_casadas, $informes_comerciales, $antec_policial, $ordenes_captura, $antec_judicial,$fecha_subido,$supervisor,$cogestor
                            ]);
                            // $sentencia->execute([$codigoDeBarras, $descripcion, $precioCompra, $precioVenta, $existencia]);
                        ?>
                            <tr>
                                <td><?php echo $cont ?></td>
                                <td><?php echo $ci ?></td>
                                <td><?php echo $nombres ?></td>
                                <td><?php echo $apellidos ?></td>
                                <td><?php echo $sexo ?></td>
                                <td><?php echo $edad ?></td>
                                <td><?php echo $fecha_nac ?></td>
                                <td><?php echo $lugar_nac ?></td>
                                <td><?php echo $estado_civil ?></td>
                                <td><?php echo $profesion ?></td>
                                <td><?php echo $direccion ?></td>
                                <td><?php echo $nacionalidad ?></td>
                                <td><?php echo $fecha_subido ?></td>
                                <td><?php echo $supervisor ?></td>
                                
                            </tr>

                        <?php
                        }
                        echo 'fueron migrados ' . $cont . ' registros';

                        ?>

                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <?php
    # Hacer commit para guardar cambios de la base de datos
    $bd->commit();
    ?>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>



    <script>
        $(document).ready(function() {
            $('#tbllistado').DataTable();
        });
    </script>

</body>

</html>
<?php

?>