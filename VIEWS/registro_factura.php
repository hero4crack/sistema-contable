<form action="../BACKEND/procesar_factura.php" method="POST" class="form-container">
    <div class="row">
        <div class="field">
            <label>Empresa:</label>
            <select name="id_empresa" required>
                </select>
        </div>
        
        <div class="field">
            <label>Nro Factura:</label>
            <input type="text" name="nro_factura" required>
        </div>
    </div>

    <div class="row">
        <div class="field">
            <label>Base Imponible ($):</label>
            <input type="number" step="0.01" name="base_imponible" id="base" oninput="calcularIVA()" required>
        </div>
        <div class="field">
            <label>IVA (16%):</label>
            <input type="number" step="0.01" name="monto_iva" id="iva" readonly>
        </div>
    </div>
    
    <button type="submit" class="primary-btn">Guardar Factura</button>
</form>

<script>
// Un pequeño script para ayudar al usuario calculando el IVA automáticamente
function calcularIVA() {
    let base = document.getElementById('base').value;
    let iva = base * 0.16;
    document.getElementById('iva').value = iva.toFixed(2);
}
</script>