// Función para buscar y aplicar filtros en la tabla
function buscarFiltros() {
    // Obtener los valores de los filtros desde los elementos del DOM
    var productId = document.getElementById("productId").value;
    var sku = document.getElementById("sku").value;
    var name = document.getElementById("name").value;
    var linea = document.getElementById("linea").value;
    var sublinea = document.getElementById("sublinea").value;
    var departamento = document.getElementById("departamento").value;

    // Incluir valores por defecto para Línea y Sublinea
    if (linea) {
        linea = "LN" + linea;
    }
    if (sublinea) {
        sublinea = "SB" + sublinea;
    }

    var url = new URL(window.location.href);

    // Eliminar los parámetros anteriores de la URL
    url.searchParams.delete("productId");
    url.searchParams.delete("sku");
    url.searchParams.delete("name");
    url.searchParams.delete("linea");
    url.searchParams.delete("sublinea");
    url.searchParams.delete("departamento");
    url.searchParams.delete("page"); // Reiniciar el paginado a la página 1

    // Establecer los nuevos parámetros de búsqueda
    if (productId) url.searchParams.set("productId", productId);
    if (sku) url.searchParams.set("sku", sku);
    if (name) url.searchParams.set("name", name);
    if (linea) url.searchParams.set("linea", linea);
    if (sublinea) url.searchParams.set("sublinea", sublinea);
    if (departamento) url.searchParams.set("departamento", departamento);

    // Actualizar la URL del navegador
    window.history.pushState({}, "", url);

    // Realizar la solicitud fetch para actualizar la tabla y la paginación
    fetch(url)
        .then((response) => response.text())
        .then((html) => {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, "text/html");
            var newContent = doc.getElementById("proveedorTable").innerHTML;
            var newPagination = doc.getElementById("pagination-links").innerHTML;
            document.getElementById("proveedorTable").innerHTML = newContent;
            document.getElementById("pagination-links").innerHTML = newPagination;

            // Reattach event listeners for pagination links
            reattachPaginationEventListeners();
        });
}

// Función para limpiar los filtros y actualizar la tabla
function limpiarFiltros() {
    document.getElementById("productId").value = "";
    document.getElementById("sku").value = "";
    document.getElementById("name").value = "";
    document.getElementById("linea").value = "";
    document.getElementById("sublinea").value = "";
    document.getElementById("departamento").value = "";
    buscarFiltros(); // Llamar a la función para actualizar la tabla
}

// Función para volver a adjuntar los event listeners para los enlaces de paginación
function reattachPaginationEventListeners() {
    document.querySelectorAll("#pagination-links a").forEach(function (link) {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            var url = new URL(event.target.href);
            fetch(url)
                .then((response) => response.text())
                .then((html) => {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(html, "text/html");
                    var newContent = doc.getElementById("proveedorTable").innerHTML;
                    var newPagination = doc.getElementById("pagination-links").innerHTML;
                    document.getElementById("proveedorTable").innerHTML = newContent;
                    document.getElementById("pagination-links").innerHTML = newPagination;

                    // Reattach event listeners for pagination links
                    reattachPaginationEventListeners();
                });
        });
    });
}

// Llamada inicial para adjuntar los event listeners de paginación
reattachPaginationEventListeners();

// Función para establecer un valor por defecto en un campo de entrada
function setDefault(id, defaultValue) {
    var input = document.getElementById(id);
    if (!input.value.startsWith(defaultValue)) {
        input.value = defaultValue;
    }
}

// Función para verificar y ajustar el valor por defecto en un campo de entrada
function checkDefault(id, defaultValue) {
    var input = document.getElementById(id);
    if (input.value === defaultValue) {
        input.value = ""; // Limpiar el input si solo contiene el valor por defecto
    } else if (!input.value.startsWith(defaultValue)) {
        input.value = defaultValue + input.value; // Asegurar que el valor por defecto se anteponga
    }
}

// Función para mostrar el modal de impresión con los datos del SKU y descripción
function showPrintModal(sku, description) {
    document.getElementById("skuInput").value = sku;
    document.getElementById("descriptionInput").value = description;
    $("#printModal").modal("show");
}

// Función para mostrar el modal de impresión con los datos del SKU y descripción (duplicada, eliminar una)
function showPrintModal(sku, description) {
    document.getElementById("modalSku").value = sku;
    document.getElementById("modalDescription").value = description;
    $("#printModal").modal("show");
}

// Función para enviar el formulario de impresión
function submitPrintForm() {
    var printForm = document.getElementById("printForm");
    var formData = new FormData(printForm);
    var form = document.createElement("form");
    form.method = "POST";
    form.action = printForm.action;
    form.target = "_blank";

    // Crear inputs ocultos para cada dato del formulario y agregarlos al nuevo formulario
    for (var [key, value] of formData.entries()) {
        var input = document.createElement("input");
        input.type = "hidden";
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }

    // Adjuntar el formulario al cuerpo del documento y enviarlo
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
