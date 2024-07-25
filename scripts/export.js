function exportToCSV() {
    var table = document.getElementById("tableTicket");
    var csvContent = "";
    var headers = [];

    table.querySelectorAll("thead th").forEach(function (th, index) {
        if (!th.classList.contains("export-ignore")) {
            headers.push(th.textContent.trim());
        }
    });
    csvContent += headers.join(",") + "\n";

    var rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
    for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName("td");
        var rowData = [];
        for (var j = 0; j < cells.length; j++) {
            if (!cells[j].classList.contains("export-ignore")) {
                var cellValue = cells[j].textContent.trim().replace(/"/g, '""');
                rowData.push('"' + cellValue + '"');
            }
        }
        csvContent += rowData.join(",") + "\n";
    }

    var blob = new Blob(["\uFEFF" + csvContent], {
        type: "text/csv;charset=utf-8"
    });

    var link = document.createElement("a");
    var url = URL.createObjectURL(blob);
    link.href = url;
    link.download = "table.csv";
    document.body.appendChild(link);
    link.click();

    setTimeout(function () {
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    }, 0);
}

function exportToExcel() {
    var exportTable = document.createElement('table');
    var exportTableBody = document.createElement('tbody');
    var headerRow = document.querySelector('#tableTicket thead tr');
    var exportHeaderRow = document.createElement('tr');

    headerRow.querySelectorAll('th').forEach(function (cell) {
        if (!cell.classList.contains('export-ignore')) {
            var exportCell = document.createElement('td');
            exportCell.textContent = cell.textContent;
            exportCell.style.border = '1px solid #000';
            exportCell.style.padding = '4px';
            exportHeaderRow.appendChild(exportCell);
        }
    });
    exportTableBody.appendChild(exportHeaderRow);

    var tableRows = document.querySelectorAll('#tableTicket tbody tr');
    tableRows.forEach(function (row) {
        var exportRow = document.createElement('tr');
        row.querySelectorAll('td').forEach(function (cell, index) {
            if (!cell.classList.contains('export-ignore')) {
                var exportCell = document.createElement('td');
                exportCell.textContent = cell.textContent;
                exportCell.style.border = '1px solid #000';
                exportCell.style.padding = '4px';
                exportRow.appendChild(exportCell);
            }
        });
        exportTableBody.appendChild(exportRow);
    });

    exportTable.appendChild(exportTableBody);

    var blob = new Blob(['\ufeff', exportTable.outerHTML], {
        type: 'application/vnd.ms-excel'
    });

    var url = URL.createObjectURL(blob);
    var a = document.createElement("a");
    a.href = url;
    a.download = "ticket.xls";
    document.body.appendChild(a);
    a.click();

    setTimeout(function () {
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }, 0);
}
async function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const response = await fetch('fontBase64.txt');
    const fontBase64 = await response.text();

    const doc = new jsPDF('landscape');
    doc.addFileToVFS('NotoSerifKhmer-Regular.ttf', fontBase64);
    doc.addFont('NotoSerifKhmer-Regular.ttf', 'Noto Serif Khmer', 'normal');
    doc.setFont('Noto Serif Khmer');
    const filteredHtml = $('#tableTicket').clone().find('.export-ignore').remove().end()[0];
    doc.autoTable({
        html: filteredHtml,
        styles: { font: 'Noto Serif Khmer', fontSize: 6, cellPadding: 2 },
        startY: 0,
        headStyles: {
            fillColor: [22, 160, 133],
            textColor: [255, 255, 255],
            fontStyle: 'bold',
            fontSize: 8,
            halign: 'center'
        },
        bodyStyles: {
            fillColor: [238, 238, 238],
            textColor: [0, 0, 0],
            fontSize: 8,
            halign: 'center'
        },
        alternateRowStyles: {
            fillColor: [255, 255, 255]
        },
        margin: {
            top: 0,
            bottom: 0,
            left: 0,
            right: 0
        },
        theme: 'grid'
    });

    const pdfData = doc.output('blob');
    const pdfUrl = URL.createObjectURL(pdfData);

    const newWindow = window.open();
    newWindow.document.write(`<iframe width="100%" height="100%" src="${pdfUrl}"></iframe>`);
    newWindow.document.write(`<a href="${pdfUrl}" download="table.pdf">Download PDF</a>`);
}