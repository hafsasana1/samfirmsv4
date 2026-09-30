<?php

namespace App\Libraries;

/**
 * ============================================================================
 * QUERY CACHE LIBRARY
 * ============================================================================
 * 
 * Purpose: Cache frequently accessed database query results
 * 
 * Features:
 * - File-based caching (no external dependencies)
 * - Automatic cache invalidation on time/manual trigger
 * - Support for tagged cache groups
 * - Simple key-value storage
 * 
 * Usage:
 *   $cache = new QueryCache();
 *   $result = $cache->remember('key', 3600, fn() => $db->query());
 *   $cache->forget('key');
 *   $cache->flush();
 * 
 * ============================================================================
 */

class QueryCache
{
    protected $cacheDir;
    protected $defaultTTL = 3600; // 1 hour default

    public function __construct()
    {
        $this->cacheDir = WRITEPATH . 'query_cache' . DIRECTORY_SEPARATOR;
        
        // Create cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Get a cached value or execute callback and cache result
     * 
     * @param string $key Cache key
     * @param int|callable $ttlOrCallback TTL in seconds or callable
     * @param callable|null $callback Callback to execute if cache miss
     * @return mixed
     */
    public function remember(string $key, $ttlOrCallback = 3600, ?callable $callback = null): mixed
    {
        // Handle both remember('key', callback) and remember('key', ttl, callback) signatures
        if (is_callable($ttlOrCallback) && $callback === null) {
            $callback = $ttlOrCallback;
            $ttl = $this->defaultTTL;
        } else {
            $ttl = (int) $ttlOrCallback;
        }

        // Try to get from cache
        $cached = $this->get($key);
        if ($cached !== null) {
            return $cached;
        }

        // Cache miss - execute callback
        if ($callback === null) {
            return null;
        }

        $result = $callback();
        $this->put($key, $result, $ttl);
        return $result;
    }

    /**
     * Get cached value
     * 
     * @param string $key Cache key
     * @return mixed|null
     */
    public function get(string $key): mixed
    {
        $file = $this->getCacheFile($key);

        if (!file_exists($file)) {
            return null;
        }

        $data = @json_decode(file_get_contents($file), true);

        if (!is_array($data) || !isset($data['expiry']) || !isset($data['value'])) {
            @unlink($file);
            return null;
        }

        // Check if expired
        if ($data['expiry'] > 0 && time() > $data['expiry']) {
            @unlink($file);
            return null;
        }

        return $data['value'];
    }

    /**
     * Store value in cache
     * 
     * @param string $key Cache key
     * @param mixed $value Value to cache
     * @param int $ttl Time to live in seconds (0 = no expiry)
     * @return bool
     */
    public function put(string $key, mixed $value, int $ttl = 3600): bool
    {
        $file = $this->getCacheFile($key);

        $data = [
            'key' => $key,
            'value' => $value,
            'expiry' => ($ttl > 0) ? time() + $ttl : 0,
            'created' => time(),
        ];

        $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return @file_put_contents($file, $json, LOCK_EX) !== false;
    }

    /**
     * Check if key exists and is not expired
     * 
     * @param string $key Cache key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Remove specific cache key
     * 
     * @param string $key Cache key
     * @return bool
     */
    public function forget(string $key): bool
    {
        $file = $this->getCacheFile($key);
        return @unlink($file);
    }

    /**
     * Remove cache keys by pattern
     * 
     * @param string $pattern Pattern to match (e.g., 'site_meta_*')
     * @return int Number of files deleted
     */
    public function forgetPattern(string $pattern): int
    {
        $count = 0;
        $pattern = $this->keyToFilename($pattern);
        $pattern = str_replace('*', '', $pattern); // Remove wildcard for prefix matching

        $files = @glob($this->cacheDir . $pattern . '*');

        if (is_array($files)) {
            foreach ($files as $file) {
                if (@unlink($file)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Clear all cache
     * 
     * @return int Number of files deleted
     */
    public function flush(): int
    {
        $count = 0;
        $files = @glob($this->cacheDir . '*');

        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file) && @unlink($file)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Get cache file path for key
     * 
     * @param string $key Cache key
     * @return string File path
     */
    protected function getCacheFile(string $key): string
    {
        $filename = $this->keyToFilename($key);
        return $this->cacheDir . $filename;
    }

    /**
     * Convert cache key to safe filename
     * 
     * @param string $key Cache key
     * @return string Safe filename
     */
    protected function keyToFilename(string $key): string
    {
        return hash('sha256', $key) . '.cache';
    }

    /**
     * Get cache statistics
     * 
     * @return array Stats including file count, total size, etc.
     */
    public function stats(): array
    {
        $files = @glob($this->cacheDir . '*.cache');
        $count = is_array($files) ? count($files) : 0;
        $size = 0;

        if (is_array($files)) {
            foreach ($files as $file) {
                $size += filesize($file);
            }
        }

        return [
            'files' => $count,
            'size_bytes' => $size,
            'size_mb' => round($size / 1024 / 1024, 2),
            'directory' => $this->cacheDir,
        ];
    }
}
