/**
 * Modal de tráiler con animación de entrada / salida.
 */
(function () {
  const ANIM_MS = 280;

  /**
   * @param {HTMLElement} root
   */
  function createTrailerModal(root) {
    const backdrop = root.querySelector('[data-trailer-backdrop]');
    const dialog = root.querySelector('.trailer-modal__dialog');
    const iframe = root.querySelector('[data-trailer-iframe]');
    const closeBtn = root.querySelector('[data-trailer-close]');
    let open = false;
    let closing = false;

    if (!(iframe instanceof HTMLIFrameElement) || !(dialog instanceof HTMLElement)) {
      return null;
    }

    const lockScroll = (locked) => {
      document.documentElement.classList.toggle('trailer-modal-open', locked);
      document.body.classList.toggle('trailer-modal-open', locked);
    };

    const setIframe = (youtubeId) => {
      iframe.src = youtubeId
        ? `https://www.youtube.com/embed/${encodeURIComponent(youtubeId)}?autoplay=1&rel=0`
        : '';
    };

    const openModal = (youtubeId) => {
      if (!youtubeId || open || closing) {
        return;
      }

      open = true;
      root.hidden = false;
      root.setAttribute('aria-hidden', 'false');
      lockScroll(true);
      setIframe(youtubeId);

      // Forzar reflow antes de animar
      void root.offsetWidth;
      root.classList.add('is-open');
      closeBtn?.focus({ preventScroll: true });
    };

    const closeModal = () => {
      if (!open || closing) {
        return;
      }

      closing = true;
      root.classList.remove('is-open');
      root.classList.add('is-closing');

      window.setTimeout(() => {
        setIframe('');
        root.classList.remove('is-closing');
        root.hidden = true;
        root.setAttribute('aria-hidden', 'true');
        lockScroll(false);
        open = false;
        closing = false;
      }, ANIM_MS);
    };

    closeBtn?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && open) {
        closeModal();
      }
    });

    return { open: openModal, close: closeModal };
  }

  document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-trailer-modal]');
    if (!(root instanceof HTMLElement)) {
      return;
    }

    // Sacarlo de .app-main para que position:fixed no quede atrapado
    if (root.parentElement !== document.body) {
      document.body.appendChild(root);
    }

    const api = createTrailerModal(root);
    if (!api) {
      return;
    }

    window.TrailerModal = api;

    document.querySelectorAll('[data-trailer-open]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-trailer-id');
        if (id) {
          api.open(id);
        }
      });
    });
  });
})();
