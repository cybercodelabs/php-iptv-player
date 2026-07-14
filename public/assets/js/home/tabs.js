/**
 * Tabs de recomendaciones (Películas / Series) con fade al cambiar.
 */
document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-home-tabs]');
  if (!root) {
    return;
  }

  const buttons = Array.from(root.querySelectorAll('[data-tab]'));
  const panels = Array.from(root.querySelectorAll('[data-panel]'));
  const FADE_MS = 220;
  let busy = false;

  const showPanel = (panel) => {
    panel.hidden = false;
    // Forzar reflow para que la transición parta desde opacity 0
    void panel.offsetWidth;
    panel.classList.add('is-active');
  };

  const hidePanel = (panel) => new Promise((resolve) => {
    panel.classList.remove('is-active');

    window.setTimeout(() => {
      panel.hidden = true;
      resolve();
    }, FADE_MS);
  });

  buttons.forEach((button) => {
    button.addEventListener('click', async () => {
      const id = button.getAttribute('data-tab');
      if (!id || busy || button.classList.contains('is-active')) {
        return;
      }

      busy = true;

      buttons.forEach((btn) => {
        const active = btn === button;
        btn.classList.toggle('is-active', active);
        btn.setAttribute('aria-selected', active ? 'true' : 'false');
      });

      const current = panels.find((panel) => panel.classList.contains('is-active'));
      const next = panels.find((panel) => panel.getAttribute('data-panel') === id);

      if (current && current !== next) {
        await hidePanel(current);
      }

      if (next) {
        showPanel(next);
      }

      busy = false;
    });
  });
});
