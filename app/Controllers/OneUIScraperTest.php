<?php

namespace App\Controllers;

/**
 * ============================================================================
 * ONE UI VERSION SCRAPER TEST
 * ============================================================================
 * 
 * Purpose: Test scraping One UI versions from SamMobile.com
 * 
 * This is a standalone test to verify SamMobile scraping works correctly
 * before integrating into the main FirmwareScraper system.
 * 
 * Test URL: /oneui-scraper-test/test/{model}
 * Example: /oneui-scraper-test/test/SM-S921N
 * 
 * SamMobile URL Pattern:
 * https://www.sammobile.com/samsung/{device-name}/firmware/{model}/
 * Example: https://www.sammobile.com/samsung/galaxy-s24/firmware/SM-S921N/
 * 
 * Author: Created by Kiro AI
 * Date: August 28, 2026
 * ============================================================================
 */

class OneUIScraperTest extends BaseController
{
    /**
     * Test scraping One UI version for a specific model
     * URL: /oneui-scraper-test/test/{model}
     * Example: /oneui-scraper-test/test/SM-S921N
     */
    public function test($model = null)
    {
        if (empty($model)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Model parameter required',
                'usage' => '/oneui-scraper-test/test/SM-S921N',
                'examples' => [
                    'SM-S921N' => 'Galaxy S24 (Korea)',
                    'SM-S928B' => 'Galaxy S24 Ultra (Global)',
                    'SM-A546E' => 'Galaxy A54 5G',
                ],
            ]);
        }

        $result = $this->scrapeOneUIFromSamMobile($model);

        return $this->response->setJSON($result);
    }

    /**
     * Test with a list of sample models
     * URL: /oneui-scraper-test/test-batch
     */
    public function testBatch()
    {
        $testModels = [
            'SM-S921N',  // Galaxy S24
            'SM-S928B',  // Galaxy S24 Ultra
            'SM-A546E',  // Galaxy A54 5G
            'SM-F721B',  // Galaxy Z Flip4
        ];

        $results = [];

        foreach ($testModels as $model) {
            $results[$model] = $this->scrapeOneUIFromSamMobile($model);
            
            // Add delay to avoid rate limiting
            sleep(2);
        }

        return $this->response->setJSON([
            'success' => true,
            'total_tested' => count($testModels),
            'results' => $results,
        ]);
    }

    /**
     * Main scraping logic for SamMobile One UI version
     */
    private function scrapeOneUIFromSamMobile($model)
    {
        try {
            // Step 1: Get device name from model
            $deviceName = $this->guessDeviceNameForURL($model);
            
            if (empty($deviceName)) {
                return [
                    'success' => false,
                    'model' => $model,
                    'error' => 'Could not determine device name from model',
                    'step' => 'device_name_detection',
                ];
            }

            // Step 2: Construct SamMobile URL
            $sammobileUrl = "https://www.sammobile.com/samsung/{$deviceName}/firmware/{$model}/";

            // Step 3: Fetch page
            $html = $this->fetchPage($sammobileUrl);

            if (!$html) {
                return [
                    'success' => false,
                    'model' => $model,
                    'device_name' => $deviceName,
                    'url' => $sammobileUrl,
                    'error' => 'Failed to fetch SamMobile page (404 or connection error)',
                    'step' => 'page_fetch',
                ];
            }

            // Step 4: Parse firmware list and extract One UI versions
            $firmwareList = $this->parseSamMobilePage($html);

            if (empty($firmwareList)) {
                return [
                    'success' => false,
                    'model' => $model,
                    'device_name' => $deviceName,
                    'url' => $sammobileUrl,
                    'error' => 'No firmware entries found on page',
                    'step' => 'parsing',
                    'html_preview' => substr($html, 0, 500),
                ];
            }

            // Clean all text data for JSON encoding
            $firmwareList = $this->cleanArrayForJson($firmwareList);
            
            return [
                'success' => true,
                'model' => $model,
                'device_name' => $deviceName,
                'url' => $sammobileUrl,
                'total_firmwares' => count($firmwareList),
                'firmwares' => $firmwareList,
                'step' => 'completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'model' => $model,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'step' => 'exception',
            ];
        }
    }

    /**
     * Fetch page with proper headers
     */
    private function fetchPage($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate'); // Handle gzip compression
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
        ]);
        
        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($html === false || $httpCode !== 200) {
            return false;
        }

        // Fix UTF-8 encoding issues
        // Remove any invalid UTF-8 characters
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
        
        // Alternative: use iconv to clean
        $html = iconv('UTF-8', 'UTF-8//IGNORE', $html);

        return $html;
    }

    /**
     * Parse SamMobile firmware page to extract One UI versions
     */
    private function parseSamMobilePage($html)
    {
        $firmwares = [];

        try {
            // Clean HTML before parsing
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
            
            // Try to extract One UI versions directly from text first
            // SamMobile shows "One UI X.X" directly in the page
            preg_match_all('/One\s+UI\s+(\d+(?:\.\d+)?)/i', $html, $oneuiMatches);
            
            $doc = new \DOMDocument();
            libxml_use_internal_errors(true); // Suppress HTML parsing warnings
            $doc->loadHTML($html);
            libxml_clear_errors();
            
            $xpath = new \DOMXPath($doc);

            // SamMobile uses a table with firmware information
            // Look for table rows with firmware data
            $rows = $xpath->query("//table//tr");
            
            $rowIndex = 0;
            foreach ($rows as $row) {
                $cells = $row->getElementsByTagName('td');
                
                // Skip header rows or rows without enough cells
                if ($cells->length < 4) {
                    continue;
                }

                try {
                    // Extract text from all cells
                    $cellCount = $cells->length;
                    $cellTexts = [];
                    for ($i = 0; $i < $cellCount; $i++) {
                        $cellTexts[] = $this->cleanText($cells->item($i)->textContent);
                    }
                    
                    // Skip header rows (detect if any cell contains header keywords)
                    $headerKeywords = ['Model', 'Country/carrier', 'Date', 'PDA', 'Version', 'Android'];
                    $isHeader = false;
                    foreach ($cellTexts as $text) {
                        if (in_array($text, $headerKeywords) || strlen($text) > 100) {
                            $isHeader = true;
                            break;
                        }
                    }
                    if ($isHeader) {
                        continue;
                    }
                    
                    // Try to find One UI version in the row HTML
                    $rowHTML = $doc->saveHTML($row);
                    $oneUIVersion = '';
                    if (preg_match('/One\s+UI\s+(\d+(?:\.\d+)?)/i', $rowHTML, $match)) {
                        $oneUIVersion = $match[1]; // e.g., "8.5", "9.0", "6.1.1"
                    }
                    
                    // Parse cell data
                    // Common patterns:
                    // Model | Region | Date | PDA Version | Android/One UI
                    $region = '';
                    $version = '';
                    $date = '';
                    
                    if ($cellCount >= 4) {
                        $region = $cellTexts[1]; // Country name
                        $date = $cellTexts[2];    // Date
                        $version = $cellTexts[3]; // PDA version
                    }

                    // Skip invalid rows
                    if (empty($version) || strlen($version) < 5 || empty($region)) {
                        continue;
                    }
                    
                    // Skip if version looks like it's still a header
                    if (stripos($version, 'Country') !== false || stripos($version, 'carrier') !== false) {
                        continue;
                    }

                    $firmwares[] = [
                        'region' => $region,
                        'pda_version' => $version,
                        'oneui' => $oneUIVersion, // Real One UI version from SamMobile!
                        'date' => $date,
                    ];
                    
                    $rowIndex++;

                } catch (\Exception $e) {
                    // Skip problematic rows
                    continue;
                }
            }

        } catch (\Exception $e) {
            throw new \Exception("Error parsing SamMobile HTML: " . $e->getMessage());
        }

        return $firmwares;
    }

    /**
     * Map Android version to One UI version
     * Based on Samsung's official release mapping
     */
    private function mapAndroidToOneUI($androidVersion)
    {
        // Clean the version (remove "Android" text, keep only number)
        $androidVersion = preg_replace('/[^0-9.]/', '', $androidVersion);
        $androidVersion = trim($androidVersion);
        
        // Handle empty or invalid versions
        if (empty($androidVersion)) {
            return '';
        }
        
        // Extract major version
        $majorVersion = (int)$androidVersion;
        
        // Map Android version to One UI version
        $mapping = [
            16 => '8.0',  // Android 16 = One UI 8.0
            15 => '7.0',  // Android 15 = One UI 7.0
            14 => '6.0',  // Android 14 = One UI 6.0
            13 => '5.0',  // Android 13 = One UI 5.0
            12 => '4.0',  // Android 12 = One UI 4.0
            11 => '3.0',  // Android 11 = One UI 3.0
            10 => '2.0',  // Android 10 = One UI 2.0
            9  => '1.0',  // Android 9 = One UI 1.0
        ];
        
        return $mapping[$majorVersion] ?? '';
    }

    /**
     * Clean text from HTML (remove extra spaces, invalid UTF-8)
     */
    private function cleanText($text)
    {
        $text = trim($text);
        $text = preg_replace('/\s+/', ' ', $text); // Normalize whitespace
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);
        return $text;
    }

    /**
     * Clean One UI version string
     * Input: "One UI 6.1.1", "One UI 7.0", "6.1"
     * Output: "6.1.1", "7.0", "6.1"
     */
    private function cleanOneUIVersion($oneUI)
    {
        if (empty($oneUI)) {
            return '';
        }

        // Extract version number from "One UI X.X.X" format
        if (preg_match('/One\s+UI\s+(\d+(?:\.\d+)?(?:\.\d+)?)/i', $oneUI, $match)) {
            return $match[1];
        }

        // If already just a number, return as-is
        if (preg_match('/^\d+(?:\.\d+)?(?:\.\d+)?$/', $oneUI)) {
            return $oneUI;
        }

        return $oneUI;
    }

    /**
     * Guess device name for SamMobile URL from model number
     * SM-S921N -> galaxy-s24
     * SM-A546E -> galaxy-a54-5g
     * SM-F721B -> galaxy-z-flip4
     */
    private function guessDeviceNameForURL($model)
    {
        // Remove SM- prefix and extract series identifier
        $model = strtoupper($model);
        
        // Common patterns
        $patterns = [
            // S Series (Flagship)
            '/^SM-S9(2[0-9])/' => function($m) {
                $variant = substr($m[1], 0, 1);
                $gen = 24; // S24 series
                if ($variant === '2') return "galaxy-s{$gen}";
                if ($variant === '6') return "galaxy-s{$gen}-plus";
                if ($variant === '8') return "galaxy-s{$gen}-ultra";
                return "galaxy-s{$gen}";
            },
            '/^SM-S9(1[0-9])/' => function($m) {
                $variant = substr($m[1], 0, 1);
                $gen = 23; // S23 series
                if ($variant === '1') return "galaxy-s{$gen}";
                if ($variant === '5') return "galaxy-s{$gen}-plus";
                if ($variant === '8') return "galaxy-s{$gen}-ultra";
                return "galaxy-s{$gen}";
            },
            
            // A Series (Mid-range)
            '/^SM-A(54[0-9])/' => 'galaxy-a54-5g',
            '/^SM-A(34[0-9])/' => 'galaxy-a34-5g',
            '/^SM-A(14[0-9])/' => 'galaxy-a14',
            
            // Z Series (Foldables)
            '/^SM-F9(4[0-9])/' => 'galaxy-z-fold5',
            '/^SM-F7(2[0-9])/' => 'galaxy-z-flip4',
            '/^SM-F7(3[0-9])/' => 'galaxy-z-flip5',
            
            // Note Series
            '/^SM-N9(8[0-9])/' => 'galaxy-note20-ultra',
            
            // Tab Series
            '/^SM-X(9[0-9])/' => 'galaxy-tab-s9-ultra',
        ];

        foreach ($patterns as $pattern => $result) {
            if (preg_match($pattern, $model, $matches)) {
                if (is_callable($result)) {
                    return $result($matches);
                }
                return $result;
            }
        }

        // Fallback: try to construct from model
        // SM-A546E -> galaxy-a54
        if (preg_match('/^SM-([A-Z])(\d{2,3})/', $model, $matches)) {
            $series = strtolower($matches[1]);
            $num = $matches[2];
            
            // Extract generation number (first 2 digits)
            $gen = substr($num, 0, 2);
            
            return "galaxy-{$series}{$gen}";
        }

        return null;
    }

    /**
     * Clean array data for JSON encoding (remove invalid UTF-8)
     */
    private function cleanArrayForJson($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->cleanArrayForJson($value);
            }
            return $data;
        }
        
        if (is_string($data)) {
            // Remove invalid UTF-8 characters
            $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            $data = iconv('UTF-8', 'UTF-8//IGNORE', $data);
            return $data;
        }
        
        return $data;
    }

    /**
     * Show usage instructions
     * URL: /oneui-scraper-test
     */
    public function index()
    {
        echo '<h1>One UI Scraper Test</h1>';
        echo '<h2>Usage</h2>';
        echo '<p><strong>Single Test:</strong> <code>/oneui-scraper-test/test/{model}</code></p>';
        echo '<p>Example: <a href="' . base_url('oneui-scraper-test/test/SM-S921N') . '">/oneui-scraper-test/test/SM-S921N</a></p>';
        echo '<hr>';
        echo '<h2>Batch Test:</h2>';
        echo '<p><a href="' . base_url('oneui-scraper-test/test-batch') . '">/oneui-scraper-test/test-batch</a></p>';
        echo '<hr>';
        echo '<h2>Test Models:</h2>';
        echo '<ul>';
        echo '<li><a href="' . base_url('oneui-scraper-test/test/SM-S921N') . '">SM-S921N</a> - Galaxy S24 (Korea)</li>';
        echo '<li><a href="' . base_url('oneui-scraper-test/test/SM-S928B') . '">SM-S928B</a> - Galaxy S24 Ultra (Global)</li>';
        echo '<li><a href="' . base_url('oneui-scraper-test/test/SM-A546E') . '">SM-A546E</a> - Galaxy A54 5G</li>';
        echo '<li><a href="' . base_url('oneui-scraper-test/test/SM-F721B') . '">SM-F721B</a> - Galaxy Z Flip4</li>';
        echo '</ul>';
    }
}
