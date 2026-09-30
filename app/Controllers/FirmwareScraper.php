<?php

namespace App\Controllers;

/**
 * ============================================================================
 * FIRMWARE SCRAPER - NEW FRESH IMPLEMENTATION
 * ============================================================================
 * 
 * Purpose: Scrapes firmware data from SXRom.com with OdinRom.com download links
 * 
 * Scraping Strategy:
 * - Primary Source: SXRom.com (firmware metadata, file sizes, faster updates)
 * - Secondary Source: OdinRom.com (ydfile.com download links via safe matching)
 * - Matching Logic: Only use OdinRom link if Model+CSC+PDA exactly match
 * - Fallback: Samsung official server URLs if no OdinRom match
 * 
 * Note: SamMobile scraper is disabled - only using SXRom.com + OdinRom matching
 * 
 * Features:
 * - Multi-source scraping with fallback
 * - Caching system to reduce load
 * - Duplicate detection
 * - Auto device name extraction
 * - CSC/Region parsing
 * - Error handling and logging
 * 
 * Database Tables Used:
 * - fw_posts (main firmware storage)
 * - fw_scraper_log (scraping history)
 * - fw_scraper_cache (cache storage)
 * 
 * Author: Created by Kiro AI
 * Date: August 19, 2026
 * ============================================================================
 */

class FirmwareScraper extends BaseController
{
    protected $db;
    protected $cacheTime = 3600; // 1 hour cache
    protected $sources = [
        'odinrom' => [
            'name' => 'OdinRom',
            'url' => 'https://www.odinrom.com/',
            'priority' => 1,
            'enabled' => true,
        ],
        // SXRom disabled - switched to OdinRom for global coverage
        // SamMobile scraper disabled - using only OdinRom.com
        /*
        'sxrom' => [
            'name' => 'SXRom',
            'url' => 'https://www.sxrom.com/',
            'priority' => 2,
            'enabled' => false,
        ],
        'sammobile' => [
            'name' => 'SamMobile',
            'url' => 'https://sammobile.com/firmwares/',
            'priority' => 2,
            'enabled' => false,
        ],
        */
    ];

    public function __construct()
    {
        helper(['url', 'global_function']);
        $this->db = \Config\Database::connect();
        date_default_timezone_set(defaultTimeZone());
    }

    // ========================================================================
    // MAIN SCRAPING ENTRY POINT
    // ========================================================================

    /**
     * Main scraping method - tries sources in priority order
     * URL: /firmware-scraper/scrape
     * Can be called manually or via cron job
     */
    public function scrape(): void
    {
        $this->logMessage('info', '========== NEW SCRAPING SESSION STARTED ==========');
        
        $totalScraped = 0;
        $totalInserted = 0;
        $totalUpdated = 0;
        $errors = [];

        // Try each source in priority order
        uasort($this->sources, fn($a, $b) => $a['priority'] <=> $b['priority']);

        foreach ($this->sources as $sourceKey => $source) {
            if (!$source['enabled']) {
                $this->logMessage('info', "{$source['name']}: Skipped (disabled)");
                continue;
            }

            $this->logMessage('info', "{$source['name']}: Starting scrape...");

            try {
                $html = $this->fetchPage($source['url'], $sourceKey);
                
                if (!$html) {
                    throw new \Exception("Failed to fetch page from {$source['name']}");
                }

                $firmwareData = $this->parseSource($sourceKey, $html);
                
                if (empty($firmwareData)) {
                    throw new \Exception("No data parsed from {$source['name']}");
                }

                $this->logMessage('info', "{$source['name']}: Found " . count($firmwareData) . " firmware entries");

                // Process and store data
                $result = $this->storeFirmwareData($firmwareData, $sourceKey);
                
                $totalScraped += $result['scraped'];
                $totalInserted += $result['inserted'];
                $totalUpdated += $result['updated'];

                $this->logMessage('info', "{$source['name']}: SUCCESS - Scraped: {$result['scraped']}, Inserted: {$result['inserted']}, Updated: {$result['updated']}");

                // If successful, we can stop (or continue to get from all sources)
                // break; // Uncomment to stop after first success
                
            } catch (\Exception $e) {
                $errorMsg = "{$source['name']}: ERROR - " . $e->getMessage();
                $this->logMessage('error', $errorMsg);
                $errors[] = $errorMsg;
                continue;
            }
        }

        // Final summary
        $this->logMessage('info', "========== SCRAPING SESSION COMPLETED ==========");
        $this->logMessage('info', "Total Scraped: {$totalScraped}");
        $this->logMessage('info', "Total Inserted: {$totalInserted}");
        $this->logMessage('info', "Total Updated: {$totalUpdated}");
        $this->logMessage('info', "Errors: " . count($errors));

        // Output JSON response
        echo json_encode([
            'success' => count($errors) === 0 || $totalInserted > 0,
            'total_scraped' => $totalScraped,
            'total_inserted' => $totalInserted,
            'total_updated' => $totalUpdated,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s'),
        ], JSON_PRETTY_PRINT);
    }

    // ========================================================================
    // FETCH PAGE WITH CACHING
    // ========================================================================

    private function fetchPage(string $url, string $sourceKey): string|false
    {
        // Check cache first
        $cacheKey = 'scraper_' . $sourceKey . '_' . date('Y-m-d-H');
        $cached = $this->getCache($cacheKey);
        
        if ($cached) {
            $this->logMessage('info', "Using cached data for {$sourceKey}");
            return $cached;
        }

        // Fetch fresh data
        $this->logMessage('info', "Fetching fresh data from: {$url}");
        
        $options = [
            'http' => [
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n" .
                           "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
                           "Accept-Language: en-US,en;q=0.5\r\n",
                'timeout' => 30,
            ],
        ];

        $context = stream_context_create($options);
        $html = @file_get_contents($url, false, $context);

        if ($html === false) {
            return false;
        }

        // Save to cache
        $this->setCache($cacheKey, $html, $this->cacheTime);
        
        return $html;
    }

    // ========================================================================
    // PARSE SOURCE DATA
    // ========================================================================

    private function parseSource(string $sourceKey, string $html): array
    {
        return match ($sourceKey) {
            'odinrom' => $this->parseOdinRom($html),
            'sxrom' => $this->parseSXRom($html),
            // 'sammobile' => $this->parseSamMobile($html), // DISABLED
            default => [],
        };
    }

    // ========================================================================
    // PARSE ODINROM.COM (NEW - Global coverage)
    // ========================================================================

