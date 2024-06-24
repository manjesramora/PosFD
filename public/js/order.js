document.addEventListener('DOMContentLoaded', function () {
    const idInput = document.getElementById('CNCDIRID');
    const nameInput = document.getElementById('CNCDIRNOM');
    const idDropdown = document.getElementById('idDropdown');
    const nameDropdown = document.getElementById('nameDropdown');
    const MAX_RESULTS = 10; // Máximo número de resultados a mostrar

    let providersCache = [];

    const getProviders = async () => {
        if (providersCache.length > 0) {
            return providersCache;
        }
        try {
            const response = await axios.get(window.getProvidersUrl);
            providersCache = response.data;
            return providersCache;
        } catch (error) {
            console.error('Error fetching providers:', error);
            return [];
        }
    };

    const filterProviders = (searchTerm, type) => {
        if (!searchTerm) return [];

        const lowerCaseSearchTerm = searchTerm.toLowerCase();
        return providersCache.filter(provider => {
            if (type === 'name') {
                return provider.CNCDIRNOM.toLowerCase().includes(lowerCaseSearchTerm);
            } else if (type === 'id') {
                return provider.CNCDIRID.toString().includes(searchTerm);
            }
        }).sort((a, b) => {
            if (type === 'name') {
                return a.CNCDIRNOM.toLowerCase().indexOf(lowerCaseSearchTerm) - b.CNCDIRNOM.toLowerCase().indexOf(lowerCaseSearchTerm);
            } else if (type === 'id') {
                return a.CNCDIRID.toString().indexOf(searchTerm) - b.CNCDIRID.toString().indexOf(searchTerm);
            }
        }).slice(0, MAX_RESULTS); // Ordenar por relevancia y limitar los resultados
    };

    const showDropdown = (inputElement, dropdownElement, providers, type) => {
        dropdownElement.innerHTML = '';
        providers.forEach(provider => {
            const item = document.createElement('a');
            item.classList.add('dropdown-item');
            item.textContent = type === 'name' ? provider.CNCDIRNOM : provider.CNCDIRID;
            item.onclick = () => selectProvider(provider.CNCDIRID, provider.CNCDIRNOM);
            dropdownElement.appendChild(item);
        });
        dropdownElement.style.display = providers.length > 0 ? 'block' : 'none';
    };

    const selectProvider = (id, name) => {
        idInput.value = id;
        nameInput.value = name;
        idDropdown.style.display = 'none';
        nameDropdown.style.display = 'none';
    };

    idInput.addEventListener('input', async function () {
        const searchTerm = this.value;
        if (providersCache.length === 0) {
            await getProviders();
        }
        const filteredProviders = filterProviders(searchTerm, 'id');
        showDropdown(idInput, idDropdown, filteredProviders, 'id');
    });

    nameInput.addEventListener('input', async function () {
        const searchTerm = this.value;
        if (providersCache.length === 0) {
            await getProviders();
        }
        const filteredProviders = filterProviders(searchTerm, 'name');
        showDropdown(nameInput, nameDropdown, filteredProviders, 'name');
    });

    document.addEventListener('click', function (event) {
        if (!idInput.contains(event.target)) {
            idDropdown.style.display = 'none';
        }
        if (!nameInput.contains(event.target)) {
            nameDropdown.style.display = 'none';
        }
    });
});


// Código para validar las fechas
document.addEventListener('DOMContentLoaded', () => {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const errorMessage = document.getElementById('errorMessage');
    const filterButton = document.getElementById('filterButton');

    const validateDates = () => {
        if (!startDate || !endDate || !errorMessage || !filterButton) {
            return;
        }

        const start = new Date(startDate.value);
        const end = new Date(endDate.value);

        if (start && end && start > end) {
            errorMessage.textContent = 'La fecha de inicio no puede ser posterior a la fecha de fin.';
            errorMessage.style.display = 'block';
            filterButton.style.display = 'none'; // Ocultar botón de filtrar
        } else {
            errorMessage.style.display = 'none';
            filterButton.style.display = 'block'; // Mostrar botón de filtrar
        }
    };

    if (startDate && endDate) {
        startDate.addEventListener('input', validateDates);
        endDate.addEventListener('input', validateDates);
    }
});

    function limpiarCampos() {
        document.getElementById('CNCDIRID').value = '';
        document.getElementById('CNCDIRNOM').value = '';
        document.getElementById('ACMROIDOC').value = '';
    }

