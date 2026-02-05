(function ($) {
    'use strict';

    class AlezuxNodeEditor {
        constructor(canvasId) {
            console.log("AlezuxNodeEditor cargado v1.0.3 - Modo Robusto");
            this.canvas = document.getElementById(canvasId);
            if (!this.canvas) return;

            this.editingNode = null;
            this.pendingConnection = null;
            this.nodes = [];
            this.connections = [];
            this.isDragging = false;
            this.dragTarget = null;
            this.initialX = 0;
            this.initialY = 0;

            // Navegación estilo Figma
            this.scale = 1;
            this.pan = { x: 0, y: 0 };
            this.isPanning = false;
            this.panStart = { x: 0, y: 0 };
            this.spacePressed = false;

            this.canvasContent = this.initCanvasContent();
            this.svgLayer = this.initSvgLayer();

            this.modalEl = document.getElementById('alezux-node-modal');

            // FIX: Move modal to body to avoid z-index/transform issues from Elementor
            if (this.modalEl && this.modalEl.parentElement !== document.body) {
                document.body.appendChild(this.modalEl);
            }

            // Re-fetch after move
            this.modalEl = document.getElementById('alezux-node-modal');

            this.modal = {
                get overlay() { return document.getElementById('alezux-node-modal'); },
                get title() { return document.getElementById('modal-title'); },
                get fields() { return document.getElementById('modal-fields'); },
                get save() { return document.getElementById('modal-save'); },
                get cancel() { return document.getElementById('modal-cancel') }
            };

            this.currentAutomationId = null;

            this.popup = {
                container: document.getElementById('alezux-editor-popup'),
                close: document.getElementById('close-editor-popup'),
                createBtn: document.getElementById('btn-create-automation'),
                nameInput: document.getElementById('automation-name')
            };

            this.drawer = {
                el: document.getElementById('alezux-side-panel'),
                subject: document.getElementById('drawer-subject'),
                content: document.getElementById('drawer-content'),
                save: document.getElementById('save-side-panel'),
                close: document.getElementById('close-side-panel'),
                preview: document.getElementById('preview-email-btn'),
                placeholders: document.getElementById('drawer-placeholders')
            };

            this.previewModal = {
                overlay: document.getElementById('alezux-preview-modal'),
                iframe: document.getElementById('preview-iframe'),
                close: document.getElementById('close-preview-modal'),
                title: document.getElementById('preview-title')
            };

            this.initEvents();
            this.loadAutomationsTable();
        }

        initCanvasContent() {
            const div = document.createElement('div');
            div.id = "alezux-marketing-canvas-content";
            div.style.position = "absolute";
            div.style.top = "0";
            div.style.left = "0";
            div.style.width = "5000px";
            div.style.height = "5000px";
            div.style.transformOrigin = "0 0";
            this.canvas.appendChild(div);
            return div;
        }

        initSvgLayer() {
            const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svg.id = "alezux-marketing-svg-layer";
            svg.style.width = "100%";
            svg.style.height = "100%";
            svg.style.position = "absolute";
            svg.style.pointerEvents = "none";
            this.canvasContent.appendChild(svg);
            return svg;
        }

        initEvents() {
            // Dashboard Events
            if (this.popup.createBtn) {
                this.popup.createBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    this.openEditor();
                });
            }

            if (this.popup.close) {
                this.popup.close.addEventListener('click', () => this.closeEditor());
            }

            // Drag and Drop from Sidebar
            const templates = document.querySelectorAll('.automation-node-template');
            templates.forEach(template => {
                template.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('node-type', template.dataset.type);
                });
            });

            this.canvas.addEventListener('dragover', (e) => e.preventDefault());
            this.canvas.addEventListener('drop', (e) => this.handleDrop(e));

            // Canvas Mouse Events
            this.canvas.addEventListener('mousedown', (e) => this.handleMouseDown(e));
            document.addEventListener('mousemove', (e) => this.handleMouseMove(e));
            document.addEventListener('mouseup', (e) => this.handleMouseUp(e));

            // Click handling
            this.canvas.addEventListener('click', (e) => this.handleClick(e));

            // Browser alert suppression (global capture if needed, but we replace them)
            window.alert = (msg) => this.showMessage("Sistema", msg);

            // Clear Button
            const clearBtn = document.getElementById('clear-canvas');
            if (clearBtn) clearBtn.addEventListener('click', () => this.clearCanvas());

            // Zoom Buttons
            const zoomInBtn = document.getElementById('btn-zoom-in');
            if (zoomInBtn) zoomInBtn.addEventListener('click', () => this.zoomCanvas(0.1));
            const zoomOutBtn = document.getElementById('btn-zoom-out');
            if (zoomOutBtn) zoomOutBtn.addEventListener('click', () => this.zoomCanvas(-0.1));

            // Import / Export
            const exportBtn = document.getElementById('btn-export');
            if (exportBtn) exportBtn.addEventListener('click', () => this.exportAutomation());
            const importBtn = document.getElementById('btn-import');
            if (importBtn) importBtn.addEventListener('click', () => document.getElementById('import-file').click());
            const importFile = document.getElementById('import-file');
            if (importFile) importFile.addEventListener('change', (e) => this.handleImportFile(e));

            // Save Button
            const saveBtn = document.getElementById('save-marketing-automation');
            if (saveBtn) saveBtn.addEventListener('click', () => this.persistAutomation());

            // Modal Cancel
            if (this.modal.cancel) {
                this.modal.cancel.addEventListener('click', () => this.closeModal());
            }

            // Modal Save
            if (this.modal.save) {
                this.modal.save.addEventListener('click', () => this.handleModalAction());
            }

            // Side Panel Events
            if (this.drawer.close) {
                this.drawer.close.addEventListener('click', () => this.closeDrawer());
            }

            if (this.drawer.save) {
                this.drawer.save.addEventListener('click', () => this.saveDrawerChanges());
            }

            if (this.drawer.preview) {
                this.drawer.preview.addEventListener('click', () => this.openPreview());
            }

            // Preview Modal Close
            if (this.previewModal.close) {
                this.previewModal.close.addEventListener('click', () => {
                    this.previewModal.overlay.style.display = 'none';
                });
            }

            // Zoom Events
            this.canvas.addEventListener('wheel', (e) => this.handleZoom(e), { passive: false });

            // Global Add Node Button
            const globalAddBtn = document.getElementById('add-global-node');
            if (globalAddBtn) {
                globalAddBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.openNodeLibrary('create');
                });
            }
        }

        handleModalAction() {
            if (this.modalAction === 'save_settings') {
                this.applyModalChanges();
            } else if (this.modalAction === 'confirm_clear') {
                this.doClearCanvas();
            } else if (this.modalAction === 'confirm_delete') {
                this.doDelete();
            } else if (this.modalAction === 'success_close') {
                this.closeModal();
                this.closeEditor();
            } else {
                this.closeModal();
            }
        }

        showMessage(title, text, action = 'info') {
            try {
                const overlay = this.modal.overlay;
                if (!overlay) {
                    alert(`${title}: ${text}`); // Fallback
                    return;
                }

                if (this.modal.title) this.modal.title.innerText = title;
                if (this.modal.fields) this.modal.fields.innerHTML = `<p style='color:#888;'>${text}</p>`;
                if (this.modal.save) this.modal.save.innerText = "Entendido";

                this.modalAction = action;

                overlay.style.display = 'flex';
                overlay.style.zIndex = '2147483647';
                console.log("Mostrando mensaje:", title, text);
            } catch (e) {
                console.error("Error mostrando mensaje:", e);
                alert(`${title}: ${text}`);
            }
        }

        handleDrop(e) {
            e.preventDefault();
            const type = e.dataTransfer.getData('node-type');
            if (!type) return;

            const rect = this.canvas.getBoundingClientRect();
            const x = e.clientX - rect.left - 110;
            const y = e.clientY - rect.top - 40;

            this.addNode(type, x, y);
            this.updatePlaceholder();
        }

        addNode(type, x, y, data = {}, forcedId = null) {
            const id = forcedId || 'node_' + Math.random().toString(36).substr(2, 9);
            const nodeEl = document.createElement('div');
            nodeEl.id = id;
            nodeEl.className = `alezux-automation-node node-${type}`;
            nodeEl.style.left = `${x}px`;
            nodeEl.style.top = `${y}px`;

            let title = 'Nodo';
            let icon = '⚙️';
            switch (type) {
                case 'trigger': title = 'Trigger Evento'; icon = '⚡'; break;
                case 'email': title = 'Enviar Email'; icon = '✉️'; break;
                case 'delay': title = 'Esperar (Delay)'; icon = '⏳'; break;
                case 'condition': title = 'Condición (If/Else)'; icon = '🔄'; break;
                case 'inactivity': title = 'Inactividad'; icon = '💤'; break;
                case 'expiration': title = 'Vencimiento Cobro'; icon = '📅'; break;
                // Nuevos Nodos
                case 'course': title = 'Curso'; icon = '🎓'; break;
                case 'student_tag': title = 'Etiqueta Estudiante'; icon = '🏷️'; break;
                case 'payment_status': title = 'Estado Pago'; icon = '💰'; break;
            }

            nodeEl.innerHTML = `
                <div class="node-box">
                    <div class="node-box-icon">${icon}</div>
                    <div class="node-menu-btn" data-node="${id}" title="Opciones">
                        <span></span><span></span><span></span>
                    </div>
                </div>
                <div class="node-labels">
                    <div class="node-title">${title}</div>
                    <div class="node-description">${data.description || 'Haz clic para configurar'}</div>
                </div>
                ${type !== 'trigger' ? `<div class="node-terminal terminal-in" data-node="${id}" title="Entrada"></div>` : ''}
                ${(type === 'condition' || type === 'payment_status' || (type === 'course' && data.action === 'check') || (type === 'student_tag' && data.action === 'check_has')) ? `
                    <div class="node-terminal terminal-out terminal-true" data-node="${id}" data-branch="true" title="Sí"></div>
                    <div class="node-terminal terminal-out terminal-false" data-node="${id}" data-branch="false" title="No"></div>
                ` : `
                    <div class="node-terminal terminal-out" data-node="${id}" title="Salida"></div>
                `}
                ${(type !== 'delay' && type !== 'condition' && !(type === 'course' && data.action === 'check') && !(type === 'student_tag' && data.action === 'check_has')) ? `<div class="node-plus-btn" data-node="${id}" title="Añadir siguiente paso">+</div>` : ''}
            `;

            const menuBtn = nodeEl.querySelector('.node-menu-btn');
            if (menuBtn) {
                menuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.showNodeContextMenu(id, e);
                });
            }

            const plusBtn = nodeEl.querySelector('.node-plus-btn');
            if (plusBtn) {
                plusBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.showQuickAppendMenu(id, e);
                });
            }

            // Evitar que el clic en el terminal se propague al nodo (que abre ajustes)
            nodeEl.querySelectorAll('.node-terminal').forEach(t => {
                t.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.handleTerminalClick(t);
                });
            });

            // Abrir ajustes con CLIC o DOBLE CLIC (si no se ha arrastrado significativamente)
            // Evento CLICK: Solo para seleccionar (futuro) o lógica simple
            nodeEl.addEventListener('click', (e) => {
                if (e.target.closest('.node-terminal') || e.target.closest('.node-menu-btn') || e.target.closest('.node-plus-btn')) return;
                // Dejamos el click simple vacío o para selección
            });

            // Evento DBLCLICK: Abrir Configuración SIEMPRE
            nodeEl.addEventListener('dblclick', (e) => {
                if (e.target.closest('.node-terminal') || e.target.closest('.node-menu-btn') || e.target.closest('.node-plus-btn')) return;
                e.preventDefault();
                e.stopPropagation();
                this.openNodeSettings(id);
            });

            this.canvasContent.appendChild(nodeEl);
            this.nodes.push({ id, type, x, y, el: nodeEl, data });
            this.updatePlusButtons();
            return id;
        }

        handleClick(e) {
            const terminal = e.target.closest('.node-terminal');
            if (terminal) {
                this.handleTerminalClick(terminal);
                return;
            }

            const line = e.target.closest('.automation-line');
            if (line && line.id !== 'temp-connection-line') {
                this.deleteConnection(line.id);
                return;
            }

            // Si el e.target es directamente un path con la clase
            if (e.target.classList.contains('automation-line') && e.target.id !== 'temp-connection-line') {
                this.deleteConnection(e.target.id);
                return;
            }

            const nodeEl = e.target.closest('.alezux-automation-node');
            if (nodeEl) {
                // No hacer nada aquí, esperar dblclick
            } else {
                // Click en canvas vacío cancela conexión pendiente
                if (this.pendingConnection) {
                    this.pendingConnection.fromTerminal.classList.remove('active');
                    this.pendingConnection = null;
                    this.removeTempLine();
                }
            }
        }

        deleteNode(nodeId) {
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return;

            // Eliminar conexiones asociadas
            const connsToRemove = this.connections.filter(c => c.from === nodeId || c.to === nodeId);
            connsToRemove.forEach(c => {
                c.path.remove();
                this.connections = this.connections.filter(conn => conn.id !== c.id);
            });

            // Eliminar elemento del DOM
            node.el.remove();

            // Eliminar del array
            this.nodes = this.nodes.filter(n => n.id !== nodeId);

            this.updatePlaceholder();
            this.updatePlusButtons();
        }

        deleteConnection(connId) {
            const conn = this.connections.find(c => c.id === connId);
            if (!conn) return;

            conn.path.remove();
            this.connections = this.connections.filter(c => c.id !== connId);
            this.updatePlusButtons();
        }

        handleTerminalClick(terminal) {
            const nodeId = terminal.dataset.node;
            const isOut = terminal.classList.contains('terminal-out');
            const branch = terminal.dataset.branch || 'default';

            if (!this.pendingConnection) {
                this.pendingConnection = {
                    from: nodeId,
                    fromTerminal: terminal,
                    isOut: isOut,
                    sourceHandle: branch
                };
                terminal.classList.add('active');
                console.log("Iniciando conexión desde:", nodeId, "Branch:", branch);
            } else {
                const targetIsOut = terminal.classList.contains('terminal-out');

                if (this.pendingConnection.from === nodeId) {
                    this.pendingConnection.fromTerminal.classList.remove('active');
                    this.pendingConnection = null;
                    this.removeTempLine();
                    return;
                }

                if (this.pendingConnection.isOut !== targetIsOut) {
                    const fromNode = this.pendingConnection.isOut ? this.pendingConnection.from : nodeId;
                    const toNode = this.pendingConnection.isOut ? nodeId : this.pendingConnection.from;
                    const handle = this.pendingConnection.isOut ? this.pendingConnection.sourceHandle : branch;

                    this.createConnection(fromNode, toNode, handle);
                    this.pendingConnection.fromTerminal.classList.remove('active');
                    this.pendingConnection = null;
                    this.removeTempLine();
                } else {
                    this.showMessage("Atención", "Debes conectar una salida con una entrada.");
                    this.pendingConnection.fromTerminal.classList.remove('active');
                    this.pendingConnection = null;
                    this.removeTempLine();
                }
            }
        }

        createConnection(fromId, toId, sourceHandle = 'default') {
            // Evitar duplicados (bidireccional)
            const exists = this.connections.some(c =>
                c.from === fromId && c.to === toId && c.sourceHandle === sourceHandle
            );

            if (exists) {
                this.showMessage("Atención", "Estos nodos ya están conectados.");
                return;
            }

            const connId = `conn_${fromId}_${toId}_${sourceHandle}`;

            const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
            path.id = connId;
            path.setAttribute("class", "automation-line active");
            if (sourceHandle === 'true') path.setAttribute("class", "automation-line active line-true");
            if (sourceHandle === 'false') path.setAttribute("class", "automation-line active line-false");

            path.style.cursor = "pointer"; // Indicar que es clickeable
            this.svgLayer.appendChild(path);

            this.connections.push({ id: connId, from: fromId, to: toId, sourceHandle: sourceHandle, path });
            this.updateConnections();
            this.updatePlusButtons();
        }

        updateConnections() {
            this.connections.forEach(conn => {
                const nodeFrom = this.nodes.find(n => n.id === conn.from);
                const nodeTo = this.nodes.find(n => n.id === conn.to);

                if (nodeFrom && nodeTo) {
                    let selector = '.terminal-out';
                    if (conn.sourceHandle === 'true') selector = '.terminal-true';
                    else if (conn.sourceHandle === 'false') selector = '.terminal-false';

                    const fromTerm = nodeFrom.el.querySelector(selector) || nodeFrom.el.querySelector('.terminal-out');
                    const toTerm = nodeTo.el.querySelector('.terminal-in');

                    if (fromTerm && toTerm) {
                        // Cálculos centrados en el nuevo diseño icónico (70px box + margin)
                        const x1 = nodeFrom.x + fromTerm.offsetLeft + 6;
                        const y1 = nodeFrom.y + fromTerm.offsetTop + 6;
                        const x2 = nodeTo.x + toTerm.offsetLeft + 6;
                        const y2 = nodeTo.y + toTerm.offsetTop + 6;

                        const dx = Math.abs(x2 - x1) * 0.5;
                        const cp1x = x1 + dx;
                        const cp2x = x2 - dx;

                        conn.path.setAttribute("d", `M ${x1} ${y1} C ${cp1x} ${y1} ${cp2x} ${y2} ${x2} ${y2}`);
                    }
                }
            });
        }

        handleZoom(e) {
            e.preventDefault();
            this.zoomCanvas(-e.deltaY * 0.001, e.clientX, e.clientY);
        }

        zoomCanvas(delta, mouseX = null, mouseY = null) {
            const rect = this.canvas.getBoundingClientRect();
            mouseX = mouseX !== null ? mouseX - rect.left : rect.width / 2;
            mouseY = mouseY !== null ? mouseY - rect.top : rect.height / 2;

            const newScale = Math.min(Math.max(0.2, this.scale + delta), 2);

            // Zoom hacia el puntero del ratón
            this.pan.x -= (mouseX / newScale - mouseX / this.scale) * newScale;
            this.pan.y -= (mouseY / newScale - mouseY / this.scale) * newScale;

            this.scale = newScale;
            this.applyTransform();
        }

        applyTransform() {
            this.canvasContent.style.transform = `translate(${this.pan.x}px, ${this.pan.y}px) scale(${this.scale})`;
        }

        removeTempLine() {
            const temp = document.getElementById('temp-connection-line');
            if (temp) temp.remove();
        }

        updatePlusButtons() {
            this.nodes.forEach(node => {
                const plusBtn = node.el.querySelector('.node-plus-btn');
                if (!plusBtn) return;

                // Un nodo es terminal si no tiene conexiones de SALIDA
                const hasOutgoing = this.connections.some(c => c.from === node.id);

                // No mostrar en condiciones (tienen ramas) o si ya tiene salida
                if (hasOutgoing || node.type === 'condition' || node.type === 'payment_status' || (node.type === 'course' && node.data.action === 'check') || (node.type === 'student_tag' && node.data.action === 'check_has') || node.type === 'delay') {
                    plusBtn.classList.remove('is-terminal');
                } else {
                    plusBtn.classList.add('is-terminal');
                }
            });
        }

        openNodeSettings(nodeId) {
            console.log("Intentando abrir ajustes para nodo:", nodeId);

            try {
                const node = this.nodes.find(n => n.id === nodeId);
                if (!node) {
                    console.error("Nodo no encontrado:", nodeId);
                    return;
                }

                // Asegurar que cerramos otros paneles
                this.closeDrawer();
                this.closeModal();

                if (node.type === 'email') {
                    this.openDrawer(node);
                    return;
                }

                this.editingNode = node;
                this.modalAction = 'save_settings';

                // Robust Modal Element Fetching
                const modalParams = {
                    title: document.getElementById('modal-title'),
                    fields: document.getElementById('modal-fields'),
                    save: document.getElementById('modal-save'),
                    overlay: document.getElementById('alezux-node-modal')
                };

                if (!modalParams.overlay || !modalParams.fields) {
                    console.error("Critical: Modal elements missing from DOM");
                    // Intento de recuperación
                    const overlay = document.getElementById('alezux-node-modal');
                    if (!overlay) {
                        alert("Error crítico: El modal de configuración no existe en la página. Recarga con Ctrl+F5.");
                        return;
                    }
                    modalParams.overlay = overlay;
                    // Intentar recuperar fields también si faltaba
                    if (!modalParams.fields) modalParams.fields = document.getElementById('modal-fields');
                }

                modalParams.overlay.style.zIndex = '99999999';

                if (modalParams.title) modalParams.title.innerText = `Configurar ${node.type.toUpperCase()}`;

                let innerHTML = ''; // Initialize innerHTML here

                if (node.type === 'trigger') {
                    let options = '<option value="">Selecciona un evento...</option>';
                    const dict = window.alezuxEventsDictionary || {};
                    for (const [key, label] of Object.entries(dict)) {
                        options += `<option value="${key}" ${node.data.event === key ? 'selected' : ''}>${label}</option>`;
                    }
                    innerHTML = `
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Evento Disparador:</label>
                        <select id="field-event" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                            ${options}
                        </select>
                    `;
                } else if (node.type === 'inactivity') {
                    innerHTML = `
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Días de Inactividad:</label>
                        <input type="number" id="field-days" value="${node.data.days || '4'}" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                    `;
                } else if (node.type === 'expiration') {
                    innerHTML = `
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Días antes del vencimiento:</label>
                        <input type="number" id="field-days" value="${node.data.days || '2'}" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                    `;
                } else if (node.type === 'delay') {
                    innerHTML = `
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Retraso (minutos):</label>
                        <input type="number" id="field-minutes" value="${node.data.minutes || '5'}" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                    `;
                } else if (node.type === 'condition') {
                    innerHTML = `
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Si el usuario...</label>
                        <select id="field-condition-type" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px; margin-bottom:15px;">
                            <option value="has_tag" ${node.data.condition_type === 'has_tag' ? 'selected' : ''}>Tiene la etiqueta</option>
                            <option value="in_course" ${node.data.condition_type === 'in_course' ? 'selected' : ''}>Está en el curso</option>
                            <option value="payment_status" ${node.data.condition_type === 'payment_status' ? 'selected' : ''}>Estado de pago es</option>
                        </select>
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Valor a comparar:</label>
                        <input type="text" id="field-condition-value" value="${node.data.condition_value || ''}" placeholder="Ej: VIP, 123, paid" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                    `;
                } else if (node.type === 'course') {
                    let courseOptions = '<option value="">Selecciona Curso...</option>';
                    // Safe check para alezux_marketing_vars
                    const vars = window.alezux_marketing_vars || {};
                    const courses = vars.courses || [];
                    courses.forEach(c => {
                        courseOptions += `<option value="${c.id}" ${node.data.course_id == c.id ? 'selected' : ''}>${c.title}</option>`;
                    });
                    innerHTML = `
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Acción:</label>
                        <select id="field-action" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px; margin-bottom:15px;">
                            <option value="enroll" ${node.data.action === 'enroll' ? 'selected' : ''}>Inscribir al curso</option>
                            <option value="check" ${node.data.action === 'check' ? 'selected' : ''}>¿Está inscrito? (Condición)</option>
                        </select>
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Curso:</label>
                        <select id="field-course-id" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                            ${courseOptions}
                        </select>
                    `;
                } else if (node.type === 'student_tag') {
                    innerHTML = `
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Acción:</label>
                        <select id="field-action" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px; margin-bottom:15px;">
                            <option value="add" ${node.data.action === 'add' ? 'selected' : ''}>Añadir Etiqueta</option>
                            <option value="remove" ${node.data.action === 'remove' ? 'selected' : ''}>Quitar Etiqueta</option>
                            <option value="check_has" ${node.data.action === 'check_has' ? 'selected' : ''}>¿Tiene la etiqueta? (Condición)</option>
                        </select>
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Nombre Etiqueta:</label>
                        <input type="text" id="field-tag" value="${node.data.tag || ''}" placeholder="Ej: VIP, Deudor, Becado" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                    `;
                } else if (node.type === 'payment_status') {
                    innerHTML = `
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Verificar si el usuario está:</label>
                        <select id="field-status" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                            <option value="active" ${node.data.status === 'active' ? 'selected' : ''}>Al día (Activo)</option>
                            <option value="overdue" ${node.data.status === 'overdue' ? 'selected' : ''}>Con Deuda (Atrasado)</option>
                            <option value="cancelled" ${node.data.status === 'cancelled' ? 'selected' : ''}>Cancelado</option>
                        </select>
                    `;
                }

                if (modalParams.fields) modalParams.fields.innerHTML = innerHTML;
                if (modalParams.save) modalParams.save.innerText = "Guardar";

                // Re-query and Force Display
                if (modalParams.overlay) {
                    modalParams.overlay.style.display = 'flex';
                    modalParams.overlay.style.zIndex = '2147483647'; // Max Int 32-bit
                    modalParams.overlay.style.visibility = 'visible';
                    modalParams.overlay.style.opacity = '1';
                    console.log("Forzando display: flex en modal", modalParams.overlay);
                }
            } catch (err) {
                console.error("EXCEPCIÓN CRÍTICA en openNodeSettings:", err);
                alert("Error crítico al abrir configuración: " + err.message);
            }
        }

        openNodeLibrary(context = 'create', node = null) {
            this.editingNode = node;
            const drawerTitle = this.drawer.el.querySelector('.drawer-header h3');

            // Ocultar siempre Vista Previa en la biblioteca, mostrar Guardar solo si estamos editando (metamorfosis)
            this.drawer.el.classList.add('drawer-hide-preview');
            if (context === 'metamorphosis' || node) {
                this.drawer.el.classList.remove('drawer-hide-save');
                this.drawer.el.classList.add('drawer-full-save');
                if (drawerTitle) drawerTitle.innerHTML = '<span class="dashicons dashicons-forms"></span> Configurar Disparador';
            } else {
                this.drawer.el.classList.add('drawer-hide-save');
                this.drawer.el.classList.remove('drawer-full-save');
                if (drawerTitle) drawerTitle.innerHTML = '<span class="dashicons dashicons-plus-alt"></span> Biblioteca de Nodos';
            }

            let html = `
                <div class="trigger-library">
                    <p style="color:#718096; font-size:12px; margin-bottom:20px;">
                        ${node ? 'Haz clic en un disparador para cambiarlo, o arrastra nuevos nodos al lienzo:' : 'Arrastra un nodo al lienzo para añadirlo:'}
                    </p>
                    
                    <div class="library-section">
                        <h4 class="library-module-title">Disparadores</h4>
                        <div class="library-item ${node && node.data.event_type === 'general' ? 'active' : ''}" 
                             draggable="true" data-type="trigger" data-event="general">
                            <span class="lib-icon">⚡</span>
                            <div class="lib-info">
                                <strong>Evento General</strong>
                                <p>Registro, pagos, cursos...</p>
                            </div>
                        </div>
                        <div class="library-item ${node && node.type === 'inactivity' ? 'active' : ''}" 
                             draggable="true" data-type="inactivity" data-event="inactivity">
                            <span class="lib-icon">💤</span>
                            <div class="lib-info">
                                <strong>Inactividad</strong>
                                <p>Cuando el alumno deja de entrar.</p>
                            </div>
                        </div>
                    </div>

                    <div class="library-section">
                        <h4 class="library-module-title">Acciones</h4>
                        <div class="library-item" draggable="true" data-type="email">
                            <span class="lib-icon">✉️</span>
                            <div class="lib-info">
                                <strong>Enviar Email</strong>
                                <p>Crea y envía un correo HTML.</p>
                            </div>
                        </div>
                        <div class="library-item" draggable="true" data-type="course">
                            <span class="lib-icon">🎓</span>
                            <div class="lib-info">
                                <strong>Curso</strong>
                                <p>Inscribir o verificar curso.</p>
                            </div>
                        </div>
                        <div class="library-item" draggable="true" data-type="student_tag">
                            <span class="lib-icon">🏷️</span>
                            <div class="lib-info">
                                <strong>Etiqueta Usuario</strong>
                                <p>Asignar rol o etiqueta.</p>
                            </div>
                        </div>
                    </div>

                    <div class="library-section">
                        <h4 class="library-module-title">Lógica</h4>
                        <div class="library-item" draggable="true" data-type="condition">
                            <span class="lib-icon">🔄</span>
                            <div class="lib-info">
                                <strong>Condición Simple</strong>
                                <p>Divide el flujo (Sí / No).</p>
                            </div>
                        </div>
                        <div class="library-item" draggable="true" data-type="payment_status">
                            <span class="lib-icon">💰</span>
                            <div class="lib-info">
                                <strong>Estado Pagos</strong>
                                <p>Verificar si tiene deuda.</p>
                            </div>
                        </div>
                        <div class="library-item ${node && node.type === 'expiration' ? 'active' : ''}" 
                             draggable="true" data-type="expiration" data-event="expiration">
                            <span class="lib-icon">📅</span>
                            <div class="lib-info">
                                <strong>Vencimiento Cobro</strong>
                                <p>Días antes del próximo pago.</p>
                            </div>
                        </div>
                    </div>

                    <div id="trigger-config-area" style="margin-top:25px; border-top:1px solid #2d3748; padding-top:20px; display:none;">
                        <!-- Se llena dinámicamente solo al configurar triggers -->
                    </div>
                </div>
            `;

            this.drawer.subject.parentElement.style.display = 'none';
            this.drawer.content.parentElement.style.display = 'none';
            this.drawer.placeholders.style.display = 'none';

            let libContainer = this.drawer.el.querySelector('.trigger-library-container');
            if (!libContainer) {
                libContainer = document.createElement('div');
                libContainer.className = 'trigger-library-container';
                this.drawer.el.querySelector('.drawer-content').appendChild(libContainer);
            }
            libContainer.innerHTML = html;
            libContainer.style.display = 'block';

            // Configurar eventos
            libContainer.querySelectorAll('.library-item').forEach(item => {
                // Arrastrar siempre permitido (crea un nuevo nodo)
                item.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('node-type', item.dataset.type);
                    e.dataTransfer.dropEffect = "copy";
                });

                // Clic para metamorfosis (solo si estamos editando un trigger y el item es un trigger)
                const isTriggerItem = ['trigger', 'inactivity', 'expiration'].includes(item.dataset.type);
                if (node && isTriggerItem) {
                    item.onclick = () => {
                        libContainer.querySelectorAll('.library-item').forEach(i => i.classList.remove('active'));
                        item.classList.add('active');
                        this.showTriggerConfig(item.dataset.type, node);
                    };
                }
            });

            if (node) {
                this.showTriggerConfig(node.type === 'trigger' ? 'general' : node.type, node);
            }

            this.drawer.el.classList.add('open');
        }

        showTriggerConfig(type, node) {
            const configArea = document.getElementById('trigger-config-area');
            configArea.style.display = 'block';

            if (type === 'trigger' || type === 'general') {
                let options = '<option value="">Selecciona un evento...</option>';
                const dict = window.alezuxEventsDictionary || {};
                for (const [key, label] of Object.entries(dict)) {
                    options += `<option value="${key}" ${node.data.event === key ? 'selected' : ''}>${label}</option>`;
                }
                configArea.innerHTML = `
                    <div class="alezux-field-group">
                        <label>Seleccionar Evento</label>
                        <select id="lib-field-event" class="alezux-select">${options}</select>
                    </div>
                `;
            } else if (type === 'inactivity') {
                configArea.innerHTML = `
                    <div class="alezux-field-group">
                        <label>Días de inactividad</label>
                        <input type="number" id="lib-field-days" value="${node.data.days || '4'}" class="alezux-input">
                    </div>
                `;
            } else if (type === 'expiration') {
                configArea.innerHTML = `
                    <div class="alezux-field-group">
                        <label>Días antes del vencimiento</label>
                        <input type="number" id="lib-field-days" value="${node.data.days || '2'}" class="alezux-input">
                    </div>
                `;
            }
        }

        saveTriggerSettings() {
            if (!this.editingNode) return;
            const node = this.editingNode;
            const activeItem = this.drawer.el.querySelector('.library-item.active');
            if (!activeItem) return;

            const newType = activeItem.dataset.type;

            // Metamorfosis de Nodo
            node.type = newType === 'general' ? 'trigger' : newType;
            node.el.className = `alezux-automation-node node-${node.type}`;

            let display = '';
            let icon = '⚙️';

            if (newType === 'general' || node.type === 'trigger') {
                const evField = document.getElementById('lib-field-event');
                const ev = evField ? evField.value : (node.data.event || '');
                node.data.event = ev;
                node.data.event_type = 'general';
                const dict = window.alezuxEventsDictionary || {};
                display = dict[ev] || 'Evento General';
                icon = '⚡';
            } else if (newType === 'inactivity') {
                const daysField = document.getElementById('lib-field-days');
                node.data.days = daysField ? daysField.value : (node.data.days || '4');
                display = `Inactividad (${node.data.days} días)`;
                icon = '💤';
            } else if (newType === 'expiration') {
                const daysField = document.getElementById('lib-field-days');
                node.data.days = daysField ? daysField.value : (node.data.days || '2');
                display = `Vencimiento (${node.data.days} días antes)`;
                icon = '📅';
            }

            node.data.description = display;

            // Actualizar vista del nodo
            const titleEl = node.el.querySelector('.node-title');
            const descEl = node.el.querySelector('.node-description');
            const iconEl = node.el.querySelector('.node-icon');

            if (titleEl) titleEl.innerText = node.type.toUpperCase();
            if (descEl) descEl.innerText = display;
            if (iconEl) iconEl.innerText = icon;

            this.renderTerminals(node); // Re-render terminals based on new type/data
            this.updatePlusButtons(); // Update plus button visibility
            this.updateConnections(); // Update connections if terminals changed

            this.closeDrawer();
            this.unsavedChanges = true;
        }

        applyModalChanges() {
            if (!this.editingNode) return;
            const node = this.editingNode;
            let display = '';

            if (node.type === 'trigger') {
                const ev = document.getElementById('field-event').value;
                node.data.event = ev;
                display = window.alezuxEventsDictionary[ev] || 'Sin configurar';
            } else if (node.type === 'email') {
                node.data.subject = document.getElementById('field-subject').value;
                node.data.content = document.getElementById('field-content').value;
                display = node.data.subject ? `Email: ${node.data.subject}` : 'Email vacío';
            } else if (node.type === 'delay') {
                node.data.minutes = document.getElementById('field-minutes').value;
                display = `Espera ${node.data.minutes} min`;
            } else if (node.type === 'condition') {
                node.data.condition_type = document.getElementById('field-condition-type').value;
                node.data.condition_value = document.getElementById('field-condition-value').value;
                const typeLabel = document.getElementById('field-condition-type').options[document.getElementById('field-condition-type').selectedIndex].text;
                display = `${typeLabel}: ${node.data.condition_value}`;
            } else if (node.type === 'inactivity') {
                node.data.days = document.getElementById('field-days').value;
                display = `Inactividad: ${node.data.days} días`;
            } else if (node.type === 'expiration') {
                node.data.days = document.getElementById('field-days').value;
                display = `Vencimiento: ${node.data.days} días antes`;
            } else if (node.type === 'course') {
                node.data.action = document.getElementById('field-action').value;
                node.data.course_id = document.getElementById('field-course-id').value;
                const courseName = document.getElementById('field-course-id').options[document.getElementById('field-course-id').selectedIndex].text;
                display = (node.data.action === 'enroll' ? 'Inscribir: ' : '¿Está en?: ') + courseName;
            } else if (node.type === 'student_tag') {
                node.data.action = document.getElementById('field-action').value;
                node.data.tag = document.getElementById('field-tag').value;
                const verb = node.data.action === 'add' ? 'Añadir' : (node.data.action === 'remove' ? 'Quitar' : '¿Tiene?');
                display = `${verb} etiqueta: ${node.data.tag}`;
            } else if (node.type === 'payment_status') {
                node.data.status = document.getElementById('field-status').value;
                const statusMap = { 'active': 'Al día', 'overdue': 'Deudor', 'cancelled': 'Cancelado' };
                display = `¿Estado es ${statusMap[node.data.status]}?`;
            }

            node.data.description = display;
            const descEl = node.el.querySelector('.node-description');
            if (descEl) descEl.innerText = display;

            // Actualizar terminales si cambió el modo (Acción vs Condición)
            this.renderTerminals(node);
            this.updatePlusButtons(); // Update plus button visibility
            this.updateConnections(); // Update connections if terminals changed

            this.closeModal();
            this.unsavedChanges = true;
        }

        renderTerminals(node) {
            // Eliminar terminales de salida existentes
            const existingOuts = node.el.querySelectorAll('.terminal-out');
            existingOuts.forEach(el => el.remove());

            let html = '';
            if (node.type === 'condition' || node.type === 'payment_status' || (node.type === 'course' && node.data.action === 'check') || (node.type === 'student_tag' && node.data.action === 'check_has')) {
                html = `
                    <div class="node-terminal terminal-out terminal-true" data-node="${node.id}" data-branch="true" title="Sí"></div>
                    <div class="node-terminal terminal-out terminal-false" data-node="${node.id}" data-branch="false" title="No"></div>
                `;
            } else {
                html = `<div class="node-terminal terminal-out" data-node="${node.id}" title="Salida"></div>`;
            }
            node.el.insertAdjacentHTML('beforeend', html);
        }

        exportAutomation() {
            const blueprint = {
                nodes: this.nodes.map(n => ({ id: n.id, type: n.type, x: n.x, y: n.y, data: n.data })),
                connections: this.connections.map(c => ({ from: c.from, to: c.to, sourceHandle: c.sourceHandle }))
            };
            const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(blueprint));
            const downloadAnchorNode = document.createElement('a');
            downloadAnchorNode.setAttribute("href", dataStr);
            downloadAnchorNode.setAttribute("download", "alezux_automation.json");
            document.body.appendChild(downloadAnchorNode);
            downloadAnchorNode.click();
            downloadAnchorNode.remove();
        }

        handleImportFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const json = JSON.parse(e.target.result);
                    if (confirm('Esto reemplazará la automatización actual. ¿Continuar?')) {
                        this.doClearCanvas();
                        this.renderBlueprint(json); // Use renderBlueprint, not loadAutomation
                        this.unsavedChanges = true;
                    }
                } catch (err) {
                    alert('Error al leer el archivo JSON.');
                }
            };
            reader.readAsText(file);
            event.target.value = ''; // Reset input
        }

        closeModal() {
            if (this.modal.overlay) this.modal.overlay.style.display = 'none';
            this.editingNode = null;
            this.modalAction = null;
        }

        closeDrawer() {
            if (this.drawer.el) this.drawer.el.classList.remove('open');
            this.editingNode = null;
        }

        handleMouseDown(e) {
            if (this.spacePressed) {
                this.isPanning = true;
                this.panStart.x = e.clientX - this.pan.x;
                this.panStart.y = e.clientY - this.pan.y;
                this.canvas.style.cursor = 'grabbing';
                return;
            }

            const nodeEl = e.target.closest('.alezux-automation-node');
            const isButton = e.target.closest('.node-terminal') || e.target.closest('.node-menu-btn') || e.target.closest('.node-plus-btn');

            if (nodeEl && !isButton) {
                this.isDragging = true;
                this.dragTarget = nodeEl;
                this.nodeWasDragged = false;

                const rect = this.canvas.getBoundingClientRect();
                const mouseCanvasX = (e.clientX - rect.left - this.pan.x) / this.scale;
                const mouseCanvasY = (e.clientY - rect.top - this.pan.y) / this.scale;

                this.dragStartX = mouseCanvasX; // Guardar inicio
                this.dragStartY = mouseCanvasY;
                this.dragDiffX = 0;
                this.dragDiffY = 0;

                this.initialX = mouseCanvasX - nodeEl.offsetLeft;
                this.initialY = mouseCanvasY - nodeEl.offsetTop;
                nodeEl.style.zIndex = 1000;
            }
        }

        handleMouseMove(e) {
            if (this.isPanning) {
                this.pan.x = e.clientX - this.panStart.x;
                this.pan.y = e.clientY - this.panStart.y;
                this.applyTransform();
                return;
            }

            const rect = this.canvas.getBoundingClientRect();
            const mouseCanvasX = (e.clientX - rect.left - this.pan.x) / this.scale;
            const mouseCanvasY = (e.clientY - rect.top - this.pan.y) / this.scale;

            if (this.isDragging && this.dragTarget) {
                this.nodeWasDragged = true;
                this.dragDiffX = mouseCanvasX - this.dragStartX; // Calcular diff
                this.dragDiffY = mouseCanvasY - this.dragStartY;

                let x = mouseCanvasX - this.initialX;
                let y = mouseCanvasY - this.initialY;

                this.dragTarget.style.left = `${x}px`;
                this.dragTarget.style.top = `${y}px`;

                const node = this.nodes.find(n => n.id === this.dragTarget.id);
                if (node) { node.x = x; node.y = y; }
                this.updateConnections();
            } else if (this.pendingConnection) {
                const fromTerm = this.pendingConnection.fromTerminal;
                const nodeFrom = this.nodes.find(n => n.id === this.pendingConnection.from);
                if (!nodeFrom) return;

                const x1 = nodeFrom.x + fromTerm.offsetLeft + 6;
                const y1 = nodeFrom.y + fromTerm.offsetTop + 6;

                let temp = document.getElementById('temp-connection-line');
                if (!temp) {
                    temp = document.createElementNS("http://www.w3.org/2000/svg", "path");
                    temp.id = 'temp-connection-line';
                    temp.setAttribute("class", "automation-line");
                    temp.setAttribute("style", "stroke: #888; stroke-dasharray: 4;");
                    this.svgLayer.appendChild(temp);
                }
                temp.style.display = 'block';

                const dx = Math.abs(mouseCanvasX - x1) * 0.5;
                temp.setAttribute("d", `M ${x1} ${y1} C ${x1 + dx} ${y1} ${mouseCanvasX - dx} ${mouseCanvasY} ${mouseCanvasX} ${mouseCanvasY}`);
            }
        }

        handleMouseUp() {
            if (this.isDragging && this.dragTarget) {
                this.dragTarget.style.zIndex = 10;
            }
            if (this.isPanning) {
                this.canvas.style.cursor = this.spacePressed ? 'grab' : 'crosshair';
            }
            this.isDragging = false;
            this.isPanning = false;
            this.dragTarget = null;
        }

        clearCanvas() {
            this.modal.title.innerText = "¡Confirmar!";
            this.modal.fields.innerHTML = "<p style='color:#888;'>¿Estás seguro de que quieres vaciar todo el lienzo?</p>";
            this.modal.save.innerText = "Sí, Limpiar";
            this.modalAction = 'confirm_clear';
            this.modal.overlay.style.display = 'flex';
        }

        doClearCanvas() {
            this.nodes.forEach(n => n.el.remove());
            this.connections.forEach(c => c.path.remove());
            this.nodes = [];
            this.connections = [];
            this.updatePlaceholder();
            this.closeModal();
        }

        updatePlaceholder() {
            const p = this.canvas.querySelector('.canvas-placeholder');
            if (p) p.style.display = this.nodes.length === 0 ? 'block' : 'none';
        }

        // QUICK APPEND LOGIC
        showQuickAppendMenu(nodeId, event) {
            this.removeQuickMenu();
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return;

            const menu = document.createElement('div');
            menu.className = 'alezux-quick-menu';
            menu.style.left = `${node.x + 220}px`;
            menu.style.top = `${node.y}px`;

            const items = [
                { type: 'email', icon: '✉️', label: 'Enviar Email', module: 'Marketing' },
                { type: 'course', icon: '🎓', label: 'Curso', module: 'Marketing' },
                { type: 'student_tag', icon: '🏷️', label: 'Etiqueta', module: 'Marketing' },
                { type: 'condition', icon: '🔄', label: 'Condición', module: 'Lógica' },
                { type: 'delay', icon: '⏳', label: 'Esperar', module: 'Lógica' },
                { type: 'payment_status', icon: '💰', label: 'Estado Pago', module: 'Lógica' }
            ];

            let currentModule = '';
            items.forEach(item => {
                if (item.module !== currentModule) {
                    currentModule = item.module;
                    const header = document.createElement('div');
                    header.className = 'quick-menu-header';
                    header.innerText = currentModule;
                    menu.appendChild(header);
                }

                const div = document.createElement('div');
                div.className = `quick-menu-item type-${item.type}`;
                div.innerHTML = `<span>${item.icon}</span> ${item.label}`;
                div.onclick = () => {
                    const newNodeId = this.addNode(item.type, node.x + 150, node.y);
                    this.createConnection(nodeId, newNodeId);
                    this.removeQuickMenu();
                };
                menu.appendChild(div);
            });

            this.canvas.appendChild(menu);

            const closeMenu = (e) => {
                if (!menu.contains(e.target)) {
                    this.removeQuickMenu();
                    document.removeEventListener('click', closeMenu);
                }
            };
            setTimeout(() => document.addEventListener('click', closeMenu), 10);
        }

        showQuickAppendMenuAt(x, y, event) {
            this.removeQuickMenu();

            const menu = document.createElement('div');
            menu.className = 'alezux-quick-menu';
            menu.style.left = `${x}px`;
            menu.style.top = `${y}px`;

            const items = [
                { type: 'trigger', icon: '⚡', label: 'Nuevo Trigger', module: 'Disparadores' },
                { type: 'email', icon: '✉️', label: 'Enviar Email', module: 'Marketing' },
                { type: 'course', icon: '🎓', label: 'Curso', module: 'Marketing' },
                { type: 'student_tag', icon: '🏷️', label: 'Etiqueta', module: 'Marketing' },
                { type: 'condition', icon: '🔄', label: 'Condición', module: 'Lógica' },
                { type: 'delay', icon: '⏳', label: 'Esperar', module: 'Lógica' },
                { type: 'payment_status', icon: '💰', label: 'Estado Pago', module: 'Lógica' }
            ];

            let currentModule = '';
            items.forEach(item => {
                if (item.module !== currentModule) {
                    currentModule = item.module;
                    const header = document.createElement('div');
                    header.className = 'quick-menu-header';
                    header.innerText = currentModule;
                    menu.appendChild(header);
                }

                const div = document.createElement('div');
                div.className = `quick-menu-item type-${item.type}`;
                div.innerHTML = `<span>${item.icon}</span> ${item.label}`;
                div.onclick = () => {
                    this.addNode(item.type, x, y);
                    this.removeQuickMenu();
                };
                menu.appendChild(div);
            });

            this.canvasContent.appendChild(menu); // Usar canvasContent para que se mueva con el pan

            const closeMenu = (e) => {
                if (!menu.contains(e.target)) {
                    this.removeQuickMenu();
                    document.removeEventListener('click', closeMenu);
                }
            };
            setTimeout(() => document.addEventListener('click', closeMenu), 10);
        }

        removeQuickMenu() {
            const m = this.canvasContent.querySelector('.alezux-quick-menu');
            if (m) m.remove();
        }

        // CONTEXT MENU LOGIC
        showNodeContextMenu(nodeId, event) {
            this.removeContextMenu();
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return;

            const menu = document.createElement('div');
            menu.className = 'node-context-menu';
            menu.style.left = `${event.clientX}px`;
            menu.style.top = `${event.clientY}px`;
            menu.style.position = 'fixed'; // Usar fixed para que no se vea afectado por el canvas-content transform

            const options = [
                { label: '⚙️ Configurar', action: () => this.openNodeSettings(nodeId) },
                { label: '🗑️ Eliminar Nodo', action: () => this.deleteNode(nodeId), class: 'danger' }
            ];

            options.forEach(opt => {
                const item = document.createElement('div');
                item.className = `context-menu-item ${opt.class || ''}`;
                item.innerText = opt.label;
                item.onclick = () => {
                    opt.action();
                    this.removeContextMenu();
                };
                menu.appendChild(item);
            });

            document.body.appendChild(menu);

            const closeMenu = (e) => {
                if (!menu.contains(e.target)) {
                    this.removeContextMenu();
                    document.removeEventListener('click', closeMenu);
                }
            };
            setTimeout(() => document.addEventListener('click', closeMenu), 10);
        }

        removeContextMenu() {
            const old = document.querySelector('.node-context-menu');
            if (old) old.remove();
        }

        removeQuickMenu() {
            const old = document.querySelector('.alezux-quick-menu');
            if (old) old.remove();
        }

        // PERSISTENCE LOGIC
        ajax_delete_automation() {
            // Este método parece no estar en la clase sino en el PHP. 
            // Implementando deleteAutomation si no existe abajo.
        }

        toggleAutomationStatus(id, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';

            $.ajax({
                url: alezux_marketing_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_toggle_automation_status',
                    nonce: alezux_marketing_vars.nonce,
                    id: id,
                    status: newStatus
                },
                success: (response) => {
                    if (response.success) {
                        this.loadAutomationsTable();
                    } else {
                        alert(response.data || "Error al cambiar estado.");
                    }
                }
            });
        }
        persistAutomation() {
            const name = this.popup.nameInput.value;
            if (!name) {
                this.showMessage("Atención", "Por favor, ingresa un nombre para la automatización.");
                return;
            }

            const blueprint = {
                nodes: this.nodes.map(n => ({ id: n.id, type: n.type, x: n.x, y: n.y, data: n.data })),
                connections: this.connections.map(c => ({ from: c.from, to: c.to, sourceHandle: c.sourceHandle }))
            };

            const saveBtn = document.getElementById('save-marketing-automation');
            const originalHtml = saveBtn.innerHTML;
            saveBtn.innerHTML = '<span class="dashicons dashicons-update alezux-spin"></span> Guardando...';
            saveBtn.disabled = true;

            $.ajax({
                url: alezux_marketing_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_save_automation',
                    nonce: alezux_marketing_vars.nonce,
                    id: this.currentAutomationId,
                    name: name,
                    blueprint: JSON.stringify(blueprint)
                },
                success: (response) => {
                    saveBtn.innerHTML = originalHtml;
                    saveBtn.disabled = false;
                    if (response.success) {
                        this.currentAutomationId = response.data.id;
                        this.showMessage("¡Éxito!", "Automatización guardada correctamente.", 'success_close');
                        this.loadAutomationsTable();
                    } else {
                        this.showMessage("Error", response.data || "Error al guardar.");
                    }
                },
                error: (xhr) => {
                    saveBtn.innerHTML = originalHtml;
                    saveBtn.disabled = false;
                    this.showMessage("Error", "Error de servidor al guardar.");
                }
            });
        }

        loadAutomation(id) {
            $.ajax({
                url: alezux_marketing_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_load_automation',
                    nonce: alezux_marketing_vars.nonce,
                    id: id
                },
                success: (response) => {
                    if (response.success) {
                        this.doClearCanvas();
                        this.currentAutomationId = response.data.id;
                        this.popup.nameInput.value = response.data.name;

                        let blueprint = response.data.blueprint;
                        if (typeof blueprint === 'string') {
                            try { blueprint = JSON.parse(blueprint); } catch (e) { blueprint = {}; }
                        }
                        this.renderBlueprint(blueprint);
                    }
                }
            });
        }

        renderBlueprint(blueprint) {
            if (!blueprint) return;

            // Cargar Nodos
            if (blueprint.nodes) {
                blueprint.nodes.forEach(n => {
                    this.addNode(n.type, n.x, n.y, n.data, n.id);
                });
            }

            // Cargar Conexiones
            setTimeout(() => {
                if (blueprint.connections) {
                    blueprint.connections.forEach(c => {
                        this.createConnection(c.from, c.to, c.sourceHandle || 'default');
                    });
                }
                this.updatePlaceholder();
                this.updatePlusButtons();
            }, 200);
        }

        loadAutomationsTable() {
            const list = document.getElementById('marketing-automations-list');
            if (!list) return;

            $.ajax({
                url: alezux_marketing_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_get_automations_list',
                    nonce: alezux_marketing_vars.nonce
                },
                success: (response) => {
                    if (response.success) {
                        if (response.data.length === 0) {
                            list.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:30px; color:#718096;">No hay automatizaciones creadas aún.</td></tr>';
                            return;
                        }

                        let html = '';
                        response.data.forEach(item => {
                            const date = new Date(item.created_at).toLocaleDateString();
                            let blueprint = item.blueprint || {};
                            if (typeof blueprint === 'string') {
                                try { blueprint = JSON.parse(blueprint); } catch (e) { blueprint = {}; }
                            }
                            const nodeCount = (blueprint.nodes && Array.isArray(blueprint.nodes)) ? blueprint.nodes.length : 0;

                            const statusClass = item.status === 'active' ? 'success' : 'danger';
                            const statusText = item.status === 'active' ? 'Activa' : 'Pausada';
                            const statusIcon = item.status === 'active' ? 'dashicons-yes-alt' : 'dashicons-no-alt';
                            const toggleBtnText = item.status === 'active' ? 'Pausar' : 'Activar';
                            const toggleBtnClass = item.status === 'active' ? 'alezux-action-btn' : 'alezux-action-btn success';
                            const toggleBtnIcon = item.status === 'active' ? 'dashicons-controls-pause' : 'dashicons-controls-play';

                            html += `
                                <tr>
                                    <td style="font-weight:600; color:#fff;">${item.name}</td>
                                    <td>
                                        <span class="alezux-badge badge-${statusClass}">
                                            <span class="dashicons ${statusIcon}"></span> ${statusText}
                                        </span>
                                    </td>
                                    <td><span class="alezux-badge" style="background: rgba(72, 187, 120, 0.1); color: #48bb78; border-color: rgba(72, 187, 120, 0.2);">${item.total_executions || 0} disparos</span></td>
                                    <td>${date}</td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <button class="alezux-action-btn edit-auto" data-id="${item.id}" title="Editar Flujo">
                                            <span class="dashicons dashicons-edit"></span> Editar
                                        </button>
                                        <button class="${toggleBtnClass} toggle-status" data-id="${item.id}" data-status="${item.status}" title="${toggleBtnText}">
                                            <span class="dashicons ${toggleBtnIcon}"></span> ${toggleBtnText}
                                        </button>
                                        <button class="alezux-action-btn danger delete-auto" data-id="${item.id}" title="Eliminar">
                                            <span class="dashicons dashicons-trash"></span>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        list.innerHTML = html;

                        // Eventos de botones
                        list.querySelectorAll('.edit-auto').forEach(btn => {
                            btn.onclick = (e) => {
                                e.stopPropagation();
                                this.openEditor(btn.dataset.id);
                            };
                        });
                        list.querySelectorAll('.toggle-status').forEach(btn => {
                            btn.onclick = (e) => {
                                e.stopPropagation();
                                this.toggleAutomationStatus(btn.dataset.id, btn.dataset.status);
                            };
                        });
                        list.querySelectorAll('.delete-auto').forEach(btn => {
                            btn.onclick = (e) => {
                                e.stopPropagation();
                                this.confirmDelete(btn.dataset.id);
                            };
                        });
                    }
                }
            });
        }

        openEditor(id = null) {
            this.doClearCanvas();
            this.currentAutomationId = id;
            if (id) {
                this.loadAutomation(id);
            } else {
                this.popup.nameInput.value = '';
                // AUTO-INSERT TRIGGER NODE (n8n Style)
                setTimeout(() => {
                    if (this.nodes.length === 0) {
                        this.addNode('trigger', 100, 200, { description: 'Haz clic para seleccionar activador' });
                        this.updatePlaceholder();
                    }
                }, 100);
            }
            this.popup.container.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        closeEditor() {
            this.popup.container.style.display = 'none';
            document.body.style.overflow = '';
        }

        confirmDelete(id) {
            this.deleteTargetId = id;
            this.modal.title.innerText = "Eliminar Automatización";
            this.modal.fields.innerHTML = "<p style='color:#888;'>¿Estás seguro de que quieres eliminar esta automatización? Esta acción no se puede deshacer.</p>";
            this.modal.save.innerText = "Sí, Eliminar";
            this.modalAction = 'confirm_delete';
            this.modal.overlay.style.display = 'flex';
        }

        doDelete() {
            $.ajax({
                url: alezux_marketing_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_delete_automation',
                    nonce: alezux_marketing_vars.nonce,
                    id: this.deleteTargetId
                },
                success: (response) => {
                    this.closeModal();
                    if (response.success) {
                        this.loadAutomationsTable();
                    }
                }
            });
        }

        getTriggerTypeForNode(nodeId) {
            // Seguir conexiones hacia atrás para encontrar el trigger original
            let currentId = nodeId;
            let visited = new Set();

            while (currentId && !visited.has(currentId)) {
                visited.add(currentId);
                const node = this.nodes.find(n => n.id === currentId);
                if (!node) break;
                if (node.type === 'trigger') return node.data.event;

                const incoming = this.connections.find(c => c.to === currentId);
                if (!incoming) break;
                currentId = incoming.from;
            }
            return null;
        }

        openDrawer(node) {
            this.editingNode = node;
            const drawerTitle = this.drawer.el.querySelector('.drawer-header h3');
            if (drawerTitle) drawerTitle.innerHTML = '<span class="dashicons dashicons-email"></span> Configurar Email';

            this.drawer.subject.value = node.data.subject || '';
            this.drawer.content.value = node.data.message || '';

            // Mostrar campos de email, ocultar biblioteca
            this.drawer.subject.parentElement.style.display = 'block';
            this.drawer.content.parentElement.style.display = 'block';
            this.drawer.placeholders.style.display = 'block';
            const libContainer = this.drawer.el.querySelector('.trigger-library-container');
            if (libContainer) libContainer.style.display = 'none';

            // Ajustar visibilidad de botones (Email requiere ambos)
            this.drawer.el.classList.remove('drawer-hide-preview', 'drawer-hide-save', 'drawer-full-save');

            // Cargar Placeholders dinámicos
            const triggerType = this.getTriggerTypeForNode(node.id);
            let placeholders = '';

            if (triggerType === 'registro_usuario') {
                placeholders = `
                    <div style="background: rgba(66, 153, 225, 0.1); border: 1px dashed #4299e1; padding: 10px; border-radius: 8px;">
                        <span style="color: #4299e1; font-size: 11px; font-weight: 600; display: block; margin-bottom: 5px;">Variables disponibles:</span>
                        <code class="placeholder-code" onclick="navigator.clipboard.writeText('{{student_name}}')">{{student_name}}</code>
                        <code class="placeholder-code" onclick="navigator.clipboard.writeText('{{student_email}}')">{{student_email}}</code>
                    </div>
                `;
            } else if (['primer_pago', 'pago_exitoso', 'pago_fallido'].includes(triggerType)) {
                placeholders = `
                    <div style="background: rgba(72, 187, 120, 0.1); border: 1px dashed #48bb78; padding: 10px; border-radius: 8px;">
                        <span style="color: #48bb78; font-size: 11px; font-weight: 600; display: block; margin-bottom: 5px;">Variables disponibles:</span>
                        <code class="placeholder-code" onclick="navigator.clipboard.writeText('{{student_name}}')">{{student_name}}</code>
                        <code class="placeholder-code" onclick="navigator.clipboard.writeText('{{plan_name}}')">{{plan_name}}</code>
                        <code class="placeholder-code" onclick="navigator.clipboard.writeText('{{amount}}')">{{amount}}</code>
                    </div>
                `;
            } else if (['curso_completado', 'logro_obtenido'].includes(triggerType)) {
                placeholders = `
                    <div style="background: rgba(237, 137, 54, 0.1); border: 1px dashed #ed8936; padding: 10px; border-radius: 8px;">
                        <span style="color: #ed8936; font-size: 11px; font-weight: 600; display: block; margin-bottom: 5px;">Variables disponibles:</span>
                        <code class="placeholder-code" onclick="navigator.clipboard.writeText('{{student_name}}')">{{student_name}}</code>
                        <code class="placeholder-code" onclick="navigator.clipboard.writeText('{{item_name}}')">{{item_name}}</code>
                    </div>
                `;
            } else {
                placeholders = `
                    <div style="background: rgba(113, 128, 150, 0.1); border: 1px dashed #718096; padding: 10px; border-radius: 8px;">
                        <p style="color: #718096; font-size: 11px; margin: 0;">Conecta este email a un disparador para ver variables disponibles.</p>
                    </div>
                `;
            }

            this.drawer.placeholders.innerHTML = placeholders;
            this.drawer.el.classList.add('open');
        }

        saveDrawerChanges() {
            if (!this.editingNode) return;
            const node = this.editingNode;

            if (node.type === 'trigger' || node.type === 'inactivity' || node.type === 'expiration') {
                this.saveTriggerSettings();
                return;
            }

            // Lógica para Emails
            node.data.subject = this.drawer.subject.value;
            node.data.message = this.drawer.content.value;
            node.data.description = "Asunto: " + (node.data.subject || 'Sin asunto').substring(0, 20) + "...";

            // Actualizar vista del nodo
            const descEl = node.el.querySelector('.node-description');
            if (descEl) descEl.innerText = node.data.description;

            this.closeDrawer();
        }

        openPreview() {
            const html = this.drawer.content.value;
            const subject = this.drawer.subject.value || 'Sin Asunto';

            this.previewModal.title.innerText = "Vista Previa: " + subject;
            this.previewModal.overlay.style.display = 'flex';

            const doc = this.previewModal.iframe.contentWindow.document;
            doc.open();

            // Reemplazo simple para vista previa
            let processedHtml = html
                .replace(/{{student_name}}/g, 'Juan Pérez (Ejemplo)')
                .replace(/{{student_email}}/g, 'juan@ejemplo.com')
                .replace(/{{plan_name}}/g, 'Membresía VIP (Ejemplo)');

            doc.write(processedHtml);
            doc.close();
        }

    }

    $(document).ready(() => {
        if ($('#alezux-marketing-canvas').length) {
            window.AlezuxMarketing = new AlezuxNodeEditor('alezux-marketing-canvas');
        }
    });

})(jQuery);
