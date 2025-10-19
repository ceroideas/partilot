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
        requireInteraction: true, // Keep notification visible until user interacts
        silent: false, // Enable sound
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
    
    // Play notification sound in background
    playNotificationSound();
});

// Function to play notification sound in Service Worker
function playNotificationSound() {
    try {
        // Create audio context in Service Worker
        const audioContext = new (self.AudioContext || self.webkitAudioContext)();
        
        // Create a more pleasant notification sound (soft bell-like tone)
        const oscillator1 = audioContext.createOscillator();
        const oscillator2 = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        const filter = audioContext.createBiquadFilter();

        // Connect nodes
        oscillator1.connect(filter);
        oscillator2.connect(filter);
        filter.connect(gainNode);
        gainNode.connect(audioContext.destination);

        // Configure filter for softer sound
        filter.type = 'lowpass';
        filter.frequency.setValueAtTime(2000, audioContext.currentTime);

        // Configure oscillators for pleasant chord
        oscillator1.type = 'sine';
        oscillator1.frequency.setValueAtTime(523, audioContext.currentTime); // C5 note
        oscillator2.type = 'sine';
        oscillator2.frequency.setValueAtTime(659, audioContext.currentTime); // E5 note
        
        // Configure volume envelope (softer, longer)
        gainNode.gain.setValueAtTime(0, audioContext.currentTime);
        gainNode.gain.linearRampToValueAtTime(0.15, audioContext.currentTime + 0.05);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.8);

        // Play the sound
        oscillator1.start(audioContext.currentTime);
        oscillator2.start(audioContext.currentTime);
        oscillator1.stop(audioContext.currentTime + 0.8);
        oscillator2.stop(audioContext.currentTime + 0.8);
    } catch (error) {
        console.log('No se pudo reproducir el sonido en Service Worker:', error);
    }
}

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
