(function ($) {
    'use strict';

    class AlezuxNodeEditor {
        constructor(canvasId) {
            console.log("AlezuxNodeEditor cargado v1.0.3 - Modo Robusto");
            this.canvas = document.getElementById(canvasId);
            if (!this.canvas) return;

            this.svgLayer = this.initSvgLayer();
            this.nodes = [];
            this.connections = [];
            this.isDragging = false;
            this.dragTarget = null;
            this.initialX = 0;
            this.initialY = 0;
            this.editingNode = null;
            this.pendingConnection = null;

            this.modal = {
                overlay: document.getElementById('alezux-node-modal'),
                fields: document.getElementById('modal-fields'),
                save: document.getElementById('modal-save'),
                cancel: document.getElementById('modal-cancel'),
                title: document.getElementById('modal-title')
            };

            this.currentAutomationId = null;
            this.initEvents();
        }

        initSvgLayer() {
            const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svg.id = "alezux-marketing-svg-layer";
            this.canvas.appendChild(svg);
            return svg;
        }

        initEvents() {
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

            // Click handling for terminals and nodes
            this.canvas.addEventListener('click', (e) => this.handleClick(e));

            // Clear Button
            const clearBtn = document.getElementById('clear-canvas');
            if (clearBtn) clearBtn.addEventListener('click', () => this.clearCanvas());

            // Save Button
            const saveBtn = document.getElementById('save-marketing-automation');
            if (saveBtn) saveBtn.addEventListener('click', () => this.persistAutomation());

            // Load Selector
            const loadSelect = document.getElementById('load-automation-select');
            if (loadSelect) {
                loadSelect.addEventListener('change', (e) => {
                    const id = e.target.value;
                    if (id) this.loadAutomation(id);
                });
            }

            // Modal Cancel
            if (this.modal.cancel) {
                this.modal.cancel.addEventListener('click', () => this.closeModal());
            }

            // Modal Save (usaremos un handler único para redireccionar)
            if (this.modal.save) {
                this.modal.save.addEventListener('click', () => this.handleModalAction());
            }
        }

        handleModalAction() {
            if (this.modalAction === 'save_settings') {
                this.applyModalChanges();
            } else if (this.modalAction === 'confirm_clear') {
                this.doClearCanvas();
            } else {
                this.closeModal();
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

        addNode(type, x, y, data = {}) {
            const id = 'node_' + Math.random().toString(36).substr(2, 9);
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
            }

            nodeEl.innerHTML = `
                <div class="node-header">${icon} ${title}</div>
                <div class="node-content">${data.description || 'Haz clic para configurar'}</div>
                <div class="node-terminal terminal-in" data-node="${id}" title="Entrada"></div>
                <div class="node-terminal terminal-out" data-node="${id}" title="Salida"></div>
                ${type !== 'delay' ? `<div class="node-plus-btn" data-node="${id}" title="Añadir siguiente paso">+</div>` : ''}
            `;

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

            this.canvas.appendChild(nodeEl);
            this.nodes.push({ id, type, x, y, el: nodeEl, data });
        }

        handleClick(e) {
            const terminal = e.target.closest('.node-terminal');
            if (terminal) {
                this.handleTerminalClick(terminal);
                return;
            }

            const nodeEl = e.target.closest('.alezux-automation-node');
            if (nodeEl) {
                this.openNodeSettings(nodeEl.id);
            } else {
                // Click en canvas vacío cancela conexión pendiente
                if (this.pendingConnection) {
                    this.pendingConnection.fromTerminal.classList.remove('active');
                    this.pendingConnection = null;
                    this.removeTempLine();
                }
            }
        }

        handleTerminalClick(terminal) {
            const nodeId = terminal.dataset.node;
            const isOut = terminal.classList.contains('terminal-out');

            if (!this.pendingConnection) {
                // Solo empezar desde puerto de salida o permitir cualquier dirección?
                // Vamos a permitir empezar desde cualquier puerto por facilidad
                this.pendingConnection = { from: nodeId, fromTerminal: terminal, isOut: isOut };
                terminal.classList.add('active');
                console.log("Iniciando conexión desde:", nodeId);
            } else {
                const targetIsOut = terminal.classList.contains('terminal-out');

                // No conectar el mismo nodo
                if (this.pendingConnection.from === nodeId) {
                    this.pendingConnection.fromTerminal.classList.remove('active');
                    this.pendingConnection = null;
                    this.removeTempLine();
                    return;
                }

                // Deben ser tipos opuestos (in -> out o out -> in)
                if (this.pendingConnection.isOut !== targetIsOut) {
                    const fromNode = this.pendingConnection.isOut ? this.pendingConnection.from : nodeId;
                    const toNode = this.pendingConnection.isOut ? nodeId : this.pendingConnection.from;

                    this.createConnection(fromNode, toNode);
                    this.pendingConnection.fromTerminal.classList.remove('active');
                    this.pendingConnection = null;
                    this.removeTempLine();
                } else {
                    // Mismo tipo, cancelar anterior y empezar nueva o simplemente cancelar
                    this.pendingConnection.fromTerminal.classList.remove('active');
                    this.pendingConnection = null;
                    this.removeTempLine();
                }
            }
        }

        createConnection(fromId, toId) {
            const connId = `conn_${fromId}_${toId}`;
            // Evitar duplicados
            if (this.connections.find(c => c.id === connId)) return;

            const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
            path.id = connId;
            path.setAttribute("class", "automation-line active");
            this.svgLayer.appendChild(path);

            this.connections.push({ id: connId, from: fromId, to: toId, path });
            this.updateConnections();
        }

        updateConnections() {
            this.connections.forEach(conn => {
                const nodeFrom = this.nodes.find(n => n.id === conn.from);
                const nodeTo = this.nodes.find(n => n.id === conn.to);

                if (nodeFrom && nodeTo) {
                    const fromTerm = nodeFrom.el.querySelector('.terminal-out');
                    const toTerm = nodeTo.el.querySelector('.terminal-in');

                    if (fromTerm && toTerm) {
                        const x1 = nodeFrom.x + fromTerm.offsetLeft + 6;
                        const y1 = nodeFrom.y + fromTerm.offsetTop + 6;
                        const x2 = nodeTo.x + toTerm.offsetLeft + 6;
                        const y2 = nodeTo.y + toTerm.offsetTop + 6;

                        const cp1x = x1 + (x2 - x1) / 2;
                        const cp2x = x1 + (x2 - x1) / 2;

                        conn.path.setAttribute("d", `M ${x1} ${y1} C ${cp1x} ${y1} ${cp2x} ${y2} ${x2} ${y2}`);
                    }
                }
            });
        }

        removeTempLine() {
            const temp = document.getElementById('temp-connection-line');
            if (temp) temp.remove();
        }

        openNodeSettings(nodeId) {
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return;

            this.editingNode = node;
            this.modalAction = 'save_settings';
            this.modal.title.innerText = `Configurar ${node.type.toUpperCase()}`;
            this.modal.fields.innerHTML = '';

            if (node.type === 'trigger') {
                let options = '<option value="">Selecciona un evento...</option>';
                const dict = window.alezuxEventsDictionary || {};

                if (Object.keys(dict).length === 0) {
                    this.modal.fields.innerHTML = '<p style="color:red;">Error: Diccionario de eventos no encontrado.</p>';
                } else {
                    for (const [key, label] of Object.entries(dict)) {
                        options += `<option value="${key}" ${node.data.event === key ? 'selected' : ''}>${label}</option>`;
                    }
                    this.modal.fields.innerHTML = `
                        <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Trigger / Evento:</label>
                        <select id="field-event" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                            ${options}
                        </select>
                    `;
                }
            } else if (node.type === 'email') {
                this.modal.fields.innerHTML = `
                    <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Asunto:</label>
                    <input type="text" id="field-subject" value="${node.data.subject || ''}" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px; margin-bottom:15px;">
                    <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Mensaje:</label>
                    <textarea id="field-content" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px; height:80px;">${node.data.content || ''}</textarea>
                `;
            } else if (node.type === 'delay') {
                this.modal.fields.innerHTML = `
                    <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Retraso (minutos):</label>
                    <input type="number" id="field-minutes" value="${node.data.minutes || '5'}" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                `;
            }

            this.modal.save.innerText = "Guardar";
            this.modal.overlay.style.display = 'flex';
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
            }

            node.data.description = display;
            node.el.querySelector('.node-content').innerText = display;
            this.closeModal();
        }

        closeModal() {
            if (this.modal.overlay) this.modal.overlay.style.display = 'none';
            this.editingNode = null;
            this.modalAction = null;
        }

        handleMouseDown(e) {
            const nodeEl = e.target.closest('.alezux-automation-node');
            if (nodeEl && !e.target.closest('.node-terminal')) {
                this.isDragging = true;
                this.dragTarget = nodeEl;
                this.initialX = e.clientX - nodeEl.offsetLeft;
                this.initialY = e.clientY - nodeEl.offsetTop;
                nodeEl.style.zIndex = 1000;
            }
        }

        handleMouseMove(e) {
            const rect = this.canvas.getBoundingClientRect();
            const mouseX = e.clientX - rect.left;
            const mouseY = e.clientY - rect.top;

            if (this.isDragging && this.dragTarget) {
                let x = e.clientX - this.initialX;
                let y = e.clientY - this.initialY;

                // Constraints
                x = Math.max(0, Math.min(x, rect.width - this.dragTarget.offsetWidth));
                y = Math.max(0, Math.min(y, rect.height - this.dragTarget.offsetHeight));

                this.dragTarget.style.left = `${x}px`;
                this.dragTarget.style.top = `${y}px`;

                const node = this.nodes.find(n => n.id === this.dragTarget.id);
                if (node) { node.x = x; node.y = y; }
                this.updateConnections();
            } else if (this.pendingConnection) {
                // Linea temporal
                const fromTerm = this.pendingConnection.fromTerminal;
                const nodeFrom = this.nodes.find(n => n.id === this.pendingConnection.from);
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

                const cp1x = x1 + (mouseX - x1) / 2;
                temp.setAttribute("d", `M ${x1} ${y1} C ${cp1x} ${y1} ${cp1x} ${mouseY} ${mouseX} ${mouseY}`);
            }
        }

        handleMouseUp() {
            if (this.isDragging && this.dragTarget) {
                this.dragTarget.style.zIndex = 10;
            }
            this.isDragging = false;
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
                { type: 'email', icon: '✉️', label: 'Enviar Email' },
                { type: 'delay', icon: '⏳', label: 'Esperar' }
            ];

            items.forEach(item => {
                const div = document.createElement('div');
                div.className = `quick-menu-item type-${item.type}`;
                div.innerHTML = `<span>${item.icon}</span> ${item.label}`;
                div.onclick = () => {
                    const newNodeId = this.addNode(item.type, node.x + 300, node.y);
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

        removeQuickMenu() {
            const old = document.querySelector('.alezux-quick-menu');
            if (old) old.remove();
        }

        // PERSISTENCE LOGIC
        persistAutomation() {
            const name = document.getElementById('automation-name').value;
            if (!name) {
                alert("Por favor, ingresa un nombre para la automatización.");
                return;
            }

            const blueprint = {
                nodes: this.nodes.map(n => ({ id: n.id, type: n.type, x: n.x, y: n.y, data: n.data })),
                connections: this.connections.map(c => ({ from: c.from, to: c.to }))
            };

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
                    if (response.success) {
                        this.currentAutomationId = response.data.id;
                        this.showMessage("¡Éxito!", "Automatización guardada correctamente.");
                        this.updateLoadList();
                    } else {
                        this.showMessage("Error", response.data || "Error al guardar.");
                    }
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
                        document.getElementById('automation-name').value = response.data.name;

                        const blueprint = JSON.parse(response.data.blueprint);

                        // Cargar Nodos
                        blueprint.nodes.forEach(n => {
                            this.addNode(n.type, n.x, n.y, n.data, n.id);
                        });

                        // Cargar Conexiones (esperar un tick para que los IDs existan)
                        setTimeout(() => {
                            blueprint.connections.forEach(c => {
                                this.createConnection(c.from, c.to);
                            });
                            this.updatePlaceholder();
                        }, 50);
                    }
                }
            });
        }

        updateLoadList() {
            $.ajax({
                url: alezux_marketing_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_get_automations_list',
                    nonce: alezux_marketing_vars.nonce
                },
                success: (response) => {
                    if (response.success) {
                        const select = document.getElementById('load-automation-select');
                        let html = '<option value="">Cargar automatización...</option>';
                        response.data.forEach(item => {
                            html += `<option value="${item.id}" ${item.id == this.currentAutomationId ? 'selected' : ''}>${item.name}</option>`;
                        });
                        select.innerHTML = html;
                    }
                }
            });
        }

        showMessage(title, text) {
            this.modal.title.innerText = title;
            this.modal.fields.innerHTML = `<p style='color:#888;'>${text}</p>`;
            this.modal.save.innerText = "Entendido";
            this.modalAction = 'info';
            this.modal.overlay.style.display = 'flex';
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
            }

            nodeEl.innerHTML = `
                <div class="node-header">${icon} ${title}</div>
                <div class="node-content">${data.description || 'Haz clic para configurar'}</div>
                <div class="node-terminal terminal-in" data-node="${id}" title="Entrada"></div>
                <div class="node-terminal terminal-out" data-node="${id}" title="Salida"></div>
                ${type !== 'delay' ? `<div class="node-plus-btn" data-node="${id}" title="Añadir siguiente paso">+</div>` : ''}
            `;

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

            this.canvas.appendChild(nodeEl);
            this.nodes.push({ id, type, x, y, el: nodeEl, data });
            return id;
        }

    }

    $(document).ready(() => {
        if ($('#alezux-marketing-canvas').length) {
            window.AlezuxMarketing = new AlezuxNodeEditor('alezux-marketing-canvas');
        }
    });

})(jQuery);
