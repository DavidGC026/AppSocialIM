const CACHE_NAME = 'calendar-v1';

// Install event
self.addEventListener('install', (event) => {
  console.log('Service Worker installing.');
  self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
  console.log('Service Worker activating.');
  event.waitUntil(clients.claim());
});

// Message event to receive data from main thread
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'CHECK_EVENTS') {
    checkUpcomingEvents(event.data.events, event.data.token);
  }
});

// Function to check upcoming events
function checkUpcomingEvents(events, token) {
  if (!events || !events.length) return;

  const now = new Date();
  const tomorrow = new Date(now);
  tomorrow.setDate(tomorrow.getDate() + 1);
  const tomorrowStr = tomorrow.toISOString().split('T')[0];

  const oneHourFromNow = new Date(now.getTime() + 60 * 60 * 1000);
  const currentDateStr = now.toISOString().split('T')[0];

  events.forEach(event => {
    // Check for events tomorrow
    if (event.date === tomorrowStr) {
      self.registration.showNotification(`Evento mañana: ${event.title}`, {
        body: `Mañana a las ${event.startTime} - ${event.location || 'Sin ubicación'}`,
        icon: '/icon.svg',
        tag: `event-${event.id}-tomorrow`,
        requireInteraction: true
      });
    }

    // Check for events in the next hour
    if (event.date === currentDateStr) {
      const [hours, minutes] = event.startTime.split(':').map(Number);
      const eventTime = new Date(now);
      eventTime.setHours(hours, minutes, 0, 0);

      if (eventTime > now && eventTime <= oneHourFromNow) {
        self.registration.showNotification(`Evento próximamente: ${event.title}`, {
          body: `En ${Math.round((eventTime.getTime() - now.getTime()) / (1000 * 60))} minutos - ${event.location || 'Sin ubicación'}`,
          icon: '/icon.svg',
          tag: `event-${event.id}-soon`,
          requireInteraction: true
        });
      }
    }
  });
}

// Notification click event
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      // Check if there's already an open window/tab
      for (let i = 0; i < clientList.length; i++) {
        const client = clientList[i];
        if (client.url.includes(self.location.origin) && 'focus' in client) {
          return client.focus();
        }
      }
      // If no open window, open a new one
      if (clients.openWindow) {
        return clients.openWindow('/');
      }
    })
  );
});