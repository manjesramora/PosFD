document.getElementById("recepcionar").addEventListener("click", function () {
    let data = [];
    let hasPendingItems = false;

    document.querySelectorAll("#dataTable tbody tr").forEach(function (row) {
        let cantidadSolicitada = parseFloat(
            row.querySelector(".acmroiqttr").textContent
        );
        let cantidadRecibida = parseFloat(
            row.querySelector(".acmroiqt input").value
        );

        if (cantidadRecibida < cantidadSolicitada) {
            hasPendingItems = true;
        }

        let item = {
            acmroilin: row.querySelector(".acmroilin").textContent,
            acmroidsc: row.querySelector(".acmroidsc").textContent,
            acmroiumt: row.querySelector(".acmroiumt").textContent,
            inprodi2: row.querySelector(".inprodid").textContent,
            inprodcbr: row.querySelector(".inprodcbr").textContent,
            acmroipesou: row.querySelector(".acmroipesou").textContent,
            acmroivolu: row.querySelector(".acmroivolu").textContent,
            acmroiqttr: cantidadSolicitada,
            acmroiqt: cantidadRecibida,
            acmroinp: row.querySelector(".acmroinp input").value,
            acmroing: row.querySelector(".acmroing").textContent,
        };
        data.push(item);
    });

    axios
        .post("/recepcionar", {
            data: data,
            hasPendingItems: hasPendingItems,
        })
        .then(function (response) {
            console.log(response.data);
            if (response.data.status === "success") {
                alert("Recepción exitosa.");
                location.reload();
            }
        })
        .catch(function (error) {
            console.log(error);
            alert("Error en la recepción. Por favor, inténtalo de nuevo.");
        });
});
$(document).ready(function () {
    // Función para obtener los datos del proveedor por número
    function getProvidersByNumber(number) {
        $.ajax({
            url: "/order/getProvidersByNumber",
            type: "GET",
            data: { number: number },
            success: function (response) {
                fillProviderList(response, "numero");
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
        });
    }

    // Función para obtener los datos del proveedor por nombre
    function getProvidersByName(name) {
        $.ajax({
            url: "/order/getProvidersByName",
            type: "GET",
            data: { name: name },
            success: function (response) {
                fillProviderList(response, "fletero");
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
        });
    }

    // Función para llenar la lista desplegable con los proveedores
    function fillProviderList(providers, inputType) {
        var listId =
            inputType === "numero" ? "#numeroFleteList" : "#fleteroList";
        $(listId).empty();
        providers.forEach(function (provider) {
            var listItem =
                inputType === "numero"
                    ? '<li class="list-group-item" data-id="' +
                      provider.CNCDIRID +
                      '" data-name="' +
                      provider.CNCDIRNOM +
                      '">' +
                      provider.CNCDIRID +
                      "</li>"
                    : '<li class="list-group-item" data-id="' +
                      provider.CNCDIRID +
                      '" data-name="' +
                      provider.CNCDIRNOM +
                      '">' +
                      provider.CNCDIRNOM +
                      "</li>";
            $(listId).append(listItem);
        });

        if (providers.length > 0) {
            $(listId).show().css("position", "absolute");
        } else {
            $(listId).hide();
        }
    }

    // Evento para buscar proveedores por número al escribir en el campo
    $("#numero").on("input", function () {
        var number = $(this).val();
        if (number.length >= 0) {
            getProvidersByNumber(number);
        } else {
            $("#numeroFleteList").hide();
        }
    });

    // Evento para buscar proveedores por nombre al escribir en el campo
    $("#fletero").on("input", function () {
        var name = $(this).val();
        if (name.length >= 0) {
            getProvidersByName(name);
        } else {
            $("#fleteroList").hide();
        }
    });

    // Evento para seleccionar un proveedor de la lista
    $(document).on("click", ".list-group-item", function () {
        var providerId = $(this).data("id");
        var providerName = $(this).data("name");

        $("#numero").val(providerId);
        $("#fletero").val(providerName);

        $(".list-group").hide();
    });

    // Ocultar la lista si se hace clic fuera de los campos de entrada
    $(document).on("click", function (e) {
        if (!$(e.target).closest("#numero, #fletero, .list-group").length) {
            $(".list-group").hide();
        }
    });
});

$(".clear-input").click(function () {
    $("#numero").val("");
    $("#fletero").val("");
});
$(document).ready(function () {
    // Función para ordenar los datos en la tabla
    function sortTable(columnIndex, asc) {
        var table = $("#dataTable");
        var rows = table
            .find("tbody tr")
            .toArray()
            .sort(function (a, b) {
                var valA = $(a).find("td").eq(columnIndex).text();
                var valB = $(b).find("td").eq(columnIndex).text();
                // Convierte los valores a números si son números
                var numA = parseFloat(valA);
                var numB = parseFloat(valB);
                if (!isNaN(numA) && !isNaN(numB)) {
                    valA = numA;
                    valB = numB;
                }
                // Ordena alfabéticamente si no son números
                return asc ? (valA > valB ? 1 : -1) : valA < valB ? 1 : -1;
            });
        table.find("tbody").empty().append(rows);
    }

    // Evento de clic en el encabezado de la columna para ordenar
    $("th").click(function () {
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
            sortTable(0, true); // Orden predeterminado
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
// Mostrar o ocultar el campo de valor del flete según la selección
$("#flete").change(function () {
    if ($(this).val() === "si") {
        $("#flete-input-container").removeClass("d-none");
    } else {
        $("#flete-input-container").addClass("d-none");
    }
});
$(document).ready(function () {
    // Mostrar el botón de regresar arriba cuando el usuario ha bajado 20px desde la parte superior de la página
    $(window).scroll(function () {
        if ($(this).scrollTop() > 20) {
            $("#scrollToTopBtn").fadeIn();
        } else {
            $("#scrollToTopBtn").fadeOut();
        }
    });

    // Cuando se hace clic en el botón, desplázate suavemente hacia arriba
    $("#scrollToTopBtn").click(function () {
        $("html, body").animate(
            {
                scrollTop: 0,
            },
            800
        );
        return false;
    });
});
// Archivo: public/js/reception.js

$(document).ready(function() {
    // Mostrar campos de Flete
    $('#flete').change(function() {
        var fleteValue = $(this).val();
        if (fleteValue === 'si') {
            $('#flete_numero_wrapper').removeClass('d-none');
            $('#flete_fletero_wrapper').removeClass('d-none');
        } else {
            $('#flete_numero_wrapper').addClass('d-none');
            $('#flete_fletero_wrapper').addClass('d-none');
        }
    });

    // Limpiar campos de Flete
    $('.clear-input').click(function() {
        $(this).siblings('input').val('');
    });

    // Validar y Enviar el Formulario
    $('#submitButton').click(function() {
        var data = [];
        var hasPendingItems = false;

        $('tbody tr').each(function() {
            var row = $(this);
            var acmroilin = row.find('td:eq(0)').text();
            var inprodi2 = row.find('td:eq(1)').text();
            var acmroiqttr = parseInt(row.find('td:eq(3)').text(), 10);
            var acmroiqt = parseInt(row.find('.acmroiqt').val(), 10);
            var acmroinp = parseFloat(row.find('.acmroinp').val());
            var acmroing = row.find('td:eq(6)').text();

            if (acmroiqt > acmroiqttr) {
                alert('La cantidad ingresada no puede ser mayor a la cantidad total recibida.');
                return false;
            }

            if (acmroiqt < acmroiqttr) {
                hasPendingItems = true;
            }

            data.push({
                acmroilin: acmroilin,
                inprodi2: inprodi2,
                acmroiqttr: acmroiqttr,
                acmroiqt: acmroiqt,
                acmroinp: acmroinp,
                acmroing: acmroing
            });
        });

        $.ajax({
            url: '/recepcionar',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                data: data,
                hasPendingItems: hasPendingItems
            },
            success: function(response) {
                alert('Recepción guardada exitosamente.');
                location.reload();
            },
            error: function() {
                alert('Ocurrió un error al guardar la recepción.');
            }
        });
    });

    // Resetear formulario
    $('#resetButton').click(function() {
        $('input[type="number"]').val('');
    });

    // Editar Precio
    $('.edit-price').click(function() {
        var id = $(this).data('id');
        var newPrice = prompt('Ingrese el nuevo precio unitario:');
        if (newPrice) {
            $('input[name="acmroinp[]"][data-id="' + id + '"]').val(newPrice);
        }
    });
});
