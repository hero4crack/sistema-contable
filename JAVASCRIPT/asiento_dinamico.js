let filaCount = 0;
function agregarFila() {
    const cuerpo = document.getElementById('cuerpoAsiento');
    const tr = document.createElement('tr');

    tr.innerHTML = `
        <td>
            <select name="cuentas[]" class="form-select select-cuenta" required>
                <option value="">Cargando cuentas...</option>
            </select>
        </td>
        <td>
            <input type="number" name="debe[]" class="form-control valor-debe" step="0.01" value="0.00" oninput="calcularTotales()">
        </td>
        <td>
            <input type="number" name="haber[]" class="form-control valor-haber" step="0.01" value="0.00" oninput="calcularTotales()">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarFila(this)">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `;

    cuerpo.appendChild(tr);

    // Buscamos el select que acabamos de crear para llenarlo con las cuentas
    const nuevoSelect = tr.querySelector('.select-cuenta');
    actualizarOpcionesCuentas(nuevoSelect); // Esta función está en tu PHP de asientos_diario
    calcularTotales();
}

function eliminarFila(boton) {
    const fila = boton.closest('tr');
    if (document.querySelectorAll('#cuerpoAsiento tr').length > 1) {
        fila.remove();
        calcularTotales();
    } else {
        alert("El asiento debe tener al menos una línea.");
    }
}

function calcularTotales() {
    let totalDebe = 0;
    let totalHaber = 0;

    document.querySelectorAll('.valor-debe').forEach(input => {
        totalDebe += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('.valor-haber').forEach(input => {
        totalHaber += parseFloat(input.value) || 0;
    });

    document.getElementById('totalDebe').innerText = totalDebe.toFixed(2);
    document.getElementById('totalHaber').innerText = totalHaber.toFixed(2);

    const status = document.getElementById('statusCuadrado');
    const btn = document.getElementById('btnGuardarAsiento');

    // Validación de cuadre contable (permitimos diferencia mínima de 0.01 por decimales)
    if (Math.abs(totalDebe - totalHaber) < 0.01 && totalDebe > 0) {
        status.innerText = "Asiento Cuadrado";
        status.className = "me-auto text-success fw-bold";
        btn.disabled = false;
    } else {
        status.innerText = "Asiento Descuadrado";
        status.className = "me-auto text-danger fw-bold";
        btn.disabled = true;
    }
}