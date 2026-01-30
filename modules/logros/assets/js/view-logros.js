

var AlezuxViewLogrosHandler = function ($scope, $) {
    var container = $scope.find('.alezux-view-logros-wrapper');

    // Check if scope itself is the wrapper (manual init case)
    if (!container.length && $scope.hasClass('alezux-view-logros-wrapper')) {
        container = $scope;
    }

    if (!container.length) return;

    // Removing strict initialization check to allow re-render update in Editor
    // OLD: if (container.data('alezux-initialized')) return; 

    console.log('Alezux Members: Initializing Logros Widget', container);

    // Reset global offset for this instance
    // Note: If multiple widgets exist on same page, global vars are bad. 
    // Ideally we attach these to the container.data, but refactoring that is bigger.
    // For now assuming single widget use-case or accepting last-one-wins for global vars.
    currentOffset = 0;

    // List Container referencing
    var listContainer = container.find('#alezux-logros-list-container');

    // --- EVENTS (Using off/on to prevent duplicates on re-init) ---

    // Search
    container.find('#alezux-logro-search').off('keyup').on('keyup', function () {
        delay(function () {
            currentOffset = 0;
            loadLogros(container, false);
        }, 500);
    });

    // Filter
    container.find('#alezux-logro-course-filter').off('change').on('change', function () {
        currentOffset = 0;
        loadLogros(container, false);
    });

    // Load More
    container.find('#alezux-load-more-logros').off('click').on('click', function (e) {
        e.preventDefault();
        loadLogros(container, true);
    });

    // Edit Button
    container.off('click', '.alezux-edit-logro').on('click', '.alezux-edit-logro', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        openEditModal(id);
    });

    // Delete Button
    container.off('click', '.alezux-delete-logro').on('click', '.alezux-delete-logro', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        openDeleteModal(id);
    });

    // Modal Close
    $('body').off('click', '.alezux-modal-close, .alezux-modal-close-btn').on('click', '.alezux-modal-close, .alezux-modal-close-btn', function (e) {
        e.preventDefault();
        $(this).closest('.alezux-modal').fadeOut();
    });

    // Delete Confirm (Bind globally or to unique ID if possible, avoiding closure issues)
    // Using body delegation for modals appended to footer/body
    $('body').off('click', '#alezux-confirm-delete-btn').on('click', '#alezux-confirm-delete-btn', function (e) {
        e.preventDefault();
        var id = $(this).data('id'); // Ensure the button gets the ID injected
        if (id) deleteLogro(id, container);
    });

    // Edit Form Submit
    $('body').off('submit', '#alezux-logro-edit-form').on('submit', '#alezux-logro-edit-form', function (e) {
        e.preventDefault();
        updateLogro(container);
    });

    // Media Uploader
    setupUploadHandler(container);

    // Initial Load calling
    loadLogros(container, false);

    // Mark as init (optional, mostly for debug now)
    container.data('alezux-initialized', true);
};

// Global pagination/state vars (Scoped strictly would be better but keeping structure)
var currentOffset = 0;
var itemsLimit = 20;
var isLoading = false;

// Initialize
jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/alezux_view_logros.default', AlezuxViewLogrosHandler);
});

// Fallback
jQuery(document).ready(function ($) {
    if (typeof elementorFrontend === 'undefined') {
        $('.alezux-view-logros-wrapper').each(function () {
            AlezuxViewLogrosHandler($(this), $);
        });
    }
});

var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

function loadLogros(container, append) {
    if (isLoading) return;

    var listContainer = container.find('#alezux-logros-list-container');
    var loadMoreBtnContainer = container.find('#alezux-logros-pagination-container');
    var search = container.find('#alezux-logro-search').val();
    var course_id = container.find('#alezux-logro-course-filter').val();

    // Reset offset if not appending
    if (!append) {
        currentOffset = 0;
        listContainer.html('<div class="alezux-loading">Cargando registros...</div>');
        loadMoreBtnContainer.hide();
    } else {
        // Show lightweight loading indicator or button state?
        // For now, change button text
        container.find('#alezux-load-more-logros').text('Cargando...');
    }

    isLoading = true;

    console.log('Alezux: loadLogros called. Append:', append, 'Offset:', currentOffset);

    jQuery.ajax({
        url: alezux_logros_vars.ajax_url,
        type: 'POST',
        data: {
            action: 'alezux_get_achievements',
            nonce: alezux_logros_vars.nonce,
            search: search,
            course_id: course_id,
            search: search,
            course_id: course_id,
            limit: itemsLimit,
            offset: currentOffset,
            image_size: listContainer.data('image-size') || 'medium'
        },
        beforeSend: function () {
            console.log('Alezux: Requesting achievements. Image Size:', listContainer.data('image-size'), 'Show Image:', listContainer.data('show-image'));
        },
        success: function (response) {
            console.log('Alezux: AJAX Response:', response);
            isLoading = false;
            container.find('#alezux-load-more-logros').text('Cargar más');

            if (response.success) {
                var data = response.data;

                if (!append) {
                    listContainer.empty(); // Clear loading message
                }

                if (data.length === 0) {
                    if (!append) {
                        listContainer.html('<div class="alezux-no-results">No se encontraron logros.</div>');
                    } else {
                        // End of list reached
                        loadMoreBtnContainer.hide();
                    }
                } else {
                    renderCards(data, listContainer);
                    currentOffset += data.length; // Update offset

                    // Logic to show/hide load more: 
                    // If we got exactly 'limit' items, there might be more. 
                    // If less, we are at the end.
                    if (data.length < itemsLimit) {
                        loadMoreBtnContainer.hide();
                    } else {
                        loadMoreBtnContainer.show();
                    }
                }

            } else {
                console.error('Alezux: Server Error:', response.data.message);
                if (!append) listContainer.html('<div class="alezux-error">' + response.data.message + '</div>');
                else alert('Error: ' + response.data.message);
            }
        },
        error: function (xhr, status, error) {
            isLoading = false;
            container.find('#alezux-load-more-logros').text('Cargar más');
            console.error('Alezux: AJAX Error:', status, error);
            if (!append) listContainer.html('<div class="alezux-error">Error al cargar los registros. Ver consola.</div>');
        }
    });
}

