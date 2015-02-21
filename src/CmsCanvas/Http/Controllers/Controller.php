<?php namespace CmsCanvas\Http\Controllers;

use Theme;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Controller extends BaseController {

    use DispatchesCommands, ValidatesRequests;

    /**
     * Name of the theme to use
     *
     * @var string 
     */
    protected $themeName;

    /**
     * Name of the theme's layout to use
     *
     * @var string 
     */
    protected $layoutName;

    /**
     * Setup the theme and layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if ($this->themeName !== null)
        {
            Theme::setTheme($this->themeName);
        }
        else
        {
            Theme::setTheme(Theme::getDefaultTheme());
        }

        if ($this->layoutName !== null)
        {
            Theme::setLayout($this->layoutName);
        }
        else
        {
            Theme::setLayout(Theme::getDefaultLayout());
        }

        $this->layout = Theme::getLayout();
    }

    /**
     * Execute an action on the controller.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        $this->setupLayout();

        $response = call_user_func_array(array($this, $method), $parameters);

        // If no response is returned from the controller action and a layout is being
        // used we will assume we want to just return the layout view as any nested
        // views were probably bound on this view during this controller actions.
        if (is_null($response) && ! is_null($this->layout))
        {
            $response = $this->layout;
        }

        return $response;
    }

}
