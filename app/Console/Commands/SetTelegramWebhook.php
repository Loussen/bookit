<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class SetTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Telegram webhook URL';

    /**
     * Execute the console command.
     * @throws TelegramSDKException
     */
    public function handle()
    {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));

        $result = $telegram->setWebhook([
            'url' => config('app.url') . '/api/webhook'
        ]);

        if ($result) {
            $this->info('Webhook URL set successfully!');
        } else {
            $this->error('Failed to set webhook URL');
        }
    }
}