    private function parseOdinRom(string $html): array
    {
        $firmwares = [];

        try {
            $doc = new \DOMDocument();
            @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($doc);

            // Find all tablist-row divs (OdinRom uses custom tablist structure)
            $rows = $xpath->query("//div[contains(@class, 'tablist-row')]");
            
            $this->logMessage('info', "OdinRom: Found {$rows->length} firmware rows");
            
            foreach ($rows as $rowIndex => $row) {
                try {
                    // Get all columns in this row
                    $cols = [];
                    $colDivs = $row->getElementsByTagName('div');
                    
                    foreach ($colDivs as $colDiv) {
                        if ($colDiv->getAttribute('class') === 'tablist-col') {
                            $cols[] = trim($colDiv->textContent);
                        }
                    }
                    
                    // Need at least 7 columns: Device, Model, Region, AP, CSC, OS, Date
                    if (count($cols) < 7) {
                        continue;
                    }
                    
                    $deviceName = $cols[0];
                    $model = $cols[1];
                    $csc = $cols[2];
                    $pdaVersion = $cols[3];
                    $cscVersion = $cols[4];
                    $androidVersion = $cols[5];
                    $date = $cols[6];

                    // Skip header rows
                    if (empty($model) || $model === 'Model' || empty($deviceName) || $deviceName === 'Device') {
                        continue;
                    }

                    // Skip invalid models
                    if (strlen($model) < 5 || !str_starts_with($model, 'SM-')) {
                        $this->logMessage('debug', "Skipping invalid model: {$model}");
                        continue;
                    }

                    // Clean device name
                    $cleanDevice = str_replace('Galaxy ', '', $deviceName);
                    $device = strtoupper(str_replace([' ', '-'], ' ', $cleanDevice));

                    $firmwares[] = [
                        'deviceName' => $deviceName,
                        'device' => $device,
                        'model' => $model,
                        'csc' => $csc,
                        'country' => '', // Will be populated later
                        'pdaVersion' => $pdaVersion,
                        'cscVersion' => $cscVersion,
                        'androidVersion' => $androidVersion,
                        'releaseDate' => $date,
                        'source' => 'odinrom',
                    ];

                    $this->logMessage('debug', "Parsed: {$device} {$model} {$csc} {$pdaVersion}");

                } catch (\Exception $e) {
                    $this->logMessage('error', "Error parsing OdinRom row {$rowIndex}: " . $e->getMessage());
                    continue;
                }
            }

            $this->logMessage('info', "OdinRom: Successfully parsed " . count($firmwares) . " firmwares");

        } catch (\Exception $e) {
            $this->logMessage('error', 'OdinRom parse error: ' . $e->getMessage());
        }

        return $firmwares;
    }

    // ========================================================================
    // PARSE SXROM.COM (LEGACY - Chinese regions only)
    // ========================================================================

    private function parseSXRom(string $html): array
    {
        $firmwares = [];

        try {
            $doc = new \DOMDocument();
            @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($doc);

            // Find all table rows (skip header row)
            $rows = $xpath->query("//table//tr");
            
            foreach ($rows as $row) {
                $cells = $row->getElementsByTagName('td');
                
                // Skip if not enough cells
                if ($cells->length < 6) {
                    continue;
                }

                try {
                    // Extract data from cells
                    $deviceName = trim($cells->item(0)->textContent);
                    $model = trim($cells->item(1)->textContent);
                    $regionRaw = trim($cells->item(2)->textContent);
                    $versions = trim($cells->item(3)->textContent);
                    $androidVersion = trim($cells->item(4)->textContent);
                    $date = trim($cells->item(5)->textContent);

                    // Skip empty rows
                    if (empty($model) || empty($deviceName)) {
                        continue;
                    }

                    // Parse region (format: "TGYHong Kong" or "XAAUnited States")
                    $csc = '';
                    $country = '';
                    if (preg_match('/^([A-Z]{3})(.+)/', $regionRaw, $matches)) {
                        $csc = $matches[1];
                        $country = trim($matches[2]);
                    }

                    // Parse versions (AP/CSC combined format)
                    // Example: "F976U1UES2AZH7F976U1OYM2AZH7"
                    $pdaVersion = '';
                    $cscVersion = '';
                    
                    if (strlen($versions) > 15) {
                        // Try to split versions (usually AP and CSC are same length)
                        $halfLen = ceil(strlen($versions) / 2);
                        $pdaVersion = substr($versions, 0, $halfLen);
                        $cscVersion = substr($versions, $halfLen);
                    } else {
                        $pdaVersion = $versions;
                    }

                    // Clean device name (remove "Galaxy " prefix if present)
                    $cleanDevice = str_replace('Galaxy ', '', $deviceName);
                    $device = strtoupper(str_replace([' ', '-'], ' ', $cleanDevice));

                    // Skip invalid entries (where model is literally "Model")
                    if ($model === 'Model' || empty($model) || strlen($model) < 5) {
                        continue;
                    }

                    // Construct detail page URL for file size
                    // Pattern: /download/{MODEL}-{CSC}-{PDAVERSION}-{CSCVERSION}.html
                    // Example: /download/SM-R910-TGY-R910XXS2DZG3-R910OXM2DZG3.html
                    $detailUrl = '';
                    if (!empty($model) && !empty($csc) && !empty($pdaVersion) && !empty($cscVersion)) {
                        $detailUrl = "https://www.sxrom.com/download/{$model}-{$csc}-{$pdaVersion}-{$cscVersion}.html";
                    }

                    $firmwares[] = [
                        'deviceName' => $deviceName,  // Keep original: "Galaxy Watch5"
                        'device' => $device,          // Uppercase: "WATCH5"
                        'model' => $model,
                        'csc' => $csc,
                        'country' => $country,
                        'pdaVersion' => $pdaVersion,
                        'cscVersion' => $cscVersion,
                        'androidVersion' => $androidVersion,
                        'releaseDate' => $date,
                        'source' => 'sxrom',
                        'detailUrl' => $detailUrl,    // For fetching file size later
                    ];

                } catch (\Exception $e) {
                    $this->logMessage('warning', "Error parsing SXRom row: " . $e->getMessage());
                    continue;
                }
            }

        } catch (\Exception $e) {
            $this->logMessage('error', "Error parsing SXRom HTML: " . $e->getMessage());
        }

        return $firmwares;
    }

    // ========================================================================
    // PARSE SAMMOBILE.COM (DISABLED - COMMENTED OUT)
    // ========================================================================
    /*
    private function parseSamMobile(string $html): array
    {
        $firmwares = [];

        try {
            $doc = new \DOMDocument();
            @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($doc);

            // Find all table rows
            $rows = $xpath->query("//table//tr");
            
            foreach ($rows as $row) {
                $cells = $row->getElementsByTagName('td');
                
                // Skip if not enough cells (SamMobile has 5 columns)
                if ($cells->length < 5) {
                    continue;
                }

                try {
                    $model = trim($cells->item(0)->textContent);
                    $countryCarrier = trim($cells->item(1)->textContent);
                    $date = trim($cells->item(2)->textContent);
                    $androidVersion = trim($cells->item(3)->textContent);
                    $pdaVersion = trim($cells->item(4)->textContent);

                    // Skip empty rows or invalid entries
                    if (empty($model) || empty($pdaVersion) || $model === 'Model') {
                        continue;
                    }

                    // Extract CSC and country from countryCarrier
                    // Format examples: "United Kingdom", "Philippines (Globe)", "Canada (EastLink)"
                    $csc = $this->guessCscFromCountry($countryCarrier);
                    $country = $this->cleanCountryName($countryCarrier);

                    // Extract device name from model
                    // SM-G991B -> S21 (will add Galaxy prefix in display)
                    $device = $this->guessDeviceFromModel($model);

                    // Try to generate CSC version from PDA version
                    // PDA: A366BXXSBCZG1 -> CSC might be similar pattern
                    $cscVersion = $this->generateCscVersion($pdaVersion, $csc);

                    $firmwares[] = [
                        'deviceName' => $this->formatDeviceName($device),  // "Galaxy A35"
                        'device' => $device,  // Just "A35"
                        'model' => $model,
                        'csc' => $csc,
                        'country' => $country,
                        'pdaVersion' => $pdaVersion,
                        'cscVersion' => $cscVersion,
                        'androidVersion' => $androidVersion,
                        'releaseDate' => $date,
                        'source' => 'sammobile',
                    ];

                } catch (\Exception $e) {
                    $this->logMessage('warning', "Error parsing SamMobile row: " . $e->getMessage());
                    continue;
                }
            }

        } catch (\Exception $e) {
            $this->logMessage('error', "Error parsing SamMobile HTML: " . $e->getMessage());
        }

        return $firmwares;
    }
    */

