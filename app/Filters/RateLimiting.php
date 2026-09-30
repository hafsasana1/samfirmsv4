<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Rate Limiting Filter
 * 
 * Prevents brute force attacks by limiting login attempts
 * - Max 5 failed attempts per IP address within 15 minutes
 * - Account lockout after exceeding limit
 * - Automatic cleanup of old attempts
 */
class RateLimiting implements FilterInterface
{
    protected $db;
    
    // Rate limiting settings
    protected $maxAttempts = 5;           // Maximum failed attempts
    protected $lockoutTime = 900;         // Lockout time in seconds (15 minutes)
    protected $attemptWindow = 900;       // Time window to count attempts (15 minutes)
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    /**
     * Check if IP is rate limited before processing request
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Only apply to login POST requests
        if ($request->getMethod() !== 'post') {
            return;
        }
        
        $ipAddress = $request->getIPAddress();
        
        // Clean up old attempts (older than 1 hour)
        $this->cleanupOldAttempts();
        
        // Check if IP is currently locked out
        if ($this->isLockedOut($ipAddress)) {
            $remainingTime = $this->getRemainingLockoutTime($ipAddress);
            
            // Set error in session
            session()->set('error', "Too many failed login attempts. Please try again in " . ceil($remainingTime / 60) . " minutes.");
            
            // Return response with 429 status
            return redirect()->back()->with('error', "Too many failed login attempts. Account temporarily locked.");
        }
        
        return;
    }
    
    /**
     * After processing - record login attempt
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // This is handled in the login controller
        return;
    }
    
    /**
     * Check if IP address is locked out
     */
    protected function isLockedOut(string $ipAddress): bool
    {
        $timeWindow = date('Y-m-d H:i:s', time() - $this->attemptWindow);
        
        $failedAttempts = $this->db->table('fw_login_attempts')
            ->where('ipAddress', $ipAddress)
            ->where('success', 'No')
            ->where('attemptTime >', $timeWindow)
            ->countAllResults();
        
        return $failedAttempts >= $this->maxAttempts;
    }
    
    /**
     * Get remaining lockout time in seconds
     */
    protected function getRemainingLockoutTime(string $ipAddress): int
    {
        $lastAttempt = $this->db->table('fw_login_attempts')
            ->select('attemptTime')
            ->where('ipAddress', $ipAddress)
            ->where('success', 'No')
            ->orderBy('attemptTime', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
        
        if (!$lastAttempt) {
            return 0;
        }
        
        $lastAttemptTime = strtotime($lastAttempt->attemptTime);
        $lockoutExpires = $lastAttemptTime + $this->lockoutTime;
        $remaining = $lockoutExpires - time();
        
        return max(0, $remaining);
    }
    
    /**
     * Clean up old login attempts (older than 1 hour)
     */
    protected function cleanupOldAttempts(): void
    {
        $cleanupTime = date('Y-m-d H:i:s', time() - 3600); // 1 hour ago
        
        $this->db->table('fw_login_attempts')
            ->where('attemptTime <', $cleanupTime)
            ->delete();
    }
    
    /**
     * Record a login attempt (call this from login controller)
     */
    public static function recordAttempt(string $ipAddress, ?string $username = null, bool $success = false): void
    {
        $db = \Config\Database::connect();
        
        $db->table('fw_login_attempts')->insert([
            'ipAddress'   => $ipAddress,
            'username'    => $username,
            'attemptTime' => date('Y-m-d H:i:s'),
            'success'     => $success ? 'Yes' : 'No',
        ]);
    }
    
    /**
     * Clear failed attempts for an IP (call after successful login)
     */
    public static function clearAttempts(string $ipAddress): void
    {
        $db = \Config\Database::connect();
        
        $db->table('fw_login_attempts')
            ->where('ipAddress', $ipAddress)
            ->where('success', 'No')
            ->delete();
    }
    
    /**
     * Get failed attempt count for an IP
     */
    public static function getAttemptCount(string $ipAddress): int
    {
        $db = \Config\Database::connect();
        $timeWindow = date('Y-m-d H:i:s', time() - 900); // 15 minutes
        
        return $db->table('fw_login_attempts')
            ->where('ipAddress', $ipAddress)
            ->where('success', 'No')
            ->where('attemptTime >', $timeWindow)
            ->countAllResults();
    }
}
