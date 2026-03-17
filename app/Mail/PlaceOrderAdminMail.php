<?php

namespace App\Mail;

use App\Models\PlaceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlaceOrderAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public PlaceOrder $order;

    public function __construct(PlaceOrder $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        // No attachments — files are shown as preview + download links in email
        return $this->subject('New Place Order - #' . $this->order->order_number)
                    ->replyTo($this->order->email, $this->order->full_name)
                    ->view('emails.place-order-admin')
                    ->with(['order' => $this->order]);
    }
}
