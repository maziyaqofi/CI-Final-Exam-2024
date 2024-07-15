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
    $routes->post('', 'ProdukController::create');
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

$routes->get('keranjang', 'TransaksiController::index', ['filter' => 'auth']);

$routes->group('transaksi', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'TransaksiController::index');
});

 $routes->get('contact', 'ContactController::index', ['filter' => 'auth']);