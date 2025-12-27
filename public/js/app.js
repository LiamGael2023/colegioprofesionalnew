/**
 * Sistema de Gestión de Colegio Profesional
 * JavaScript Principal
 */

// Auto-ocultar alerts después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// Validación de formularios
function validarDNI(input) {
    const dni = input.value.trim();
    if (dni.length !== 8 || !/^\d+$/.test(dni)) {
        input.setCustomValidity('El DNI debe tener 8 dígitos');
    } else {
        input.setCustomValidity('');
    }
}

// Confirmación de eliminación
function confirmarEliminacion(mensaje) {
    return confirm(mensaje || '¿Está seguro de que desea eliminar este registro?');
}

// Formatear moneda
function formatMoney(amount) {
    return 'S/. ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Calcular total de checkboxes seleccionados
function calcularTotal() {
    const checkboxes = document.querySelectorAll('.cuota-check:checked');
    let total = 0;

    checkboxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const montoCell = row.querySelector('td:nth-child(3)');
        if (montoCell) {
            const monto = parseFloat(montoCell.textContent.replace(/[^\d.]/g, ''));
            total += monto;
        }
    });

    const totalElement = document.getElementById('total-pago');
    if (totalElement) {
        totalElement.textContent = formatMoney(total);
    }
}

// Event listeners para cuotas
document.addEventListener('DOMContentLoaded', function() {
    const cuotaChecks = document.querySelectorAll('.cuota-check');
    cuotaChecks.forEach(check => {
        check.addEventListener('change', calcularTotal);
    });

    // Select All checkbox
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            cuotaChecks.forEach(check => {
                check.checked = this.checked;
            });
            calcularTotal();
        });
    }

    // Validación de formularios de pago
    const formPago = document.getElementById('formPago');
    if (formPago) {
        formPago.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.cuota-check:checked').length;
            if (checked === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos una cuota para procesar el pago');
                return false;
            }
        });
    }
});

// Imprimir recibo
function imprimirRecibo() {
    window.print();
}

// Búsqueda en tablas
function filtrarTabla(input, tabla) {
    const filter = input.value.toUpperCase();
    const rows = tabla.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        let found = false;
        const cells = rows[i].getElementsByTagName('td');

        for (let j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toUpperCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }

        rows[i].style.display = found ? '' : 'none';
    }
}

// Validación de fechas
function validarRangoFechas(desde, hasta) {
    const fechaDesde = new Date(desde);
    const fechaHasta = new Date(hasta);

    if (fechaHasta < fechaDesde) {
        alert('La fecha hasta no puede ser anterior a la fecha desde');
        return false;
    }

    return true;
}

// AJAX helper
async function fetchJSON(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            ...options
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Error en la petición');
        }

        return data;
    } catch (error) {
        console.error('Error:', error);
        alert(error.message || 'Ocurrió un error en la petición');
        throw error;
    }
}

// Loading spinner
function showLoading() {
    const loader = document.createElement('div');
    loader.id = 'loading-spinner';
    loader.innerHTML = '<div class="spinner">Cargando...</div>';
    loader.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;justify-content:center;align-items:center;z-index:9999;';
    document.body.appendChild(loader);
}

function hideLoading() {
    const loader = document.getElementById('loading-spinner');
    if (loader) {
        loader.remove();
    }
}

// Export to CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');

    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');

        for (let j = 0; j < cols.length; j++) {
            row.push('"' + cols[j].textContent.replace(/"/g, '""') + '"');
        }

        csv.push(row.join(','));
    }

    downloadCSV(csv.join('\n'), filename);
}

function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], { type: 'text/csv' });
    const downloadLink = document.createElement('a');

    downloadLink.download = filename || 'export.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';

    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
