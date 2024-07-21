<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

 $routes->get('/', 'Home::index', ['filter' => 'auth']);

 $routes->get('login', 'AuthController::login', ['filter' => 'redirect']);
 $routes->post('login', 'AuthController::login', ['filter' => 'redirect']);
 $routes->get('logout', 'AuthController::logout');

$routes->group('produk', ['filter' => 'auth'], function ($routes){
    $routes->get('', 'ProdukController::index');
    $routes->post('create', 'ProdukController::create');
    //$routes->post('', 'ProdukController::create');
    $routes->post('edit/(:any)', 'ProdukController::edit/$1');
    $routes->get('delete/(:any)', 'ProdukController::delete/$1');
    $routes->get('download', 'ProdukController::download');
});

$routes->group('keranjang', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'TransaksiController::index');
    $routes->post('', 'TransaksiController::cart_add');
    $routes->post('edit', 'TransaksiController::cart_edit');
    $routes->get('delete/(:any)', 'TransaksiController::cart_delete/$1');
    $routes->get('clear', 'TransaksiController::cart_clear');
});

$routes->get('checkout', 'TransaksiController::checkout', ['filter' => 'auth']);
$routes->get('getcity', 'TransaksiController::getcity', ['filter' => 'auth']);
$routes->get('getcost', 'TransaksiController::getcost', ['filter' => 'auth']);
$routes->post('buy', 'TransaksiController::buy', ['filter'=>'auth']);

$routes->get('keranjang', 'TransaksiController::index', ['filter' => 'auth']);

$routes->get('contact', 'ContactController::index', ['filter' => 'auth']);
$routes->get('profile', 'Home::profile', ['filter' => 'auth']);

$routes->group('api', function ($routes) {
$routes->post('monthly', 'ApiController::monthly');
});
$routes->post('api/yearly', 'ApiController::yearly');

$routes->get('transaksi', 'TransaksiController::history');
$routes->post('transaksi/ubah_status', 'TransaksiController::ubah_status');
$routes->get('transaksi', 'TransaksiController::history');

$routes->get('transaksi/download', 'TransaksiController::download');


