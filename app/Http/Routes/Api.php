<?php
declare(strict_types=1);

/** @var \Laravel\Lumen\Routing\Router $router */

// MailChimp group
$router->group(['prefix' => 'mailchimp', 'namespace' => 'MailChimp'], function () use ($router) {
    // Lists group
    $router->group(['prefix' => 'lists'], function () use ($router) {
        $router->post('/', 'ListsController@create');
        $router->get('/{listId}', 'ListsController@show');
        $router->put('/{listId}', 'ListsController@update');
        $router->delete('/{listId}', 'ListsController@remove');
    });

    //List members group
    $router->group(['prefix' => 'lists'], function () use ($router) {
        $router->post('/{listId}/members', 'ListMembersController@create');
        $router->get('/{listId}/members/{memberId}', 'ListMembersController@show');
        $router->put('/{listId}/members/{memberId}', 'ListMembersController@update');
        $router->delete('/{listId}/members/{memberId}', 'ListMembersController@remove');
    });
});
