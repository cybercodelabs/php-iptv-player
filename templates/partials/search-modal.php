<div
    class="search-modal"
    id="searchModal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="searchModalTitle"
    aria-hidden="true"
>
    <div class="search-modal__backdrop" data-search-close tabindex="-1"></div>
    <div class="search-modal__dialog" role="document">
        <div class="search-modal__header">
            <h2 class="search-modal__title" id="searchModalTitle">Buscar</h2>
            <button type="button" class="search-modal__close" data-search-close aria-label="Cerrar búsqueda">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form class="search-modal__form" id="searchForm" autocomplete="off" role="search">
            <label class="visually-hidden" for="searchInput">Buscar películas o series</label>
            <div class="search-modal__input-wrap">
                <svg class="search-modal__input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>
                </svg>
                <input
                    type="search"
                    id="searchInput"
                    class="search-modal__input"
                    placeholder="Buscar películas o series…"
                    spellcheck="false"
                >
            </div>
        </form>

        <div class="search-modal__body" id="searchResults" aria-live="polite">
            <p class="search-modal__hint">Escribe al menos 2 caracteres para buscar en películas y series.</p>
        </div>
    </div>
</div>
