<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\{Payments, RoomUnavailableDates};
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function show(Payments $payment)
    {
        $payment->load('reservation.room.hotel');
        return view('pages.payments.show', compact('payment'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'card_number' => 'required|string|size:16',
            'expiry' => 'required|string|size:5',
            'cvv' => 'required|string|size:3',
            'card_name' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $payment = Payments::with('reservation')->findOrFail($validated['payment_id']);
            $reservation = $payment->reservation;

            // Burada gerçek ödeme işlemi yapılacak
            // Şimdilik örnek olarak başarılı kabul ediyoruz

            // Payment durumunu güncelle
            $payment->updateStatus('completed', 'Payment successful', [
                'card_last4' => substr($validated['card_number'], -4)
            ]);

            // Rezervasyon durumunu güncelle
            $reservation->update(['status' => 'paid']);

            // Tarihleri unavailable olarak işaretle
            $dates = $this->generateDateRange(
                $reservation->check_in,
                $reservation->check_out
            );

            foreach ($dates as $date) {
                RoomUnavailableDates::create([
                    'room_id' => $reservation->room_id,
                    'reservation_id' => $reservation->id,
                    'date' => $date
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment successful'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Payment history'ye hatayı kaydet
            if (isset($payment)) {
                $payment->updateStatus('failed', $e->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    private function generateDateRange($start, $end)
    {
        $dates = [];
        $current = Carbon::parse($start);
        $end = Carbon::parse($end);

        while ($current <= $end) {
            $dates[] = $current->copy()->format('Y-m-d');
            $current->addDay();
        }

        return $dates;
    }
}
