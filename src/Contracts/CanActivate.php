<?php

namespace NewJapanOrders\EmailActivation\Contracts;

interface CanActivate
{
    public function getTokenAttribute();
    public function setTokenAttribute($value);
    public function sendEmailActivateNotification();
}

