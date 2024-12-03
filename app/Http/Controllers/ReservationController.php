<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\{Payments, Reservations, Rooms};
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'client_telegram_id' => 'required',
            'client_name' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $room = Rooms::findOrFail($validated['room_id']);

            if (!$room->isAvailableForDates($validated['check_in'], $validated['check_out'])) {
                throw new \Exception('Room is not available for selected dates');
            }

            // Toplam fiyat hesaplama
            $checkIn = Carbon::parse($validated['check_in']);
            $checkOut = Carbon::parse($validated['check_out']);
            $nights = $checkIn->diffInDays($checkOut);
            $totalPrice = $room->price * $nights;

            // Rezervasyon oluştur
            $reservation = Reservations::create([
                'room_id' => $validated['room_id'],
                'client_telegram_id' => $validated['client_telegram_id'],
                'client_name' => $validated['client_name'],
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'guest_count' => $validated['guests'],
                'total_price' => $totalPrice,
                'status' => 'pending'
            ]);

            // Ödeme kaydı oluştur
            $payment = Payments::create([
                'reservation_id' => $reservation->id,
                'amount' => $totalPrice,
                'status' => 'pending'
            ]);

            // Payment history oluştur
            $payment->addHistory('Reservation created');

            DB::commit();

            return response()->json([
                'success' => true,
                'reservation' => $reservation,
                'payment_url' => route('payments.show', $payment->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function myReservations($clientTelegramId)
    {
        $reservations = Reservations::with(['room.hotel', 'payment'])
            ->where('client_telegram_id', $clientTelegramId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reservations);
    }
}
