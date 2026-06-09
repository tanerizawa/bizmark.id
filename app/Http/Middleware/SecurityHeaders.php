<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Enable XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy (restrict access to browser features)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Content Security Policy (CSP)
        // Only set if not already present to avoid duplicates
        if (! $response->headers->has('Content-Security-Policy')) {
            // Allow Vite dev server in local/development environment
            $viteDevServer = app()->environment('local') ? ' http://localhost:5173 ws://localhost:5173' : '';

            $csp = [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: https://cdn.tailwindcss.com https://unpkg.com https://cdnjs.cloudflare.com https://www.googletagmanager.com https://www.google-analytics.com https://cdn.jsdelivr.net https://static.cloudflareinsights.com https://cdn.ckeditor.com https://embed.tawk.to https://cdn.tawk.to".$viteDevServer,
                "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://cdn.ckeditor.com https://cdn.tawk.to".$viteDevServer,
                "img-src 'self' data: https: blob:",
                "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://cdn.ckeditor.com",
                "connect-src 'self' https://www.google-analytics.com https://www.googletagmanager.com https://analytics.google.com https://wa.me https://cdn.jsdelivr.net https://cloudflareinsights.com https://cdn.ckeditor.com https://unpkg.com https://cdnjs.cloudflare.com https://*.tile.openstreetmap.org https://server.arcgisonline.com https://ibnux.github.io https://va.tawk.to https://embed.tawk.to https://cdn.tawk.to".$viteDevServer,
                "frame-src 'self' https://www.youtube.com https://embed.tawk.to https://va.tawk.to",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
            ];
            $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        }

        // Strict Transport Security (HSTS) - Only in production with HTTPS
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
