<?php
// app/Http/Controllers/TelegramBotController.php
namespace App\Http\Controllers;

use App\Models\Hotels;
use App\Models\Reservations;
use App\Models\Rooms;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\Cache;

class TelegramBotController extends Controller
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function handleWebhook()
    {
        $update = $this->telegram->getWebhookUpdate();

        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->handleCallback($update['callback_query']);
        }
    }

    private function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        $bookingState = Cache::get("booking_state_{$chatId}");

        if ($bookingState) {
            return $this->handleBookingState($chatId, $text, $bookingState);
        }

        switch ($text) {
            case '/start':
                $this->showMainMenu($chatId);
                break;
            case '🏨 Oteller Listesi':
                $this->showHotels($chatId);
                break;
//            case '📝 Rezervasyonlarım':
//                $this->showMyReservations($chatId);
//                break;
//            case '📞 İletişim':
//                $this->showContact($chatId);
//                break;
        }
    }

    private function showMainMenu($chatId)
    {
        $keyboard = [
            ['🏨 Oteller Listesi'],
            ['📝 Rezervasyonlarım'],
            ['📞 İletişim']
        ];

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "🏨 *OTEL REZERVASYON*\n\nHoş geldiniz!\nLütfen bir seçenek seçin:",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'resize_keyboard' => true
            ])
        ]);
    }

    private function handleCallback($callback)
    {
        $chatId = $callback['message']['chat']['id'];
        $data = $callback['data'];

        list($action, $id) = explode(':', $data);

        switch ($action) {
            case 'hotel':
                $this->showHotelDetails($chatId, $id);
                break;
            case 'rooms':
                $this->showHotelRooms($chatId, $id);
                break;
            case 'room':
                $this->showRoomDetails($chatId, $id);
                break;
            case 'book':
                $this->startBooking($chatId, $id);
                break;
            case 'confirm':
                $this->confirmBooking($chatId, $id);
                break;
        }
    }

    private function showHotels($chatId)
    {
        $hotels = Hotels::all();
        $buttons = [];

        foreach ($hotels as $hotel) {
            $buttons[] = [[
                'text' => "🏨 {$hotel->name}",
                'callback_data' => "hotel:{$hotel->id}"
            ]];
        }

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "📋 *Mevcut Otellerimiz*\n\nDetayları görmek için bir otel seçin:",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ])
        ]);
    }

    private function showHotelDetails($chatId, $hotelId)
    {
        $hotel = Hotels::findOrFail($hotelId);

        // Otel fotoğrafını gönder
        if ($hotel->photo_url) {
            $this->telegram->sendPhoto([
                'chat_id' => $chatId,
                'photo' => $hotel->photo_url,
                'caption' => "*{$hotel->name}*",
                'parse_mode' => 'Markdown'
            ]);
        }

        $buttons = [
            [
                ['text' => '🛏 Odalar', 'callback_data' => "rooms:{$hotelId}"],
                ['text' => '📍 Konum', 'callback_data' => "location:{$hotelId}"]
            ],
            [
                ['text' => '« Ana Menü', 'callback_data' => "main_menu"]
            ]
        ];

        $text = "*{$hotel->name}*\n\n";
        $text .= "📍 *Adres:* {$hotel->address}\n";
        $text .= "⭐️ *Rating:* {$hotel->rating}\n";
        $text .= "💰 *Fiyat Aralığı:* {$hotel->price_range}\n\n";
        $text .= $hotel->description;

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ])
        ]);
    }

    private function startBooking($chatId, $roomId)
    {
        Cache::put("booking_state_{$chatId}", [
            'step' => 'name',
            'room_id' => $roomId
        ], 3600);

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "Rezervasyon işlemini başlatıyoruz!\n\nLütfen adınızı ve soyadınızı yazın:",
            'reply_markup' => json_encode([
                'force_reply' => true
            ])
        ]);
    }

    private function handleBookingState($chatId, $text, $state): void
    {
        switch ($state['step']) {
            case 'name':
                Cache::put("booking_state_{$chatId}", [
                    'step' => 'phone',
                    'room_id' => $state['room_id'],
                    'name' => $text
                ], 3600);

                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Teşekkürler! Şimdi lütfen telefon numaranızı yazın:",
                ]);
                break;

            case 'phone':
                Cache::put("booking_state_{$chatId}", [
                    'step' => 'dates',
                    'room_id' => $state['room_id'],
                    'name' => $state['name'],
                    'phone' => $text
                ], 3600);

                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Harika! Son olarak, check-in tarihini GG.AA.YYYY formatında yazın:",
                ]);
                break;

            case 'dates':
                // Rezervasyonu kaydet ve admin'e bildir
                $room = Rooms::find($state['room_id']);

                $reservation = Reservations::create([
                    'room_id' => $state['room_id'],
                    'client_telegram_id' => $chatId,
                    'client_name' => $state['name'],
                    'client_phone' => $state['phone'],
                    'check_in' => $text,
                    'status' => 'pending'
                ]);

                Cache::forget("booking_state_{$chatId}");

                // Admin'e bildir
                $adminId = env('TELEGRAM_ADMIN_CHAT_ID');
                $this->notifyAdmin($adminId, $reservation);

                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "✅ Rezervasyon talebiniz alındı!\n\nOtel yetkilisi onayladıktan sonra size bilgi vereceğiz."
                ]);
                break;
        }
    }

    private function notifyAdmin($adminId, $reservation)
    {
        $room = $reservation->room;
        $hotel = $room->hotel;

        $text = "🆕 *YENİ REZERVASYON TALEBİ*\n\n";
        $text .= "🏨 *Otel:* {$hotel->name}\n";
        $text .= "🛏 *Oda:* {$room->name}\n";
        $text .= "👤 *Müşteri:* {$reservation->client_name}\n";
        $text .= "📞 *Telefon:* {$reservation->client_phone}\n";
        $text .= "📅 *Giriş:* {$reservation->check_in}\n";

        $buttons = [
            [
                ['text' => '✅ Onayla', 'callback_data' => "approve:{$reservation->id}"],
                ['text' => '❌ Reddet', 'callback_data' => "reject:{$reservation->id}"]
            ]
        ];

        $this->telegram->sendMessage([
            'chat_id' => $adminId,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ])
        ]);
    }
}
