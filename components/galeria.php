<?php

$dir = 'public/img/' . $destino["carpeta"];


$files = array_diff(scandir($dir), array('.', '..'));

$images = [];

foreach ($files as $file) {

    $images[] = "$dir/$file";

}

header('Content-Type: application/json');

echo json_encode($images);

?>