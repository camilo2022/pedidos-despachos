<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailOrder extends Mailable
{
    public $data;
    public $file;

    public function __construct($data, $file)
    {
        $this->data = $data;
        $this->file = $file;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('NOTIFICACION REGISTRO DE PEDIDO.')
            ->view('Dashboard.Emails.Order')
            ->with('order', $this->data)
            ->attach($this->file, [
                'as' => "PEDIDO N° {$this->data->id}.pdf",
                'mime' => 'application/pdf',
            ]);
    }
}