    // ========================================================================
    // EXTRACT BINARY VERSION FROM FIRMWARE VERSION STRING
    // ========================================================================

    private function extractBinaryVersion(string $version): string
    {
        if (empty($version) || strlen($version) < 5) {
            return ''; // No binary version if string too short
        }

        // Extract character at position -5 (5th from end)
        // This can be a digit (1,2,3) or letter (A,B,C,S,E, etc.)
        // Examples:
        //   R910XXS2DZG3 → position -5 = "2"
        //   X516BXXSEEZG3 → position -5 = "E"
        //   A366BXXSBCZG1 → position -5 = "B"
        //   F976U1UES2AZH7 → position -5 = "2"
        
        $binaryChar = substr($version, -5, 1);
        
        // Return the character as-is (digit or letter)
        return $binaryChar;
    }

    // ========================================================================
    // FETCH ODINROM DOWNLOAD LINK (SAFE MATCHING)
    // ========================================================================

    private function fetchOdinRomDownloadLink(string $model, string $csc, string $pdaVersion, string $cscVersion): string
    {
        try {
            // Construct OdinRom detail page URL
            // Format: https://www.odinrom.com/download/{MODEL}-{CSC}-{PDAVERSION}-{CSCVERSION}.html
            $odinUrl = "https://www.odinrom.com/download/{$model}-{$csc}-{$pdaVersion}-{$cscVersion}.html";
            
            // Check cache first
            $cacheKey = 'odinrom_' . md5($odinUrl);
            $cacheData = $this->getCache($cacheKey);

            if ($cacheData) {
                $html = $cacheData;
            } else {
                // Fetch OdinRom page
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $odinUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                
                $html = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                // If page not found (404), no match exists
                if ($html === false || $httpCode === 404) {
                    $this->logMessage('info', "OdinRom: No match found for {$model}-{$csc}");
                    return '';
                }

                if ($httpCode !== 200) {
                    $this->logMessage('warning', "OdinRom: Failed to fetch {$odinUrl} - HTTP {$httpCode}");
                    return '';
                }
                
                // Cache for 6 hours
                $this->setCache($cacheKey, $html, 21600);
            }

            // Extract ydfile.com download link from JavaScript variable
            // Pattern: var p3 = '/929d2a757ea44df4?filename=...';
            if (preg_match("/var\s+p3\s*=\s*'(\/[^']+)'/", $html, $matches)) {
                $ydfilePath = $matches[1];
                $downloadLink = "https://www.ydfile.com" . $ydfilePath;
                
                $this->logMessage('info', "OdinRom: Found download link for {$model}-{$csc}: {$downloadLink}");
                return $downloadLink;
            }

            $this->logMessage('info', "OdinRom: Page found but no download link extracted for {$model}-{$csc}");
            return '';

        } catch (\Exception $e) {
            $this->logMessage('error', "OdinRom fetch error for {$model}-{$csc}: " . $e->getMessage());
            return '';
        }
    }

    // ========================================================================
    // FETCH FILE SIZE FROM SXROM DETAIL PAGE
    // ========================================================================

