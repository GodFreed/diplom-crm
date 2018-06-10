<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Auth;
use Lang;
use App\Models\Lead;

class LeadActionNotification extends Notification
{
    use Queueable;

    private $lead;
    private $action;

    /**
     * Create a new notification instance.
     * LeadActionNotification constructor.
     * @param $lead
     * @param $action
     */
    public function __construct($lead, $action)
    {
        $this->lead = $lead;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        /*return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', 'https://laravel.com')
                    ->line('Thank you for using our application!'); */
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        switch ($this->action) {
            case 'created':
                $text = __('Лід ":title" була створена користувачем ":creator" і призначена вам', [
                'title' => $this->lead->title,
                'creator' => $this->lead->creator->name
                ]);
                break;
            case 'updated_status':
                $text = __('Лід ":title" був виконаний користувачем :username', [
                'title' => $this->lead->title,
                'username' =>  Auth()->user()->name
                ]);
                break;
            case 'updated_deadline':
                $text = __('Користувач ":username" оновив крайній срок для ліда ":title"', [
                'title' => $this->lead->title,
                'username' =>  Auth()->user()->name
                ]);
                break;
            case 'updated_assign':
                $text = __('Користувач ":username" призначив лід ":title" для вас', [
                'title' => $this->lead->title,
                'username' =>  Auth()->user()->name
                ]);
                break;
            default:
                break;
        }
        return [
            'assigned_user' => $notifiable->id, //Assigned user ID
            'created_user' => $this->lead->creator->id,
            'message' => $text,
            'type' => Lead::class,
            'type_id' =>  $this->lead->id,
            'url' => url('leads/' . $this->lead->id),
            'action' => $this->action
        ];
    }
}
