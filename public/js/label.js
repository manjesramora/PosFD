document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(e) {
        if (e.target.closest('#pagination-links a')) {
            e.preventDefault();
            var url = new URL(e.target.closest('#pagination-links a').href);
            filtrar(url.searchParams.get('page'));
        }
    });
});

function filtrar(page = 1) {
    var sku = document.getElementById('sku').value;
    var name = document.getElementById('name').value;
    var linea = document.getElementById('linea').value ? 'LN' + document.getElementById('linea').value : '';
    var sublinea = document.getElementById('sublinea').value ? 'SB' + document.getElementById('sublinea').value : '';
    var departamento = document.getElementById('departamento').value;

    var url = new URL(window.location.href);
    url.searchParams.set('sku', sku);
    url.searchParams.set('name', name);
    if (linea !== '') {
        url.searchParams.set('linea', linea);
    } else {
        url.searchParams.delete('linea');
    }
    if (sublinea !== '') {
        url.searchParams.set('sublinea', sublinea);
    } else {
        url.searchParams.delete('sublinea');
    }
    if (departamento !== '') {
        url.searchParams.set('departamento', departamento);
    } else {
        url.searchParams.delete('departamento');
    }
    url.searchParams.set('page', page);
    window.history.pushState({}, '', url);

    fetch(url)
        .then(response => response.text())
        .then(html => {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newContent = doc.getElementById('proveedorTable').innerHTML;
            document.getElementById('proveedorTable').innerHTML = newContent;
            var newPagination = doc.getElementById('pagination-links').innerHTML;
            document.getElementById('pagination-links').innerHTML = newPagination;
        });
}






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

    function limpiarFiltros() {
        document.getElementById('sku').value = '';
        document.getElementById('name').value = '';
        document.getElementById('linea').value = '';
        document.getElementById('sublinea').value = '';
        document.getElementById('departamento').value = '';
    
        filtrar(1);
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