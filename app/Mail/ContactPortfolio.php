<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactPortfolio extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data,$type=1)
    {
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Terima Kasih telah menghubungi";
        if ($this->type == 2) {
            $subject = "[PESAN BARU] ".$this->data->subject;
        }
        return $this->from($address = 'no-reply@deprakoso.com', $name = 'Erkade Assistant')
                    ->subject($subject)
                    ->view('email.contact_portfolio')
                    ->with(['data' => $this->data,'type'=>$this->type]);
    }
}
