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
        <div class="wrap" id="dup-challenge-main-page"></div>
        <?php

        submit_button('Start Scan', 'primary', 'start-scan', false);
    }
}