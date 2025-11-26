<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

/*
 * API REST v1 Routes
 * Prefix: /api/v1/
 */
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api\V1'], static function ($routes) {
    // Detections endpoints
    $routes->get('detections', 'DetectionsController::index');
    $routes->get('detections/latest', 'DetectionsController::latest');
    $routes->post('detections', 'DetectionsController::create');

    // Manufacturers endpoints
    $routes->get('manufacturers', 'ManufacturersController::index');

    // Stats endpoint
    $routes->get('stats', 'StatsController::index');
});
