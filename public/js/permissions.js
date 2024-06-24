document.addEventListener("DOMContentLoaded", function () {
    document
        .getElementById("searchPermission")
        .addEventListener("input", filterPermissions);

    function filterPermissions() {
        let searchTerm = document
            .getElementById("searchPermission")
            .value.toLowerCase();
        let permissionsTable = document.getElementById("dataTable");
        let rows = permissionsTable.querySelectorAll("tbody tr");

        rows.forEach((row) => {
            let name = row
                .querySelector("td:nth-child(1)")
                .textContent.toLowerCase();
            let description = row
                .querySelector("td:nth-child(2)")
                .textContent.toLowerCase();

            if (name.includes(searchTerm) || description.includes(searchTerm)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
});
