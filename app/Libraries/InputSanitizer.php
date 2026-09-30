<?php

namespace App\Libraries;

/**
 * Input Sanitizer Library
 * 
 * Provides comprehensive input validation and sanitization methods
 * to prevent XSS, SQL Injection, and other security vulnerabilities
 */
class InputSanitizer
{
    /**
     * Sanitize string - remove HTML tags and encode special characters
     */
    public static function sanitizeString(?string $input): string
    {
        if ($input === null) {
            return '';
        }
        
        // Remove HTML tags
        $input = strip_tags($input);
        
        // Encode special characters
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        // Trim whitespace
        $input = trim($input);
        
        return $input;
    }
    
    /**
     * Sanitize email address
     */
    public static function sanitizeEmail(?string $email): string
    {
        if ($email === null) {
            return '';
        }
        
        // Remove all characters except letters, digits and !#$%&'*+-=?^_`{|}~@.[]
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        return trim($email);
    }
    
    /**
     * Validate email address
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Sanitize URL
     */
    public static function sanitizeUrl(?string $url): string
    {
        if ($url === null) {
            return '';
        }
        
        // Remove all characters except letters, digits and $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=
        $url = filter_var($url, FILTER_SANITIZE_URL);
        
        return trim($url);
    }
    
    /**
     * Validate URL
     */
    public static function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Sanitize integer
     */
    public static function sanitizeInt($input): int
    {
        return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Sanitize float/decimal
     */
    public static function sanitizeFloat($input): float
    {
        return (float) filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    
    /**
     * Sanitize HTML content (for rich text editors)
     * Allows safe HTML tags only
     */
    public static function sanitizeHtml(?string $html): string
    {
        if ($html === null) {
            return '';
        }
        
        // Define allowed tags for rich content
        $allowedTags = '<p><br><strong><em><u><s><a><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><code><pre><img><table><thead><tbody><tr><th><td><div><span>';
        
        // Strip tags except allowed ones
        $html = strip_tags($html, $allowedTags);
        
        // Remove potentially dangerous attributes
        $html = preg_replace('/<(\w+)[^>]*?(on\w+\s*=)[^>]*?>/i', '<$1>', $html);
        $html = preg_replace('/<(\w+)[^>]*?(javascript:)[^>]*?>/i', '<$1>', $html);
        
        return $html;
    }
    
    /**
     * Sanitize filename - remove path traversal and dangerous characters
     */
    public static function sanitizeFilename(?string $filename): string
    {
        if ($filename === null) {
            return '';
        }
        
        // Remove path traversal attempts
        $filename = basename($filename);
        
        // Remove special characters except dots, dashes, underscores
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Prevent double extensions
        $filename = preg_replace('/\.+/', '.', $filename);
        
        return $filename;
    }
    
    /**
     * Sanitize slug/URL-friendly string
     */
    public static function sanitizeSlug(?string $slug): string
    {
        if ($slug === null) {
            return '';
        }
        
        // Convert to lowercase
        $slug = strtolower($slug);
        
        // Replace spaces and underscores with hyphens
        $slug = preg_replace('/[\s_]+/', '-', $slug);
        
        // Remove all characters except letters, numbers, and hyphens
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        
        // Remove consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Trim hyphens from edges
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Sanitize phone number
     */
    public static function sanitizePhone(?string $phone): string
    {
        if ($phone === null) {
            return '';
        }
        
        // Remove all non-numeric characters except + at the beginning
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Ensure + is only at the beginning
        if (strpos($phone, '+') !== false) {
            $phone = '+' . str_replace('+', '', $phone);
        }
        
        return $phone;
    }
    
    /**
     * Validate phone number (basic validation)
     */
    public static function validatePhone(string $phone): bool
    {
        // Basic validation: 10-15 digits, optional + at start
        return preg_match('/^\+?[0-9]{10,15}$/', $phone) === 1;
    }
    
    /**
     * Sanitize array of strings
     */
    public static function sanitizeArray(?array $array): array
    {
        if ($array === null) {
            return [];
        }
        
        return array_map([self::class, 'sanitizeString'], $array);
    }
    
    /**
     * Validate required field
     */
    public static function validateRequired($value): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }
        
        return !empty($value);
    }
    
    /**
     * Validate minimum length
     */
    public static function validateMinLength(string $value, int $minLength): bool
    {
        return strlen($value) >= $minLength;
    }
    
    /**
     * Validate maximum length
     */
    public static function validateMaxLength(string $value, int $maxLength): bool
    {
        return strlen($value) <= $maxLength;
    }
    
    /**
     * Validate alphanumeric
     */
    public static function validateAlphanumeric(string $value): bool
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $value) === 1;
    }
    
    /**
     * Validate alphabetic only
     */
    public static function validateAlpha(string $value): bool
    {
        return preg_match('/^[a-zA-Z]+$/', $value) === 1;
    }
    
    /**
     * Validate numeric only
     */
    public static function validateNumeric(string $value): bool
    {
        return is_numeric($value);
    }
    
    /**
     * Prevent SQL injection in direct queries
     * Note: Use query builder or prepared statements instead when possible
     */
    public static function escapeSql(string $value): string
    {
        $db = \Config\Database::connect();
        return $db->escapeString($value);
    }
    
    /**
     * Validate file upload
     */
    public static function validateFileUpload(array $file, array $allowedTypes, int $maxSize): array
    {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $errors[] = 'No file uploaded';
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error: ' . $file['error'];
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed (' . ($maxSize / 1024 / 1024) . 'MB)';
        }
        
        // Validate file type
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowedTypes)) {
            $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes);
        }
        
        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'ico' => 'image/x-icon',
            'zip' => 'application/zip',
        ];
        
        if (isset($allowedMimes[$fileExt]) && $mimeType !== $allowedMimes[$fileExt]) {
            $errors[] = 'Invalid file type (MIME type mismatch)';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'sanitizedName' => self::sanitizeFilename($file['name']),
            'mimeType' => $mimeType,
        ];
    }
    
    /**
     * Sanitize and validate username
     */
    public static function sanitizeUsername(?string $username): string
    {
        if ($username === null) {
            return '';
        }
        
        // Allow only alphanumeric, underscore, hyphen, and dot
        $username = preg_replace('/[^a-zA-Z0-9._-]/', '', $username);
        
        return strtolower(trim($username));
    }
    
    /**
     * Validate password strength
     */
    public static function validatePasswordStrength(string $password, int $minLength = 8): array
    {
        $errors = [];
        
        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least $minLength characters long";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
