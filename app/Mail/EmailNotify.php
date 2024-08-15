<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailNotify extends Mailable
{
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
