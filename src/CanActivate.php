<?php

namespace NewJapanOrders\EmailActivation;

use NewJapanOrders\EmailActivation\Notifications\EmailActivateNotification;

trait CanActivate
{
    protected $token;
    public function getTokenAttribute()
    {
        return $this->token;
    }

    public function setTokenAttribute($value)
    {
        $this->token = $value;
    }

    public function sendEmailActivateNotification()
    {
        $this->notify(new EmailActivateNotification($this));
    }
}

