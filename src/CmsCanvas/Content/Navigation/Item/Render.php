<?php 

namespace CmsCanvas\Content\Navigation\Item;

use CmsCanvas\Content\Navigation\Builder\Item as ItemBuilder;
use CmsCanvas\Support\Contracts\View\Render as ViewRender;

class Render implements ViewRender {

    /**
     * The navigation item builder to render from
     *
     * @var \CmsCanvas\Content\Navigation\Builder\Item
     */
    protected $itemBuilder;

    /**
     * Constructor of the navigation item render
     *
     * @param  \CmsCanvas\Content\Navigation\Builder\Item  $itemBuilder
     * @return void
     */
    public function __construct(ItemBuilder $itemBuilder)
    {
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * Magic method to retrive rendered data
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'title':
                return $this->title();
                break;
        }
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
     * Reutrns the firstFlag class property
     *
     * @return bool
     */
    public function isFirst()
    {
        return $this->itemBuilder->getFirstFlag();
    }

    /**
     * Reutrns the lastFlag class property
     *
     * @return bool
     */
    public function isLast()
    {
        return $this->itemBuilder->getLastFlag();
    }

    /**
     * Reutrns the index class property
     *
     * @return int
     */
    public function index()
    {
        return $this->itemBuilder->getIndex();
    }

    /**
     * Reutrns the depth class property
     *
     * @return int
     */
    public function depth()
    {
        return $this->itemBuilder->getDepth();
    }

    /**
     * HTML attributes string for the <li> tag
     *
     * @return string
     */
    public function listItemAttributes()
    {
        return $this->itemBuilder->getListItemAttributes();
    }

    /**
     * HTML attributes string for the <a> tag
     *
     * @return string
     */
    public function anchorAttributes()
    {
        return $this->itemBuilder->getAnchorAttributes();
    }

    /**
     * Returns the url for the current navigation item
     *
     * @return string
     */
    public function url()
    {
        return $this->itemBuilder->getUrl();
    }

    /**
     * Returns the title for the current navigation item
     *
     * @return string
     */
    public function title()
    {
        return $this->itemBuilder->getTitle();
    }

    /**
     * Returns the children for the current navigation item
     *
     * @return string
     */
    public function children()
    {
        return $this->itemBuilder->renderChildren();
    }

    /**
     * Returns the seperator for the current navigation item
     *
     * @return string
     */
    public function seperator()
    {
        return $this->itemBuilder->getSeperator();
    }

    /**
     * Magic method to render the navigation item as a string
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return (string) $this->itemBuilder->renderContents();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Returns the resource type for this render
     *
     * @return string
     */
    public function getResourceType()
    {
        return 'NAVIGATION_ITEM';
    }

}