<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VerifyDbStats extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'db:verify-stats';
    protected $description = 'Verify database post status and stats';

    public function run(array $params = [])
    {
        $db = \Config\Database::connect();
        
        CLI::write('=== DATABASE VERIFICATION ===', 'green');
        CLI::newLine();

        // Check 1: Post Status Distribution
        CLI::write('1️⃣  POST STATUS DISTRIBUTION:', 'yellow');
        $result = $db->query("SELECT postStatus, COUNT(*) as total FROM fw_posts GROUP BY postStatus")->getResult();
        foreach ($result as $row) {
            CLI::write("   {$row->postStatus}: {$row->total} posts");
        }

        // Check 2: Active posts with NULL publishedAt
        CLI::newLine();
        CLI::write('2️⃣  ACTIVE POSTS WITH NULL publishedAt:', 'yellow');
        $nullPublish = $db->table('fw_posts')
            ->where('postStatus', 'Active')
            ->where('publishedAt IS NULL')
            ->countAllResults();
        CLI::write("   Count: {$nullPublish} posts");

        // Check 3: Recent adds (last 7 days)
        CLI::newLine();
        CLI::write('3️⃣  POSTS ADDED IN LAST 7 DAYS:', 'yellow');
        $recentCount = $db->query("
            SELECT postStatus, COUNT(*) as total 
            FROM fw_posts 
            WHERE createdTime >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY postStatus
        ")->getResult();
        foreach ($recentCount as $row) {
            CLI::write("   {$row->postStatus}: {$row->total} posts");
        }

        // Check 4: Total unique models and countries
        CLI::newLine();
        CLI::write('4️⃣  CURRENT STATS (as function sees them):', 'yellow');
        $activeModels = $db->table('fw_posts')
            ->select('model')
            ->where('postStatus', 'Active')
            ->where('model !=', '')
            ->groupBy('model')
            ->countAllResults();
        CLI::write("   Active Models: {$activeModels}");

        $activeCountries = $db->table('fw_posts')
            ->select('country')
            ->where('postStatus', 'Active')
            ->where('country !=', '')
            ->groupBy('country')
            ->countAllResults();
        CLI::write("   Active Countries: {$activeCountries}");

        $activeFirmware = $db->table('fw_posts')
            ->where('postStatus', 'Active')
            ->countAllResults();
        CLI::write("   Active Firmware Files: {$activeFirmware}");

        // Check 5: Cache status
        CLI::newLine();
        CLI::write('5️⃣  CACHE STATUS:', 'yellow');
        $cache = \Config\Services::cache();
        $cachedStats = $cache->get('site_stats_v1');
        if ($cachedStats === null) {
            CLI::write("   ❌ Cache MISS - will recalculate", 'red');
        } else {
            CLI::write("   ✅ Cache HIT - current cached stats:", 'green');
            CLI::write("   " . json_encode($cachedStats, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        CLI::newLine();
        CLI::write('=== END VERIFICATION ===', 'green');
    }
}
