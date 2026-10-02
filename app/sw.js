// Service Worker do App Caminhos da Fé v11 (Network-First para HTML)
const CACHE_NAME = 'caminhos-da-fe-v11';
const ASSETS_TO_CACHE = [
  './manifest.json',
  './images/app-icon.jpg',
  './images/vela-altar.jpg',
  './images/frequencia-som.jpg',
  './images/manuscrito-9-palavras.jpg',
  './images/padre-elias.jpg'
];

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS_TO_CACHE))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) return caches.delete(key);
        })
      );
    })
  );
  self.clients.claim();
});

// Network-First para páginas HTML (garante que atualizações do app entrem imediatamente no telemóvel do cliente)
self.addEventListener('fetch', (e) => {
  const isHtml = e.request.mode === 'navigate' || (e.request.headers.get('accept') && e.request.headers.get('accept').includes('text/html'));

  if (isHtml) {
    e.respondWith(
      fetch(e.request)
        .then((res) => {
          const clone = res.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(e.request, clone));
          return res;
        })
        .catch(() => caches.match(e.request))
    );
  } else {
    e.respondWith(
      caches.match(e.request).then((cached) => cached || fetch(e.request).then((res) => {
        const clone = res.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(e.request, clone));
        return res;
      }))
    );
  }
});
