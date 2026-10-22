<?php

namespace App\Notifications;

use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification
{
    use Queueable;

    public function __construct(public Account $account) {}

   
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

 
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low balance alert - '.$this->account->account_number)
            ->line('Your account '.$this->account->account_number.' has reached its minimum balance.')
            ->line('Current balance: $'.number_format((float) $this->account->balance, 2))
            ->line('Minimum balance: $'.number_format((float) $this->account->minimum_balance, 2));
    }

  
    public function toArray(object $notifiable): array
    {
        return [
            'account_id' => $this->account->id,
            'account_number' => $this->account->account_number,
            'balance' => number_format((float) $this->account->balance, 2),
            'minimum_balance' => number_format((float) $this->account->minimum_balance, 2),
            'message' => 'Account '.$this->account->account_number.' is at its minimum balance of $'
                .number_format((float) $this->account->minimum_balance, 2).'.',
        ];
    }
}
