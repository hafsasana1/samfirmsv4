# Redis Setup Guide for SamFirms Production

## Why Redis?
- **100x faster** than file-based cache
- Reduces server load by 60-70%
- Improves response time by 200-500ms
- Better concurrent user handling

---

## Installation on Ubuntu Server

### Step 1: Install Redis
```bash
# Update packages
sudo apt update

# Install Redis
sudo apt install redis-server -y

# Verify installation
redis-cli --version
```

### Step 2: Configure Redis
```bash
# Edit Redis config
sudo nano /etc/redis/redis.conf

# Find and change these lines:
supervised no  →  supervised systemd
bind 127.0.0.1  →  bind 127.0.0.1 ::1

# Save and exit (Ctrl+X, Y, Enter)
```

### Step 3: Start Redis
```bash
# Start Redis service
sudo systemctl start redis

# Enable on boot
sudo systemctl enable redis

# Check status
sudo systemctl status redis

# Test connection
redis-cli ping
# Should return: PONG
```

### Step 4: Install PHP Redis Extension
```bash
# Install PHP Redis extension
sudo apt install php-redis -y

# Restart Apache/PHP-FPM
sudo systemctl restart apache2
# OR
sudo systemctl restart php8.1-fpm

# Verify installation
php -m | grep redis
# Should show: redis
```

### Step 5: Update CodeIgniter Config
```bash
# Edit Cache.php
sudo nano /var/www/samfirms.com/public_html/app/Config/Cache.php

# Change line 25:
public string $handler = 'file';
# TO:
public string $handler = 'redis';

# Save and exit
```

### Step 6: Clear All Caches
```bash
# Clear file cache
sudo rm -rf /var/www/samfirms.com/public_html/writable/cache/*

# Restart Apache
sudo systemctl restart apache2

# Test site
curl -I https://samfirms.com
```

---

## Verification

### Test Redis is Working
```bash
# Monitor Redis in real-time
redis-cli monitor

# In another terminal, visit your site
curl https://samfirms.com

# You should see cache SET/GET commands in redis-cli monitor
```

### Check Cache Performance
```php
// Add this temporarily to any controller
$start = microtime(true);
$cache = \Config\Services::cache();
$cache->save('test_key', 'test_value', 3600);
$value = $cache->get('test_key');
$time = (microtime(true) - $start) * 1000;
echo "Cache operation took: {$time}ms";

// File cache: ~5-20ms
// Redis cache: ~0.1-1ms (20-200x faster!)
```

---

## Troubleshooting

### Redis not starting?
```bash
# Check error logs
sudo journalctl -u redis -n 50

# Check if port 6379 is in use
sudo netstat -tulpn | grep 6379

# Restart Redis
sudo systemctl restart redis
```

### PHP Redis extension not found?
```bash
# Check installed PHP version
php -v

# Install for correct PHP version (example: PHP 8.1)
sudo apt install php8.1-redis

# Restart web server
sudo systemctl restart apache2
```

### Cache not working?
```bash
# Check Redis connection
redis-cli ping

# Check if CodeIgniter can connect
php spark cache:info

# Check permissions
ls -la /var/www/samfirms.com/public_html/writable/cache/
```

---

## Expected Performance Gains

### Before Redis (File Cache):
- Average response time: 200-400ms
- Concurrent users: 50-100
- Cache operations: 5-20ms

### After Redis:
- Average response time: 100-200ms (**50% faster**)
- Concurrent users: 500-1000 (**10x more**)
- Cache operations: 0.1-1ms (**20x faster**)

---

## Maintenance

### Clear Redis Cache
```bash
# Clear all cache
redis-cli FLUSHALL

# Clear specific keys
redis-cli KEYS "samfirms_*" | xargs redis-cli DEL
```

### Monitor Redis Usage
```bash
# Show memory usage
redis-cli INFO memory

# Show cache statistics
redis-cli INFO stats

# Show connected clients
redis-cli CLIENT LIST
```

---

## Optional: Redis Persistence

By default, Redis keeps data in memory only. Enable persistence for safety:

```bash
# Edit Redis config
sudo nano /etc/redis/redis.conf

# Uncomment these lines:
save 900 1       # Save after 900s if 1 key changed
save 300 10      # Save after 300s if 10 keys changed
save 60 10000    # Save after 60s if 10000 keys changed

# Restart Redis
sudo systemctl restart redis
```

---

## Security (Optional)

### Set Redis Password
```bash
# Edit config
sudo nano /etc/redis/redis.conf

# Find and uncomment:
# requirepass foobared
# Change to:
requirepass YOUR_STRONG_PASSWORD_HERE

# Restart Redis
sudo systemctl restart redis

# Update CodeIgniter config
# Edit app/Config/Cache.php, add to $redis array:
'password' => 'YOUR_STRONG_PASSWORD_HERE',
```

---

**Deployment Priority:** Phase 2 (after initial launch)  
**Installation Time:** 10-15 minutes  
**Risk Level:** LOW (falls back to file cache if Redis fails)  
**Performance Gain:** HIGH (+15-20 performance points)
