<?php
// BACKEND/obtener_proveedor.php
// Usamos "conecxion_bd.php" con tu ortografía exacta para evitar fallos de inclusión
require_once 'conecxion_bd.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Consulta limpia para buscar las columnas del proveedor por su ID
    $sql = "SELECT * FROM proveedores WHERE id_proveedor = $id LIMIT 1";
    $resultado = $conexion->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        $p = $resultado->fetch_assoc();
        
        // Retornamos la estructura HTML optimizada para el diseño de tu sistema
        ?>
        <div class="row g-3">
            <div class="col-md-4">
                <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.75rem;">RIF Fiscal</small>
                <span class="fs-5 fw-bold text-dark border-bottom d-block pb-1"><?php echo $p['rif']; ?></span>
            </div>
            <div class="col-md-8">
                <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.75rem;">Razón Social</small>
                <span class="fs-5 fw-semibold text-primary border-bottom d-block pb-1"><?php echo htmlspecialchars($p['razon_social']); ?></span>
            </div>
            
            <div class="col-md-6 mt-3">
                <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.75rem;">Nombre Comercial</small>
                <span class="text-dark border-bottom d-block pb-1"><?php echo !empty($p['nombre_comercial']) ? htmlspecialchars($p['nombre_comercial']) : '<i>No registrado</i>'; ?></span>
            </div>
            <div class="col-md-3 mt-3">
                <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.75rem;">Contribuyente</small>
                <span class="badge bg-dark bg-opacity-10 text-dark border-0 px-2 mt-1 d-inline-block"><?php echo $p['tipo_contribuyente']; ?></span>
            </div>
            <div class="col-md-3 mt-3">
                <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.75rem;">Porcentaje Retención</small>
                <span class="fs-5 fw-bold text-danger border-bottom d-block pb-1"><?php echo $p['porcentaje_retencion']; ?>%</span>
            </div>

            <div class="col-12 mt-4">
                <h6 class="text-secondary border-bottom pb-1 fw-bold"><i class="fas fa-map-marked-alt me-2"></i>Domicilio Fiscal</h6>
                <p class="bg-light p-3 border rounded text-dark mb-0" style="font-size: 0.95rem; line-height: 1.5; background-color: #f8fafc !important;">
                    <?php echo htmlspecialchars($p['direccion_fiscal']); ?>
                </p>
            </div>

            <div class="col-md-6 mt-4">
                <h6 class="text-secondary border-bottom pb-1 fw-bold"><i class="fas fa-phone-alt me-2"></i>Contacto Corporativo</h6>
                <table class="table table-sm table-borderless mt-2" style="font-size: 0.9rem;">
                    <tr>
                        <td class="text-muted fw-semibold" style="width: 35%;">Teléfono:</td>
                        <td class="text-dark fw-bold"><?php echo !empty($p['telefono']) ? $p['telefono'] : 'No asignado'; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Correo:</td>
                        <td class="text-dark" style="word-break: break-all;"><?php echo !empty($p['correo_electronico']) ? $p['correo_electronico'] : 'No asignado'; ?></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6 mt-4">
                <h6 class="text-secondary border-bottom pb-1 fw-bold"><i class="fas fa-user-check me-2"></i>Asesor de Ventas o Contacto</h6>
                <table class="table table-sm table-borderless mt-2" style="font-size: 0.9rem;">
                    <tr>
                        <td class="text-muted fw-semibold" style="width: 35%;">Nombre:</td>
                        <td class="text-dark fw-semibold"><?php echo !empty($p['nombre_contacto']) ? htmlspecialchars($p['nombre_contacto']) : 'No asignado'; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Teléfono Directo:</td>
                        <td class="text-dark fw-bold"><?php echo !empty($p['telefono_contacto']) ? $p['telefono_contacto'] : 'No asignado'; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    } else {
        echo '<div class="alert alert-danger shadow-sm mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Error: El expediente de este proveedor no pudo ser localizado en la base de datos.</div>';
    }
} else {
    echo '<div class="alert alert-warning shadow-sm mb-0"><i class="fas fa-ban me-2"></i>Petición inválida: No se proporcionó un identificador de proveedor válido.</div>';
}
$conexion->close();
?>