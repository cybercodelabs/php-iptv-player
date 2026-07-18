/**
 * Reproductor VOD: Plyr CDN nativo (sin personalización).
 */
document.addEventListener('DOMContentLoaded', () => {
  const video = document.querySelector('[data-vod-player]');
  if (!(video instanceof HTMLVideoElement)) {
    return;
  }

  const src = video.getAttribute('data-src');
  if (!src) {
    return;
  }

  const source = document.createElement('source');
  source.src = src;
  source.type = 'video/mp4';
  video.appendChild(source);

  if (typeof Plyr === 'undefined') {
    video.src = src;
    return;
  }

  const player = new Plyr('#plyr-video', {
    controls: [
      'play-large',
      'play',
      'rewind',
      'fast-forward',
      'progress',
      'current-time',
      'duration',
      'mute',
      'volume',
      'settings',
      'fullscreen',
    ],
    seekTime: 10,
  });

  window.player = player;
});
