// Import Firebase scripts
importScripts('https://www.gstatic.com/firebasejs/12.4.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging-compat.js');

// Initialize Firebase
firebase.initializeApp({
    apiKey: "AIzaSyABsAHy3BtYUkcV4z3gjCl3NNU35ye4LFs",
    authDomain: "inicio-de-sesion-94ddc.firebaseapp.com",
    databaseURL: "https://inicio-de-sesion-94ddc.firebaseio.com",
    projectId: "inicio-de-sesion-94ddc",
    storageBucket: "inicio-de-sesion-94ddc.firebasestorage.app",
    messagingSenderId: "204683025370",
    appId: "1:204683025370:web:c424b261eff8d566be7ee3"
});

// Initialize Firebase Messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('Received background message ', payload);
    
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        tag: payload.data?.notification_id || 'notification',
        data: payload.data,
        actions: [
            {
                action: 'view',
                title: 'Ver',
                icon: '/icons/view.svg'
            },
            {
                action: 'dismiss',
                title: 'Cerrar',
                icon: '/icons/close.svg'
            }
        ]
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Get base URL from service worker scope
function getBaseUrl() {
    const scope = self.registration.scope;
    const url = new URL(scope);
    return url.pathname.replace(/\/$/, ''); // Remove trailing slash
}

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);
    
    event.notification.close();
    
    const baseUrl = getBaseUrl();
    
    if (event.action === 'view') {
        // Open the app
        event.waitUntil(
            clients.openWindow(`${baseUrl}/notifications`)
        );
    } else if (event.action === 'dismiss') {
        // Just close the notification
        return;
    } else {
        // Default action - open the app
        event.waitUntil(
            clients.openWindow(`${baseUrl}/notifications`)
        );
    }
});
