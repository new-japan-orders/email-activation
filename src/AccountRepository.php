<?php

namespace NewJapanOrders\EmailActivation;

use NewJapanOrders\EmailActivation\Contracts\AccountRepositoryInterface;
use NewJapanOrders\EmailActivation\Contracts\CanActivate;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AccountRepository implements AccountRepositoryInterface
{
    /**
     * The Hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /** 
     * The token database table.
     *
     * @var string
     */
    protected $table;

    /** 
     * The hashing key.
     *
     * @var string
     */
    protected $hashKey;

    /** 
     * The number of seconds a token should last.
     *
     * @var int
     */
    protected $expires;

    /** 
     * Create a new database user provider.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $model
     * @return void
     */
    public function __construct(HasherContract $hasher, $table, $hashKey, $expires)
    {
        $this->hasher = $hasher;
        $this->table = $table;
        $this->hashKey = $hashKey;
        $this->expires = $expires * 60;
    }

    /**
     * Create a new account record.
     *
     * @param  \NewJapanOrders\EmailActivation\Contracts\CanActivate $user
     * @return \NewJapanOrders\EmailActivation\Contracts\CanActivate
     */
    public function create(CanActivate $user)
    {
        \DB::beginTransaction();       
        try {
            $user->save();
            $user->token = $this->createNewToken();
            $table = $user->getConnection()->table($this->table); 
            $table->insert([
                'email' => $user->email, 
                'token' => $this->hasher->make($user->token), 
                'created_at' => new Carbon
            ]);
            \DB::commit(); 
        } catch (Exception $e) {
            \DB::rollBack();
            throw $e;
        }
 
        return $user;
    }

    public function activate(CanActivate $user, $token)
    {
        if (!$this->existsToken($user, $token)) {
            throw new ModelNotFoundException();
        }
        $activate_record = $this->getToken($user);
        $table = $user->getConnection()->table($this->table);

        \DB::beginTransaction();
        try {
            $user->activated_at = new Carbon;    
            $user->save();
            $this->deleteToken($user, $token);
            
            \DB::commit(); 
        } catch (Exception $e) {
            \DB::rollBack();
            throw $e;
        }

        return $user; 
    }

    public function existsToken(CanActivate $user, $token)
    {
        $table = $user->getConnection()->table($this->table);
        $activate_record = $this->getToken($user, $token);
        return isset($activate_record) && 
               $this->hasher->check($token, $activate_record->token);
    }

    public function deleteToken(CanActivate $user)
    {
        $table = $user->getConnection()->table($this->table);
        $token = $table->where('email', $user->email)->delete();
    }

    public function deleteExpiredToken(CanActivate $user)
    {

    }

    public function getToken(CanActivate $user)
    {
        $table = $user->getConnection()->table($this->table);
        $token = $table->where('email', $user->email)
                       ->first();
        return $token;
    }

    /**
     * Build the record payload for the table.
     *
     * @param  string  $email
     * @param  string  $token
     * @return array
     */
    protected function getPayload($email, $token)
    {   
        return ['email' => $email, 'token' => $this->hasher->make($token), 'created_at' => new Carbon];
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    protected function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }
}
