<?php

namespace NewJapanOrders\EmailActivation\Contracts;

use Illuminate\Foundation\Auth\User as Authenticatable;
use NewJapanOrders\EmailActivation\Contracts\CanActivate;

interface AccountRepositoryInterface
{
    public function create(CanActivate $user);
    public function activate(CanActivate $user, $token);
    public function deleteToken(CanActivate $user);
    public function deleteExpiredToken(CanActivate $user);
}
