/**
 * Reproductor live: Plyr CDN + HLS.js (Safari nativo cuando aplique).
 */
document.addEventListener('DOMContentLoaded', () => {
  const video = document.querySelector('[data-live-player]');
  if (!(video instanceof HTMLVideoElement)) {
    return;
  }

  const src = video.getAttribute('data-src');
  if (!src) {
    return;
  }

  const hint = document.querySelector('[data-live-hint]');

  const showHint = (message) => {
    if (!(hint instanceof HTMLElement)) {
      return;
    }
    hint.textContent = message;
    hint.hidden = false;
  };

  const plyrOptions = {
    autoplay: true,
    muted: true,
    ratio: '16:9',
    controls: [
      'play-large',
      'play',
      'mute',
      'volume',
      'settings',
      'pip',
      'airplay',
      'fullscreen',
    ],
    settings: ['quality'],
    invertTime: false,
  };

  const initPlyr = () => {
    if (typeof Plyr === 'undefined') {
      return null;
    }

    const player = new Plyr(video, plyrOptions);
    window.player = player;
    return player;
  };

  const tryPlay = (player) => {
    const target = player || video;
    const playPromise = target.play();
    if (playPromise && typeof playPromise.catch === 'function') {
      playPromise.catch(() => {
        // Autoplay bloqueado: el usuario puede pulsar play
      });
    }
  };

  const canNativeHls = video.canPlayType('application/vnd.apple.mpegurl') !== '';

  if (typeof Hls !== 'undefined' && Hls.isSupported()) {
    const hls = new Hls({
      enableWorker: true,
      lowLatencyMode: true,
      backBufferLength: 90,
      maxBufferLength: 30,
      liveSyncDurationCount: 3,
      liveMaxLatencyDurationCount: 10,
    });

    hls.loadSource(src);
    hls.attachMedia(video);

    const player = initPlyr();

    hls.on(Hls.Events.MANIFEST_PARSED, () => {
      tryPlay(player);
    });

    hls.on(Hls.Events.ERROR, (_event, data) => {
      if (!data || !data.fatal) {
        return;
      }

      if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
        hls.startLoad();
        showHint('Problema de red con el stream. Reintentando…');
        return;
      }

      if (data.type === Hls.ErrorTypes.MEDIA_ERROR) {
        hls.recoverMediaError();
        return;
      }

      hls.destroy();
      showHint('No se pudo reproducir este canal. El servidor puede bloquear la señal en el navegador.');
    });

    window.liveHls = hls;
    return;
  }

  if (canNativeHls) {
    video.src = src;
    const player = initPlyr();
    video.addEventListener('loadedmetadata', () => tryPlay(player), { once: true });
    video.addEventListener('error', () => {
      showHint('No se pudo reproducir este canal. Prueba otro o recarga la página.');
    });
    return;
  }

  showHint('Tu navegador no soporta reproducción HLS en vivo.');
});
