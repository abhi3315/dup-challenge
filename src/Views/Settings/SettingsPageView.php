<?php

namespace DupChallenge\Views\Settings ;

class SettingsPageView
{
    /**
     * Render main page
     *
     * @return void
     */
    public static function renderMainPage()
    {
        ?>
        <div class="wrap">
            <h1>
                <?php echo esc_html(get_admin_page_title()); ?>
            </h1>

            <p>
                <?php _e('Here is the settings page', 'dup-challenge'); ?>
            </p>
        </div>
        <?php
    }
}