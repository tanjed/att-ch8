<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class QueuedVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        // Force HTTPS in production, HTTP otherwise
        if (app()->environment('production')) {
            URL::forceScheme('https');
        } else {
            URL::forceScheme('http');
        }

        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Debug logging
        Log::info('===== VERIFICATION URL GENERATED =====', [
            'app_env' => app()->environment(),
            'app_url' => config('app.url'),
            'generated_url' => $url,
            'url_scheme' => parse_url($url, PHP_URL_SCHEME),
            'url_host' => parse_url($url, PHP_URL_HOST),
            'url_path' => parse_url($url, PHP_URL_PATH),
            'url_query' => parse_url($url, PHP_URL_QUERY),
        ]);

        return $url;
    }
}
