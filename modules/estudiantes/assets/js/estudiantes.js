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


    /**
     * ================================
     * Lógica de Registro Manual
     * ================================
     */
    $('#alezux-manual-register-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn  = $form.find('button[type="submit"]');
        var $msg  = $form.find('.alezux-form-message');
        var $spinner = $btn.find('i.fa-spinner');

        $btn.prop('disabled', true);
        $spinner.show();
        $msg.html('');

        var formData = {
            action: 'alezux_register_student',
            nonce: nonce,
            first_name: $form.find('input[name="first_name"]').val(),
            last_name:  $form.find('input[name="last_name"]').val(),
            email:      $form.find('input[name="email"]').val(),
            course_id:  $form.find('select[name="course_id"]').val()
        };

        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $msg.html('<div style="color: green; margin-top: 10px;">' + response.data.message + '</div>');
                    $form[0].reset();
                } else {
                    $msg.html('<div style="color: red; margin-top: 10px;">' + response.data.message + '</div>');
                }
            },
            error: function() {
                $msg.html('<div style="color: red; margin-top: 10px;">Error de conexión.</div>');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.hide();
            }
        });
    });


    /**
     * ================================
     * Lógica de Registro Masivo (CSV)
     * ================================
     */
    var $dropzone = $('#alezux-csv-dropzone');
    var $fileInput = $('#alezux-csv-file-input');
    var $progressContainer = $('#alezux-csv-progress-container');
    var $progressBarFill = $progressContainer.find('.alezux-progress-fill');
    var $statusText = $progressContainer.find('.status-text');
    var $statusCount = $progressContainer.find('.status-count');
    var $report = $('#alezux-csv-report');

    // Drag & Drop UI
    $dropzone.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    $dropzone.on('dragleave drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });
    $dropzone.on('click', function() {
        $fileInput.click();
    });

    // File Selection
    $dropzone.on('drop', function(e) {
        var files = e.originalEvent.dataTransfer.files;
        if (files.length) handleCSVFile(files[0]);
    });
    $fileInput.on('change', function(e) {
        if (this.files.length) handleCSVFile(this.files[0]);
    });

    function handleCSVFile(file) {
        if (file.type !== "text/csv" && file.type !== "application/vnd.ms-excel" && !file.name.match(/\.csv$/i)) {
            alert("Por favor sube un archivo CSV válido.");
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            var text = e.target.result;
            var students = parseCSV(text);
            
            if (students.length === 0) {
                alert("El archivo CSV parece estar vacío o mal formateado.");
                return;
            }

            if (confirm("Se encontraron " + students.length + " estudiantes. ¿Iniciar importación?")) {
                startBatchProcess(students);
            }
        };
        reader.readAsText(file);
    }

    // Simple CSV Parser (Nombre, Apellido, Email)
    function parseCSV(text) {
        var lines = text.split(/\r\n|\n/);
        var students = [];
        
        // Detectar si hay header (si la primera linea tiene 'email' o 'correo')
        var startIndex = 0;
        if (lines[0] && (lines[0].toLowerCase().includes('email') || lines[0].toLowerCase().includes('correo'))) {
            startIndex = 1;
        }

        for (var i = startIndex; i < lines.length; i++) {
            var line = lines[i].trim();
            if (!line) continue;
            
            // Separar por coma o punto y coma
            var parts = line.split(/,|;/);
            if (parts.length >= 3) {
                // Asumimos formato: Nombre, Apellido, Email
                var first = parts[0].trim().replace(/^"|"$/g, '');
                var last  = parts[1].trim().replace(/^"|"$/g, '');
                var email = parts[2].trim().replace(/^"|"$/g, '');
                
                if (email && email.includes('@')) {
                    students.push({
                        first_name: first,
                        last_name: last,
                        email: email
                    });
                }
            }
        }
        return students;
    }

    // Batch Queue System
    function startBatchProcess(students) {
        var courseId = $('#alezux-csv-course-select').val();
        var batchSize = 5; // 5 correos por lote
        var total = students.length;
        var processed = 0;
        var successCount = 0;
        var errors = [];

        $progressContainer.show();
        $report.html('');
        $progressBarFill.css('width', '0%');
        updateStatus("Iniciando cola...", 0, total);

        // Recursive Batch Function
        function processNextBatch() {
            if (processed >= total) {
                finishProcess(successCount, total, errors);
                return;
            }

            var batch = students.slice(processed, processed + batchSize);
            
            updateStatus("Enviando lote...", processed, total);

            $.ajax({
                url: ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_register_batch_csv',
                    nonce: nonce,
                    students: batch,
                    course_id: courseId
                },
                success: function(response) {
                    if (response.success) {
                        processed += parseInt(response.data.processed);
                        successCount += parseInt(response.data.success);
                        if (response.data.errors && response.data.errors.length) {
                            errors = errors.concat(response.data.errors);
                        }
                    } else {
                        // Si falla todo el lote, contamos como procesados pero con error
                        processed += batch.length;
                        errors.push("Error de lote: " + (response.data.message || "Desconocido"));
                    }
                    
                    var percent = Math.round((processed / total) * 100);
                    $progressBarFill.css('width', percent + '%');
                    updateStatus("Procesando...", processed, total);

                    // Esperar 500ms antes del siguiente lote para no saturar
                    setTimeout(processNextBatch, 500);
                },
                error: function() {
                    processed += batch.length;
                    errors.push("Error de conexión en lote.");
                    setTimeout(processNextBatch, 1000);
                }
            });
        }

        processNextBatch();
    }

    function updateStatus(text, current, total) {
        $statusText.text(text);
        $statusCount.text(current + '/' + total);
    }

    function finishProcess(success, total, errors) {
        $progressBarFill.css('width', '100%');
        $progressBarFill.css('background-color', '#4CAF50'); // Green
        updateStatus("¡Proceso completado!", total, total);
        
        var reportHtml = "<div><strong>Resumen:</strong> " + success + " registrados exitosamente.</div>";
        if (errors.length > 0) {
            reportHtml += "<div style='color:#ff6b6b; margin-top:5px;'><strong>Errores (" + errors.length + "):</strong><br>";
            $.each(errors, function(i, err) {
                reportHtml += "- " + err + "<br>";
            });
            reportHtml += "</div>";
        }
        $report.html(reportHtml);
        
        alert("Proceso finalizado.\nEnviados: " + success + "\nErrores: " + errors.length);
        $fileInput.val(''); // Reset input
    }


});
```
