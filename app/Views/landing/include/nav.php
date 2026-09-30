<?php
// CI4: $this->web → $web (passed from controller)
//      $this->uri->segment() → service('request')->getUri()->getSegment()
$request_obj = service('request');

// ✅ FIX: Load web settings directly if not passed from controller
if (!isset($web)) {
    $web = @json_decode(@file_get_contents(RESOURCE_PATH . 'web-setting.info'));
}

$webLogo = base_url().'resource/thq-logo.jpg';
$favicon = base_url().'resource/favicon.ico';
if (isset($web) && file_exists(RESOURCE_PATH . $web->webLogo)) {
    $webLogo = base_url().'resource/'.$web->webLogo;
}
$webLogo = $webLogo.'?v='.time();
$webTitle = $web->webTitle ?? '';
?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <nav class="navbar navbar-expand-lg navbar-light navigation">
                    <a class="navbar-brand" href="<?= base_url() ?>">
                        <?php 
                        // Extract filename from URL (remove query string and base_url)
                        $logoFile = '';
                        if (!empty($webLogo)) {
                            $logoUrl = explode('?', $webLogo)[0]; // Remove ?v=timestamp
                            $logoFile = str_replace(base_url().'resource/', '', $logoUrl);
                        }
                        
                        // Check if logo file exists
                        $logoExists = !empty($logoFile) && file_exists(RESOURCE_PATH . $logoFile);
                        
                        if ($logoExists): 
                        ?>
                            <img src="<?= $webLogo ?>" 
                                 alt="<?= $webTitle ?? 'SamFirms Logo' ?>" 
                                 loading="lazy" 
                                 width="180" 
                                 height="40" 
                                 style="max-width: 300px; height: auto;">
                        <?php else: ?>
                            <span style="font-size: 24px; font-weight: bold; color: #1FBF8F; font-family: 'Inter', sans-serif;">
                                <?= $webTitle ?: 'SamFirms' ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto main-nav">
                            <li class="nav-item <?= $request_obj->getUri()->getSegment(1) == '' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url() ?>">Home</a>
                            </li>
                            <li class="nav-item <?= $request_obj->getUri()->getSegment(1) == 'firmware' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url() ?>firmware">Firmware</a>
                            </li>
                            <li class="nav-item <?= $request_obj->getUri()->getSegment(1) == 'blog' ? 'active' : '' ?>">
                                <a class="nav-link" href="<?= base_url() ?>blog">Blog/News</a>
                            </li>
                            <?php
                            foreach (getActivePages('header') as $pagee) {
                                echo '<li class="nav-item"><a class="nav-link '.($pagee->isButton == 'Yes' ? 'btn-nav btn btn-primary' : '').'" href="'.base_url($pagee->slugUrl).'">'.$pagee->navTitle.'</a></li>';
                            }
                            ?>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</section>
<?php if (!empty($header_ads)) { echo '<div class="container"><div class="col-md-12 ads-area" style="margin: 20px 0;">'.$header_ads.'</div></div>'; } ?>
<section class="dashboard section">
    <div class="container">
        <div class="row">
