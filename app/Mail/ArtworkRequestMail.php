<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ArtworkRequestMail extends Mailable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $mail = $this->subject('New Artwork Request Received')
                     ->replyTo($this->data->email, $this->data->full_name)
                     ->view('emails.artwork-request')
                     ->with(['data' => $this->data]);

        if (!empty($this->data->artwork_file)) {
            $images = is_string($this->data->artwork_file)
                ? json_decode($this->data->artwork_file)
                : $this->data->artwork_file;

            if (!empty($images)) {
                foreach ($images as $img) {
                    $path = public_path('uploads/artwork/' . $img);
                    if (file_exists($path)) {
                        $mail->attachData(
                            file_get_contents($path),
                            $img,
                            ['mime' => mime_content_type($path)]
                        );
                    }
                }
            }
        }

        return $mail;
    }
}
