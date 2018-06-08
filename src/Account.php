<?php

namespace NewJapanOrders\EmailActivation;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Carbon;
use NewJapanOrders\EmailActivation\Contracts\CanActivate;
use NewJapanOrders\EmailActivation\Contracts\Account as AccountContract;
use NewJapanOrders\EmailActivation\Contracts\AccountRepositoryInterface;
use Closure;

class Account implements AccountContract 
{
    /** 
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $provider;

    /**
     * The account repository.
     *
     * @var \NewJapanOrders\EmailActivations\Contracts\AccountRepositoryInterface
     */
    protected $repository;

    /**
     * Create a new password broker instance.
     *
     * @param  \NewJapanOrders\EmailActivations\Contracts\AccountRepositoryInterface  $accounts
     * @return void
     */
    public function __construct(UserProvider $provider, 
                                AccountRepositoryInterface $repository)
    {
        $this->provider = $provider;
        $this->repository = $repository;
    }
    
    public function register(CanActivate $user)
    {
        return $this->repository->create($user);
    }

    public function activate($id, $token)
    {
        $model = $this->provider->createModel(); 
        $user = $model->findOrFail($id);
        
        $user = $this->repository->activate($user, $token);

        return $user;
    }
}
