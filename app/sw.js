// Service Worker do App Caminhos da Fé
const CACHE_NAME = 'caminhos-da-fe-v8';
const ASSETS_TO_CACHE = [
  './index.html',
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

self.addEventListener('fetch', (e) => {
  e.respondWith(
    caches.match(e.request).then((res) => res || fetch(e.request))
  );
});
