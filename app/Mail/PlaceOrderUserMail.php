<?php

namespace App\Mail;

use App\Models\PlaceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlaceOrderUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public PlaceOrder $order;

    public function __construct(PlaceOrder $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Your Order Has Been Placed - #' . $this->order->order_number)
                    ->view('emails.place-order-user')
                    ->with(['order' => $this->order]);
    }
}
