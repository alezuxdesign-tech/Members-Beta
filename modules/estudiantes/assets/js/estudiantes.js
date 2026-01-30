jQuery(document).ready(function ($) {
    var timer;

    $('.alezux-estudiantes-search input').on('keyup', function () {
        var $input = $(this);
        var $wrapper = $input.closest('.alezux-estudiantes-wrapper');
        var $tbody = $wrapper.find('.alezux-estudiantes-table tbody');
        var query = $input.val();

        clearTimeout(timer);

        timer = setTimeout(function () {
            $wrapper.addClass('loading');

            $.ajax({
                url: alezux_estudiantes_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_search_students',
                    nonce: alezux_estudiantes_vars.nonce,
                    search: query
                },
                success: function (response) {
                    if (response.success) {
                        renderTableRows($tbody, response.data);
                    } else {
                        console.error(response.data.message);
                    }
                    $wrapper.removeClass('loading');
                },
                error: function () {
                    console.error('Error en la petición AJAX');
                    $wrapper.removeClass('loading');
                }
            });
        }, 500); // Debounce de 500ms
    });

    function renderTableRows($tbody, students) {
        $tbody.empty();

        if (students.length === 0) {
            $tbody.append('<tr><td colspan="5" style="text-align:center; padding: 20px;">No se encontraron resultados.</td></tr>');
            return;
        }

        students.forEach(function (student) {
            var row = `
                <tr>
                    <td class="col-foto">
                        <img src="${student.avatar_url}" alt="${student.name}">
                    </td>
                    <td class="col-nombre">
                        ${student.name}
                        <div style="font-size: 12px; color: #999;">@${student.username}</div>
                    </td>
                    <td class="col-correo">
                        ${student.email}
                    </td>
                    <td class="col-estado">
                        <span class="${student.status_class}">
                            <i class="fa fa-circle" style="font-size: 8px; margin-right: 4px;"></i>
                            ${student.status_label}
                        </span>
                    </td>
                    <td class="col-funciones">
                        <button class="btn-gestionar" data-student-id="${student.id}">
                            <i class="fa fa-cog"></i> Gestionar
                        </button>
                    </td>
                </tr>
            `;
            $tbody.append(row);
        });
    }

    // Funcionalidad placeholder para botón gestionar (delegación de eventos para items dinámicos)
    $(document).on('click', '.alezux-estudiantes-table .btn-gestionar', function (e) {
        e.preventDefault();
        var studentId = $(this).data('student-id');
        alert('Gestionar estudiante ID: ' + studentId);
    });
});
