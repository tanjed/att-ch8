<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserActionSetting;
use App\Jobs\ProcessAutomatedActionJob;
use App\Models\ActionLog;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ProcessAutomatedActions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-automated-actions {action_id?}';
    protected $description = 'Process automated HR platform actions for the current minute';

    public function handle()
    {
        if ($actionId = $this->argument('action_id')) {
            $setting = UserActionSetting::find($actionId);
            if ($setting && $setting->is_active) {
                // Dispatch directly to queue if triggered manually via action_id
                ProcessAutomatedActionJob::dispatch($setting->id);
                $this->info("Dispatched job for action ID: {$setting->id}");
            } else {
                $this->error("Action ID {$actionId} not found or inactive.");
            }
            return;
        }

        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        $this->info("Checking for actions at or before: {$currentTime}");

        // Find active settings matching the current time or earlier 
        // to ensure no actions get missed if cron drops for a few minutes
        $settings = UserActionSetting::where('is_active', true)
            ->where('target_time', '<=', $currentTime)
            ->get();

        if ($settings->isEmpty()) {
            $this->info("No actions scheduled for this time or earlier.");
            return;
        }

        $dispatchedCount = 0;

        foreach ($settings as $setting) {
            // Atomic cache lock to ensure the same action doesn't get hit twice 
            // per day even if target_time <= currentTime continues to match
            $cacheKey = "action_executed_{$setting->id}_{$now->format('Y-m-d')}";

            if (Cache::add($cacheKey, true, $now->copy()->endOfDay())) {
                // Fallback: Check the database to make sure it wasn't already executed today (in case Cache was flushed/failed)
                $alreadyExecuted = ActionLog::where('user_id', $setting->user_id)
                    ->where('platform_action_id', $setting->platform_action_id)
                    ->whereDate('executed_at', $now->format('Y-m-d'))
                    ->exists();

                if (!$alreadyExecuted) {
                    ProcessAutomatedActionJob::dispatch($setting->id);
                    $this->info("Queued background job for setting ID: {$setting->id}");
                    $dispatchedCount++;
                } else {
                    $this->info("Skipped background job for setting ID: {$setting->id} (Found in DB Fallback)");
                }
            }
        }

        if ($dispatchedCount === 0) {
            $this->info("All matching actions have already been executed today.");
        } else {
            $this->info("Successfully dispatched {$dispatchedCount} actions.");
        }
    }
}
