<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\StyleCI\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

/**
 * This is the main routes class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class MainRoutes
{
    /**
     * Defines if these routes are for the browser.
     *
     * @var bool
     */
    public static $browser = true;

    /**
     * Define the main routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     *
     * @return void
     */
    public function map(Registrar $router)
    {
        $router->get('/', [
            'as'   => 'home',
            'uses' => 'HomeController@handle',
        ]);

        $router->get('privacy', [
            'as'   => 'privacy_policy',
            'uses' => 'HomeController@handlePrivacy',
        ]);

        $router->get('terms', [
            'as'   => 'terms_of_service',
            'uses' => 'HomeController@handleTerms',
        ]);
    }
}
