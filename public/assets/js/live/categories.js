/**
 * Guía de canales: centra el ítem activo en el rail móvil.
 */
document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-live-categories]');
  if (!root) {
    return;
  }

  const nav = root.querySelector('.tv-rail__nav');
  const active = root.querySelector('.tv-rail__item.is-active');

  if (!nav || !active) {
    return;
  }

  // En desktop el rail es vertical sticky; solo centra en scroll horizontal (móvil)
  const isHorizontal = () => window.getComputedStyle(nav).flexDirection !== 'column';

  const centerActive = () => {
    if (!isHorizontal()) {
      return;
    }

    const navRect = nav.getBoundingClientRect();
    const itemRect = active.getBoundingClientRect();
    const offset = (itemRect.left - navRect.left) - (navRect.width / 2) + (itemRect.width / 2);
    nav.scrollLeft += offset;
  };

  window.requestAnimationFrame(centerActive);
  window.addEventListener('resize', () => {
    window.requestAnimationFrame(centerActive);
  });
});
