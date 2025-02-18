<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class EmailWallet extends Mailable implements ShouldQueue
{
    use Queueable;

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
            ->subject('RECORDATORIO DE PAGO DE DEUDA EN MORA.')
            ->view('Dashboard.Emails.Wallet')
            ->with('order', $this->data)
            ->attach($this->file, [
                'as' => "CARTERA {$this->data->client->client_name}.pdf",
                'mime' => 'application/pdf',
            ]);
    }
}
