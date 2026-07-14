/**
 * Tabs de recomendaciones (estilo PLAYGO content__tabs).
 */
document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-home-tabs]');
  if (!root) {
    return;
  }

  const buttons = root.querySelectorAll('[data-tab]');
  const panels = root.querySelectorAll('[data-panel]');

  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-tab');
      if (!id) {
        return;
      }

      buttons.forEach((btn) => {
        const active = btn === button;
        btn.classList.toggle('is-active', active);
        btn.setAttribute('aria-selected', active ? 'true' : 'false');
      });

      panels.forEach((panel) => {
        panel.hidden = panel.getAttribute('data-panel') !== id;
      });
    });
  });
});
