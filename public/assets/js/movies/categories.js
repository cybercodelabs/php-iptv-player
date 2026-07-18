/**
 * Catálogo de películas: centra el chip activo en el scroll horizontal.
 */
document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-movies-categories]');
  if (!root) {
    return;
  }

  const nav = root.querySelector('.vod-filters__nav');
  const active = root.querySelector('.vod-chip.is-active');

  if (!nav || !active) {
    return;
  }

  const centerActive = () => {
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
