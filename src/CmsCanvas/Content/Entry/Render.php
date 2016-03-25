<?php 

namespace CmsCanvas\Content\Entry;

use CmsCanvas\Content\Entry\Builder\Entry as EntryBuilder;

class Render {

    /**
     * The entry to render from
     *
     * @var \CmsCanvas\Content\Entry\Builder\Entry
     */
    protected $entryBuilder;

    /**
     * Constructor fo rthe entry render
     *
     * @param  \CmsCanvas\Content\Entry\Builder\Entry  $entryBuilder
     * @return void
     */
    public function __construct(EntryBuilder $entryBuilder)
    {
        $this->entryBuilder = $entryBuilder;
    }

    /**
     * Magic method to retrive rendered data
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->entryBuilder->getData($name);
    }

    /**
     * Magic method to trigger twig to call __get
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        return true;
    }

    /**
     * Magic method to render the entry as a string
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return (string) $this->entryBuilder->renderContents();
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Reutrns the firstFlag class property
     *
     * @return bool
     */
    public function isFirst()
    {
        return $this->entryBuilder->getFirstFlag();
    }

    /**
     * Reutrns the lastFlag class property
     *
     * @return bool
     */
    public function isLast()
    {
        return $this->entryBuilder->getLastFlag();
    }

    /**
     * Reutrns the index class property
     *
     * @return int
     */
    public function index()
    {
        return $this->entryBuilder->getIndex();
    }

    /**
     * Returns the full route for the entry
     *
     * @return string
     */
    public function route()
    {
        return $this->entryBuilder->getEntry()->getRoute();
    }

    /**
     * Returns the url for the current entry
     *
     * @return string
     */
    public function url()
    {
        return $this->entryBuilder->getEntry()->getUrl();
    }

    /**
     * Returns the uri for the current entry
     *
     * @return string
     */
    public function uri()
    {
        return $this->entryBuilder->getEntry()->getUri();
    }

    /**
     * Used to determine if the current render is an entry
     *
     * @return boolean
     */
    public function isEntry()
    {
        return true;
    }

    /**
     * Returns the author of the entry
     *
     * @return boolean
     */
    public function getAuthor()
    {
        $author = $this->entryBuilder->getEntry()->author;

        if ($author != null) {
            return $author->render();
        }

        return '';
    }

    /**
     * Set the metadata for the current entry to the theme
     *
     * @return void
     */
    public function setThemeMetadata()
    {
        $this->entryBuilder->getEntry()->setThemeMetadata();
    }

}