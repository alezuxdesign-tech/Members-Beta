jQuery(document).ready(function ($) {

    // Initialize Logros View Widget
    if ($('.alezux-view-logros-wrapper').length) {
        loadLogros();

        // Search and Filter Events
        $('#alezux-logro-search').on('keyup', function () {
            delay(function () {
                loadLogros();
            }, 500);
        });

        $('#alezux-logro-course-filter').on('change', function () {
            loadLogros();
        });

        // Delete Event
        $(document).on('click', '.alezux-delete-logro', function (e) {
            e.preventDefault();
            if (confirm('¿Estás seguro de que deseas eliminar este logro?')) {
                var id = $(this).data('id');
                deleteLogro(id);
            }
        });

        // Edit Event - Open Modal
        $(document).on('click', '.alezux-edit-logro', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            openEditModal(id);
        });

        // Close Modal
        $('.alezux-modal-close').on('click', function () {
            $('#alezux-logro-edit-modal').fadeOut();
        });

        // Save Edit Event
        $('#alezux-logro-edit-form').on('submit', function (e) {
            e.preventDefault();
            updateLogro();
        });
    }

    var delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    function loadLogros() {
        var container = $('#alezux-logros-table-container');
        var search = $('#alezux-logro-search').val();
        var course_id = $('#alezux-logro-course-filter').val();

        container.html('<div class="alezux-loading">Cargando registros...</div>');

        $.ajax({
            url: alezux_logros_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_get_achievements',
                nonce: alezux_logros_vars.nonce,
                search: search,
                course_id: course_id
            },
            success: function (response) {
                if (response.success) {
                    renderTable(response.data);
                } else {
                    container.html('<div class="alezux-error">' + response.data.message + '</div>');
                }
            },
            error: function () {
                container.html('<div class="alezux-error">Error al cargar los registros.</div>');
            }
        });
    }

    function renderTable(data) {
        var container = $('#alezux-logros-table-container');

        if (data.length === 0) {
            container.html('<div class="alezux-no-results">No se encontraron logros.</div>');
            return;
        }

        var html = '<table class="alezux-logros-table">';
        html += '<thead><tr>';
        html += '<th>ID</th>';
        html += '<th>Curso</th>';
        html += '<th>Estudiante</th>';
        html += '<th>Mensaje</th>';
        html += '<th>Fecha</th>';
        html += '<th>Imágen</th>'
        html += '<th>Acciones</th>';
        html += '</tr></thead>';
        html += '<tbody>';

        $.each(data, function (index, item) {
            html += '<tr>';
            html += '<td>' + item.id + '</td>';
            html += '<td>' + item.course_title + '</td>';
            html += '<td>' + item.student_name + ' (' + item.student_email + ')</td>';
            html += '<td>' + item.message + '</td>';
            html += '<td>' + item.created_at + '</td>';
            html += '<td>' + item.image_id + '</td>';
            html += '<td class="alezux-actions-cell">';
            html += '<button class="alezux-btn-icon alezux-edit-logro" data-id="' + item.id + '" title="Editar"><i class="fa fa-pencil"></i></button>';
            html += '<button class="alezux-btn-icon alezux-btn-danger alezux-delete-logro" data-id="' + item.id + '" title="Eliminar"><i class="fa fa-trash"></i></button>';
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        container.html(html);
    }

    function deleteLogro(id) {
        $.ajax({
            url: alezux_logros_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_delete_achievement',
                nonce: alezux_logros_vars.nonce,
                id: id
            },
            success: function (response) {
                if (response.success) {
                    loadLogros(); // Reload table
                } else {
                    alert(response.data.message);
                }
            }
        });
    }

    function openEditModal(id) {
        var modal = $('#alezux-logro-edit-modal');

        // Cargar datos
        $.ajax({
            url: alezux_logros_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_get_achievement',
                nonce: alezux_logros_vars.nonce,
                id: id
            },
            success: function (response) {
                if (response.success) {
                    var data = response.data;
                    $('#edit-logro-id').val(data.id);
                    $('#edit-course-id').val(data.course_id);
                    $('#edit-student-id').val(data.student_id);
                    $('#edit-message').val(data.message);
                    $('#edit-image-id').val(data.image_id);

                    modal.fadeIn();
                } else {
                    alert(response.data.message);
                }
            }
        });
    }

    function updateLogro() {
        var form = $('#alezux-logro-edit-form');
        var formData = form.serialize();

        // Add action and nonce
        formData += '&action=alezux_update_achievement&nonce=' + alezux_logros_vars.nonce;

        $.ajax({
            url: alezux_logros_vars.ajax_url,
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    $('#alezux-logro-edit-modal').fadeOut();
                    loadLogros(); // Reload table
                    alert('Logro actualizado correctamente.');
                } else {
                    alert(response.data.message);
                }
            }
        });
    }

});
