/* Paragon CMS — Admin JS */
(function () {
    'use strict';

    // ---- Sidebar toggle ----
    const sidebar = document.getElementById('sidebar');
    const sidebarOpen = document.getElementById('sidebarOpen');
    const sidebarClose = document.getElementById('sidebarClose');
    const adminMain = document.getElementById('adminMain');

    function openSidebar() {
        sidebar && sidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar && sidebar.classList.remove('open');
        document.body.style.overflow = '';
    }
    sidebarOpen && sidebarOpen.addEventListener('click', openSidebar);
    sidebarClose && sidebarClose.addEventListener('click', closeSidebar);
    adminMain && adminMain.addEventListener('click', function (e) {
        if (window.innerWidth <= 900 && sidebar && sidebar.classList.contains('open')) closeSidebar();
    });

    // ---- Auto-dismiss flash messages ----
    document.querySelectorAll('.flash').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 400);
        }, 5000);
    });

    // ---- Tabs ----
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const group = btn.closest('[data-tabs]') || document.body;
            const target = btn.dataset.tab;
            group.querySelectorAll('.tab-btn').forEach(function (b) { b.classList.remove('active'); });
            group.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
            btn.classList.add('active');
            const panel = group.querySelector('[data-panel="' + target + '"]');
            if (panel) panel.classList.add('active');
        });
    });

    // ---- Confirm dialogs ----
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.dataset.confirm || 'Are you sure?')) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });

    // ---- Slug auto-generation ----
    const slugSource = document.getElementById('slugSource');
    const slugTarget = document.getElementById('slug');
    if (slugSource && slugTarget) {
        slugSource.addEventListener('input', function () {
            if (slugTarget.dataset.manual === 'true') return;
            slugTarget.value = slugify(slugSource.value);
        });
        slugTarget.addEventListener('input', function () {
            slugTarget.dataset.manual = 'true';
        });
    }
    function slugify(str) {
        return str.toLowerCase()
            .replace(/[àáäâ]/g, 'a').replace(/[èéëê]/g, 'e').replace(/[ìíïî]/g, 'i')
            .replace(/[òóöô]/g, 'o').replace(/[ùúüû]/g, 'u').replace(/[ñ]/g, 'n')
            .replace(/[^\w\s-]/g, '').replace(/[\s_]+/g, '-')
            .replace(/-+/g, '-').replace(/^-+|-+$/g, '');
    }

    // ---- Character counter ----
    document.querySelectorAll('[data-maxlength]').forEach(function (el) {
        const max = parseInt(el.dataset.maxlength);
        const counter = document.createElement('span');
        counter.className = 'text-sm text-muted';
        el.parentNode.insertBefore(counter, el.nextSibling);
        function update() {
            const len = el.value.length;
            counter.textContent = len + ' / ' + max;
            counter.style.color = len > max * 0.9 ? '#dc2626' : '';
        }
        el.addEventListener('input', update);
        update();
    });

    // ---- Media picker ----
    document.querySelectorAll('[data-media-picker]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = btn.dataset.target;
            const preview = btn.dataset.preview;
            openMediaPicker(function (url, id) {
                const input = document.getElementById(target);
                if (input) input.value = url || id;
                const img = document.getElementById(preview);
                if (img && url) { img.src = url; img.style.display = ''; }
            });
        });
    });

    function openMediaPicker(cb) {
        const win = window.open('/admin/media?picker=1', 'mediapicker', 'width=900,height=600,scrollbars=yes');
        window._mediaPickerCallback = cb;
        const check = setInterval(function () { if (!win || win.closed) clearInterval(check); }, 500);
    }
    window._mediaPick = function (url, id) {
        if (window.opener && window.opener._mediaPickerCallback) {
            window.opener._mediaPickerCallback(url, id);
            window.close();
        }
    };

    // ---- Repeater rows ----
    document.querySelectorAll('[data-repeater]').forEach(function (container) {
        const template = container.querySelector('[data-repeater-template]');
        const addBtn = document.querySelector('[data-repeater-add="' + container.id + '"]');
        if (!template || !addBtn) return;
        let idx = container.querySelectorAll('[data-repeater-item]').length;
        addBtn.addEventListener('click', function () {
            const clone = template.cloneNode(true);
            clone.removeAttribute('data-repeater-template');
            clone.setAttribute('data-repeater-item', '');
            clone.innerHTML = clone.innerHTML.replace(/__IDX__/g, idx++);
            clone.style.display = '';
            container.insertBefore(clone, template);
            initRepeaterRemove(clone);
        });
        container.querySelectorAll('[data-repeater-item]').forEach(initRepeaterRemove);
    });
    function initRepeaterRemove(item) {
        item.querySelectorAll('[data-repeater-remove]').forEach(function (btn) {
            btn.addEventListener('click', function () { item.remove(); });
        });
    }

    // ---- Sortable drag list ----
    document.querySelectorAll('.drag-list').forEach(initDragList);
    function initDragList(list) {
        let dragging = null;
        list.querySelectorAll('.drag-item').forEach(function (item) {
            item.setAttribute('draggable', 'true');
            item.addEventListener('dragstart', function () { dragging = item; item.style.opacity = '0.4'; });
            item.addEventListener('dragend', function () { item.style.opacity = ''; dragging = null; });
            item.addEventListener('dragover', function (e) { e.preventDefault(); if (dragging && dragging !== item) { const r = item.getBoundingClientRect(); const after = e.clientY > r.top + r.height / 2; list.insertBefore(dragging, after ? item.nextSibling : item); } });
        });
    }

    // ---- Image preview on file input ----
    document.querySelectorAll('[data-image-input]').forEach(function (input) {
        const previewId = input.dataset.imageInput;
        input.addEventListener('change', function () {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.getElementById(previewId);
                if (img) { img.src = e.target.result; img.style.display = ''; }
            };
            reader.readAsDataURL(file);
        });
    });

    // ---- Inline color preview ----
    document.querySelectorAll('[data-color-preview]').forEach(function (input) {
        const swatch = document.getElementById(input.dataset.colorPreview);
        function update() { if (swatch) swatch.style.background = input.value; }
        input.addEventListener('input', update);
        update();
    });

    // ---- Ajax form submission (for notes/status updates) ----
    document.querySelectorAll('[data-ajax-form]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const url = form.action || window.location.href;
            const data = new FormData(form);
            fetch(url, { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (json) {
                    if (json.success) {
                        const successEl = form.querySelector('[data-ajax-success]');
                        if (successEl) { successEl.textContent = json.message || 'Saved.'; successEl.style.display = ''; }
                        if (json.reload) location.reload();
                    } else {
                        alert(json.message || 'An error occurred.');
                    }
                })
                .catch(function () { alert('Network error. Please try again.'); });
        });
    });

    // ---- Select all checkboxes ----
    document.querySelectorAll('[data-select-all]').forEach(function (master) {
        const target = master.dataset.selectAll;
        master.addEventListener('change', function () {
            document.querySelectorAll(target).forEach(function (cb) { cb.checked = master.checked; });
        });
    });

    // ---- Simple rich text editor (contenteditable + execCommand) ----
    document.querySelectorAll('[data-editor]').forEach(function (container) {
        const hiddenInput = document.getElementById(container.dataset.editor);
        const toolbar = container.querySelector('.editor-toolbar');
        const area = container.querySelector('.editor-area');
        if (!toolbar || !area || !hiddenInput) return;
        area.innerHTML = hiddenInput.value;
        toolbar.querySelectorAll('[data-cmd]').forEach(function (btn) {
            btn.addEventListener('mousedown', function (e) {
                e.preventDefault();
                const cmd = btn.dataset.cmd;
                const arg = btn.dataset.arg || null;
                document.execCommand(cmd, false, arg);
                area.focus();
            });
        });
        area.addEventListener('input', function () { hiddenInput.value = area.innerHTML; });
        area.addEventListener('paste', function (e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text/plain');
            document.execCommand('insertText', false, text);
        });
    });

    // ---- Collapsible sections ----
    document.querySelectorAll('[data-collapse-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = document.getElementById(btn.dataset.collapseToggle);
            if (target) {
                const open = target.style.display !== 'none';
                target.style.display = open ? 'none' : '';
                btn.setAttribute('aria-expanded', !open);
            }
        });
    });

    // ---- Copy to clipboard ----
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const text = document.getElementById(btn.dataset.copy);
            if (text) navigator.clipboard.writeText(text.value || text.textContent).then(function () {
                const orig = btn.textContent;
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = orig; }, 1500);
            });
        });
    });

    // ---- Search bar debounce ----
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        let t;
        searchInput.addEventListener('input', function () {
            clearTimeout(t);
            t = setTimeout(function () {
                const url = new URL(window.location.href);
                url.searchParams.set('search', searchInput.value);
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }, 600);
        });
    }

    // ---- Mark active nav based on URL ----
    // (Already handled server-side but ensure no JS collision)

})();
