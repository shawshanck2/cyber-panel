<?php
/**
 * By MahmoudAp
 * Github: https://github.com/mahmoud-ap
 */

$apiRoutes =  $app->group('/api/v1', function () use ($app) {
    
});

$apiRoutes->add(new \App\Middlewares\ApiAuth($container));
