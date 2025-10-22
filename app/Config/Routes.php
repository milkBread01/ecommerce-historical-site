<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('home','Home::index');


$routes->group('admin', function ($routes) {
    
    $routes->get('dashboard','admin_controllers\AdminController_c::index');

    $routes->get('new-product-details','admin_controllers\AdminController_c::new_product_details');

    $routes->get('new-product-images','admin_controllers\AdminController_c::new_product_images');

    $routes->get('edit-products-dash','admin_controllers\EditProducts_c::index');
    
    $routes->get('products/category/(:segment)', 'admin_controllers\EditProducts_c::edit_product_category/$1');

    $routes->get('edit-product/(:segment)','admin_controllers\EditProducts_c::edit_product/$1');

    $routes->post('partials/product-fields', 'admin_controllers\AdminController_c::productFields');

    $routes->post('validate_npf_details', 'admin_controllers\AdminController_c::validate_npf_details');

    $routes->post('validate_npf_images', 'admin_controllers\AdminController_c::validate_npf_images');

    $routes->post('change-visibility','admin_controllers\EditProducts_c::changeVisibility');

});

$routes->group('products', function ($routes) {
    $routes->get('category/(:segment)','customer_controllers\CustomerController_c::index/$1');

});

$routes->get('navigation/json', 'Navigation::getNavigationJson');
