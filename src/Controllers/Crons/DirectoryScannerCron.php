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

    /**
     * Event hook
     *
     * @var string
     */
    const EVENT_HOOK = 'dup_challenge_directory_scan';

    /**
     * Option name for cron interval
     *
     * @var string
     */
    const OPTION_CRON_INTERVAL = 'dup_challenge_cron_interval';


    /**
     * Recurring interval
     *
     * @var string
     */
    const CRON_RECURRING_INTERVAL = 'every_x_hours';

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function schedule()
    {
        if ($this->isCronEnabled()) {
            $this->unschedule();
        }

        $timestamp = time() + $this->getCronInterval() * HOUR_IN_SECONDS;
        wp_schedule_event($timestamp, self::CRON_RECURRING_INTERVAL, self::EVENT_HOOK);
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

    /**
     * Get cron interval
     *
     * @return string
     */
    public function getCronInterval()
    {
        return get_option(self::OPTION_CRON_INTERVAL, 1);
    }

    /**
     * Get cron enabled
     *
     * @return bool
     */
    public function isCronEnabled()
    {
        return wp_next_scheduled(self::EVENT_HOOK) !== false;
    }

    /**
     * Set cron interval
     *
     * @param string $interval Interval
     *
     * @return void
     */
    public function setCronInterval($interval)
    {
        if (!is_numeric($interval) || $interval < 1) {
            $interval = 1;
        }

        update_option(self::OPTION_CRON_INTERVAL, $interval);
    }

    /**
     * Add recurring interval
     *
     * @param array<string, mixed> $schedule Schedule
     *
     * @return array<string, string> Schedule
     */
    public function addRecurringInterval($schedule)
    {
        $interval = self::getCronInterval();

        $schedule[self::CRON_RECURRING_INTERVAL] = [
            'interval' => $interval * HOUR_IN_SECONDS,
            'display' => sprintf(__('Every %d hours', 'dup-challenge'), $interval),
        ];

        return $schedule;
    }

    /**
     * Delete cron options
     *
     * @return void
     */
    public function deleteCronOptions()
    {
        delete_option(self::OPTION_CRON_INTERVAL);
    }
}
