<?php
// Application middleware

$app->hook('slim.before.dispatch', function() use ($app) { 
        $app->response->headers->set('Content-Type', 'application/json');
        $app->response->headers->set('Access-Control-Allow-Origin', '*');
    });