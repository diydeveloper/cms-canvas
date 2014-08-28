<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content_plugin extends Plugin
{
    /*
     * Entry ID
     *
     * Returns ID for a specified entry id
     *
     * @return int
     */
    public function entry_id()
    {
        return $this->attribute('entry_id');
    }

    // ------------------------------------------------------------------------
    
    /*
     * Entry URL
     *
     * Returns URL for a specified entry id
     *
     * @return string
     */
    public function entry_url()
    {
        return entry_url($this->attribute('entry_id'));
    }

    // ------------------------------------------------------------------------

    /*
     * Entry Name
     *
     * Returns entry title for a specified entry id
     *
     * @return string
     */
    public function entry_name()
    {
        return entry_name($this->attribute('entry_id'));
    }

    // ------------------------------------------------------------------------

    /*
     * Entries
     *
     * Queries and returns categories based on passed attributes
     *
     * @return array
     */
    public function entries()
    {
        $attributes = $this->attributes(); 
        $attributes['_content'] = $this->content();

        $data = entries($attributes);

        return $data;
    }

    // ------------------------------------------------------------------------

    /*
     * Is Home
     *
     * Checks if the current page matches that of the selected hompage
     *
     * @return bool
     */
    public function is_home()
    {
        return is_home();
    }

    // ------------------------------------------------------------------------

    /*
     * Categories
     *
     * Queries and returns categories based on passed attributes
     *
     * @return string
     */
    public function categories()
    {
        $attributes = $this->attributes();
        $attributes['_content'] = $this->content();

        return categories($attributes);
    }

    // ------------------------------------------------------------------------

    /*
     * Archive
     *
     * Queries month year combinations for content type provided
     *
     * @return array
     */
    public function archive()
    {
        $attributes = $this->attributes();
        $attributes['_content'] = $this->content();

        return archive($attributes);
    }

    // ------------------------------------------------------------------------

    /*
     * Snippet
     *
     * Returns resusable code snippets
     *
     * @return array
     */
    public function snippets($data)
    {
        $attributes = $this->attributes();
        return snippets($attributes, $data);
    }
}

