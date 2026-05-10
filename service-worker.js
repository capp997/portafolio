const CACHE_NAME = 'portfolio-v5-cache-v4';

self.addEventListener('install', event => {
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.map(key => {
        if (key !== CACHE_NAME) return caches.delete(key);
      }))
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') {
    event.respondWith(fetch(event.request));
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then(response => {
        const clone = response.clone();
        caches.open(CACHE_NAME).then(cache => {
          cache.put(event.request, clone);
        });
        return response;
      })
      .catch(() => caches.match(event.request))
  );
});

self.addEventListener('notificationclick', event => {
  event.notification.close();

  event.waitUntil(
    clients.openWindow('/index_v5.php')
  );
});

self.addEventListener('message', event => {
  if(event.data && event.data.type === 'SHOW_NOTIFICATION'){
    self.registration.showNotification(event.data.title, {
      body: event.data.body,
      icon: '/assets/icons/icon-192.png',
      badge: '/assets/icons/icon-192.png'
    });
  }
});
