<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

/**
 * * API Routes
 */

$routes->get("token", "Token::index");

$routes->get('leads', 'Leads::index', ['filter' => 'authFilter']);
$routes->get('leads/(:num)', 'Leads::show/$1', ['filter' => 'authFilter']);
$routes->post('leads', 'Leads::create', ['filter' => 'authFilter']);
$routes->delete('leads/(:num)', 'Leads::delete/$1', ['filter' => 'authFilter']);
