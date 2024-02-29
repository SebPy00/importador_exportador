<?php
/**
 * Ejemplo de cómo usar PDO y PHPSpreadSheet para
 * exportar datos de MySQL a Excel de manera
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
require_once "vendor/autoload.php";

# Nuestra base de datos
require_once "bd2.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

# Obtener base de datos
$bd = obtenerBD();



$spread = new Spreadsheet();
$spread
    ->getProperties()
    ->setCreator("Nombre del autor")
    ->setLastModifiedBy('Juan Perez')
    ->setTitle('Excel creado con PhpSpreadSheet')
    ->setSubject('Excel de prueba')
    ->setDescription('Excel generado como prueba')
    ->setKeywords('PHPSpreadsheet')
    ->setCategory('Categoría de prueba');
 


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte de revision de publicaciones"');
header('Cache-Control: max-age=0');
 



# Como ya hay una hoja por defecto, la obtenemos, no la creamos
$hojaDeProductos = $spread->getActiveSheet();
$hojaDeProductos->setTitle("Productos");

# Escribir encabezado de los productos
$encabezado = ["Código de barras", "Descripción", "Precio de compra", "Precio de venta", "Existencia"];
# El último argumento es por defecto A1 pero lo pongo para que se explique mejor
$hojaDeProductos->fromArray($encabezado, null, 'A1');

$consulta = "select codigo, descripcion, precioCompra, precioVenta, existencia from productos";
$sentencia = $bd->prepare($consulta, [
    PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL,
]);
$sentencia->execute();
# Comenzamos en la 2 porque la 1 es del encabezado
$numeroDeFila = 2;
while ($producto = $sentencia->fetchObject()) {
    # Obtener los datos de la base de datos
    $codigo = $producto->codigo;
    $descripcion = $producto->descripcion;
    $precioCompra = $producto->precioCompra;
    $precioVenta = $producto->precioVenta;
    $existencia = $producto->existencia;
    # Escribirlos en el documento
    $hojaDeProductos->setCellValueByColumnAndRow(1, $numeroDeFila, $codigo);
    $hojaDeProductos->setCellValueByColumnAndRow(2, $numeroDeFila, $descripcion);
    $hojaDeProductos->setCellValueByColumnAndRow(3, $numeroDeFila, $precioCompra);
    $hojaDeProductos->setCellValueByColumnAndRow(4, $numeroDeFila, $precioVenta);
    $hojaDeProductos->setCellValueByColumnAndRow(5, $numeroDeFila, $existencia);
    $numeroDeFila++;
}


$writer = IOFactory::createWriter($spread, 'Xlsx');
$writer->save('php://output');
exit;