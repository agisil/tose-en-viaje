<?php

    // query lista destinos

    require_once __DIR__ . '/../../config.php';

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
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

    <div class="accordion accordion-flush" id="lista-destinos">
        <h3>Nuestro recorrido:</h3>
    </div>

<script>
  const paises = <?php echo json_encode($paises); ?>;
  const numeros = ["One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten"];

  const listaDestinos = document.getElementById("lista-destinos");

  let i = 0;

  paises.forEach(pais => {
    const numero = numeros[i];

    const HTMLPais = `
      <div class="accordion-item" id="${pais.id}">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse${numero}" aria-expanded="false" aria-controls="flush-collapse${numero}">
            ${pais.pais}
          </button>
        </h2>
        <div id="flush-collapse${numero}" class="accordion-collapse collapse" data-bs-parent="#lista-destinos">
          <div class="accordion-body" id="body-${pais.id}">
          </div>
        </div>
      </div>`;

    listaDestinos.insertAdjacentHTML("beforeend", HTMLPais);

    const contenedorDestinos = document.getElementById(`body-${pais.id}`);

    pais.destinos.forEach(destino => {
      contenedorDestinos.insertAdjacentHTML("beforeend", `<p>${destino.destino}</p>`);
    });

    i++;
  });

</script>