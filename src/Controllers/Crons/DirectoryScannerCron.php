<?php

namespace DupChallenge\Controllers\Crons;

use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\CronInterface;
use DupChallenge\Controllers\DirectoryScannerController;

/**
 * Directory scanner cron
 */
class DirectoryScannerCron implements CronInterface
{
    use SingletonTrait;

    const EVENT_HOOK = 'dup_challenge_directory_scan';

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function schedule()
    {
        if (!wp_next_scheduled(self::EVENT_HOOK)) {
            wp_schedule_event(time(), 'hourly', self::EVENT_HOOK);
        }
    }

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function unschedule()
    {
        wp_clear_scheduled_hook(self::EVENT_HOOK);
    }

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function hookCallback()
    {
        DirectoryScannerController::getInstance()->startScanJob();
    }
}
