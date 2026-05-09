let filaCount = 0;

function agregarFila() {
    filaCount++;
    const cuerpo = document.getElementById('cuerpoAsiento');
    const nuevaFila = document.createElement('tr');
    nuevaFila.id = `fila_${filaCount}`;
    
    // NOTA: Para que el SELECT de cuentas funcione desde un archivo .js puro,
    // lo ideal es que el HTML del select ya esté definido o lo pasemos como plantilla.
    // Por ahora, usaremos una solución estándar.
    
    nuevaFila.innerHTML = `
        <td>
            <select name="id_cuentas" class="form-select cuenta-select" required>
                <option value="">Cargando cuentas...</option>
            </select>
        </td>
        <td><input type="number" step="0.01" name="debe" class="form-control input-monto" value="0.00" oninput="validarAsiento()"></td>
        <td><input type="number" step="0.01" name="haber" class="form-control input-monto" value="0.00" oninput="validarAsiento()"></td>
        <td class="text-center">
            <button type="button" class="btn btn-link text-danger" onclick="eliminarFila(${filaCount})"><i class="fas fa-trash"></i></button>
        </td>
    `;
    cuerpo.appendChild(nuevaFila);
    actualizarOpcionesCuentas(nuevaFila.querySelector('.cuenta-select'));
}

function eliminarFila(id) {
    document.getElementById(`fila_${id}`).remove();
    validarAsiento();
}

function validarAsiento() {
    let tDebe = 0;
    let tHaber = 0;
    
    document.querySelectorAll('input[name="debe"]').forEach(input => tDebe += parseFloat(input.value || 0));
    document.querySelectorAll('input[name="haber"]').forEach(input => tHaber += parseFloat(input.value || 0));

    document.getElementById('totalDebe').innerText = tDebe.toFixed(2);
    document.getElementById('totalHaber').innerText = tHaber.toFixed(2);

    const btn = document.getElementById('btnGuardarAsiento');
    const status = document.getElementById('statusCuadrado');

    if (tDebe.toFixed(2) === tHaber.toFixed(2) && tDebe > 0) {
        btn.disabled = false;
        status.innerHTML = '<i class="fas fa-check-circle"></i> Asiento Cuadrado';
        status.className = "me-auto text-success";
    } else {
        btn.disabled = true;
        status.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Asiento Descuadrado';
        status.className = "me-auto text-danger";
    }
}