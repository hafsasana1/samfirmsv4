<!-- Table Body -->
<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
    <?php
    $ii = ($current_page - 1) * $per_page;
    foreach($post_records as $rec){
        $post_link = postUrl($rec);
        echo '<tr id="autopost-'.$rec->postId.'" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">';
            echo '<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">'.++$ii.'</td>';
            echo '<td class="px-4 py-3 text-sm"><a href="'.$post_link.'" target="_blank" class="text-accent hover:text-accent-hover font-medium">'.replacePostToken($rec->postTitle,$rec).'</a></td>';
            echo '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">'.$rec->modifiedTime.'</td>';
            echo '<td class="px-4 py-3 whitespace-nowrap text-sm">';
                echo '<button onclick="postDownload(\''.$rec->postId.'\',\''.$rec->externalFileLink.'\')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors">';
                    echo '<i class="fas fa-link mr-1.5"></i> Post File Link';
                echo '</button>';
            echo '</td>';
        echo '</tr>';
    }
    ?>
</tbody>

<!-- Pagination Footer -->
<?php if($total_post_rows > $per_page): ?>
<tfoot>
    <tr>
        <td colspan="4" class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Entry Counter -->
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing 
                    <span class="font-medium"><?= number_format(($current_page - 1) * $per_page + 1) ?></span>
                    to 
                    <span class="font-medium"><?= number_format(min($current_page * $per_page, $total_post_rows)) ?></span>
                    of 
                    <span class="font-medium"><?= number_format($total_post_rows) ?></span>
                    entries
                </div>
                
                <!-- Pagination Links -->
                <div class="flex items-center gap-2">
                    <?php
                    $total_pages = ceil($total_post_rows / $per_page);
                    
                    // Previous Button
                    if($current_page > 1):
                    ?>
                        <button onclick="loadPostAutomationPage(<?= $current_page - 1 ?>)" 
                               class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-chevron-left mr-1"></i> Previous
                        </button>
                    <?php else: ?>
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md cursor-not-allowed">
                            <i class="fas fa-chevron-left mr-1"></i> Previous
                        </span>
                    <?php endif; ?>
                    
                    <!-- Page Numbers -->
                    <div class="flex items-center gap-1">
                        <?php
                        // Show max 7 page numbers
                        $start_page = max(1, $current_page - 3);
                        $end_page = min($total_pages, $current_page + 3);
                        
                        // Always show first page
                        if($start_page > 1):
                        ?>
                            <button onclick="loadPostAutomationPage(1)" 
                                   class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                1
                            </button>
                            <?php if($start_page > 2): ?>
                                <span class="px-2 text-gray-500 dark:text-gray-400">...</span>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Middle page numbers -->
                        <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                            <?php if($i == $current_page): ?>
                                <span class="px-3 py-2 text-sm font-medium text-white bg-accent border border-accent rounded-md">
                                    <?= $i ?>
                                </span>
                            <?php else: ?>
                                <button onclick="loadPostAutomationPage(<?= $i ?>)" 
                                       class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <?= $i ?>
                                </button>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <!-- Always show last page -->
                        <?php if($end_page < $total_pages): ?>
                            <?php if($end_page < $total_pages - 1): ?>
                                <span class="px-2 text-gray-500 dark:text-gray-400">...</span>
                            <?php endif; ?>
                            <button onclick="loadPostAutomationPage(<?= $total_pages ?>)" 
                                   class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <?= $total_pages ?>
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Next Button -->
                    <?php if($current_page < $total_pages): ?>
                        <button onclick="loadPostAutomationPage(<?= $current_page + 1 ?>)" 
                               class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Next <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    <?php else: ?>
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md cursor-not-allowed">
                            Next <i class="fas fa-chevron-right ml-1"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </td>
    </tr>
</tfoot>
<?php endif; ?>
