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
        return $this->subject('New Membership Request - Prosix Sports')
                    ->view('emails.membership-request')
                    ->with(['data' => $this->request]);
    }
}