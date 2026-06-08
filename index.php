<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <style>@import url('https://fonts.googleapis.com/css2?family=Titan+One&display=swap');</style>
    <link rel="icon" type="image/x-icon" href="/public/img/logo.svg">
    <title>Tose en Viaje</title>
</head>
<body>

<?php

    include "components/navbar.php";

    require_once __DIR__ . '/../config.php';

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");
        
    // query lista destinos
    
    $queryDestinos = "
        SELECT destinos.*, viajes.fecha FROM `destinos` inner JOIN viajes ON destinos.id = viajes.id_destino_llegada ORDER by viajes.fecha ASC;
        ";

    $resultadoDestinos = $conn->query($queryDestinos);

    $destinos = [];

    while ($fila = $resultadoDestinos->fetch_assoc()) {
        $destinos[] = $fila;
    }

    // query lista paises
        
    $queryPaises = "
        SELECT * FROM `paises` order by id ASC;
        ";

    $resultadoPaises = $conn->query($queryPaises);

    $paises = [];

    while ($fila2 = $resultadoPaises->fetch_assoc()) {
        $paises[] = $fila2;
    }



    foreach ($paises as &$pais) {
        $pais_id = $pais['id'];
        $pais['destinos'] = array_values(array_filter($destinos, function($destino) use ($pais_id) {
        return $destino['id_pais'] === $pais_id;
        }));
    }

    // query lista partidas
        
    $queryViajes = "
        SELECT viajes.*, partida.latitud AS latitud_partida, partida.longitud AS longitud_partida, llegada.latitud AS latitud_llegada, llegada.longitud AS longitud_llegada FROM viajes INNER JOIN destinos AS partida ON viajes.id_destino_partida = partida.id INNER JOIN destinos AS llegada ON viajes.id_destino_llegada = llegada.id ORDER BY viajes.id ASC;
        ";

    $resultadoViajes = $conn->query($queryViajes);

    $viajes = [];

    while ($fila3 = $resultadoViajes->fetch_assoc()) {
        $viajes[] = $fila3;
    }

    $conn->close();

?>

<section>
    <div id="lista-destinos">
        <h3>Nuestro recorrido:</h3>
    </div>


    <div id="map">
    </div>
</section>

<script>
    const destinos = <?php echo json_encode($destinos, JSON_UNESCAPED_UNICODE); ?>;
    const paises = <?php echo json_encode($paises, JSON_UNESCAPED_UNICODE); ?>;
    const viajes = <?php echo json_encode($viajes, JSON_UNESCAPED_UNICODE); ?>;
</script>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="public/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

</body>
</html>

