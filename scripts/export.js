function exportToCSV() {
    // Get table element
    var table = document.getElementById("example1");

    // Initialize empty string to hold CSV content
    var csvContent = "";

    // Get table headers excluding those with the "export-ignore" class
    var headers = [];
    table.querySelectorAll("thead th").forEach(function (th, index) {
        if (!th.classList.contains("export-ignore")) {
            headers.push(th.textContent.trim());
        }
    });
    // Append headers to CSV content
    csvContent += headers.join(",") + "\n";

    // Loop through each row in the table body
    var rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
    for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName("td");
        var rowData = [];
        // Loop through each cell in the row
        for (var j = 0; j < cells.length; j++) {
            // Skip cells with the "export-ignore" class
            if (!cells[j].classList.contains("export-ignore")) {
                // Append cell value to rowData array, ensuring it is properly escaped
                var cellValue = cells[j].textContent.trim().replace(/"/g, '""'); // Handle double quotes by escaping them
                rowData.push('"' + cellValue + '"'); // Enclose cell value in double quotes to handle special characters
            }
        }
        // Append rowData to CSV content, join with commas
        csvContent += rowData.join(",") + "\n";
    }

    // Create a blob object from CSV content with UTF-8 encoding
    var blob = new Blob(["\uFEFF" + csvContent], {
        type: "text/csv;charset=utf-8"
    });

    // Create a temporary anchor element to trigger download
    var link = document.createElement("a");
    var url = URL.createObjectURL(blob);
    link.href = url;
    link.download = "ticket.csv";

    // Append anchor to document body and trigger download
    document.body.appendChild(link);
    link.click();

    // Cleanup
    setTimeout(function () {
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    }, 0);
}

function exportToExcel() {
    // Create a new HTML table with only the relevant columns
    var exportTable = document.createElement('table');
    var exportTableBody = document.createElement('tbody');

    // Get the header row from the HTML table
    var headerRow = document.querySelector('#example1 thead tr');

    // Create a new row for the export table and add the header cells
    var exportHeaderRow = document.createElement('tr');
    headerRow.querySelectorAll('th').forEach(function (cell) {
        if (!cell.classList.contains('export-ignore')) { // Check for exclusion class
            var exportCell = document.createElement('td');
            exportCell.textContent = cell.textContent;
            exportCell.style.border = '1px solid #000'; // Add tiny border
            exportCell.style.padding = '4px'; // Add padding for readability
            exportHeaderRow.appendChild(exportCell);
        }
    });
    exportTableBody.appendChild(exportHeaderRow);

    // Iterate over each row of the HTML table and add the data rows
    var tableRows = document.querySelectorAll('#example1 tbody tr');
    tableRows.forEach(function (row) {
        // Create a new row for the export table
        var exportRow = document.createElement('tr');

        // Iterate over each cell of the row and create corresponding cells in the export table
        row.querySelectorAll('td').forEach(function (cell, index) {
            if (!cell.classList.contains('export-ignore')) { // Check for exclusion class
                var exportCell = document.createElement('td');
                exportCell.textContent = cell.textContent;
                exportCell.style.border = '1px solid #000'; // Add tiny border
                exportCell.style.padding = '4px'; // Add padding for readability
                exportRow.appendChild(exportCell);
            }
        });

        // Append the row to the export table body
        exportTableBody.appendChild(exportRow);
    });

    // Append the table body to the export table
    exportTable.appendChild(exportTableBody);

    // Create a Blob object containing the HTML table
    var blob = new Blob(['\ufeff', exportTable.outerHTML], {
        type: 'application/vnd.ms-excel'
    });

    // Create a link element to download the Blob
    var url = URL.createObjectURL(blob);
    var a = document.createElement("a");
    a.href = url;
    a.download = "ticket.xls";
    document.body.appendChild(a);
    a.click();

    // Cleanup
    setTimeout(function () {
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }, 0);
}

function exportToPDF() {
    // Create new jsPDF instance
    const {
        jsPDF
    } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'landscape'
    });

    // Include a Khmer font (Nokora) from Google Fonts
    var fontUrl = 'https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@100..900&display=swap';
    var head = document.getElementsByTagName('head')[0];
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = fontUrl;
    head.appendChild(link);

    // Modify the table to exclude certain columns based on the export-ignore class
    const filteredHtml = $('#example1').clone().find('.export-ignore').remove().end()[0];

    // Use autoTable to export modified HTML table to PDF
    doc.autoTable({
        html: filteredHtml,
        startY: 20,
        headStyles: {
            fillColor: [22, 160, 133],
            textColor: [255, 255, 255],
            fontStyle: 'bold',
            fontSize: 6,
            halign: 'center'
        },
        bodyStyles: {
            fillColor: [238, 238, 238],
            textColor: [0, 0, 0],
            fontSize: 6,
            halign: 'center'
        },
        alternateRowStyles: {
            fillColor: [255, 255, 255]
        },
        margin: {
            top: 30
        },
        theme: 'grid'
    });

    // Save PDF
    doc.save("ticket.pdf");
}