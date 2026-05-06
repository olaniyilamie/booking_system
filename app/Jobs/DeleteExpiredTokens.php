<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;

class DeleteExpiredTokens implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public function backoff()
    {
        return [10, 30, 60, 120];
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            PersonalAccessToken::whereNotNull('expires_at')
                        ->where('expires_at', '<=', now())
                        ->chunkById(1000, function ($tokens) {
                            foreach ($tokens as $token) {
                                $token->delete();
                            }
                        });

            Log::info("DeleteExpiredTokens: deleted expired access tokens.");
        } catch (\Throwable $e) {
            Log::error('DeleteExpiredTokens failed: '.$e->getMessage());
            throw $e;
        }
    }
}
