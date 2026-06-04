<script>
    // FUNCIÓN: Alterna la visibilidad de los libros en caliente
    function cambiarLibro(tipo) {
        const tabVentas = document.getElementById('btnTabVentas');
        const tabCompras = document.getElementById('btnTabCompras');
        const contenedorVentas = document.getElementById('contenedorVentas');
        const contenedorCompras = document.getElementById('contenedorCompras');

        if (tipo === 'VENTAS') {
            tabVentas.classList.add('active-tab');
            tabCompras.classList.remove('active-tab');
            contenedorVentas.classList.remove('d-none');
            contenedorCompras.classList.add('d-none');
        } else {
            tabCompras.classList.add('active-tab');
            tabVentas.classList.remove('active-tab');
            contenedorCompras.classList.remove('d-none');
            contenedorVentas.classList.add('d-none');
        }
    }

    function calcularTotalesModal() {
        const base = parseFloat(document.getElementById('base_modal').value) || 0;
        const exento = parseFloat(document.getElementById('exento_modal').value) || 0;
        const iva = base * 0.16;
        const total = base + exento + iva;

        document.getElementById('iva_modal').value = iva.toFixed(2);
        document.getElementById('total_modal_hidden').value = total.toFixed(2);
        document.getElementById('iva_total_visual').value = total.toLocaleString('es-VE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + " Bs.";
    }

    function alternarTerceros() {
        const tipo = document.getElementById('tipo_transaccion').value;
        const grupoCliente = document.getElementById('grupo_cliente');
        const grupoProveedor = document.getElementById('grupo_proveedor');
        const grupoComprobante = document.getElementById('grupo_comprobante');
        const selectCliente = document.getElementById('id_empresa');
        const selectProveedor = document.getElementById('id_proveedor');
        const inputComprobante = document.getElementById('nro_comprobante_retencion');

        if (tipo === 'COMPRA') {
            grupoCliente.classList.add('d-none');
            grupoProveedor.classList.remove('d-none');
            grupoComprobante.classList.remove('d-none');
            selectCliente.required = false;
            selectProveedor.required = true;
        } else {
            grupoProveedor.classList.add('d-none');
            grupoCliente.classList.remove('d-none');
            grupoComprobante.classList.add('d-none');
            selectProveedor.required = false;
            selectCliente.required = true;
            inputComprobante.value = ""; // Se limpia al pasar a Venta
        }
    }

    function limpiarFormularioNuevaFactura() {
        document.getElementById('formFactura').action = "../BACKEND/guardar_factura.php";
        document.getElementById('tituloModal').innerHTML = '<i class="fas fa-file-invoice me-2"></i> Nueva Factura Fiscal';
        document.getElementById('edit_id_factura').value = "";
        document.getElementById('fecha_documento').value = "<?php echo date('Y-m-d'); ?>";
        document.getElementById('tipo_transaccion').value = "VENTA";
        document.getElementById('nro_factura').value = "";
        document.getElementById('nro_control').value = "";
        document.getElementById('nro_comprobante_retencion').value = "";
        document.getElementById('base_modal').value = "";
        document.getElementById('exento_modal').value = "0.00";
        document.getElementById('id_empresa').value = "";
        document.getElementById('id_proveedor').value = "";

        const btn = document.getElementById('btnSubmitModal');
        btn.className = "btn btn-success";
        btn.style.background = "#10b981";
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Procesar Factura';

        alternarTerceros();
        calcularTotalesModal();
    }

    function editarFactura(id) {
        fetch(`../BACKEND/obtener_factura_json.php?id=${id}`)
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    const f = res.data;

                    document.getElementById('formFactura').action = "../BACKEND/modificar_factura.php";
                    document.getElementById('tituloModal').innerHTML = '<i class="fas fa-edit me-2"></i> Modificar Factura Fiscal';

                    document.getElementById('edit_id_factura').value = f.id_factura;
                    document.getElementById('fecha_documento').value = f.fecha_documento;
                    document.getElementById('tipo_transaccion').value = f.tipo_transaccion;
                    document.getElementById('nro_factura').value = f.nro_factura;
                    document.getElementById('nro_control').value = f.nro_control;
                    document.getElementById('base_modal').value = f.base_imponible;
                    document.getElementById('nro_comprobante_retencion').value = f.nro_comprobante_retencion || "";

                    alternarTerceros();

                    if (f.tipo_transaccion === 'COMPRA') {
                        document.getElementById('id_proveedor').value = f.id_tercero;
                        document.getElementById('id_empresa').value = "";
                        document.getElementById('exento_modal').value = "0.00";
                        cambiarLibro('COMPRAS');
                    } else {
                        document.getElementById('id_empresa').value = f.id_empresa;
                        document.getElementById('id_proveedor').value = "";
                        document.getElementById('exento_modal').value = f.monto_exento;
                        cambiarLibro('VENTAS');
                    }

                    calcularTotalesModal();

                    const btn = document.getElementById('btnSubmitModal');
                    btn.className = "btn btn-warning text-white";
                    btn.style.background = "#f59e0b";
                    btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Actualizar Factura';

                    const modal = new bootstrap.Modal(document.getElementById('modalRegistro'));
                    modal.show();
                } else {
                    alert("Error: " + res.message);
                }
            })
            .catch(error => {
                console.error("Error en AJAX:", error);
                alert("No se pudieron extraer los datos del documento fiscal.");
            });
    }
</script>

<script src="../JQUERY/jquery.js"></script>
<script src="../DATATABLE/datatables1.js"></script>

<script>
    $(document).ready(function() {
        $('#tabla').DataTable({
            lengthMenu: [5, 10, 25, 50],
            pageLength: 10,
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                }
            }
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('#tabla2').DataTable({
            lengthMenu: [5, 10, 25, 50],
            pageLength: 10,
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                }
            }
        });
    });
</script>

<script>
    document.getElementById('userMenu').addEventListener('click', function(e) {
        // Previene que se cierre si haces clic dentro del menú
        e.stopPropagation();
        this.classList.toggle('active');
    });

    // Cierra el menú si haces clic fuera de él
    window.addEventListener('click', function() {
        document.getElementById('userMenu').classList.remove('active');
    });
</script>