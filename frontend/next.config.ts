import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  // Production-ready configuration
  reactStrictMode: true,
  
  // Configure allowed development origins
  allowedDevOrigins: ["127.0.0.1", "localhost"],
  
  // Turbopack configuration (stable in Next.js 15)
  turbopack: {
    rules: {
      '*.svg': {
        loaders: ['@svgr/webpack'],
        as: '*.js',
      },
    },
  },
  
  // Experimental features untuk production performance
  experimental: {
    optimizePackageImports: ['@headlessui/react', 'lucide-react'],
  },
  
  // Compiler options
  compiler: {
    removeConsole: process.env.NODE_ENV === 'production',
  },
  
  // Webpack configuration yang benar
  webpack: (config, { dev, isServer }) => {
    if (dev && !isServer) {
      // Proper webpack configuration
      config.resolve.fallback = {
        ...config.resolve.fallback,
        fs: false,
        net: false,
        tls: false,
      };
      
      // Fix untuk development
      config.watchOptions = {
        poll: 1000,
        aggregateTimeout: 300,
        ignored: [
          '**/node_modules',
          '**/.next/**',
          '**/dist/**',
        ],
      };
    }
    
    return config;
  },
  
  // Headers untuk security (production)
  async headers() {
    return [
      {
        source: '/(.*)',
        headers: [
          {
            key: 'X-Frame-Options',
            value: 'DENY',
          },
          {
            key: 'X-Content-Type-Options',
            value: 'nosniff',
          },
          {
            key: 'Referrer-Policy',
            value: 'origin-when-cross-origin',
          },
        ],
      },
    ];
  },
  
  // Environment variables
  env: {
    PORT: process.env.PORT || '3000',
    NEXT_PUBLIC_API_URL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3001',
  },
  
  // Images optimization
  images: {
    domains: ['localhost'],
    formats: ['image/webp', 'image/avif'],
  },
  
  // Output configuration for production
  output: 'standalone',
};

export default nextConfig;
