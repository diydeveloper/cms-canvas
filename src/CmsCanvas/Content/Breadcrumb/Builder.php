<?php 

namespace CmsCanvas\Content\Breadcrumb;

use Config;
use CmsCanvas\Content\Navigation\Builder as NavigationBuilder;
use CmsCanvas\Models\Content\Navigation\Item;
use CmsCanvas\Content\Navigation\Item\RenderCollection;

class Builder {

    /**
     * @var string
     */
    protected $shortName;

    /**
     * @var \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected $breadcrumbs;

    /**
     * @var string
     */
    protected $seperator;

    /**
     * @var bool
     */
    protected $hideSingleFlag = false;

    /**
     * @var bool
     */
    protected $includeHomeFlag = false;

    /**
     * @var string
     */
    protected $ulId;

    /**
     * @var string
     */
    protected $ulClass;

    /**
     * Constructor
     *
     * @param string $shortName
     * @param array $config
     * @return void
     */
    public function __construct($shortName, array $config = [])
    {
        $this->setShortName($shortName);

        $this->buildFromArray($config);
    }

    /**
     * Construct the object from an array
     *
     * @param array $config
     * @return void
     */
    protected function buildFromArray(array $config)
    {
        foreach ($config as $key => $value) {
            switch ($key) {
                case 'seperator':
                    $this->setSeperator($value);
                    break;

                case 'hide_single':
                    $this->setHideSingle($value);
                    break;

                case 'include_home':
                    $this->setIncludeHome($value);
                    break;

                case 'ul_id':
                    $this->setUlId($value);
                    break;

                case 'ul_class':
                    $this->setUlClass($value);
                    break;
            }
        } 
    }

    /**
     * Sets the navigation short name to build from
     *
     * @param string $shortName
     * @return self
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName; 

        return $this;
    }

    /**
     * Sets the seperator to be used between breadcrumbs
     *
     * @param string $seperator
     * @return self
     */
    public function setSeperator($seperator)
    {
        $this->seperator = $seperator; 

        return $this;
    }

    /**
     * Sets the hideSingleFlag property
     *
     * @param  bool $hideSingleFlag
     * @return self
     */
    public function setHideSingle($hideSingleFlag)
    {
        $this->hideSingleFlag = (bool) $hideSingleFlag; 

        return $this;
    }

    /**
     * Sets the includeHomeFlag property
     *
     * @param  bool $includeHomeFlag
     * @return self
     */
    public function setIncludeHome($includeHomeFlag)
    {
        $this->includeHomeFlag = (bool) $includeHomeFlag; 

        return $this;
    }

    /**
     * Sets the id attribute string for the <ul> tag
     *
     * @param  string $id
     * @return self
     */
    public function setUlId($id)
    {
        $this->ulId = $id; 

        return $this;
    }

    /**
     * Sets the class attribute string for the <ul> tag
     *
     * @param  string $class
     * @return self
     */
    public function setUlClass($class)
    {
        $this->ulClass = $class; 

        return $this;
    }

    /**
     * Returns collection of breadcrumbs
     *
     * @return \CmsCanvas\Content\Breadcrumb\Item\RenderCollection
     */
    public function get()
    {
        return new RenderCollection($this->getBreadcrumbs(), $this);
    }

    /**
     * Returns array of breadcrumbs
     *
     * @return \CmsCanvas\Content\Navigation\Builder\Item|array
     */
    public function getBreadcrumbs()
    {
        $this->compile();

        return $this->breadcrumbs;
    }

    /**
     * Compile the breadcrumbs from the current object
     *
     * @return void
     */
    protected function compile()
    {
        $navigation = new NavigationBuilder($this->shortName);
        $navigationTree = $navigation->getNavigationTree();

        $item = $this->getFirstCurrentItem($navigationTree);

        $breadcrumbs = [];
        while ($item != null) {
            array_unshift($breadcrumbs, $item);
            $item = $item->getParent();
        };

        if ($this->includeHomeFlag) {
            $firstBreadcrumb = current($breadcrumbs);
            if ($firstBreadcrumb != null && ! $firstBreadcrumb->isHomePage()) {
                $homePageItem = $this->getHomePageItem($navigationTree);

                if ($homePageItem != null) {
                    array_unshift($breadcrumbs, $homePageItem);
                }
            }
        }

        if ($this->hideSingleFlag && count($breadcrumbs) <= 1) {
            $breadcrumbs = [];
        }

        $this->breadcrumbs = $breadcrumbs;

        $this->compileIndexes();
        $this->compileSeperator();
    }

    /**
     * Reindex breadcrumb builder items
     *
     * @return void
     */
    protected function compileIndexes()
    {
        $counter = 1;
        $breadcrumbCount = count($this->breadcrumbs);

        foreach ($this->breadcrumbs as $breadcrumb) {
            $breadcrumb->unsetParent()
                ->unsetChildren()
                ->setCurrentItemAncestorFlag(false)
                ->setIndex($counter - 1);

            $breadcrumb->setFirstFlag(($counter === 1));
            $breadcrumb->setLastFlag(($counter === $breadcrumbCount));
            $counter++;
        }
    }

    /**
     * Compile the seperator for breadcrumb items
     *
     * @return void
     */
    protected function compileSeperator()
    {
        foreach ($this->breadcrumbs as $breadcrumb) {
            if (! $breadcrumb->getLastFlag()) {
                $breadcrumb->setSeperator($this->seperator);
            }
        }
    }

    /**
     * Returns the first current navigation item found in the navigation tree
     *
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     * @param int
     * @return \CmsCanvas\Content\Navigation\Builder\Item
     */
    protected function getFirstCurrentItem($items, $depth = 1)
    {
        foreach ($items as $item) {
            if ($item->isCurrentItem()) {
                return $item;
            }

            if (count($item->getChildren()) > 0) {
                $currentItem = $this->getFirstCurrentItem($item->getChildren(), $depth + 1);

                if ($currentItem != null) {
                    return $currentItem;
                }
            }
        }

        return null;
    }

    /**
     * Returns the first home page item found in the navigation tree
     *
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     * @param int
     * @return \CmsCanvas\Content\Navigation\Builder\Item
     */
    protected function getHomePageItem($items, $depth = 1)
    {
        foreach ($items as $item) {
            if ($item->isHomePage()) {
                return $item;
            }

            if (count($item->getChildren()) > 0) {
                $homePageItem = $this->getHomePageItem($item->getChildren(), $depth + 1);

                if ($homePageItem != null) {
                    return $homePageItem;
                }
            }
        }

        return null;
    }

    /**
     * Builds html attributes string for the <ul> tag
     *
     * @return string
     */
    public function getAttributes()
    {
        $attributes = '';

        if (! empty($this->ulId)) {
            $attributes .= ' id="'.$this->ulId.'"';
        }

        if (! empty($this->ulClass)) {
            $attributes .= ' class="'.$this->ulClass.'"';
        }

        return $attributes;
    }

}