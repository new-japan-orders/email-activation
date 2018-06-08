<?php
namespace NewJapanOrders\EmailActivation\Facades;

use Illuminate\Support\Facades\Facade;

class Issuer extends Facade
{
  protected static function getFacadeAccessor() {
    return 'issuer';
  }
}
