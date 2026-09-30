<?php
// CI4: FCPATH fix
$web     = @json_decode(@file_get_contents(RESOURCE_PATH . 'web-setting.info'));
$webLogo = base_url() . 'resource/logo.png';
$favicon = base_url() . 'resource/favicon.ico';

if ($web && !empty($web->webLogo)) {
    $webLogo = base_url() . 'resource/' . $web->webLogo;
}
if ($web && !empty($web->favicon)) {
    $favicon = base_url() . 'resource/' . $web->favicon;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Not Found &middot; <?= $web->webTitle ?? '' ?></title>
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="<?= $favicon ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,400italic,500,700">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/vendor.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/elephant.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/errors.min.css">
  </head>
  <body>
    <div class="error">
      <div class="error-body">
        <h1 class="error-heading">Page Not Found</h1>
        <h4 class="error-subheading">We are sorry, the page you requested cannot be found.</h4>
        <p>
          <small>The URL may be misspelled or the page you're looking for is no longer available.</small>
        </p>
        <p><a class="btn btn-primary btn-pill btn-thick" href="<?= base_url() ?>">Return to homepage</a></p>
      </div>
      <div class="error-footer">
        <p>
          <small>&copy; <?= date('Y') ?> <a href="<?= base_url() ?>"><?= $web->webTitle ?? '' ?></a></small>
        </p>
      </div>
    </div>
    <script src="<?= base_url() ?>assets/js/vendor.min.js"></script>
    <script src="<?= base_url() ?>assets/js/elephant.min.js"></script>
  </body>
</html>