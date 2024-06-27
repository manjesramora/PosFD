// public/js/label.js
function buscarFiltros() {
    var productId = document.getElementById('productId').value;
    var sku = document.getElementById('sku').value;
    var name = document.getElementById('name').value;
    var linea = document.getElementById('linea').value;
    var sublinea = document.getElementById('sublinea').value;
    var departamento = document.getElementById('departamento').value;

    // Incluir valores por defecto para Línea y Sublinea
    if (linea) {
        linea = 'LN' + linea;
    }
    if (sublinea) {
        sublinea = 'SB' + sublinea;
    }

    var url = new URL(window.location.href);

    // Eliminar los parámetros anteriores de la URL
    url.searchParams.delete('productId');
    url.searchParams.delete('sku');
    url.searchParams.delete('name');
    url.searchParams.delete('linea');
    url.searchParams.delete('sublinea');
    url.searchParams.delete('departamento');
    url.searchParams.delete('page'); // Reiniciar el paginado a la página 1

    // Establecer los nuevos parámetros de búsqueda
    if (productId) url.searchParams.set('productId', productId);
    if (sku) url.searchParams.set('sku', sku);
    if (name) url.searchParams.set('name', name);
    if (linea) url.searchParams.set('linea', linea);
    if (sublinea) url.searchParams.set('sublinea', sublinea);
    if (departamento) url.searchParams.set('departamento', departamento);

    window.history.pushState({}, '', url);

    fetch(url)
        .then(response => response.text())
        .then(html => {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newContent = doc.getElementById('proveedorTable').innerHTML;
            var newPagination = doc.getElementById('pagination-links').innerHTML;
            document.getElementById('proveedorTable').innerHTML = newContent;
            document.getElementById('pagination-links').innerHTML = newPagination;

            // Reattach event listeners for pagination links
            reattachPaginationEventListeners();
        });
}

function limpiarFiltros() {
    document.getElementById('productId').value = '';
    document.getElementById('sku').value = '';
    document.getElementById('name').value = '';
    document.getElementById('linea').value = '';
    document.getElementById('sublinea').value = '';
    document.getElementById('departamento').value = '';
    buscarFiltros(); // Llamar a la función para actualizar la tabla
}

function reattachPaginationEventListeners() {
    document.querySelectorAll('#pagination-links a').forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var url = new URL(event.target.href);
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(html, 'text/html');
                    var newContent = doc.getElementById('proveedorTable').innerHTML;
                    var newPagination = doc.getElementById('pagination-links').innerHTML;
                    document.getElementById('proveedorTable').innerHTML = newContent;
                    document.getElementById('pagination-links').innerHTML = newPagination;

                    // Reattach event listeners for pagination links
                    reattachPaginationEventListeners();
                });
        });
    });
}

// Initial call to attach event listeners
reattachPaginationEventListeners();



function setDefault(id, defaultValue) {
    var input = document.getElementById(id);
    if (!input.value.startsWith(defaultValue)) {
        input.value = defaultValue;
    }
}

function checkDefault(id, defaultValue) {
    var input = document.getElementById(id);
    if (input.value === defaultValue) {
        input.value = ''; // Clear the input if it only contains the default value
    } else if (!input.value.startsWith(defaultValue)) {
        input.value = defaultValue + input.value; // Ensure the default value is prepended
    }
}


    function showPrintModal(sku, description) {
        document.getElementById('skuInput').value = sku;
        document.getElementById('descriptionInput').value = description;
        $('#printModal').modal('show');
    }

  
    

    function showPrintModal(sku, description) {
        document.getElementById('modalSku').value = sku;
        document.getElementById('modalDescription').value = description;
        $('#printModal').modal('show');
    }
    
    function submitPrintForm() {
        var printForm = document.getElementById('printForm');
        var formData = new FormData(printForm);
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = printForm.action;
        form.target = '_blank';
        
        for (var [key, value] of formData.entries()) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }