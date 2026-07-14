/**
 * Carrusel Populares al estilo PLAYGO (Owl):
 * loop infinito, autoplay cada 2s, transición ~1.8s y arrastre con mouse.
 */
document.addEventListener('DOMContentLoaded', () => {
  // Misma cadencia que scripts/home/carousel.js de PLAYGO
  const AUTOPLAY_MS = 2000;
  const ANIMATION_MS = 1800;
  const SNAP_MS = 450;

  document.querySelectorAll('[data-rail-wrap]').forEach((wrap) => {
    const viewport = wrap.querySelector('[data-rail]');
    const track = wrap.querySelector('[data-rail-track]');
    const prevBtn = wrap.querySelector('[data-rail-prev]');
    const nextBtn = wrap.querySelector('[data-rail-next]');

    if (!viewport || !track) {
      return;
    }

    const originals = Array.from(track.children);
    const realCount = originals.length;

    if (realCount === 0) {
      return;
    }

    // Clones delante y detrás → loop visual continuo (Owl loop: true)
    originals.forEach((node) => {
      track.appendChild(node.cloneNode(true));
    });
    originals
      .slice()
      .reverse()
      .forEach((node) => {
        track.insertBefore(node.cloneNode(true), track.firstChild);
      });

    let index = realCount;
    let autoplayId = null;
    let isDragging = false;
    let activePointerId = null;
    let dragStartX = 0;
    let dragOriginX = 0;
    let lastPointerX = 0;
    let moved = false;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const gap = () => {
      const styles = window.getComputedStyle(track);
      return parseFloat(styles.columnGap || styles.gap || '0') || 0;
    };

    const step = () => {
      const card = track.querySelector('.catalog-card');
      if (!card) {
        return 230;
      }
      return card.getBoundingClientRect().width + gap();
    };

    const currentTranslateX = () => {
      const style = window.getComputedStyle(track).transform;
      if (!style || style === 'none') {
        return 0;
      }
      try {
        return new DOMMatrixReadOnly(style).m41;
      } catch (_) {
        const match = style.match(/matrix\(([^)]+)\)/);
        if (match) {
          const parts = match[1].split(',');
          return parseFloat(parts[4]) || 0;
        }
        return 0;
      }
    };

    const offsetX = () => -(index * step());

    const applyTransform = (animate, durationMs = ANIMATION_MS) => {
      const duration = reduceMotion ? 0 : durationMs;
      track.style.transition = animate && duration > 0
        ? `transform ${duration}ms ease`
        : 'none';
      track.style.transform = `translate3d(${offsetX()}px, 0, 0)`;
    };

    const normalizeLoop = () => {
      let shifted = false;

      if (index >= realCount * 2) {
        index -= realCount;
        shifted = true;
      } else if (index < realCount) {
        index += realCount;
        shifted = true;
      }

      if (shifted) {
        applyTransform(false);
      }
    };

    const go = (dir) => {
      if (isDragging) {
        return;
      }

      index += dir;
      applyTransform(true, ANIMATION_MS);
    };

    track.addEventListener('transitionend', (event) => {
      if (event.target !== track || event.propertyName !== 'transform') {
        return;
      }
      if (isDragging) {
        return;
      }
      normalizeLoop();
    });

    const startAutoplay = () => {
      stopAutoplay();
      autoplayId = window.setInterval(() => {
        if (isDragging || document.hidden) {
          return;
        }
        go(1);
      }, AUTOPLAY_MS);
    };

    const stopAutoplay = () => {
      if (autoplayId !== null) {
        window.clearInterval(autoplayId);
        autoplayId = null;
      }
    };

    if (prevBtn) {
      prevBtn.disabled = false;
      prevBtn.addEventListener('click', () => {
        go(-1);
        startAutoplay();
      });
    }

    if (nextBtn) {
      nextBtn.disabled = false;
      nextBtn.addEventListener('click', () => {
        go(1);
        startAutoplay();
      });
    }

    // Evita que el navegador “robe” el gesto arrastrando la imagen/enlace
    viewport.addEventListener('dragstart', (event) => {
      event.preventDefault();
    });

    viewport.addEventListener('pointerdown', (event) => {
      if (event.pointerType === 'mouse' && event.button !== 0) {
        return;
      }

      // Congela la posición real (aunque haya autoplay a mitad de animación)
      const liveX = currentTranslateX();
      track.style.transition = 'none';
      track.style.transform = `translate3d(${liveX}px, 0, 0)`;
      index = Math.round(-liveX / step());

      isDragging = true;
      activePointerId = event.pointerId;
      moved = false;
      dragStartX = event.clientX;
      lastPointerX = event.clientX;
      dragOriginX = liveX;

      viewport.classList.add('is-dragging');
      viewport.setPointerCapture(event.pointerId);
      stopAutoplay();
    });

    viewport.addEventListener('pointermove', (event) => {
      if (!isDragging || event.pointerId !== activePointerId) {
        return;
      }

      lastPointerX = event.clientX;
      const dx = lastPointerX - dragStartX;
      if (Math.abs(dx) > 4) {
        moved = true;
      }
      track.style.transition = 'none';
      track.style.transform = `translate3d(${dragOriginX + dx}px, 0, 0)`;
    });

    const endDrag = (event) => {
      if (!isDragging || (activePointerId !== null && event.pointerId !== activePointerId)) {
        return;
      }

      isDragging = false;
      activePointerId = null;
      viewport.classList.remove('is-dragging');

      try {
        viewport.releasePointerCapture(event.pointerId);
      } catch (_) {
        // ignore
      }

      // Usa la última X conocida (pointercancel a veces no trae clientX fiable)
      const dx = lastPointerX - dragStartX;
      const cardStep = step() || 1;
      const projectedX = dragOriginX + dx;

      // Encaja en la tarjeta más cercana (varios slides si el arrastre fue largo)
      index = Math.round(-projectedX / cardStep);
      applyTransform(true, SNAP_MS);

      if (moved) {
        const preventClick = (e) => {
          e.preventDefault();
          e.stopPropagation();
          viewport.removeEventListener('click', preventClick, true);
        };
        viewport.addEventListener('click', preventClick, true);
      }

      startAutoplay();
    };

    viewport.addEventListener('pointerup', endDrag);
    viewport.addEventListener('pointercancel', endDrag);
    viewport.addEventListener('lostpointercapture', (event) => {
      if (isDragging) {
        endDrag(event);
      }
    });

    window.addEventListener('resize', () => {
      applyTransform(false);
    });

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        stopAutoplay();
      } else {
        startAutoplay();
      }
    });

    applyTransform(false);
    window.requestAnimationFrame(() => {
      applyTransform(false);
      startAutoplay();
    });
  });
});
