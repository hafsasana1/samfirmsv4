<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Security Headers Filter
 * 
 * Adds comprehensive security headers to all responses to prevent:
 * - Clickjacking attacks (X-Frame-Options)
 * - MIME type sniffing (X-Content-Type-Options)
 * - XSS attacks (X-XSS-Protection, Content-Security-Policy)
 * - Man-in-the-middle attacks (Strict-Transport-Security)
 * - Unauthorized feature access (Permissions-Policy)
 */
class SecurityHeaders implements FilterInterface
{
    /**
     * Add security headers to response
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Headers will be added in after()
        return;
    }

    /**
     * Add security headers after response is generated
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $security = config('Security');
        
        // ✅ X-Frame-Options: Prevent clickjacking
        // DENY = page cannot be displayed in a frame at all
        // SAMEORIGIN = page can only be displayed in a frame on the same origin as the page itself
        $response->setHeader('X-Frame-Options', $security->frameOptions);
        
        // ✅ X-Content-Type-Options: Prevent MIME type sniffing
        // nosniff = disables MIME type guessing
        $response->setHeader('X-Content-Type-Options', $security->contentTypeOptions);
        
        // ✅ X-XSS-Protection: Enable browser XSS filter (legacy but still recommended)
        // 1; mode=block = enable filter and prevent rendering if attack is detected
        $response->setHeader('X-XSS-Protection', $security->xssProtection);
        
        // ✅ Strict-Transport-Security: Force HTTPS connections
        // Instructs browsers to always use HTTPS for future connections to this domain
        // This header is only sent over HTTPS connections
        if ($request->isSecure()) {
            $response->setHeader('Strict-Transport-Security', $security->hsts);
        }
        
        // ⚠️ DISABLED: Content-Security-Policy blocks advertisement scripts
        // Uncomment to enable CSP (will block ads unless properly configured)
        // $response->setHeader('Content-Security-Policy', $security->csp);
        
        // ✅ Referrer-Policy: Control referrer information
        // strict-no-referrer = never send referrer information
        $response->setHeader('Referrer-Policy', $security->referrerPolicy);
        
        // ⚠️ MODIFIED: Permissions-Policy - Allow unload event for ads
        // Removed restrictions that block ad functionality
        $response->setHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=()');
        
        // ✅ Additional Security Headers
        
        // X-UA-Compatible: Force IE to use latest rendering engine (for older browsers)
        $response->setHeader('X-UA-Compatible', 'IE=edge');
        
        // Disable caching for sensitive pages
        if (strpos($request->getPath(), ADMIN_PATH) === 0) {
            $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->setHeader('Pragma', 'no-cache');
            $response->setHeader('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
        }
        
        return;
    }
}
