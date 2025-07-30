/**
 * Production Performance Optimization Configuration
 * Optimizations for production deployment and performance monitoring
 */

import type { NextConfig } from 'next';

const nextConfig: NextConfig = {
  // Production optimizations
  swcMinify: true,
  compress: true,
  poweredByHeader: false,
  
  // Performance optimizations
  images: {
    formats: ['image/webp', 'image/avif'],
    deviceSizes: [640, 750, 828, 1080, 1200, 1920, 2048, 3840],
    imageSizes: [16, 32, 48, 64, 96, 128, 256, 384],
    minimumCacheTTL: 86400, // 24 hours
    dangerouslyAllowSVG: true,
    contentSecurityPolicy: "default-src 'self'; script-src 'none'; sandbox;",
  },
  
  // Bundle optimization
  experimental: {
    optimizeCss: true,
    optimizePackageImports: [
      'lucide-react',
      '@headlessui/react',
      'date-fns',
    ],
    serverComponentsExternalPackages: ['@node-rs/argon2'],
  },
  
  // Output configuration for Docker
  output: 'standalone',
  
  // Security headers
  async headers() {
    return [
      {
        source: '/(.*)',
        headers: [
          {
            key: 'X-DNS-Prefetch-Control',
            value: 'on'
          },
          {
            key: 'Strict-Transport-Security',
            value: 'max-age=63072000; includeSubDomains; preload'
          },
          {
            key: 'X-Frame-Options',
            value: 'DENY'
          },
          {
            key: 'X-Content-Type-Options',
            value: 'nosniff'
          },
          {
            key: 'Referrer-Policy',
            value: 'strict-origin-when-cross-origin'
          },
          {
            key: 'Permissions-Policy',
            value: 'camera=(), microphone=(), geolocation=(), interest-cohort=()'
          }
        ],
      },
      {
        source: '/api/(.*)',
        headers: [
          {
            key: 'Cache-Control',
            value: 'no-store, no-cache, must-revalidate, proxy-revalidate'
          }
        ],
      },
      {
        source: '/static/(.*)',
        headers: [
          {
            key: 'Cache-Control',
            value: 'public, max-age=31536000, immutable'
          }
        ],
      }
    ];
  },
  
  // Redirects for SEO
  async redirects() {
    return [
      {
        source: '/dashboard/home',
        destination: '/dashboard',
        permanent: true,
      },
      {
        source: '/api/docs',
        destination: '/api/documentation',
        permanent: true,
      }
    ];
  },
  
  // Environment variables validation
  env: {
    NEXT_PUBLIC_API_URL: process.env.NEXT_PUBLIC_API_URL,
    NEXT_PUBLIC_APP_ENV: process.env.NODE_ENV,
    NEXT_PUBLIC_VERSION: process.env.npm_package_version || '1.0.0',
  },
  
  // Webpack optimizations
  webpack: (config, { dev }) => {
    // Production optimizations
    if (!dev) {
      config.optimization.splitChunks = {
        chunks: 'all',
        cacheGroups: {
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            chunks: 'all',
            name: 'vendors',
            priority: 20,
          },
          common: {
            minChunks: 2,
            chunks: 'all',
            name: 'common',
            priority: 10,
          },
        },
      };
      
      // Bundle analyzer in production builds
      if (process.env.ANALYZE === 'true') {
        console.log('Bundle analysis enabled. Install webpack-bundle-analyzer to use this feature.');
      }
    }
    
    return config;
  },
};

export default nextConfig;
