<?php

namespace DupChallenge;

/**
 * Install class
 */
class Install
{
    /**
     * Register install hoosk
     *
     * @return void
     */
    public static function register()
    {
        if (is_admin()) {
            register_activation_hook(DUP_CHALLENGE_FILE, array(__CLASS__, 'onActivation'));
        }
    }

    /**
     * Install plugin
     *
     * @return void
     */
    public static function onActivation()
    {
    }
}
