<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    $r->addRoute('GET', '/complaints', [
        'controller' => \App\Supplier\app\Http\Controllers\Complaints\ComplaintsController::class,
        'method'     => 'index',
    ]);

    $r->addRoute('GET', '/ajax/complaints/attachment/{uuid}', [
        'controller' => \App\Supplier\app\Http\Controllers\Complaints\ComplaintsController::class,
        'method'     => 'getAttachmentPresignedUrl',
    ]);

    $r->addRoute('PATCH', '/ajax/complaints/{id}', [
        'controller' => \App\Supplier\app\Http\Controllers\Complaints\ComplaintsController::class,
        'method'     => 'updateComplaint',
    ]);
};
