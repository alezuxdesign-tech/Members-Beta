(function ($) {
    'use strict';

    class AlezuxNodeEditor {
        constructor(canvasId) {
            this.canvas = document.getElementById(canvasId);
            this.svgLayer = this.initSvgLayer();
            this.nodes = [];
            this.connections = [];
            this.isDragging = false;
            this.dragTarget = null;
            this.currentX = 0;
            this.currentY = 0;
            this.initialX = 0;
            this.initialY = 0;
            this.editingNode = null;

            this.modal = {
                overlay: document.getElementById('alezux-node-modal'),
                fields: document.getElementById('modal-fields'),
                save: document.getElementById('modal-save'),
                cancel: document.getElementById('modal-cancel'),
                title: document.getElementById('modal-title')
            };

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

            // Connection Events
            this.canvas.addEventListener('click', (e) => this.handleClick(e));

            // Save Button
            const saveBtn = document.getElementById('save-automation');
            if (saveBtn) saveBtn.addEventListener('click', () => this.saveAutomation());

            // Modal Events
            this.modal.cancel.addEventListener('click', () => this.closeModal());
            this.modal.save.addEventListener('click', () => this.applyModalChanges());
        }

        handleDrop(e) {
            e.preventDefault();
            const type = e.dataTransfer.getData('node-type');
            if (!type) return;

            const rect = this.canvas.getBoundingClientRect();
            const x = e.clientX - rect.left - 110;
            const y = e.clientY - rect.top - 40;

            this.addNode(type, x, y);
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
                <div class="node-terminal terminal-in" data-node="${id}"></div>
                <div class="node-terminal terminal-out" data-node="${id}"></div>
            `;

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
            }
        }

        handleTerminalClick(terminal) {
            const nodeId = terminal.dataset.node;
            const isOut = terminal.classList.contains('terminal-out');

            if (!this.pendingConnection) {
                if (isOut) {
                    this.pendingConnection = { from: nodeId, fromTerminal: terminal };
                    terminal.classList.add('active');
                }
            } else {
                if (!isOut && this.pendingConnection.from !== nodeId) {
                    this.createConnection(this.pendingConnection.from, nodeId);
                    this.pendingConnection.fromTerminal.classList.remove('active');
                    this.pendingConnection = null;
                } else {
                    this.pendingConnection.fromTerminal.classList.remove('active');
                    this.pendingConnection = null;
                }
            }
        }

        createConnection(fromId, toId) {
            const id = `conn_${fromId}_${toId}`;
            if (this.connections.find(c => c.id === id)) return;

            const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
            path.id = id;
            path.setAttribute("class", "automation-line active");
            this.svgLayer.appendChild(path);

            this.connections.push({ id, from: fromId, to: toId, path });
            this.updateConnections();
        }

        updateConnections() {
            this.connections.forEach(conn => {
                const nodeFrom = this.nodes.find(n => n.id === conn.from);
                const nodeTo = this.nodes.find(n => n.id === conn.to);

                if (nodeFrom && nodeTo) {
                    const x1 = nodeFrom.x + 220; // Terminal Out
                    const y1 = nodeFrom.y + 45;
                    const x2 = nodeTo.x; // Terminal In
                    const y2 = nodeTo.y + 45;

                    const cp1x = x1 + (x2 - x1) / 2;
                    const cp2x = x1 + (x2 - x1) / 2;

                    conn.path.setAttribute("d", `M ${x1} ${y1} C ${cp1x} ${y1} ${cp2x} ${y2} ${x2} ${y2}`);
                }
            });
        }

        openNodeSettings(nodeId) {
            const node = this.nodes.find(n => n.id === nodeId);
            this.editingNode = node;

            this.modal.title.innerText = `Configurar ${node.type.charAt(0).toUpperCase() + node.type.slice(1)}`;
            this.modal.fields.innerHTML = ''; // Limpiar

            if (node.type === 'trigger') {
                let options = '<option value="">Selecciona un evento...</option>';
                for (const [key, label] of Object.entries(window.alezuxEventsDictionary || {})) {
                    options += `<option value="${key}" ${node.data.event === key ? 'selected' : ''}>${label}</option>`;
                }

                this.modal.fields.innerHTML = `
                    <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Evento que dispara la acción:</label>
                    <select id="field-event" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                        ${options}
                    </select>
                `;
            } else if (node.type === 'email') {
                this.modal.fields.innerHTML = `
                    <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Asunto del correo:</label>
                    <input type="text" id="field-subject" value="${node.data.subject || ''}" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px; margin-bottom:15px;">
                    <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">ID de Plantilla / Contenido:</label>
                    <textarea id="field-content" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px; height:80px;">${node.data.content || ''}</textarea>
                `;
            } else if (node.type === 'delay') {
                this.modal.fields.innerHTML = `
                    <label style="color:#888; display:block; margin-bottom:10px; font-size:12px;">Tiempo de espera (minutos):</label>
                    <input type="number" id="field-minutes" value="${node.data.minutes || '0'}" style="width:100%; background:#000; color:#fff; border:1px solid #333; padding:10px; border-radius:10px;">
                `;
            }

            this.modal.overlay.style.display = 'flex';
        }

        closeModal() {
            this.modal.overlay.style.display = 'none';
            this.editingNode = null;
        }

        applyModalChanges() {
            if (!this.editingNode) return;

            const node = this.editingNode;
            let description = '';

            if (node.type === 'trigger') {
                const event = document.getElementById('field-event').value;
                node.data.event = event;
                description = window.alezuxEventsDictionary[event] || 'Evento no configurado';
            } else if (node.type === 'email') {
                node.data.subject = document.getElementById('field-subject').value;
                node.data.content = document.getElementById('field-content').value;
                description = `Email: ${node.data.subject || 'Sin asunto'}`;
            } else if (node.type === 'delay') {
                node.data.minutes = document.getElementById('field-minutes').value;
                description = `Esperar ${node.data.minutes} min`;
            }

            node.data.description = description;
            node.el.querySelector('.node-content').innerText = description;

            this.closeModal();
        }

        saveAutomation() {
            const blueprint = {
                nodes: this.nodes.map(n => ({ id: n.id, type: n.type, x: n.x, y: n.y, data: n.data })),
                connections: this.connections.map(c => ({ from: c.from, to: c.to }))
            };

            console.log("Saving Blueprint:", blueprint);
            alert("¡Blueprint generado en consola! Integrado con AJAX próximamente.");
        }

        handleMouseDown(e) {
            const nodeEl = e.target.closest('.alezux-automation-node');
            if (nodeEl && !e.target.closest('.node-terminal')) {
                this.isDragging = true;
                this.dragTarget = nodeEl;

                const rect = nodeEl.closest('#alezux-marketing-canvas').getBoundingClientRect();
                this.initialX = e.clientX - nodeEl.offsetLeft;
                this.initialY = e.clientY - nodeEl.offsetTop;
            }
        }

        handleMouseMove(e) {
            if (this.isDragging && this.dragTarget) {
                e.preventDefault();

                const canvasRect = this.canvas.getBoundingClientRect();
                let x = e.clientX - this.initialX;
                let y = e.clientY - this.initialY;

                // Constraints
                x = Math.max(0, Math.min(x, canvasRect.width - this.dragTarget.offsetWidth));
                y = Math.max(0, Math.min(y, canvasRect.height - this.dragTarget.offsetHeight));

                this.dragTarget.style.left = `${x}px`;
                this.dragTarget.style.top = `${y}px`;

                // Update node data
                const node = this.nodes.find(n => n.id === this.dragTarget.id);
                if (node) { node.x = x; node.y = y; }

                this.updateConnections();
            }
        }

        handleMouseUp(e) {
            this.isDragging = false;
            this.dragTarget = null;
        }

        clearCanvas() {
            if (confirm('¿Estás seguro de que quieres limpiar todo el lienzo?')) {
                this.nodes.forEach(n => n.el.remove());
                this.connections.forEach(c => c.path.remove());
                this.nodes = [];
                this.connections = [];
                this.updatePlaceholder();
            }
        }

        updatePlaceholder() {
            const placeholder = this.canvas.querySelector('.canvas-placeholder');
            if (placeholder) {
                placeholder.style.display = this.nodes.length === 0 ? 'block' : 'none';
            }
        }
    }

    // Initialize when ready
    $(document).ready(function () {
        if ($('#alezux-marketing-canvas').length) {
            window.AlezuxMarketing = new AlezuxNodeEditor('alezux-marketing-canvas');
        }
    });

})(jQuery);
