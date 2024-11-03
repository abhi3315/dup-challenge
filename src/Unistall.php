<?php

namespace DupChallenge;

/**
 * Uninstall class
 */
class Unistall
{
    /**
     * Register unistall hoosk
     *
     * @return void
     */
    public static function register()
    {
        if (is_admin()) {
            register_deactivation_hook(DUP_CHALLENGE_FILE, array(__CLASS__, 'deactivate'));
        }
    }

    /**
     * Deactivation Hook
     *
     * @return void
     */
    public static function deactivate()
    {
    }
}
