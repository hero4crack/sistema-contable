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