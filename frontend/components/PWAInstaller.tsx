'use client';

import { useEffect } from 'react';

export default function PWAInstaller() {
  useEffect(() => {
    // Register service worker
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js')
        .then((registration) => {
          console.log('SW registered: ', registration);
          
          // Check for updates
          registration.addEventListener('updatefound', () => {
            const newWorker = registration.installing;
            if (newWorker) {
              newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                  // New version available
                  if (confirm('Versi baru tersedia. Refresh halaman?')) {
                    newWorker.postMessage({ type: 'SKIP_WAITING' });
                    window.location.reload();
                  }
                }
              });
            }
          });
        })
        .catch((registrationError) => {
          console.log('SW registration failed: ', registrationError);
        });
    }

    // Handle service worker updates
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.addEventListener('controllerchange', () => {
        window.location.reload();
      });
    }

    // Handle online/offline status
    const handleOnlineStatus = () => {
      const isOnline = navigator.onLine;
      const statusElement = document.getElementById('network-status');
      
      if (statusElement) {
        if (isOnline) {
          statusElement.style.display = 'none';
        } else {
          statusElement.style.display = 'block';
          statusElement.innerHTML = `
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
              <strong class="font-bold">Offline!</strong>
              <span class="block sm:inline"> Anda sedang offline. Beberapa fitur mungkin tidak tersedia.</span>
            </div>
          `;
        }
      }
    };

    window.addEventListener('online', handleOnlineStatus);
    window.addEventListener('offline', handleOnlineStatus);
    handleOnlineStatus(); // Check initial status

    // PWA install prompt
    let deferredPrompt: BeforeInstallPromptEvent | null = null;

    window.addEventListener('beforeinstallprompt', (e) => {
      // Prevent Chrome 67 and earlier from automatically showing the prompt
      e.preventDefault();
      // Stash the event so it can be triggered later
      deferredPrompt = e as BeforeInstallPromptEvent;
      
      // Show install button
      const installButton = document.getElementById('pwa-install-button');
      if (installButton) {
        installButton.style.display = 'block';
      }
    });

    // Handle install button click
    const handleInstallClick = async () => {
      if (deferredPrompt) {
        // Show the prompt
        deferredPrompt.prompt();
        
        // Wait for the user to respond to the prompt
        const { outcome } = await deferredPrompt.userChoice;
        console.log(`User response to the install prompt: ${outcome}`);
        
        // Clear the saved prompt
        deferredPrompt = null;
        
        // Hide install button
        const installButton = document.getElementById('pwa-install-button');
        if (installButton) {
          installButton.style.display = 'none';
        }
      }
    };

    // Add event listener to install button
    const installButton = document.getElementById('pwa-install-button');
    if (installButton) {
      installButton.addEventListener('click', handleInstallClick);
    }

    // Cleanup
    return () => {
      window.removeEventListener('online', handleOnlineStatus);
      window.removeEventListener('offline', handleOnlineStatus);
      if (installButton) {
        installButton.removeEventListener('click', handleInstallClick);
      }
    };
  }, []);

  return null; // This component only handles side effects
}

// Type definition for beforeinstallprompt event
interface BeforeInstallPromptEvent extends Event {
  readonly platforms: string[];
  readonly userChoice: Promise<{
    outcome: 'accepted' | 'dismissed';
    platform: string;
  }>;
  prompt(): Promise<void>;
}
