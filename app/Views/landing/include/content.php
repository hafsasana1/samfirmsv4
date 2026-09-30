<?php
// CI4: replaced $this->load->view() chain with view() calls
echo view('landing/include/header',  $data ?? []);
echo view('landing/include/sidebar', $data ?? []);
echo view('landing/' . $request,     $data ?? []);
echo view('landing/include/footer',  $data ?? []);
