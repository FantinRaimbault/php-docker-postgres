<?php

namespace App\Modules\Users;

use Exception;

function users($router): array
{
    return [
        $router->get('/:id', [hasAccess()], function ($id) { echo 'my userId: ' . $id; }),
        $router->get('/:id/name', [], function () { echo 'show only user name'; })
    ];
}

// ptit middleware pour test
function hasAccess(): \Closure
{
    return function ($id) {
        if ($id !== '12') {
            throw new Exception('User not allowed to access API', 1);
        }
        return 0;
    };
};
