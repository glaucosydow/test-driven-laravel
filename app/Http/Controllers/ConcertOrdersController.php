<?php

namespace App\Http\Controllers;

use App\Billing\NotEnoughTicketsException;
use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Order;
use App\Reservation;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $ticketQuantity = request('ticket_quantity');
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(
            request(),
            [
                'email' => ['required', 'email'],
                'ticket_quantity' => ['required', 'integer', 'min:1'],
                'payment_token' => ['required'],
            ]
        );

        try {
            $reservation = $concert->reserveTickets($ticketQuantity, request('email'));
            $order = $reservation->complete($this->paymentGateway, request('payment_token'));

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            $reservation->cancel();

            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            $this->paymentGateway->chargeBack($ticketQuantity * $concert->ticket_price, request('payment_token'));

            return response()->json([], 422);
        }
    }
}
