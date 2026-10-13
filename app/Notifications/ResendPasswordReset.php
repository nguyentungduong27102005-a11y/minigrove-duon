<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;

class ResendPasswordReset extends ResetPassword
{
    public function toMail($notifiable)
    {
        return parent::toMail($notifiable)
            ->mailer('resend')
            ->from('onboarding@resend.dev', 'MiniGrove');
    }
}
