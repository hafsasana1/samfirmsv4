<?php
$imeiSearch = session()->get('imeiSearch') ?? [];
?>

<!-- Main Content -->
<div class="flex-1 overflow-auto">
    <div class="p-6">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center space-x-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span>IMEI Data</span>
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">View and search IMEI check requests</p>
        </div>

        <!-- Search Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="p-6">
                <form action="<?= base_url(ADMIN_PATH . '/imei') ?>" method="post" class="space-y-4">
                    <?= csrf_field() ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Search
                            </label>
                            <input type="text" 
                                   name="searchIn" 
                                   value="<?= esc($imeiSearch['searchIn'] ?? '') ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter IMEI, serial, model name or model number...">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Search in: <strong>model | imei | serial | modelNumber</strong>
                            </p>
                        </div>
                        
                        <div class="flex items-end space-x-2">
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search
                            </button>
                            
                            <?php if (session()->has('imeiSearch')) { ?>
                            <a href="<?= base_url(ADMIN_PATH . '/imei?reset=true') ?>" 
                               class="px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white font-semibold rounded-lg transition-colors shadow-sm">
                                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- IMEI Data Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    IMEI Check Requests
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2">
                        (<?= number_format($total_rows) ?> total records)
                    </span>
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Visitor IP</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Result</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php
                        if (!empty($record)) {
                            $i = $page_record ?? 0;
                            foreach ($record as $rec) {
                                $i++;
                                
                                // Build result display
                                $resultDisplay = '<div class="space-y-1 text-sm">';
                                
                                // Model Info
                                if (!empty($rec->modelInfo)) {
                                    $resultDisplay .= '<div><strong>Model Info:</strong> ' . esc($rec->modelInfo) . '</div>';
                                }
                                
                                // Search Term (IMEI)
                                if (!empty($rec->imei)) {
                                    $resultDisplay .= '<div><strong>Search Term:</strong> ' . esc($rec->imei) . '</div>';
                                }
                                
                                // IMEI
                                if (!empty($rec->imei)) {
                                    $resultDisplay .= '<div><strong>IMEI:</strong> ' . esc($rec->imei) . '</div>';
                                }
                                
                                // Serial Number
                                if (!empty($rec->serial)) {
                                    $resultDisplay .= '<div><strong>Serial Number:</strong> ' . esc($rec->serial) . '</div>';
                                }
                                
                                // Model Desc
                                if (!empty($rec->modelDesc)) {
                                    $resultDisplay .= '<div><strong>Model Desc:</strong> ' . esc($rec->modelDesc) . '</div>';
                                }
                                
                                // Model Name
                                if (!empty($rec->modelName)) {
                                    $resultDisplay .= '<div><strong>Model Name:</strong> ' . esc($rec->modelName) . '</div>';
                                }
                                
                                // Model Number
                                if (!empty($rec->modelNumber)) {
                                    $resultDisplay .= '<div><strong>Model Number:</strong> ' . esc($rec->modelNumber) . '</div>';
                                }
                                
                                // Warranty Status
                                if (!empty($rec->warrantyStatus)) {
                                    $resultDisplay .= '<div><strong>Warranty Status:</strong> ' . esc($rec->warrantyStatus) . '</div>';
                                }
                                
                                // Production Date
                                if (!empty($rec->productionDate)) {
                                    $resultDisplay .= '<div><strong>Production Date:</strong> ' . esc($rec->productionDate) . '</div>';
                                }
                                
                                // Country
                                if (!empty($rec->country)) {
                                    $resultDisplay .= '<div><strong>Country:</strong> ' . esc($rec->country) . '</div>';
                                }
                                
                                // Carrier
                                if (!empty($rec->carrier)) {
                                    $resultDisplay .= '<div><strong>Carrier:</strong> ' . esc($rec->carrier) . '</div>';
                                }
                                
                                $resultDisplay .= '</div>';
                                
                                echo '<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">';
                                echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">' . $i . '</td>';
                                echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-mono">' . esc($rec->orderId ?? '-') . '</td>';
                                echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">' . date('Y-m-d H:i', strtotime($rec->createdTime)) . '</td>';
                                echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400 font-mono">' . esc($rec->visitorIP ?? '-') . '</td>';
                                echo '<td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">' . $resultDisplay . '</td>';
                                echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">' . ($rec->price > 0 ? '$' . number_format($rec->price, 2) : '-') . '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr>';
                            echo '<td colspan="6" class="px-6 py-12 text-center">';
                            echo '<div class="flex flex-col items-center justify-center space-y-3">';
                            echo '<svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                            echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>';
                            echo '</svg>';
                            echo '<p class="text-gray-500 dark:text-gray-400 font-medium">No IMEI check requests found</p>';
                            echo '<p class="text-sm text-gray-400 dark:text-gray-500">Try adjusting your search criteria</p>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (!empty($record) && $total_rows > $per_page) { ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Showing <strong><?= (($page - 1) * $per_page) + 1 ?></strong> to 
                        <strong><?= min($page * $per_page, $total_rows) ?></strong> of 
                        <strong><?= number_format($total_rows) ?></strong> results
                    </div>
                    <?= $pager->links('default', 'default_full') ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
