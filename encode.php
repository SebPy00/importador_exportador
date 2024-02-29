<?php
$str = 'afadfa;ñ';
echo mb_detect_encoding($str, "auto");

$strx = mb_convert_encoding($str, "UTF-8", "auto");

echo $strx;



?>