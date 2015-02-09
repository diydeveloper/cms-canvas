<?php namespace CmsCanvas\Exception;

use Theme, View;

class Exception extends \RuntimeException implements ExceptionDisplayInterface {

    /**
     * @var string
     */
    protected $heading = 'Error Encountered';

    /**
     * Returns the heading property
     *
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     * @return void
     */
    public function __construct($message, $code = 0, Exception $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the heading property
     *
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Returns a view of the error display
     *
     * @return \Illuminate\View\View
     */
    public function getView()
    {
        Theme::setTheme('admin');
        Theme::setLayout('default');
        Theme::addPackage(array('jquery', 'jquerytools', 'admin_jqueryui'));
        $layout = Theme::getLayout();

        $layout->content = View::make('cmscanvas::admin.error')
            ->with('exception', $this);

        return $layout;
    }

    /**
     * Method to set the heading property
     *
     * @param string $heading
     * @return void
     */
    public function setHeading($heading)
    {
        return $this->heading = $heading;
    }

}