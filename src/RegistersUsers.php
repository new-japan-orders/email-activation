<?php

namespace NewJapanOrders\EmailActivation;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers as BaseRegistersUsers;
use Illuminate\Support\Str;

trait RegistersUsers
{
    use BaseRegistersUsers;

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        $user = $this->account()->register($user); 

        event(new Registered($user));

        if (method_exists($user, 'sendEmailActivateNotification')) {
            $user->sendEmailActivateNotification();
        }

        return $this->registered($request, $user)
            ?: back()->with('confirmation-success', trans('confirmation::confirmation.message'));
    }
    
    /**
     * Handle a confirmation request
     *
     * @param  integer $id
     * @param  string  $activation_code
     * @return \Illuminate\Http\Response
     */
    public function activate($id, $activation_code)
    {
        $user = $this->account()->activate($id, $activation_code);
        
        return redirect($this->redirectTo);
/*
        return redirect(route('login'))->with('confirmation-success', trans('confirmation::confirmation.success'));
*/
    }

    protected function validateActivate($user)
    {

    }

    /**
     * Handle a resend request
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
/*
    public function resend(Request $request)
    {
        if ($request->session()->has('user_id')) {

            $model = config('auth.providers.users.model');

            $user = $model::findOrFail($request->session()->get('user_id'));
            if (empty($user->confirmation_code)) {
                $user->confirmation_code = str_random(30);
                $user->save();
            }

            //$this->notifyUser($user);
            
            return redirect(route('login'))->with('confirmation-success', trans('confirmation::confirmation.resend'));
        }

        return redirect('/');
    }
*/

}
