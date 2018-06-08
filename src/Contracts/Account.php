<?php

namespace NewJapanOrders\EmailActivation\Contracts;

use Closure;
use NewJapanOrders\EmailActivation\Contracts\CanActivate;

interface Account
{


    /** 
     * Register User for the given token.
     *
     * @param  \NewJapanOrders\EmailActivation\Contracts\CanActivate
     * @return mixed
     */
    public function register(CanActivate $user);

    /** 
     * Activate User for the given token.
     *
     * @param  $id
     * @param  $token
     * @return mixed
     */
    public function activate($id, $token);
}