    private function fetchFileSizeFromDetail(string $detailUrl): string
    {
        try {
            $cacheKey = 'sxrom_detail_' . md5($detailUrl);
            $cacheData = $this->getCache($cacheKey);

            if ($cacheData) {
                $html = $cacheData;
            } else {
                // Use cURL for better reliability
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $detailUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                
                $html = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($html === false || $httpCode !== 200) {
                    $this->logMessage('warning', "Failed to fetch {$detailUrl} - HTTP {$httpCode}");
                    return '';
                }
                
                $this->setCache($cacheKey, $html, 3600); // Cache for 1 hour
            }

            // Parse HTML to find file size
            // Look for patterns like "3.5 GB" or "Firmware size approximately"
            if (preg_match('/(\d+\.?\d*)\s*(GB|MB|KB)/i', $html, $matches)) {
                $size = $matches[1];
                $unit = strtoupper($matches[2]);
                return $size . ' ' . $unit;
            }

            $this->logMessage('warning', "No file size found in {$detailUrl}");
            return '';

        } catch (\Exception $e) {
            $this->logMessage('warning', "Error fetching file size from {$detailUrl}: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Fetch file size from OdinRom detail page
     * Enhanced with retry logic and better timeout handling
     */
    private function fetchFileSizeFromOdinRom(string $detailUrl): string
    {
        try {
            $cacheKey = 'odinrom_detail_' . md5($detailUrl);
            $cacheData = $this->getCache($cacheKey);

            if ($cacheData) {
                $html = $cacheData;
            } else {
                // Use cURL with retry logic for better reliability
                $maxRetries = 3;
                $html = false;
                
                for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $detailUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Increased from 10 to 15 seconds
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                    
                    $html = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($html !== false && $httpCode === 200) {
                        break; // Success, exit retry loop
                    }
                    
                    if ($attempt < $maxRetries) {
                        $this->logMessage('warning', "Retry {$attempt}/{$maxRetries} for {$detailUrl} - HTTP {$httpCode}");
                        usleep(500000); // Wait 0.5 second before retry
                    }
                }

                if ($html === false || $httpCode !== 200) {
                    $this->logMessage('warning', "Failed to fetch {$detailUrl} after {$maxRetries} attempts - HTTP {$httpCode}");
                    return '';
                }
                
                $this->setCache($cacheKey, $html, 3600); // Cache for 1 hour
            }

            // Parse HTML to find file size from OdinRom
            // OdinRom structure: <div class="label">Size</div><div class="value">10.2 GB</div>
            
            // Method 1: Specific pattern for OdinRom's div structure (most reliable)
            if (preg_match('/<div class="value">([0-9.]+\s+[GMK]B)<\/div>/i', $html, $matches)) {
                return $matches[1]; // Already formatted (e.g., "10.2 GB")
            }
            
            // Method 2: Look for "Size" label followed by value div
            if (preg_match('/<div class="label">Size<\/div>\s*<div class="value">([0-9.]+\s+[GMK]B)<\/div>/i', $html, $matches)) {
                return $matches[1];
            }
            
            // Method 3: Fallback - general pattern for "Size: X.X GB" format
            if (preg_match('/(?:File Size|Size|Firmware Size)[:\s]*([0-9.]+)\s*([GMK]B)/i', $html, $matches)) {
                return $matches[1] . ' ' . strtoupper($matches[2]);
            }
            
            // Method 4: Last resort - any number followed by GB/MB
            if (preg_match('/([0-9.]+)\s*([GMK]B)/i', $html, $matches)) {
                return $matches[1] . ' ' . strtoupper($matches[2]);
            }

            $this->logMessage('debug', "No file size found in {$detailUrl}");
            return '';

        } catch (\Exception $e) {
            $this->logMessage('warning', "Error fetching file size from {$detailUrl}: " . $e->getMessage());
            return '';
        }
    }

    // ========================================================================
    // GENERATE CSC VERSION
    // ========================================================================

    private function generateCscVersion(string $pdaVersion, string $csc): string
    {
        // Try to construct CSC version from PDA version pattern
        // PDA: A366BXXSBCZG1
        // CSC: A366BOXMBCZG1 (OXM instead of XXS)
        
        if (strlen($pdaVersion) > 10) {
            // Replace middle part with OXM/OYM pattern
            $prefix = substr($pdaVersion, 0, 5); // A366B
            $suffix = substr($pdaVersion, 8);     // BCZG1
            
            // Common CSC patterns
            $cscMiddle = match($csc) {
                'XAA', 'TMB', 'TMK', 'VZW', 'ATT', 'SPR', 'USC' => 'OYM', // US carriers
                'BTU', 'DBT', 'XEF', 'PHE', 'ITV' => 'OXM', // Europe
                'TGY', 'BRI' => 'OZS', // Asia
                default => 'OXM',
            };
            
            return $prefix . $cscMiddle . $suffix;
        }
        
        return '';
    }

    // ========================================================================
    // STORE FIRMWARE DATA
    // ========================================================================

    private function storeFirmwareData(array $firmwareData, string $source): array
    {
        $scraped = count($firmwareData);
        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        // Add batch processing to prevent MySQL overload
        $batchSize = 5; // Process 5 at a time
        $batchDelay = 500000; // 0.5 second delay between batches (microseconds)
        
        $this->logMessage('info', "Processing {$scraped} firmwares in batches of {$batchSize}...");

        $autoData = getSiteMeta('postAutomation');
        $cscList = getCSCList();
        
        // Check if scheduler columns exist (do this ONCE, not in loop)
        $schedulerInstalled = false;
        try {
            $schedulerColumns = $this->db->query("
                SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'fw_posts' 
                AND COLUMN_NAME IN ('autoScheduled', 'priority', 'isNewModel')
            ")->getResult();
            $schedulerInstalled = (count($schedulerColumns) >= 3);
        } catch (\Exception $e) {
            // Scheduler not installed, continue without it
        }

        $processedCount = 0;
        foreach ($firmwareData as $firmware) {
            try {
                // Add batch delay every N items
                if ($processedCount > 0 && $processedCount % $batchSize === 0) {
                    $this->logMessage('debug', "Batch delay: processed {$processedCount}/{$scraped}");
                    usleep($batchDelay);
                }
                $processedCount++;
                
                // Skip invalid entries or UNKNOWN devices
                if (empty($firmware['model']) || 
                    $firmware['model'] === 'Model' || 
                    empty($firmware['pdaVersion']) || 
                    $firmware['device'] === 'UNKNOWN') {
                    $this->logMessage('warning', "Skipping invalid/unknown entry: Model={$firmware['model']}, Device={$firmware['device']}");
                    $skipped++;
                    continue;
                }

                // Check if firmware already exists
                $exists = $this->db->table('fw_posts')
                    ->where('model', $firmware['model'])
                    ->where('pdaVersion', $firmware['pdaVersion'])
                    ->where('csc', $firmware['csc'])
                    ->countAllResults();

                if ($exists > 0) {
                    // Skip existing firmware - no need to update if nothing changed
                    $skipped++;
                    continue;
                }

                // Get country ISO from CSC
                $countryISO = $cscList[$firmware['csc']] ?? $firmware['country'];

                // Extract binary version from PDA version
                // Samsung firmware version format varies, but binary digit is usually:
                // 1. At position -5 if it's a digit (R910XXS2DZG3 → "2")
                // 2. Extracted from specific pattern if position -5 is a letter
                $bit = $this->extractBinaryVersion($firmware['pdaVersion']);

                // Fetch file size and download link from OdinRom detail page
                $fileSize = '';
                $downloadLink = '';
                
                if ($source === 'odinrom') {
                    // OdinRom detail URL format
                    $detailUrl = "https://www.odinrom.com/download/{$firmware['model']}-{$firmware['csc']}-{$firmware['pdaVersion']}-{$firmware['cscVersion']}.html";
                    
                    // Fetch file size from detail page
                    $fileSize = $this->fetchFileSizeFromOdinRom($detailUrl);
                    if (!empty($fileSize)) {
                        $this->logMessage('info', "Fetched file size for {$firmware['model']}: {$fileSize}");
                    }
                    
                    // Fetch download link
                    $downloadLink = $this->fetchOdinRomDownloadLink(
                        $firmware['model'],
                        $firmware['csc'],
                        $firmware['pdaVersion'],
                        $firmware['cscVersion']
                    );
                    
                    if (!empty($downloadLink)) {
                        $this->logMessage('info', "Fetched download link for {$firmware['model']}-{$firmware['csc']}");
                    }
                }
                
                // Legacy: Fetch from SXRom if still using it
                if ($source === 'sxrom' && !empty($firmware['detailUrl'])) {
                    $fileSize = $this->fetchFileSizeFromDetail($firmware['detailUrl']);
                    if (!empty($fileSize)) {
                        $this->logMessage('info', "Fetched file size for {$firmware['model']}: {$fileSize}");
                    }
                    
                    // Try to fetch download link from OdinRom (safe matching)
                    $downloadLink = $this->fetchOdinRomDownloadLink(
                        $firmware['model'],
                        $firmware['csc'],
                        $firmware['pdaVersion'],
                        $firmware['cscVersion']
                    );
                    
                    if (!empty($downloadLink)) {
                        $this->logMessage('info', "Matched OdinRom download link for {$firmware['model']}-{$firmware['csc']}");
                    }
                }

                // Fallback to Samsung official server URL if no OdinRom link
                $officialUrl = '';
                if (empty($downloadLink) && !empty($firmware['model']) && !empty($firmware['csc']) && 
                    !empty($firmware['pdaVersion']) && !empty($firmware['cscVersion'])) {
                    $officialUrl = "https://cloud-neofussvr.sslcs.cdngc.net/NF_DownloadGenerateForSecureServlet.do?file=" . 
                                   "{$firmware['model']}_{$firmware['csc']}_{$firmware['pdaVersion']}_{$firmware['cscVersion']}.zip.enc4";
                }

                // Use OdinRom link if available, otherwise Samsung URL
                $finalDownloadLink = !empty($downloadLink) ? $downloadLink : $officialUrl;

                // Prepare post data
                $postData = [
                    'userId' => 1, // ✅ FIX: Set default userId for scraped posts
                    'postTitle' => $autoData['title'] ?? 'Firmware {device} {version}',
                    'postContent' => $autoData['template'] ?? '',
                    'metaTitle' => $autoData['metaTitle'] ?? '',
                    'metaDesription' => $autoData['metaDesription'] ?? '',
                    'metaTags' => $autoData['metaTags'] ?? '',
                    'device' => $firmware['device'],
                    'model' => $firmware['model'],
                    'version' => $firmware['pdaVersion'], // Main version
                    'pdaVersion' => $firmware['pdaVersion'], // Separate PDA
                    'cscversion' => $firmware['cscVersion'], // CSC version
                    'os' => $firmware['androidVersion'],
                    'bit' => $bit,
                    'csc' => $firmware['csc'],
                    'country' => $countryISO,
                    'buildDate' => $firmware['releaseDate'],
                    'fileSize' => $fileSize, // Add file size
                    'externalFileLink' => $finalDownloadLink, // OdinRom link or Samsung URL
                    'autoPost' => 'Yes',
                    'scrapedFrom' => $source,
                    'postStatus' => 'Pending', // Pending for review/publishing
                    'createdTime' => date('Y-m-d H:i:s'),
                    'modifiedTime' => date('Y-m-d H:i:s'),
                    'specsSynced' => 'No',
                    'externalFileUploaded' => 'No',
                    'downloadButton' => json_encode([]), // Empty download buttons initially
                ];
                
                // Add scheduler columns if they exist (for compatibility with scheduler feature)
                $schedulerColumns = $this->db->query("
                    SELECT COLUMN_NAME 
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'fw_posts' 
                    AND COLUMN_NAME IN ('autoScheduled', 'priority', 'isNewModel')
                ")->getResult();
                
                if (count($schedulerColumns) >= 3) {
                    // Scheduler is installed, use Draft status for auto-scheduling
                    $postData['postStatus'] = 'Draft';
                    $postData['autoScheduled'] = 'No'; // Scheduler will process this
                    $postData['priority'] = 'normal';
                    $postData['isNewModel'] = 'No';
                }

                // Insert into database
                $this->db->table('fw_posts')->insert($postData);
                $postId = $this->db->insertID();

                // Generate slug
                $mpost = getPost($postId);
                if ($mpost !== '0') {
                    $postSlug = url_title(replacePostToken($mpost->postTitle, $mpost), '-', true);
                    $this->db->table('fw_posts')
                        ->where('postId', $postId)
                        ->update(['postSlug' => $postSlug . '-' . $postId]);
                }

                $inserted++;

            } catch (\Exception $e) {
                $this->logMessage('error', "Error storing firmware {$firmware['model']}: " . $e->getMessage());
                $skipped++;
            }
        }

        // Auto-schedule all newly scraped drafts with smart priority
        if ($inserted > 0) {
            $this->logMessage('info', "Triggering auto-scheduler for {$inserted} new drafts...");
            try {
                $scheduler = new \App\Controllers\FirmwareScheduler();
                $scheduler->autoSchedule();
                $this->logMessage('success', "Auto-scheduling completed successfully");
                
                // ✅ FIX: Clear cache after scraping so dashboard stats update immediately
                $this->clearScraperCache();
                
            } catch (\Exception $e) {
                $this->logMessage('error', "Auto-scheduling failed: " . $e->getMessage());
            }
        }

        return [
            'scraped' => $scraped,
            'inserted' => $inserted,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * ✅ FIX: Clear query cache after scraping
     * This ensures dashboard and homepage show new data immediately
     */
    private function clearScraperCache()
    {
        try {
            // Clear query cache if it exists
            if (class_exists('\App\Libraries\QueryCache')) {
                $cache = new \App\Libraries\QueryCache();
                
                // Clear cache patterns affected by scraping
                $cache->forgetPattern('scheduler_stats*');
                $cache->forgetPattern('pending_drafts*');
                $cache->forgetPattern('recent_models*');
                $cache->forgetPattern('dashboard*');
            }
            
            // Clear CodeIgniter's cache if enabled
            $cacheInstance = \Config\Services::cache();
            if ($cacheInstance) {
                $cacheInstance->delete('scheduler_stats');
                $cacheInstance->delete('pending_drafts');
                $cacheInstance->delete('scheduler_dashboard');
            }
            
            $this->logMessage('info', "Cache cleared successfully");
        } catch (\Exception $e) {
            $this->logMessage('warning', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    // ========================================================================
    // FORMAT DEVICE NAME WITH "GALAXY" PREFIX
    // ========================================================================

    private function formatDeviceName(string $device): string
    {
        // Don't add Galaxy prefix to UNKNOWN or already-prefixed names
        if ($device === 'UNKNOWN' || str_starts_with($device, 'GALAXY')) {
            return $device;
        }

        // Add Galaxy prefix to all Samsung devices
        return 'GALAXY ' . $device;
    }

    // ========================================================================
    // HELPER METHODS
    // ========================================================================

    private function guessCscFromCountry(string $countryStr): string
    {
        // Common CSC mappings
        $cscMap = [
            'United Kingdom' => 'BTU',
            'United States' => 'XAA',
            'Germany' => 'DBT',
            'France' => 'XEF',
            'Spain' => 'PHE',
            'Italy' => 'ITV',
            'Canada' => 'XAC',
            'Australia' => 'XSA',
            'Hong Kong' => 'TGY',
            'China' => 'CHC',
            'India' => 'INS',
            'Singapore' => 'XSG',
            'Philippines' => 'XTC',
            'Japan' => 'DCM',
        ];

        foreach ($cscMap as $country => $csc) {
            if (stripos($countryStr, $country) !== false) {
                return $csc;
            }
        }

        return '';
    }

    private function cleanCountryName(string $countryStr): string
    {
        // Remove carrier names in parentheses
        return trim(preg_replace('/\s*\([^)]*\)/', '', $countryStr));
    }

    private function guessDeviceFromModel(string $model): string
    {
        // Enhanced model-to-device mapping with proper names
        $modelMap = [
            // S Series (Flagship)
            '/SM-S90[0-4]B?/' => 'S22',
            '/SM-S90[5-9]B?/' => 'S22 PLUS',
            '/SM-S908B?/' => 'S22 ULTRA',
            '/SM-S91[0-4]B?/' => 'S23',
            '/SM-S91[5-9]B?/' => 'S23 PLUS',
            '/SM-S918B?/' => 'S23 ULTRA',
            '/SM-S92[0-4]B?/' => 'S24',
            '/SM-S92[5-9]B?/' => 'S24 PLUS',
            '/SM-S928B?/' => 'S24 ULTRA',
            '/SM-G99[0-4]B?/' => 'S21',
            '/SM-G99[5-9]B?/' => 'S21 PLUS',
            '/SM-G998B?/' => 'S21 ULTRA',
            
            // A Series (Mid-range) - Specific models
            '/SM-A05[0-9]/' => 'A05',
            '/SM-A14[0-9]/' => 'A14',
            '/SM-A15[0-9]/' => 'A15',
            '/SM-A24[0-9]/' => 'A24',
            '/SM-A25[0-9]/' => 'A25',
            '/SM-A34[0-9]/' => 'A34',
            '/SM-A35[0-9]/' => 'A35',
            '/SM-A36[0-9]/' => 'A36',
            '/SM-A54[0-9]/' => 'A54',
            '/SM-A55[0-9]/' => 'A55',
            '/SM-A73[0-9]/' => 'A73',
            '/SM-A74[0-9]/' => 'A74',
            
            // Z Fold Series
            '/SM-F97[0-9]/' => 'Z FOLD8',
            '/SM-F976/' => 'Z FOLD8 ULTRA',
            '/SM-F96[0-9]/' => 'Z FOLD7',
            '/SM-F95[0-9]/' => 'Z FOLD6',
            '/SM-F94[0-9]/' => 'Z FOLD5',
            '/SM-F93[0-9]/' => 'Z FOLD4',
            '/SM-F92[0-9]/' => 'Z FOLD3',
            '/SM-F91[0-9]/' => 'Z FOLD2',
            '/SM-F90[0-9]/' => 'Z FOLD',
            
            // Z Flip Series
            '/SM-F77[0-9]/' => 'Z FLIP8',
            '/SM-F76[0-9]/' => 'Z FLIP7',
            '/SM-F75[0-9]/' => 'Z FLIP6',
            '/SM-F74[0-9]/' => 'Z FLIP5',
            '/SM-F73[0-9]/' => 'Z FLIP4',
            '/SM-F72[0-9]/' => 'Z FLIP3',
            '/SM-F71[0-9]/' => 'Z FLIP',
            '/SM-F70[0-9]/' => 'Z FLIP',
            
            // Watch Series
            '/SM-R9[0-2][0-9]/' => 'WATCH5',
            '/SM-R8[8-9][0-9]/' => 'WATCH4',
            '/SM-R8[6-7][0-9]/' => 'WATCH3',
            
            // Tab Series
            '/SM-X[0-9]{3}/' => 'TAB ' . strtoupper(substr($model, 4, 1)) . substr($model, 5, 2),
            '/SM-P[0-9]{3}/' => 'TAB',
            '/SM-T[0-9]{3}/' => 'TAB',
        ];

        foreach ($modelMap as $pattern => $device) {
            if (preg_match($pattern, $model)) {
                return $device;
            }
        }

        // Fallback: try to extract from model number
        if (preg_match('/SM-([A-Z])(\d{2,3})([A-Z])?/', $model, $matches)) {
            $series = $matches[1];
            $number = $matches[2];
            $variant = $matches[3] ?? '';
            
            if ($series === 'A' && strlen($number) >= 2) {
                // Extract A-series number: SM-A3660 -> A36, SM-A356E -> A35
                $deviceNum = substr($number, 0, 2);
                return 'A' . $deviceNum;
            }
            
            return match($series) {
                'S' => 'S' . substr($number, 0, 2),
                'F' => 'Z SERIES',
                'R' => 'WATCH',
                'X', 'P', 'T' => 'TAB',
                default => strtoupper($series) . ' SERIES',
            };
        }

        return 'UNKNOWN';
    }

    // ========================================================================
    // CACHING METHODS
    // ========================================================================

    private function getCache(string $key): string|false
    {
        try {
            $record = $this->db->table('fw_scraper_cache')
                ->where('cacheKey', $key)
                ->where('expiryTime >', date('Y-m-d H:i:s'))
                ->get();

            if ($record->getNumRows() > 0) {
                return $record->getRow()->cacheData;
            }
        } catch (\Exception $e) {
            // Cache table unavailable (read-only or locked) - continue without cache
            log_message('debug', "[FirmwareScraper] Cache read failed: " . $e->getMessage());
        }

        return false;
    }

    private function setCache(string $key, string $data, int $ttl): void
    {
        try {
            $expiryTime = date('Y-m-d H:i:s', time() + $ttl);

            // Check if cache key exists
            $exists = $this->db->table('fw_scraper_cache')
                ->where('cacheKey', $key)
                ->countAllResults();

            if ($exists > 0) {
                $this->db->table('fw_scraper_cache')
                    ->where('cacheKey', $key)
                    ->update([
                        'cacheData' => $data,
                        'expiryTime' => $expiryTime,
                        'updatedTime' => date('Y-m-d H:i:s'),
                    ]);
            } else {
                $this->db->table('fw_scraper_cache')->insert([
                    'cacheKey' => $key,
                    'cacheData' => $data,
                    'createdTime' => date('Y-m-d H:i:s'),
                    'expiryTime' => $expiryTime,
                ]);
            }
        } catch (\Exception $e) {
            // Cache table unavailable (read-only or locked) - continue without cache
            log_message('debug', "[FirmwareScraper] Cache write failed: " . $e->getMessage());
        }
    }

    // ========================================================================
    // LOGGING METHODS
    // ========================================================================

    private function logMessage(string $level, string $message): void
    {
        // Validate log level (CodeIgniter only accepts: debug, info, notice, warning, error, critical, alert, emergency)
        $validLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        $ciLevel = in_array($level, $validLevels) ? $level : 'info';
        
        // Log to database (we can use custom levels here)
        try {
            $this->db->table('fw_scraper_log')->insert([
                'logLevel' => $level,
                'logMessage' => $message,
                'logTime' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            // Silently handle database errors (table might be read-only or locked)
            // Continue logging to file system instead
        }

        // Log to CodeIgniter log (use valid level)
        log_message($ciLevel, "[FirmwareScraper] {$message}");

        // Output to console (if running via CLI)
        if (is_cli()) {
            $prefix = match ($level) {
                'error' => '❌',
                'warning' => '⚠️',
                'success' => '✅',
                default => 'ℹ️',
            };
            echo "{$prefix} [{$level}] {$message}\n";
        }
    }

    // ========================================================================
    // TEST/DEBUG METHODS
    // ========================================================================

    /**
     * Test method to check if scraping works
     * URL: /firmware-scraper/test
     */
    public function test(): void
    {
        echo "<h1>Firmware Scraper - Test Mode</h1>";
        echo "<p>Testing SXRom.com scraping...</p>";

        try {
            $html = $this->fetchPage('https://www.sxrom.com/', 'sxrom');
            
            if (!$html) {
                echo "<p style='color:red;'>❌ Failed to fetch page</p>";
                return;
            }

            echo "<p style='color:green;'>✅ Page fetched successfully (" . strlen($html) . " bytes)</p>";

            $firmwares = $this->parseSXRom($html);
            echo "<p>Parsed " . count($firmwares) . " firmware entries</p>";

            if (!empty($firmwares)) {
                echo "<h2>Sample Data (first 5 entries):</h2>";
                echo "<pre>" . json_encode(array_slice($firmwares, 0, 5), JSON_PRETTY_PRINT) . "</pre>";
            }

        } catch (\Exception $e) {
            echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
        }
    }

    /**
     * Test OdinRom parser before implementation
     * URL: /firmware-scraper/test-odinrom
     */
    public function testOdinrom(): void
    {
        // Disable any output buffering that might interfere
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        echo "<h1>OdinRom Parser Test</h1>";
        echo "<p>Testing OdinRom.com scraping before implementation...</p>";
        echo "<hr>";
        flush();

        try {
            // Fetch OdinRom homepage
            echo "<h2>Step 1: Fetching OdinRom.com</h2>";
            flush();
            
            $html = $this->fetchPage('https://www.odinrom.com/', 'odinrom');
            
            if (!$html) {
                echo "<p style='color:red;'>❌ Failed to fetch OdinRom.com</p>";
                return;
            }

            echo "<p style='color:green;'>✅ Successfully fetched (" . number_format(strlen($html)) . " bytes)</p>";
            echo "<hr>";
            flush();
            
            // Debug: Show table structure
            echo "<h3>Debug: Table Structure Analysis</h3>";
            $doc = new \DOMDocument();
            @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($doc);
            
            $tables = $xpath->query("//table");
            echo "<p>Tables found: " . $tables->length . "</p>";
            
            if ($tables->length > 0) {
                $table = $tables->item(0);
                $rows = $table->getElementsByTagName('tr');
                echo "<p>Rows in first table: " . $rows->length . "</p>";
                
                if ($rows->length > 0) {
                    echo "<h4>First 3 rows structure:</h4>";
                    for ($i = 0; $i < min(3, $rows->length); $i++) {
                        $row = $rows->item($i);
                        $cells = $row->getElementsByTagName('td');
                        $headers = $row->getElementsByTagName('th');
                        
                        echo "<p><strong>Row {$i}:</strong> ";
                        echo "TD cells: " . $cells->length . ", TH cells: " . $headers->length . "</p>";
                        
                        if ($cells->length > 0) {
                            echo "<pre>";
                            for ($j = 0; $j < $cells->length; $j++) {
                                echo "Cell {$j}: " . htmlspecialchars(trim($cells->item($j)->textContent)) . "\n";
                            }
                            echo "</pre>";
                        }
                        
                        if ($headers->length > 0) {
                            echo "<pre>";
                            for ($j = 0; $j < $headers->length; $j++) {
                                echo "Header {$j}: " . htmlspecialchars(trim($headers->item($j)->textContent)) . "\n";
                            }
                            echo "</pre>";
                        }
                    }
                }
            } else {
                // Try to find div-based tables or lists
                echo "<p>No &lt;table&gt; tag found. Checking for alternative structures...</p>";
                
                $divTables = $xpath->query("//div[contains(@class, 'table')]");
                echo "<p>Divs with 'table' class: " . $divTables->length . "</p>";
                
                $lists = $xpath->query("//ul | //ol");
                echo "<p>Lists found: " . $lists->length . "</p>";
            }
            
            echo "<hr>";

            // Parse using test parser
            echo "<h2>Step 2: Parsing Firmware Data</h2>";
            $firmwares = $this->parseOdinRomTest($html);
            
            echo "<p><strong>Total firmware entries parsed: " . count($firmwares) . "</strong></p>";
            echo "<hr>";

            if (empty($firmwares)) {
                echo "<p style='color:red;'>❌ No firmware data found!</p>";
                echo "<h3>HTML Sample (first 3000 chars):</h3>";
                echo "<pre>" . htmlspecialchars(substr($html, 0, 3000)) . "</pre>";
                
                echo "<h3>Searching for firmware patterns:</h3>";
                $doc = new \DOMDocument();
                @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
                $xpath = new \DOMXPath($doc);
                
                // Look for common firmware identifiers
                echo "<p>Searching for 'SM-' pattern: ";
                if (preg_match_all('/SM-[A-Z0-9]+/', $html, $matches)) {
                    echo count($matches[0]) . " found</p>";
                    echo "<pre>" . print_r(array_slice($matches[0], 0, 10), true) . "</pre>";
                } else {
                    echo "None found</p>";
                }
                
                echo "<p>Searching for firmware divs/sections:</p>";
                $firmwareDivs = $xpath->query("//div[contains(@class, 'firmware') or contains(@class, 'rom') or contains(@class, 'download')]");
                echo "<p>Firmware-related divs: " . $firmwareDivs->length . "</p>";
                
                // Look for firmware-item, card, or row structures
                $allDivs = $xpath->query("//div[contains(@class, 'item') or contains(@class, 'card') or contains(@class, 'row') or contains(@class, 'list')]");
                echo "<p>Item/card/row divs: " . $allDivs->length . "</p>";
                
                if ($allDivs->length > 0) {
                    echo "<h4>First item div classes:</h4>";
                    for ($i = 0; $i < min(3, $allDivs->length); $i++) {
                        $div = $allDivs->item($i);
                        $class = $div->getAttribute('class');
                        echo "<p>Div {$i}: class='{$class}'</p>";
                        echo "<pre>" . htmlspecialchars(substr($div->textContent, 0, 300)) . "</pre>";
                    }
                }
                
                // Look for anchor tags with models
                echo "<h4>Searching for links with SM- models:</h4>";
                $links = $xpath->query("//a[contains(text(), 'SM-')]");
                echo "<p>Links with SM- models: " . $links->length . "</p>";
                
                if ($links->length > 0) {
                    echo "<h5>First 5 firmware links:</h5>";
                    echo "<table border='1' cellpadding='5'>";
                    echo "<tr><th>#</th><th>Text</th><th>Href</th></tr>";
                    for ($i = 0; $i < min(5, $links->length); $i++) {
                        $link = $links->item($i);
                        echo "<tr>";
                        echo "<td>" . ($i+1) . "</td>";
                        echo "<td>" . htmlspecialchars($link->textContent) . "</td>";
                        echo "<td>" . htmlspecialchars($link->getAttribute('href')) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                    // Analyze parent structure
                    echo "<h5>Full row structure analysis:</h5>";
                    $firstLink = $links->item(0);
                    
                    // Find the row container (parent of all columns)
                    $col = $firstLink->parentNode; // tablist-col
                    $row = $col->parentNode; // should be tablist-row
                    
                    echo "<p>Column parent: " . $col->nodeName . " (class: " . $col->getAttribute('class') . ")</p>";
                    echo "<p>Row parent: " . $row->nodeName . " (class: " . $row->getAttribute('class') . ")</p>";
                    
                    // Get all columns in this row
                    $allCols = $row->getElementsByTagName('div');
                    echo "<p>Columns in row: " . $allCols->length . "</p>";
                    
                    echo "<h5>All columns in first firmware row:</h5>";
                    echo "<table border='1' cellpadding='5'>";
                    echo "<tr><th>Col#</th><th>Class</th><th>Content</th></tr>";
                    
                    $colIndex = 0;
                    foreach ($allCols as $c) {
                        if ($c->getAttribute('class') === 'tablist-col') {
                            echo "<tr>";
                            echo "<td>" . $colIndex . "</td>";
                            echo "<td>tablist-col</td>";
                            echo "<td>" . htmlspecialchars(trim($c->textContent)) . "</td>";
                            echo "</tr>";
                            $colIndex++;
                        }
                    }
                    echo "</table>";
                    
                    // Now find all rows
                    echo "<h5>Finding all firmware rows:</h5>";
                    $allRows = $xpath->query("//div[contains(@class, 'tablist-row')]");
                    echo "<p>Total tablist-row divs: " . $allRows->length . "</p>";
                }
                
                return;
            }

            // Display sample data
            echo "<h2>Step 3: Sample Data (First 5 Entries)</h2>";
            echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #4CAF50; color: white;'>";
            echo "<th>#</th><th>Device</th><th>Model</th><th>CSC</th><th>PDA</th><th>CSC Ver</th><th>Android</th><th>Date</th></tr>";
            
            foreach (array_slice($firmwares, 0, 5) as $i => $fw) {
                echo "<tr>";
                echo "<td>" . ($i+1) . "</td>";
                echo "<td>" . htmlspecialchars($fw['deviceName']) . "</td>";
                echo "<td><strong>" . htmlspecialchars($fw['model']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($fw['csc']) . "</td>";
                echo "<td>" . htmlspecialchars($fw['pdaVersion']) . "</td>";
                echo "<td>" . htmlspecialchars($fw['cscVersion']) . "</td>";
                echo "<td>" . htmlspecialchars($fw['androidVersion']) . "</td>";
                echo "<td>" . htmlspecialchars($fw['releaseDate']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<hr>";

            // Region analysis
            echo "<h2>Step 4: Region Coverage</h2>";
            $regions = [];
            foreach ($firmwares as $fw) {
                $regions[$fw['csc']] = ($regions[$fw['csc']] ?? 0) + 1;
            }
            
            echo "<p><strong>Unique regions found: " . count($regions) . "</strong></p>";
            echo "<p>Regions: " . implode(', ', array_keys($regions)) . "</p>";
            
            $globalRegions = ['XAA', 'TMB', 'VZW', 'ATT', 'EUR', 'DBT', 'BTU', 'TGY', 'CHC', 'CCT'];
            $foundGlobal = array_intersect($globalRegions, array_keys($regions));
            
            if (!empty($foundGlobal)) {
                echo "<p style='color:green; font-weight:bold;'>✅ GLOBAL COVERAGE CONFIRMED!</p>";
                echo "<p>Found " . count($foundGlobal) . " major global regions: " . implode(', ', $foundGlobal) . "</p>";
            }
            echo "<hr>";

            // Test download link
            echo "<h2>Step 5: Download Link Test</h2>";
            $testFw = $firmwares[0];
            $detailUrl = $testFw['detailUrl'];
            echo "<p>Testing: <a href='{$detailUrl}' target='_blank'>{$detailUrl}</a></p>";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $detailUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                echo "<p style='color:green;'>✅ Detail page exists (HTTP {$httpCode})</p>";
            } else {
                echo "<p style='color:orange;'>⚠️ HTTP {$httpCode}</p>";
            }
            echo "<hr>";

            // Final verdict
            echo "<h2>Final Verdict</h2>";
            if (count($firmwares) > 5 && count($regions) > 3) {
                echo "<div style='background: #4CAF50; color: white; padding: 20px;'>";
                echo "<h3>✅ TEST PASSED!</h3>";
                echo "<p>OdinRom parser is working correctly!</p>";
                echo "<ul>";
                echo "<li>✅ Parsed " . count($firmwares) . " firmware entries</li>";
                echo "<li>✅ Found " . count($regions) . " regions</li>";
                if (!empty($foundGlobal)) {
                    echo "<li>✅ Global coverage: " . count($foundGlobal) . " major regions</li>";
                }
                echo "</ul>";
                echo "<p><strong>READY FOR IMPLEMENTATION!</strong></p>";
                echo "</div>";
            } else {
                echo "<div style='background: #f44336; color: white; padding: 20px;'>";
                echo "<h3>❌ TEST FAILED</h3>";
                echo "<p>Parser needs adjustment</p>";
                echo "</div>";
            }

        } catch (\Exception $e) {
            echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    }

    /**
     * Test parser for OdinRom (updated for tablist structure)
     */
    private function parseOdinRomTest(string $html): array
    {
        $firmwares = [];

        try {
            $doc = new \DOMDocument();
            @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($doc);

            // Find all tablist-row divs
            $rows = $xpath->query("//div[contains(@class, 'tablist-row')]");
            
            foreach ($rows as $row) {
                try {
                    // Get all columns in this row
                    $cols = [];
                    $colDivs = $row->getElementsByTagName('div');
                    
                    foreach ($colDivs as $colDiv) {
                        if ($colDiv->getAttribute('class') === 'tablist-col') {
                            $cols[] = trim($colDiv->textContent);
                        }
                    }
                    
                    // Need at least 7 columns (Device, Model, Region, AP, CSC, OS, Date)
                    if (count($cols) < 7) {
                        continue;
                    }
                    
                    $deviceName = $cols[0];
                    $model = $cols[1];
                    $csc = $cols[2];
                    $pdaVersion = $cols[3];
                    $cscVersion = $cols[4];
                    $androidVersion = $cols[5];
                    $date = $cols[6];

                    // Skip header rows
                    if (empty($model) || $model === 'Model' || empty($deviceName) || $deviceName === 'Device') {
                        continue;
                    }

                    // Skip invalid models
                    if (strlen($model) < 5 || !str_starts_with($model, 'SM-')) {
                        continue;
                    }

                    $cleanDevice = str_replace('Galaxy ', '', $deviceName);
                    $device = strtoupper(str_replace([' ', '-'], ' ', $cleanDevice));

                    $firmwares[] = [
                        'deviceName' => $deviceName,
                        'device' => $device,
                        'model' => $model,
                        'csc' => $csc,
                        'country' => '',
                        'pdaVersion' => $pdaVersion,
                        'cscVersion' => $cscVersion,
                        'androidVersion' => $androidVersion,
                        'releaseDate' => $date,
                        'source' => 'odinrom',
                        'detailUrl' => "https://www.odinrom.com/download/{$model}-{$csc}-{$pdaVersion}-{$cscVersion}.html",
                    ];

                } catch (\Exception $e) {
                    continue;
                }
            }

        } catch (\Exception $e) {
            // Silent fail
        }

        return $firmwares;
    }

    /**
     * Update existing firmwares with improved device names and binary levels
     * URL: /firmware-scraper/update-existing
     */
    public function updateExisting()
    {
        try {
            $this->logMessage('info', '========== UPDATING EXISTING FIRMWARES ==========');
            
            $updated = 0;
            $deleted = 0;
            $failed = 0;

            // First, delete all UNKNOWN device entries
            $unknownDeleted = $this->db->table('fw_posts')
                ->where('device', 'UNKNOWN')
                ->where('scrapedFrom IS NOT NULL')
                ->delete();
            
            $deleted = $this->db->affectedRows();
            $this->logMessage('info', "Deleted {$deleted} UNKNOWN device entries");

            // Get all firmwares scraped by our system (excluding UNKNOWN)
            $records = $this->db->table('fw_posts')
                ->where('scrapedFrom IS NOT NULL')
                ->where('device !=', 'UNKNOWN')
                ->get()
                ->getResult();

            foreach ($records as $record) {
                try {
                    $updates = [];

                    // Fix device name if it's generic
                    if (in_array($record->device, ['A SERIES', 'Z SERIES']) || empty($record->device)) {
                        $newDevice = $this->guessDeviceFromModel($record->model);
                        if ($newDevice !== 'UNKNOWN' && $newDevice !== $record->device) {
                            $updates['device'] = $newDevice;
                        }
                    }

                    // Update binary version (always re-extract to ensure accuracy)
                    if (!empty($record->version)) {
                        $bit = $this->extractBinaryVersion($record->version);
                        if (!empty($bit) && $bit !== $record->bit) {
                            $updates['bit'] = $bit;
                        }
                    }

                    // Add CSC version if missing (for SamMobile entries)
                    if (empty($record->cscversion) && !empty($record->version) && !empty($record->csc)) {
                        $cscVersion = $this->generateCscVersion($record->version, $record->csc);
                        if (!empty($cscVersion)) {
                            $updates['cscversion'] = $cscVersion;
                        }
                    }

                    // Apply updates if any
                    if (!empty($updates)) {
                        $this->db->table('fw_posts')
                            ->where('postId', $record->postId)
                            ->update($updates);
                        $updated++;
                        
                        $this->logMessage('info', "Updated {$record->model}: " . json_encode($updates));
                    }

                } catch (\Exception $e) {
                    $this->logMessage('error', "Failed to update post {$record->postId}: " . $e->getMessage());
                    $failed++;
                }
            }

            $this->logMessage('info', "========== UPDATE COMPLETED ==========");
            $this->logMessage('info', "Deleted: {$deleted}, Updated: {$updated}, Failed: {$failed}");

            return $this->response->setJSON([
                'success' => true,
                'deleted' => $deleted,
                'updated' => $updated,
                'failed' => $failed,
                'message' => 'Existing firmwares updated with improved data',
                'timestamp' => date('Y-m-d H:i:s'),
            ]);

        } catch (\Exception $e) {
            $this->logMessage('error', "Update existing failed: " . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * View scraper logs
     * URL: /firmware-scraper/logs
     */
    public function logs(): void
    {
        $limit = $this->request->getGet('limit') ?? 100;
        
        $logs = $this->db->table('fw_scraper_log')
            ->orderBy('logId', 'desc')
            ->limit($limit)
            ->get()
            ->getResult();

        echo "<h1>Firmware Scraper Logs</h1>";
        echo "<p>Showing last {$limit} log entries</p>";
        echo "<table border='1' cellpadding='5' style='width:100%; border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Level</th><th>Message</th><th>Time</th></tr>";

        foreach ($logs as $log) {
            $color = match ($log->logLevel) {
                'error' => '#ffcccc',
                'warning' => '#fff4cc',
                'success' => '#ccffcc',
                default => '#ffffff',
            };
            echo "<tr style='background:{$color};'>";
            echo "<td>{$log->logId}</td>";
            echo "<td><strong>{$log->logLevel}</strong></td>";
            echo "<td>{$log->logMessage}</td>";
            echo "<td>{$log->logTime}</td>";
            echo "</tr>";
        }

        echo "</table>";
    }

    /**
     * Clear cache
     * URL: /firmware-scraper/clear-cache
     */
    public function clearCache(): void
    {
        try {
            $this->db->table('fw_scraper_cache')->truncate();
            echo json_encode([
                'success' => true,
                'message' => 'Cache cleared successfully',
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Cache table unavailable (read-only mode)',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
