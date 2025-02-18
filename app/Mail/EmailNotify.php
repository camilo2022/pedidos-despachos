<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class EmailNotify extends Mailable implements ShouldQueue
{
    use Queueable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('NOTIFICACION PEDIDO ASENTADO.')
            ->view('Dashboard.Emails.Notify')
            ->with('order', $this->data);
    }
}
