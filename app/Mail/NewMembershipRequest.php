<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\MembershipRequest;

class NewMembershipRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    public function __construct(MembershipRequest $request)
    {
        $this->request = $request;
    }

    public function build()
    {
        $mail = $this->subject('New Membership Request - Prosix Sports')
                     ->replyTo($this->request->email, $this->request->name)
                     ->view('emails.membership-request')
                     ->with(['data' => $this->request]);

        if (!empty($this->request->image)) {
            $path = storage_path('app/public/' . $this->request->image);
            if (file_exists($path)) {
                $mail->attachData(
                    file_get_contents($path),
                    basename($path),
                    ['mime' => mime_content_type($path)]
                );
            }
        }

        return $mail;
    }
}
