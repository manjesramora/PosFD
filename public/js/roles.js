function filterRoles() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchRole");
    filter = input.value.toUpperCase();
    table = document.getElementById("dataTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0]; // Asumiendo que la columna 'Rol' es la primera
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}


$(document).ready(function() {
    // Evento de clic en el encabezado de la columna para ordenar
    $("th.sortable").click(function () {
        var columnIndex = $(this).index();
        var asc = $(this).hasClass("asc");
        var desc = $(this).hasClass("desc");

        // Si la columna ya está ordenada en orden ascendente, ordenar descendentemente
        if (asc) {
            $("th").removeClass("asc").removeClass("desc");
            $(this).addClass("desc");
            // Mostrar indicador de ordenamiento en el encabezado de la columna
            $(this).find(".sort-indicator").remove();
            $(this).append('<span class="sort-indicator">&#x25BE;</span>'); // Flecha hacia abajo
            sortTable(columnIndex, false);
        }
        // Si la columna ya está ordenada en orden descendente, quitar el ordenamiento
        else if (desc) {
            $("th").removeClass("asc").removeClass("desc");
            $(this).find(".sort-indicator").remove();
            sortTable(0, true); // Orden predeterminado (primera columna ascendente)
        }
        // Si la columna no está ordenada, ordenar ascendente
        else {
            $("th").removeClass("asc").removeClass("desc");
            $(this).addClass("asc");
            // Mostrar indicador de ordenamiento en el encabezado de la columna
            $(this).find(".sort-indicator").remove();
            $(this).append('<span class="sort-indicator">&#x25B4;</span>'); // Flecha hacia arriba
            sortTable(columnIndex, true);
        }
    });
});

function sortTable(columnIndex, asc) {
    var table = document.getElementById("dataTable");
    var rows = table.rows;
    var switching = true;
    var shouldSwitch;
    var i;
    var x, y;

    while (switching) {
        switching = false;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[columnIndex];
            y = rows[i + 1].getElementsByTagName("TD")[columnIndex];

            if (asc) {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            } else {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        }
    }
}