function renderCards(data, container) {
    var html = '';

    jQuery.each(data, function (index, item) {
        // Fallback for image if no ID but has URL logic in PHP? Or just placeholder.
        // PHP `alezux_get_achievements` currently returns `image_id`. We might need URL.
        // Assuming we need to fetch image URL via JS or PHP should have sent it. 
        // Ideally PHP sends URL, but let's assume image_id for now or placeholder if empty/invalid.

        var imgUrl = item.image_url ? item.image_url : '';
        var imgHtml = '';
        if (imgUrl) {
            imgHtml = '<img src="' + imgUrl + '" alt="Logro">';
        } else if (item.image_id) {
            // If we only have ID and no URL in response, we might need to fetch it or generic.
            // Let's assume there is a generic placeholder or the PHP response has been updated to include image_url?
            // Checking PHP `ajax_get_achievements`: it does NOT seem to return image_url in `renderTable` prev logic.
            // But wait, the previous logic just showed ID. The new design needs visual.
            // We will assume a placeholder if no URL, but we will fix PHP separately if needed.
            // For now, placeholder.
            imgHtml = '<div class="alezux-card-placeholder-img"></div>';
        } else {
            imgHtml = '<div class="alezux-card-placeholder-img"></div>';
        }

        var showImage = container.data('show-image');
        // Default to yes if undefined, but data attr usually returns string 'yes'/'no' or undefined
        if (showImage === undefined) showImage = 'yes';

        html += '<div class="alezux-logro-card">';

        // Image Column - Only render if showImage is yes
        if (showImage === 'yes') {
            html += '<div class="alezux-card-image">';
            html += imgHtml;
            html += '</div>';
        }

        // Content Column
        html += '<div class="alezux-card-content">';

        // Header: Badge + Date (Date usually right aligned but simplified here to match design flow)
        html += '<div class="alezux-card-header">';
        html += '<span class="alezux-course-badge">' + (item.course_title || 'Sin Curso') + '</span>';
        html += '<span class="alezux-card-date">' + (item.formatted_date || item.created_at || '') + '</span>';
        html += '</div>';

        // Message body
        html += '<p class="alezux-card-message">' + item.message + '</p>';

        // Footer: Student + Actions
        html += '<div class="alezux-card-footer">';

        // Student Info
        html += '<div class="alezux-card-student">';
        if (item.student_avatar) {
            html += '<div class="alezux-student-avatar"><img src="' + item.student_avatar + '" alt="Avatar"></div>';
        } else {
            html += '<div class="alezux-student-avatar"><i class="fas fa-user-circle"></i></div>';
        }
        html += '<span class="alezux-student-name">' + (item.student_name || 'Estudiante Desconocido') + '</span>';
        html += '</div>';

        // Actions
        html += '<div class="alezux-card-actions">';
        html += '<button class="alezux-btn-card-action alezux-btn-card-edit alezux-edit-logro" data-id="' + item.id + '">Editar</button>';
        html += '<button class="alezux-btn-card-action alezux-btn-card-delete alezux-delete-logro" data-id="' + item.id + '">Eliminar</button>';
        html += '</div>'; // End actions

        html += '</div>'; // End footer
        html += '</div>'; // End content

        html += '</div>'; // End card
    });

    container.append(html);
}

// ---------------------
// MODAL & ACTION LOGIC
// ---------------------

function openDeleteModal(id) {
    var modal = jQuery('#alezux-delete-modal');
    jQuery('#alezux-confirm-delete-btn').data('id', id);
    modal.fadeIn();
}

