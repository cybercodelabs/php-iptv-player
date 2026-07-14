/**
 * Buscador de películas y series (modal del header).
 */
document.addEventListener('DOMContentLoaded', () => {
  const openBtn = document.getElementById('openSearchModal');
  const modal = document.getElementById('searchModal');
  const input = document.getElementById('searchInput');
  const results = document.getElementById('searchResults');
  const form = document.getElementById('searchForm');

  if (!openBtn || !modal || !input || !results || !form) {
    return;
  }

  const catalogUrl = openBtn.getAttribute('data-catalog-url');
  if (!catalogUrl) {
    return;
  }

  let catalog = null;
  let loading = null;
  let debounceId = null;
  let closing = false;
  const RESULT_LIMIT = 40;
  const ANIM_MS = 260;

  const normalize = (value) => value
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .trim();

  const escapeHtml = (value) => String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');

  const isOpen = () => modal.classList.contains('is-open');

  const openModal = async () => {
    if (isOpen() || closing) {
      return;
    }

    modal.classList.remove('is-closing');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('search-modal-open');

    // Doble frame para que el CSS anime desde el estado cerrado
    modal.classList.add('is-opening');
    void modal.offsetWidth;
    modal.classList.remove('is-opening');
    modal.classList.add('is-open');

    window.requestAnimationFrame(() => {
      input.focus();
      input.select();
    });

    try {
      await ensureCatalog();
      runSearch();
    } catch (_) {
      // El error ya se muestra en results
    }
  };

  const closeModal = () => {
    if (!isOpen() || closing) {
      return;
    }

    closing = true;
    modal.classList.remove('is-open');
    modal.classList.add('is-closing');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('search-modal-open');

    window.setTimeout(() => {
      modal.classList.remove('is-closing');
      closing = false;
    }, ANIM_MS);
  };

  const ensureCatalog = async () => {
    if (catalog) {
      return catalog;
    }

    if (loading) {
      return loading;
    }

    results.innerHTML = '<p class="search-modal__loading">Cargando catálogo…</p>';

    loading = fetch(catalogUrl, {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    })
      .then(async (response) => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
          throw new Error(data.error || 'No se pudo cargar el catálogo.');
        }
        catalog = {
          movies: Array.isArray(data.movies) ? data.movies : [],
          series: Array.isArray(data.series) ? data.series : [],
        };
        return catalog;
      })
      .catch((error) => {
        catalog = null;
        results.innerHTML = `<p class="search-modal__error">${escapeHtml(error.message || 'Error de búsqueda')}</p>`;
        throw error;
      })
      .finally(() => {
        loading = null;
      });

    return loading;
  };

  const matchItems = (items, query, typeLabel) => {
    const q = normalize(query);
    if (q.length < 2) {
      return [];
    }

    return items
      .filter((item) => normalize(String(item.title || '')).includes(q))
      .slice(0, RESULT_LIMIT)
      .map((item) => ({ ...item, typeLabel }));
  };

  const renderResults = (items, query) => {
    if (normalize(query).length < 2) {
      results.innerHTML = '<p class="search-modal__hint">Escribe al menos 2 caracteres para buscar en películas y series.</p>';
      return;
    }

    if (items.length === 0) {
      results.innerHTML = '<p class="search-modal__empty">Sin resultados para tu búsqueda.</p>';
      return;
    }

    const html = items.map((item) => {
      const thumb = item.image
        ? `<img class="search-modal__thumb" src="${escapeHtml(item.image)}" alt="" loading="lazy">`
        : '<span class="search-modal__thumb search-modal__thumb--empty" aria-hidden="true">N/A</span>';

      return `
        <li>
          <a class="search-modal__item" href="${escapeHtml(item.href)}">
            ${thumb}
            <span class="search-modal__meta">
              <span class="search-modal__name">${escapeHtml(item.title)}</span>
              <span class="search-modal__type">${escapeHtml(item.typeLabel)}</span>
            </span>
          </a>
        </li>
      `;
    }).join('');

    results.innerHTML = `<ul class="search-modal__list">${html}</ul>`;
  };

  const runSearch = () => {
    if (!catalog) {
      return;
    }

    const query = input.value;
    // Siempre películas + series
    const items = []
      .concat(matchItems(catalog.movies, query, 'Película'))
      .concat(matchItems(catalog.series, query, 'Serie'))
      .sort((a, b) => String(a.title).localeCompare(String(b.title), 'es'));

    renderResults(items.slice(0, RESULT_LIMIT), query);
  };

  openBtn.addEventListener('click', () => {
    openModal();
  });

  modal.querySelectorAll('[data-search-close]').forEach((el) => {
    el.addEventListener('click', closeModal);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && isOpen()) {
      closeModal();
    }
  });

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    runSearch();
  });

  input.addEventListener('input', () => {
    window.clearTimeout(debounceId);
    debounceId = window.setTimeout(runSearch, 180);
  });
});
