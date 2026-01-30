jQuery(document).ready(function ($) {
    var timer;

    $('.alezux-estudiantes-search input').on('keyup', function () {
        var $input = $(this);
        var $wrapper = $input.closest('.alezux-estudiantes-wrapper');
        var $tbody = $wrapper.find('.alezux-estudiantes-table tbody');
        var query = $input.val();

        clearTimeout(timer);

        ```javascript
jQuery(document).ready(function($) {
    var searchTimer;
    var ajax_url = alezux_estudiantes_vars.ajax_url;
    var nonce    = alezux_estudiantes_vars.nonce;
    var $wrapper = $('.alezux-estudiantes-wrapper');
    var $tableBody = $wrapper.find('.alezux-estudiantes-table tbody');
    var $pagination = $wrapper.find('.alezux-estudiantes-pagination');
    var limit = $wrapper.data('limit') || 10;
    
    // Estado inicial
    var currentPage = 1;
    var totalPages = $pagination.data('total-pages') || 1;

    // Renderizar paginación inicial si es necesario
    renderPagination(currentPage, totalPages);

    // Evento Search
    $('.alezux-estudiantes-search input').on('keyup', function() {
        var query = $(this).val();
        clearTimeout(searchTimer);

        searchTimer = setTimeout(function() {
            currentPage = 1; // Resetear a página 1 en nueva búsqueda
            loadStudents(query, currentPage);
        }, 500); 
    });

    // Evento Click Paginación
    $wrapper.on('click', '.page-link', function(e) {
        e.preventDefault();
        if ($(this).hasClass('disabled') || $(this).hasClass('active')) return;

        var page = $(this).data('page');
        var query = $('.alezux-estudiantes-search input').val();
        
        loadStudents(query, page);
    });

    // Cargar estudiantes inicialmente
    loadStudents($('.alezux-estudiantes-search input').val(), currentPage);

    function loadStudents(query, page) {
        $wrapper.addClass('loading');

        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_search_students',
                nonce: nonce,
                search: query,
                page: page,
                limit: limit
            },
            success: function(response) {
                if (response.success) {
                    renderTable(response.data.students);
                    currentPage = response.data.current_page;
                    totalPages = response.data.total_pages;
                    renderPagination(currentPage, totalPages);
                } else {
                    console.log('Error:', response);
                }
                $wrapper.removeClass('loading');
            },
            error: function() {
                alert('Error al buscar estudiantes');
                $wrapper.removeClass('loading');
            }
        });
    }

    function renderTable(students) {
        $tableBody.empty();

        if (students.length === 0) {
            $tableBody.append(
                '<tr><td colspan="5" style="text-align:center; padding: 20px;">No se encontraron estudiantes.</td></tr>'
            );
            return;
        }

        $.each(students, function(index, student) {
            var row = `
            < tr >
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
                </tr >
            `;
            $tableBody.append(row);
        });
    }

    function renderPagination(current, total) {
        $pagination.empty();
        if (total <= 1) return;

        var html = '';

        // Previous Button
        var prevDisabled = current === 1 ? 'disabled' : '';
        html += `< span class="page-link prev ${prevDisabled}" data - page="${current - 1}" > <i class="fa fa-angle-left"></i> Previous</span > `;

        // Logic for page numbers (similar to image: 1, 2, 3, 4 ... 13, 14)
        // Mostramos rango alrededor de current page + first + last
        var range = 2; // Páginas a mostrar alrededor de la actual
        var addedDots1 = false;
        var addedDots2 = false;

        for (var i = 1; i <= total; i++) {
            // Mostrar si es primero, último, o está en rango current +/- 2
            if (i === 1 || i === total || (i >= current - range && i <= current + range)) {
                var active = i === current ? 'active' : '';
                html += `< span class="page-link number ${active}" data - page="${i}" > ${ i }</span > `;
            } else if (i < current - range && !addedDots1) {
                html += `< span class="page-link dots" >...</span > `;
                addedDots1 = true;
            } else if (i > current + range && !addedDots2) {
                html += `< span class= "page-link dots" >...</span > `;
                addedDots2 = true;
            }
        }

        // Next Button
        var nextDisabled = current === total ? 'disabled' : '';
        html += `< span class= "page-link next ${nextDisabled}" data - page="${current + 1}" > Next < i class= "fa fa-angle-right" ></i ></span > `;

        $pagination.html(html);
    }

    // Funcionalidad Placeholder "Gestionar"
    $wrapper.on('click', '.btn-gestionar', function() {
        var id = $(this).data('student-id');
        alert('Gestionar estudiante ID: ' + id + ' (Próximamente modal)');
    });


});
```
