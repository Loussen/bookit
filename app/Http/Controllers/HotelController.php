<?php
namespace App\Http\Controllers;

use App\Models\Hotels;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    // Ana sayfa - otel listesi
    public function index()
    {
        $hotels = Hotels::with(['rooms' => function($query) {
            $query->where('is_available', true);
        }])->get();

        return view('pages.hotels.index', compact('hotels'));
    }

    // Otel detay sayfası
    public function show(Hotels $hotel)
    {
        $hotel->load(['rooms.photos']);

        return view('pages.hotels.show', compact('hotel'));
    }

    // Otel müsaitlik kontrolü
    public function checkAvailability(Request $request, Hotels $hotel)
    {
        $request->validate([
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1'
        ]);

        $availableRooms = $hotel->rooms()->where('capacity', '>=', $request->guests)
            ->get()
            ->filter(function($room) use ($request) {
                return $room->isAvailableForDates($request->check_in, $request->check_out);
            });

        return response()->json([
            'available' => $availableRooms->isNotEmpty(),
            'rooms' => $availableRooms
        ]);
    }
}
