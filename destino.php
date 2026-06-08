<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://fonts.googleapis.com/css2?family=Nanum+Pen+Script&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <title><?php echo $destino['destino']; ?> - Tose en Viaje</title>
</head>
<body>

<?php

    include "components/navbar.php";

    $id = $_GET['id'] ?? null;

    // Validación básica: si no hay id o no es numérico, cortamos
    if (!$id || !ctype_digit((string)$id)) {
        http_response_code(400);
        die("Destino no válido.");
    }

    require_once __DIR__ . '/../config.php';

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    $queryDestino = 'SELECT destinos.*, viajes.fecha, paises.pais 
                    FROM destinos 
                    INNER JOIN viajes ON destinos.id = viajes.id_destino_llegada 
                    INNER JOIN paises ON destinos.id_pais = paises.id 
                    WHERE destinos.id = ?';

    $stmt = $conn->prepare($queryDestino);
    $stmt->bind_param("i", $id);  // "i" = integer
    $stmt->execute();

    $resultadoDestino = $stmt->get_result();
    $destino = $resultadoDestino->fetch_assoc();

    $stmt->close();
    $conn->close();

    if (!$destino) {
        http_response_code(404);
        die("Destino no encontrado.");
    }

    // GALERIA
    if ($destino["carpeta"] !== null) {

        $dir = 'public/img/' . $destino["carpeta"];

        $files = array_diff(scandir($dir), array('.', '..'));

        $imagenes = [];

        foreach ($files as $file) {

            $imagenes[] = "$dir/$file";

        }
    } else {
        $imagenes = null;
    };

    echo '<div class="container">' . '<h1 id="destino">' . $destino["destino"] . '</h1><h2 id="pais">' . $destino["pais"] . '</h2>';

?>

  <div id="galeria" class="galeria"></div>

  <script>

        const contenedor = document.getElementById('galeria');
        const infoDestino = <?php echo json_encode($destino) ?>;
        const imagenes = <?php echo json_encode($imagenes) ?>;

        if(infoDestino.carpeta == null) {

            contenedor.insertAdjacentHTML('beforeend', '<p id="no-disp">Las fotos de este destino aún no están disponibles</p>');

        } else {

            //ciclo sobre objeto con fotos
            let i = 0;

            imagenes.forEach(url => {
                
                i++;

                const div = document.createElement('div');
                
                div.style.backgroundImage = `url(${url})`;
                
                contenedor.appendChild(div);

            });
        }

  </script>

<?php

    echo '</div>'

?>

   

    
</body>
</html>