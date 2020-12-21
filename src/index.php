<?php

namespace App;

require "./frameworks/autoload/Autoloader.php";

Autoloader::register();

use App\Core\Router\Router;
use function App\Modules\Users\users;

require './modules/users/api.php';

// ça depend du .htaccess, si on prend celui du prof on peut pas faire $_GET['url']
$router = new Router($_GET['url']);

// add un middleware pour toutes les routes
$router->addMiddleware(function () {
    echo 'global middleware';
    echo '<br>';
});

$router->use('/users', users($router));

$router->run();
