<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');


// custom
$routes->get('pages', 'Pages::index');
// $routes->get('(:any)', 'Pages::view/$1');

$routes->get('account', 'Account::index');
$routes->get('account/orders', 'Account::orders');
$routes->get('account/order/(:num)', 'Account::order/$1');
$routes->get('account/order/(:num)/cancel', 'Account::orderCancel/$1');
$routes->get('account/watchlist', 'Account::watchlist');
$routes->get('account/messages', 'Account::messages');
$routes->get('account/message/(:num)', 'Account::message/$1');
$routes->post('account/message/(:num)', 'Account::message/$1');
$routes->get('message', 'Account::initMessageChain');
$routes->get('account/login', 'Account::login');
$routes->post('account/login', 'Account::login');
$routes->post('account/register', 'Account::register');
$routes->get('account/register', 'Account::register');
$routes->get('account/logout', 'Account::logout');


$routes->get('search', 'Search::index');

$routes->get('shop/(:num)', 'Shop::index/$1');
$routes->get('shop/edit', 'Shop::edit');
$routes->post('shop/edit', 'Shop::edit');
$routes->post('shop/media/delete', 'Shop::deleteMedia');
$routes->post('shop/media/add', 'Shop::addMedia');
$routes->get('shop/inventory', 'Shop::inventory');
$routes->get('shop/orders', 'Shop::orders');
$routes->get('shop/order/(:num)', 'Shop::order/$1');
$routes->post('shop/order/complete', 'Shop::completeOrder');
$routes->get('shop/stats', 'Shop::stats');

// product
$routes->get('product/(:num)', 'Shop::product/$1');
$routes->get('product/edit/(:num)', 'Shop::productCreateEdit/$1');
$routes->post('product/edit/(:num)', 'Shop::productCreateEdit/$1');
$routes->post('product/delete', 'Shop::productDelete');
$routes->get('product/create', 'Shop::productCreateEdit');
$routes->post('product/create', 'Shop::productCreateEdit');
$routes->post('product/media/(:num)', 'Shop::productAddMedia/$1');
$routes->post('product/media/(:num)/delete', 'Shop::productDeleteMedia/$1');
$routes->post('product/(:num)/review', 'Shop::productReview/$1');


//cart
$routes->get('cart', 'Cart::index');
$routes->post('cart/add', 'Cart::add');
$routes->get('cart/remove/(:num)', 'Cart::remove/$1');
$routes->post('cart/checkout', 'Cart::checkout');
//$routes->get('cart/success', 'Cart::success');

// watch
$routes->get('watch/add/(:num)', 'Watch::watch/$1');
$routes->get('watch/remove/(:num)', 'Watch::unwatch/$1');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
