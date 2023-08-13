<?php
/**
 * By MahmoudAp
 * Github: https://github.com/mahmoud-ap
 */

$cronRoutes =  $app->group('/cron', function () use ($app) {
    $app->get('/master',            'App\Controllers\Cronjob:master');
    $app->get('/multi-users',       'App\Controllers\Cronjob:multiUser');
    $app->get('/expire-users',      'App\Controllers\Cronjob:expireUsers');
    $app->get('/sync-traffic',      'App\Controllers\Cronjob:syncTraffic');
});

