<?php

namespace DupChallenge\Views\Main ;

class MainPageView
{
    /**
     * Render main page
     *
     * @return void
     */
    public static function renderMainPage()
    {
        ?>
        <form method="get" action="">
            <div class="wrap">
                <h1>
        <?php echo esc_html(get_admin_page_title()); ?>
                </h1>

                <input type="checkbox" name="start-scan" value="1" />
                
                <p>
        <?php _e('Here is the main page', 'dup-challenge'); ?>
                </p>
            </div>
        <?php

        submit_button('Scan Directory', 'primary', 'submit', true);

        ?>

        </form>

        <?php
    }
}