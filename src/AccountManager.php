<?php

namespace NewJapanOrders\EmailActivation;

use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use Illuminate\Auth\CreatesUserProviders;
use NewJapanOrders\EmailActivation\AccountRepository;
use InvalidArgumentException;

class AccountManager extends Manager
{
    use CreatesUserProviders;
    
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $accounts = [];

    /**
     * Create a new AccountIssuer Manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {   
        $this->app = $app;
    }

    public function account($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return isset($this->accounts[$name])
                    ? $this->accounts[$name]
                    : $this->accounts[$name] = $this->resolve($name);
    }

    /**
     * Resolve the given broker.
     *
     * @param  string  $name
     * @return 
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        return new Account(
            $this->createUserProvider($config['provider'] ?? null),
            $this->createAccountRepository($config)
        );
    }

    /** 
     * Get the account issuer configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {   
        return $this->app['config']["auth.accounts.{$name}"];
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['auth.defaults.accounts'];
    }

    public function setDefaultDriver($name)
    {
        $this->app['config']['auth.defaults.accounts'] = $name;
    }

    /** 
     * Create a token repository instance based on the given configuration.
     *
     * @param  array  $config
     * @return EloquentAccountRepository 
     */
    protected function createAccountRepository(array $config)
    {   
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        } 

        return new AccountRepository(
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire']
        );  
    } 
}
