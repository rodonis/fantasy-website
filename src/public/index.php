<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';

$router = new Router();

$router->get('/',                           [PageController::class, 'home']);
$router->get('/wiki/{slug}',                [PageController::class, 'view']);
$router->get('/wiki/{slug}/edit',           [PageController::class, 'edit']);
$router->post('/wiki/{slug}/save',          [PageController::class, 'save']);
$router->get('/wiki/{slug}/history',        [PageController::class, 'history']);
$router->post('/wiki/{slug}/preview',       [PageController::class, 'preview']);
$router->get('/wiki/{slug}/backlinks',      [PageController::class, 'backlinks']);
$router->get('/wiki/{slug}/create',         [PageController::class, 'create']);
$router->get('/search',                     [SearchController::class, 'index']);
$router->get('/login',                      [AuthController::class, 'loginForm']);
$router->post('/login',                     [AuthController::class, 'login']);
$router->get('/logout',                     [AuthController::class, 'logout']);
$router->get('/register',                   [AuthController::class, 'registerForm']);
$router->post('/register',                  [AuthController::class, 'register']);
$router->post('/tweaks',                    [PageController::class, 'tweaks']);
$router->post('/upload',                    [UploadController::class, 'upload']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
