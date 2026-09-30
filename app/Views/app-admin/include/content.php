<?php
// CI4: replaced $this->load->view() chain with view() calls
echo view('app-admin/include/header', $data ?? []);
echo view('app-admin/include/nav',    []);
echo view('app-admin/' . $request,    $data ?? []);
echo view('app-admin/include/footer', $data ?? []);