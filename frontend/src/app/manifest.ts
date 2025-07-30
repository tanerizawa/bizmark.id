import { NextResponse } from 'next/server';

export async function GET() {
  const manifest = {
    name: 'Bizmark.id - Platform Perizinan UMKM',
    short_name: 'Bizmark.id',
    description: 'Platform digital untuk pengurusan izin usaha UMKM Indonesia',
    start_url: '/',
    display: 'standalone',
    background_color: '#ffffff',
    theme_color: '#2563eb',
    orientation: 'portrait-primary',
    scope: '/',
    lang: 'id',
    categories: ['business', 'productivity', 'government'],
    
    icons: [
      {
        src: '/icons/icon-72x72.png',
        sizes: '72x72',
        type: 'image/png',
        purpose: 'maskable any'
      },
      {
        src: '/icons/icon-96x96.png',
        sizes: '96x96',
        type: 'image/png',
        purpose: 'maskable any'
      },
      {
        src: '/icons/icon-128x128.png',
        sizes: '128x128',
        type: 'image/png',
        purpose: 'maskable any'
      },
      {
        src: '/icons/icon-144x144.png',
        sizes: '144x144',
        type: 'image/png',
        purpose: 'maskable any'
      },
      {
        src: '/icons/icon-152x152.png',
        sizes: '152x152',
        type: 'image/png',
        purpose: 'maskable any'
      },
      {
        src: '/icons/icon-192x192.png',
        sizes: '192x192',
        type: 'image/png',
        purpose: 'maskable any'
      },
      {
        src: '/icons/icon-384x384.png',
        sizes: '384x384',
        type: 'image/png',
        purpose: 'maskable any'
      },
      {
        src: '/icons/icon-512x512.png',
        sizes: '512x512',
        type: 'image/png',
        purpose: 'maskable any'
      }
    ],
    
    shortcuts: [
      {
        name: 'Dashboard',
        short_name: 'Dashboard',
        description: 'Lihat dashboard utama',
        url: '/dashboard',
        icons: [{ src: '/icons/dashboard-96x96.png', sizes: '96x96' }]
      },
      {
        name: 'Aplikasi Baru',
        short_name: 'Aplikasi',
        description: 'Buat aplikasi izin baru',
        url: '/dashboard/applications/create',
        icons: [{ src: '/icons/application-96x96.png', sizes: '96x96' }]
      },
      {
        name: 'Dokumen',
        short_name: 'Dokumen',
        description: 'Kelola dokumen',
        url: '/dashboard/documents',
        icons: [{ src: '/icons/document-96x96.png', sizes: '96x96' }]
      }
    ],
    
    screenshots: [
      {
        src: '/screenshots/desktop-dashboard.png',
        sizes: '1280x720',
        type: 'image/png',
        platform: 'wide',
        label: 'Dashboard UMKM'
      },
      {
        src: '/screenshots/mobile-dashboard.png',
        sizes: '360x640',
        type: 'image/png',
        platform: 'narrow',
        label: 'Dashboard Mobile'
      }
    ],
    
    related_applications: [],
    prefer_related_applications: false,
    
    display_override: ['window-controls-overlay', 'standalone', 'minimal-ui'],
    
    edge_side_panel: {
      preferred_width: 400
    },
    
    launch_handler: {
      client_mode: 'navigate-existing'
    }
  };

  return NextResponse.json(manifest, {
    headers: {
      'Content-Type': 'application/manifest+json',
      'Cache-Control': 'public, max-age=31536000, immutable'
    }
  });
}
