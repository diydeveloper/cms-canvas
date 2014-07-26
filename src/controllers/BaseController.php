<?php namespace CmsCanvas\Controllers;

use Theme, Controller;

class BaseController extends Controller {

    /**
     * Setup the theme and layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (isset($this->theme) && ! is_null($this->theme))
        {
            Theme::setTheme($this->theme);
        }
        else
        {
            Theme::setTheme(Theme::getDefaultTheme());
        }

        if (isset($this->layout) && ! is_null($this->layout))
        {
            Theme::setLayout($this->layout);
        }
        else
        {
            Theme::setLayout(Theme::getDefaultLayout());
        }

        $this->layout = Theme::getLayout();
    }

}
