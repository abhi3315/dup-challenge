<?php

namespace DupChallenge\Interfaces;

/**
 * Cron interface
 */
interface CronInterface
{
    /**
     * Schedule cron
     *
     * @return void
     */
    public function schedule();

    /**
     * Unschedule cron
     *
     * @return void
     */
    public function unschedule();

    /**
     * Hook callback
     *
     * @return void
     */
    public function hookCallback();
}
