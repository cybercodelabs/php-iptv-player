/**
 * Temporadas + cambio de vista cuadrícula / lista.
 */
document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-serie-seasons]');
  if (!(root instanceof HTMLElement)) {
    return;
  }

  const select = root.querySelector('[data-season-select]');
  const panels = root.querySelectorAll('[data-season-panel]');
  const toggle = root.querySelector('[data-view-toggle]');
  const label = root.querySelector('[data-view-label]');
  const grids = root.querySelectorAll('[data-episodes-view]');

  if (select instanceof HTMLSelectElement && panels.length > 0) {
    const showSeason = (value) => {
      panels.forEach((panel) => {
        if (!(panel instanceof HTMLElement)) {
          return;
        }
        const active = panel.getAttribute('data-season-panel') === value;
        panel.classList.toggle('is-active', active);
        panel.hidden = !active;
      });
    };

    select.addEventListener('change', () => {
      showSeason(select.value);
    });
  }

  if (toggle instanceof HTMLButtonElement && grids.length > 0) {
    toggle.addEventListener('click', () => {
      const current = toggle.getAttribute('data-view') === 'list' ? 'list' : 'grid';
      const next = current === 'grid' ? 'list' : 'grid';

      toggle.setAttribute('data-view', next);
      if (label) {
        label.textContent = next === 'grid' ? 'Cuadrícula' : 'Lista';
      }

      grids.forEach((grid) => {
        if (grid instanceof HTMLElement) {
          grid.setAttribute('data-view', next);
        }
      });
    });
  }
});
