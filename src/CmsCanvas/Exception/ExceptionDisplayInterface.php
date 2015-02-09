<?php namespace CmsCanvas\Exception;

interface ExceptionDisplayInterface
{

    /**
     * Returns a heading for the exception.
     *
     * @return string
     */
    public function getHeading();

    /**
     * A view of the error display
     *
     * @return \Illuminate\View\View
     */
    public function getView();

    /**
     * Method to set the heading property
     *
     * @param string $heading
     * @return void
     */
    public function setHeading($heading);

}
