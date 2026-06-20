<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('catalog', 'Home::catalog');
$routes->get('checkout', 'Home::checkout');
$routes->post('checkout/process', 'Home::checkoutProcess');
$routes->get('status', 'Home::status');
$routes->get('profile', 'Home::profile');
$routes->post('updateProfile', 'Home::updateProfile');
$routes->get('api/check_order_updates', 'Home::checkOrderUpdates');
$routes->get('api/daily_capacity', 'Home::getDailyCapacity');
$routes->get('api/reorder/(:num)', 'Home::reorder/$1');
$routes->post('order/cancel/(:num)', 'Home::cancelOrder/$1');

$routes->group('auth', function($routes) {
    $routes->get('/', 'Auth::index');
    $routes->get('user', 'Auth::user');
    $routes->get('manager', 'Auth::manager');
    $routes->get('owner', 'Auth::owner');
    $routes->post('login_process', 'Auth::loginProcess');
    $routes->post('register_process', 'Auth::registerProcess');
    $routes->get('logout', 'Auth::logout');
});

$routes->group('manager', ['filter' => 'auth:manager,owner,admin'], function($routes) {
    $routes->get('/', 'Manager::index');
    $routes->get('stock_report', 'Manager::stockReport');
    $routes->post('add_stock', 'Manager::addStock');
    $routes->post('create_product', 'Manager::createProduct');
    $routes->get('delete_product/(:num)', 'Manager::deleteProduct/$1');
    $routes->post('edit_product', 'Manager::editProduct');
    $routes->get('distribution', 'Manager::distribution');
    $routes->post('update_order_status', 'Manager::updateOrderStatus');
    $routes->post('verify_payment', 'Manager::verifyPayment');
    $routes->post('reject_payment', 'Manager::rejectPayment');
    $routes->get('users', 'Manager::users');
    $routes->post('create_user', 'Manager::createUser');
    $routes->post('edit_user', 'Manager::editUser');
    $routes->get('delete_user/(:num)', 'Manager::deleteUser/$1');

    // Kelola Kurir / Driver (Revisi 4.0)
    $routes->get('couriers', 'Manager::couriers');
    $routes->post('create_courier', 'Manager::createCourier');
    $routes->post('edit_courier', 'Manager::editCourier');
    $routes->get('delete_courier/(:num)', 'Manager::deleteCourier/$1');
    $routes->post('assign_courier', 'Manager::assignCourier');
    $routes->get('reports', 'Manager::reports');
    $routes->get('export_sales_excel', 'Manager::exportSalesExcel');
    $routes->get('export_sales_pdf', 'Manager::exportSalesPdf');
    $routes->post('submit_report', 'Manager::submitReport');
    $routes->get('delete_report/(:num)', 'Manager::deleteReport/$1');
    $routes->post('edit_report', 'Manager::editReport');
    $routes->post('preview_report_data', 'Manager::previewReportData');
    $routes->get('export_report_excel/(:num)', 'Manager::exportReportExcel/$1');
    $routes->get('view_report/(:num)', 'Manager::viewReport/$1');

    // Fitur Pengaturan Bisnis & Backup (sebelumnya milik Owner)
    $routes->get('settings', 'Manager::settings');
    $routes->post('update_profile', 'Manager::updateProfile');
    $routes->get('backup_db', 'Manager::backupDb');

    // API: Auto-sync pesanan admin
    $routes->get('api/check_order_updates', 'Manager::checkOrderUpdatesAdmin');
});