function deleteLogro(id, container) {
    var modal = jQuery('#alezux-delete-modal');
    var confirmBtn = jQuery('#alezux-confirm-delete-btn');
    var originalText = confirmBtn.text();
    confirmBtn.text('Eliminando...').prop('disabled', true);

    jQuery.ajax({
        url: alezux_logros_vars.ajax_url,
        type: 'POST',
        data: {
            action: 'alezux_delete_achievement',
            nonce: alezux_logros_vars.nonce,
            id: id
        },
        success: function (response) {
            confirmBtn.text(originalText).prop('disabled', false);
            modal.fadeOut();
            if (response.success) {
                // Remove item from DOM directly to avoid full reload flicker?
                // Or reload list. Reloading list is safer for offset consistency if we implemented smart index handling,
                // but simpler for now is to reload list (resetting offset to 0).
                loadLogros(container, false);
            } else {
                alert(response.data.message);
            }
        },
        error: function () {
            confirmBtn.text(originalText).prop('disabled', false);
            alert('Error de conexión');
        }
    });
}

function openEditModal(id) {
    var modal = jQuery('#alezux-logro-edit-modal');

    // Cargar datos
    jQuery.ajax({
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
                var form = jQuery('#alezux-logro-edit-form');

                form.find('#edit-logro-id').val(data.id);
                form.find('#edit-course-id').val(data.course_id);
                form.find('#edit-student-id').val(data.student_id);
                form.find('#edit-message').val(data.message);

                // Image handling
                var imgId = data.image_id;
                var imgContainer = form.find('.alezux-upload-box');

                // Reset UI first
                updateUploadUI(imgContainer, null);

                if (imgId) {
                    // We have ID, ideally we need URL for preview. 
                    // Does response have URL? If not, we can only set ID.
                    // The preview might be broken if we don't fetch URL.
                    // For now set ID. If data has `image_url` property use it.
                    form.find('#edit-image-id').val(imgId);

                    if (data.image_url) {
                        // Mock attachment object for UI update
                        var mockAttachment = { id: imgId, url: data.image_url };
                        updateUploadUI(imgContainer, mockAttachment);
                    }
                }

                modal.fadeIn();
            } else {
                alert(response.data.message);
            }
        }
    });
}

function updateLogro(container) {
    var form = jQuery('#alezux-logro-edit-form');
    var formData = form.serialize();

    // Add action and nonce
    formData += '&action=alezux_update_achievement&nonce=' + alezux_logros_vars.nonce;

    var btn = form.find('.alezux-logro-submit');
    btn.prop('disabled', true).css('opacity', 0.7).text('Guardando...');

    jQuery.ajax({
        url: alezux_logros_vars.ajax_url,
        type: 'POST',
        data: formData,
        success: function (response) {
            btn.prop('disabled', false).css('opacity', 1).text('Guardar Cambios');
            if (response.success) {
                jQuery('#alezux-logro-edit-modal').fadeOut();
                if (container) loadLogros(container, false); // Reload table from scratch
                else location.reload();
                // alert('Logro actualizado correctamente.'); // Removed alert as per cleaner UX requests usually, modal close is enough or toast? User requested personalized alerts, maybe toast later?
            } else {
                alert(response.data.message);
            }
        },
        error: function () {
            btn.prop('disabled', false).css('opacity', 1).text('Guardar Cambios');
            alert('Error al guardar.');
        }
    });
}


// MEDIA UPLOADER LOGIC (Shared)
var file_frame;

function setupUploadHandler(container) {
    var body = jQuery('body');

    // Prevent multiple bindings
    body.off('click', '.alezux-upload-box').on('click', '.alezux-upload-box', function (event) {
        if (jQuery(event.target).closest('.alezux-remove-img').length) return;

        event.preventDefault();

        // Check for wp.media
        if (typeof wp === 'undefined' || !wp.media) {
            console.error('WP Media not found. Ensure wp_enqueue_media() is called.');
            return;
        }

        var $box = jQuery(this);

        // Reopen frame if exists
        if (file_frame) {
            file_frame.open();
            return;
        }

        // Create frame
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Seleccionar Imagen',
            button: { text: 'Usar imagen' },
            multiple: false
        });

        // On Select
        file_frame.on('select', function () {
            var attachment = file_frame.state().get('selection').first().toJSON();
            updateUploadUI($box, attachment);
        });

        file_frame.open();
    });

    body.off('click', '.alezux-remove-img').on('click', '.alezux-remove-img', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $box = jQuery(this).closest('.alezux-upload-box');
        updateUploadUI($box, null);
    });
}

function updateUploadUI($box, attachment) {
    var $container = $box.closest('.alezux-logro-upload-container');
    var $input = $container.find('.alezux-logro-image-id');
    var $previewBox = $box.find('.alezux-upload-preview');
    var $placeholder = $box.find('.alezux-upload-placeholder');
    var $previewImg = $box.find('.alezux-preview-img');

    if (attachment) {
        $input.val(attachment.id);
        var url = attachment.url;
        if (attachment.sizes && attachment.sizes.medium) {
            url = attachment.sizes.medium.url;
        }

        $previewImg.attr('src', url);
        $placeholder.hide();
        $previewBox.css('display', 'flex').hide().fadeIn();
    } else {
        $input.val('');
        $previewImg.attr('src', '');
        $previewBox.hide();
        $placeholder.fadeIn();
    }
}
