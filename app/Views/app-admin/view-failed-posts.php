<?php
// CI4: $this->page_record → $page_record
//      $this->pagination->create_links() → $pager->links()
?>
<div class="layout-content">
    <div class="layout-content-body">
        <div class="title-bar">
            <h1 class="title-bar-title"><span class="d-ib">Failed Posts (<?= $total_rows ?>)</span></h1>
        </div>
        <div class="panel">
            <div class="panel-body container-fluid">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead><th>#</th><th>Data</th><th>Error</th><th>Action</th></thead>
                            <tbody>
                                <?php
                                $i = $page_record ?? 0;
                                foreach ($record as $rec) {
                                    $crawlData = @json_decode($rec->crawlData, true);
                                    echo '<tr id="crawl-'.$rec->crawlId.'">';
                                        echo '<td>'.++$i.'</td>';
                                        echo '<td>'.$rec->urlLink.'<br><pre>'; print_r($crawlData); echo '</pre></td>';
                                        echo '<td><b>'.$rec->createdTime.'</b><br>'.$rec->processingError.'</td>';
                                        echo '<td>';
                                            echo '<a href="javascript:;" onclick="updateStatus(\''.$rec->crawlId.'\')"><i class="icon icon-pencil"></i></a>&nbsp;&nbsp;';
                                            echo '<a href="'.base_url('pinger/autoPost?crawlId='.$rec->crawlId).'" target="_blank"><i class="icon icon-refresh"></i></a>&nbsp;&nbsp;';
                                            echo '<a href="javascript:;" onclick="deletePost('.$rec->crawlId.')"><i class="icon icon-trash"></i></a>';
                                        echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                        <?= isset($pager) ? '<ul class="pagination pull-right">'.$pager->links().'</ul>' : '' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
function deletePost(crawlId){swal({type:'warning',title:'Are you sure to delete this?',text:'',showConfirmButton:true,showCancelButton:true,confirmButtonText:'Delete',confirmButtonColor:'#CD0000'},function(){$.ajax({type:'post',data:{crawlId:crawlId},url:'<?= base_url('app-admin/deleteAutoPost') ?>',success:function(response){if($.trim(response)!='success'){toastr.error(response,'',{timeOut:5000,positionClass:'toast-top-center'});}else{toastr.success('Action performed successfully','',{timeOut:5000,positionClass:'toast-top-center'});$('#crawl-'+crawlId).remove();}},error:function(err){toastr.error('Unable to process your request','',{timeOut:5000,positionClass:'toast-top-center'});}});});}
function updateStatus(crawlId){swal({type:'info',title:'Are you sure to set this as processed?',text:'',showConfirmButton:true,showCancelButton:true,confirmButtonText:'Continue',confirmButtonColor:'#CD0000'},function(){$.ajax({type:'post',data:{crawlId:crawlId},url:'<?= base_url('app-admin/updateAutoPost') ?>',success:function(response){if($.trim(response)!='success'){toastr.error(response,'',{timeOut:5000,positionClass:'toast-top-center'});}else{toastr.success('Action performed successfully','',{timeOut:5000,positionClass:'toast-top-center'});$('#crawl-'+crawlId).remove();}},error:function(err){toastr.error('Unable to process your request','',{timeOut:5000,positionClass:'toast-top-center'});}});});}
</script>
