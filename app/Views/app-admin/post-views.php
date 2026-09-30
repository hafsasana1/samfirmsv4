<?php
$postTitle = replacePostToken($post->postTitle, $post);
$worldCountries = worldCountries('2');
?>
<div class="p-4 lg:p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Post Views</h1>
            <p class="text-sm text-gray-500 mt-1"><?= $postTitle ?></p>
        </div>
        <a href="<?= base_url(ADMIN_PATH.'/posts') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <!-- Views Table Card -->
    <div class="bg-white rounded-lg shadow-card">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 w-12">#</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">IP Address</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Country</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Referrer URL</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Visit URL</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">User Agent</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php
                        $i = 0;
                        foreach($viewsrecord as $vrec){
                            echo '<tr class="hover:bg-gray-50 transition-colors">';
                                echo '<td class="py-3 px-4 text-sm text-gray-600">'.++$i.'</td>';
                                echo '<td class="py-3 px-4 text-sm font-mono text-gray-800">'.$vrec->ipAddress.'</td>';
                                echo '<td class="py-3 px-4 text-sm text-gray-700">';
                                    echo '<span class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs">';
                                        echo $worldCountries[$vrec->country]['name'];
                                    echo '</span>';
                                echo '</td>';
                                echo '<td class="py-3 px-4 text-sm text-gray-600 max-w-xs truncate" title="'.$vrec->referrelUrl.'">'.$vrec->referrelUrl.'</td>';
                                echo '<td class="py-3 px-4 text-sm">';
                                    echo '<a href="'.$vrec->visitingUrl.'" target="_blank" class="text-accent hover:text-accent-dark break-all">';
                                        echo str_replace(base_url(), '', $vrec->visitingUrl);
                                    echo '</a>';
                                echo '</td>';
                                echo '<td class="py-3 px-4 text-xs text-gray-500 max-w-xs truncate" title="'.$vrec->userAgent.'">'.$vrec->userAgent.'</td>';
                                echo '<td class="py-3 px-4 text-xs text-gray-500 whitespace-nowrap">';
                                    echo '<i class="fas fa-clock mr-1"></i>'.$vrec->viewTime;
                                echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
