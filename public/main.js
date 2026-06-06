function guardarFecha() {
    const fecha = new Date();
    const ano = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
    const dia = String(fecha.getDate()).padStart(2, '0');
    fechaFormato = `${ano}-${mes}-${dia}`;
}
guardarFecha();

const numeros = ["One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten"];
let i = 0;

let listaDestinos = document.getElementById('lista-destinos');


paises.forEach(pais => {

    const numero = numeros[i];

    if (pais.id !== "7") {
        // Crear el contenedor del país
        const item = document.createElement("div");
        item.classList.add("accordion-item");

        item.innerHTML = `
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#collapse${numero}" aria-expanded="true" aria-controls="collapse${numero}">
                    <img src="public/img/pais${pais.id}.svg" class="bandera" alt="Bandera ${pais.pais}">
                    <span>${pais.pais}</span>
                    <img src="public/img/plus.svg" alt="flecha" class="flecha">
                </button>
            </h2>
            <div id="collapse${numero}" class="accordion-collapse collapse" data-bs-parent="#lista-destinos">
                <div class="accordion-body" id="accordion-body-${pais.id}"></div>
            </div>
        `;

        // Agregar el país al acordeón principal
        listaDestinos.appendChild(item);
    }

    const divDestino = document.getElementById(`accordion-body-${pais.id}`);
    const destinosPais = pais.destinos;

    destinosPais.forEach(destino => {
        if (destino.id !== "37" && destino.id !== "35") {
            // Crear el destino como nodo
            const div = document.createElement("div");
            div.classList.add("destino");

            div.innerHTML = `
                <a href="destino.php?id=${destino.id}">${destino.destino}</a>
                <span class="check-destino"></span>
            `;

            divDestino.appendChild(div);

            // Ahora sí, seleccionamos el span recién creado
            const check = div.querySelector(".check-destino");

            if (destino.fecha < fechaFormato) {
                check.innerHTML = `<span class="material-symbols-outlined check">check_circle</span>`;
            }
        }
    });

    i++;
    
});

// CREAR MAPA
var map = L.map('map').setView([-23, -60.06], 3);

var myMarkerIcon = L.icon({
    iconUrl: 'public/img/line-end.svg',
    iconSize: [48, 48],
    iconAnchor: [24, 44],
    popupAnchor: [-3, -3],
});

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 21,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

destinos.forEach(destino => {   

    var marker = L.marker([destino.latitud, destino.longitud], /*{icon: myMarkerIcon}*/).addTo(map);
    
    if(destino.id == "37" || destino.id == "35") {
        marker.bindPopup(`<b>${destino.destino}</b>`);
    } else {
        marker.bindPopup(`
            <a href="destino.php?id=${destino.id}">
                <b>${destino.destino}</b>
            </a>`);
    }

});

// AGREGAR LINEAS

viajes.forEach(viaje => {

    // if(viaje.fecha < fechaFormato) {

    myLines = 
        {
        type: "LineString",
        coordinates: [
            [viaje.longitud_partida, viaje.latitud_partida],
            [viaje.longitud_llegada, viaje.latitud_llegada]
        ]
        };

    var myStyle = {
        "color": "#386150",
        "weight": 1,
        "opacity": 1,
    };

    L.geoJSON(myLines, {
        style: myStyle
    }).addTo(map);

    var myInvisibleStyle = {
        color: "transparent",
        weight: 20,
        opacity: 0,
    };

    const iconosViaje = {
        "1": "flight",
        "2": "directions_bus",
        "3": "directions_boat",
        "4": "hiking",
        "5": "directions_car",
        "6": "pedal_bike",
    }
    

    L.geoJSON(myLines, {
        style: myInvisibleStyle
    }).bindTooltip(`<span class="material-icons">${iconosViaje[viaje.tipo]}</span>`, {
        sticky: true
    }).openTooltip().addTo(map);


//    }

})

// ------------------------------------------------------- BOTTONE + -------------------------------------------------------

$(".accordion-button").click(function() {

    $(this).parent("h2").parent("div").siblings("div").children("h2").children(".accordion-button").children("img.flecha").removeClass("rotate");
    $(this).children("img.flecha").toggleClass("rotate");

})