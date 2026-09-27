(() => {
    'use strict';

    const meta = (name) => document.querySelector(`meta[name="${name}"]`)?.content ?? '';
    const baseUrl = meta('base-url');
    const csrfToken = meta('csrf-token');

    /* ------------------------------------------------------------------
     * Conferma prima dell'invio: data-confirm sul form o sul pulsante.
     * Registrato in fase di capture così blocca anche gli altri listener.
     * ------------------------------------------------------------------ */
    document.addEventListener('submit', (event) => {
        const message = event.submitter?.dataset.confirm || event.target.dataset.confirm;
        if (message && !window.confirm(message)) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);

    /* ------------------------------------------------------------------
     * Catalogo: ricerca live con debounce
     * ------------------------------------------------------------------ */
    const catalogForm = document.getElementById('catalog-form');
    const results = document.getElementById('results');

    if (catalogForm && results) {
        let timer = null;
        let controller = null;

        const search = () => {
            const params = new URLSearchParams(new FormData(catalogForm));
            for (const [key, value] of [...params]) {
                if (value === '') params.delete(key);
            }

            controller?.abort();
            const current = controller = new AbortController();
            results.classList.add('loading');

            fetch(`${baseUrl}/api/search.php?${params}`, {
                headers: { Accept: 'application/json' },
                signal: current.signal,
            })
                .then((response) => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then((data) => {
                    results.innerHTML = data.html;
                    history.replaceState(null, '', `${baseUrl}/catalog.php?${params}`);
                })
                .catch((error) => {
                    // Se la ricerca via fetch fallisce, si ripiega sull'invio classico del form
                    if (error.name !== 'AbortError') catalogForm.submit();
                })
                .finally(() => {
                    if (controller === current) results.classList.remove('loading');
                });
        };

        catalogForm.elements.q.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(search, 300);
        });
        catalogForm.elements.genre.addEventListener('change', search);
        catalogForm.elements.sort.addEventListener('change', search);
        catalogForm.addEventListener('submit', (event) => {
            event.preventDefault();
            clearTimeout(timer);
            search();
        });
    }

    /* ------------------------------------------------------------------
     * Pagina dettagli: aggiunta / modifica / rimozione dalla lista
     * ------------------------------------------------------------------ */
    const listForm = document.getElementById('list-form');

    if (listForm) {
        const statusBox = listForm.querySelector('.form-status');
        const saveButton = listForm.querySelector('button[value="save"]');
        const removeButton = listForm.querySelector('button[value="remove"]');
        const buttons = [saveButton, removeButton];

        const showStatus = (text, isError = false) => {
            statusBox.textContent = text;
            statusBox.classList.toggle('error', isError);
        };

        const setInList = (inList) => {
            listForm.dataset.inList = inList ? '1' : '0';
            removeButton.hidden = !inList;
            saveButton.textContent = inList ? saveButton.dataset.labelUpdate : saveButton.dataset.labelAdd;
        };

        const setText = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        };

        listForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const body = new FormData(listForm);
            body.set('action', event.submitter?.value ?? 'save');

            buttons.forEach((b) => { b.disabled = true; });
            showStatus('Salvataggio…');

            try {
                // getAttribute: listForm.action restituirebbe i pulsanti name="action", non l'URL
                const response = await fetch(listForm.getAttribute('action'), {
                    method: 'POST',
                    body,
                    headers: { Accept: 'application/json', 'X-CSRF-Token': csrfToken },
                });

                if (response.status === 401) {
                    const next = location.pathname.split('/').pop() + location.search;
                    location.href = `${baseUrl}/login.php?next=${encodeURIComponent(next)}`;
                    return;
                }

                const data = await response.json().catch(() => ({ ok: false, error: 'Risposta non valida dal server.' }));
                if (!data.ok) throw new Error(data.error || 'Si è verificato un errore.');

                setInList(data.in_list);
                if (data.in_list) {
                    listForm.elements.progress.value = data.entry.progress ?? '';
                } else {
                    listForm.elements.status.value = 'planned';
                    listForm.elements.score.value = '';
                    listForm.elements.progress.value = '';
                }

                setText('avg-score', data.avg_score);
                setText('votes', data.votes);
                setText('rank', data.rank);
                showStatus(data.message);
            } catch (error) {
                showStatus(error.message, true);
            } finally {
                buttons.forEach((b) => { b.disabled = false; });
            }
        });
    }
})();
