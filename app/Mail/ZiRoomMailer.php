<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ZiRoomMailer extends Mailable
{
    use Queueable, SerializesModels;

    protected $url;

    protected $title;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url, $title)
    {
        $this->url = $url;
        $this->title = $title;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.ziroom')
            ->with([
                'url' => $this->url,
                'title' => $this->title,
            ]);
    }
}
