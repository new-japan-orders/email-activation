<?php

namespace NewJapanOrders\EmailActivation\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EmailActivateNotification extends Notification
{

    protected $user;
    public function __construct($user)
    {   
        $this->user = $user;
    } 
   
    /** 
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {   
        return ['mail'];
    } 
 
    /** 
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {   

        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', url(config('app.url').route('front.password.reset', $this->user->token, false)))
            ->line('If you did not request a password reset, no further action is required.');           
    }
}
