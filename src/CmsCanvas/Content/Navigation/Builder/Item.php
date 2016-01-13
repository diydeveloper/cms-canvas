<?php 

namespace CmsCanvas\Content\Navigation\Builder;

use CmsCanvas\Models\Content\Navigation\Item as NavigationItem;
use CmsCanvas\Content\Navigation\Item\Render;
use CmsCanvas\Content\Navigation\Item\RenderCollection;

class Item {

    /**
     * @var \CmsCanvas\Models\Content\Navigation\Item
     */
    protected $navigationItem;

    /**
     * @var \CmsCanvas\Content\Navigation\Builder\Item
     */
    protected $parent;

    /**
     * @var \CmsCanvas\Content\Navigation\Builder\Item|array
     */
    protected $children = [];

    /**
     * Set true if the item links to the current url
     *
     * @var bool
     */
    protected $currentItemFlag = false;

    /**
     * Set true if the item is an ancestor of the current item 
     *
     * @var bool
     */
    protected $currentItemAncestorFlag = false;

    /**
     * Set true if the item is solo or first in a collection
     *
     * @var bool
     */
    protected $firstFlag = true;

    /**
     * Set true if the item is solo or last in a collection
     *
     * @var bool
     */
    protected $lastFlag = true;

    /**
     * The position the item is in a collection
     *
     * @var bool
     */
    protected $index = 0;

    /**
     * The seperator for the current item
     *
     * @var string
     */
    protected $seperator;

    /**
     * Constructor
     *
     * @param  \CmsCanvas\Models\Content\Navigation\Item $navigationItem
     * @param  \CmsCanvas\Content\Navigation\Builder\Item $parent
     * @return void
     */
    public function __construct(NavigationItem $navigationItem, Item $parent = null)
    {
        $this->navigationItem = $navigationItem;

        $this->parent = $parent;
        $this->buildChildren();
    }

    /**
     * Recursively builds loaded navigation item children
     *
     * @param void
     */
    protected function buildChildren()
    {
        $items = $this->navigationItem->getLoadedChildren();
        $this->children = NavigationItem::newItemBuilderCollection($items, $this);
    }

    /**
     * Returns a render instance
     *
     * @return \CmsCanvas\Content\Navigation\Item\Render
     */
    public function render()
    {
        return new Render($this);
    }

    /**
     * Returns a render collection instance of the children
     *
     * @return \CmsCanvas\Content\Navigation\Item\RenderCollection
     */
    public function renderChildren()
    {
        return new RenderCollection($this->children);
    }

    /**
     * Generates a view with the navigation item
     *
     * @return string
     */
    public function renderContents()
    {
        $contents = '<li'.$this->getListItemAttributes().'>';
        $contents .= '<a'.$this->getAnchorAttributes().'>';
        $contents .= $this->getTitle();
        $contents .= '</a>';

        if ($this->seperator != null) {
            $contents .= '<span>'.$this->seperator.'</span>';
        }

        if (count($this->children) > 0) {
            $contents .= $this->renderChildren();
        }

        $contents .= '</li>';

        return $contents;
    }

    /**
     * Builds html attributes string for the <li> tag
     *
     * @return string
     */
    public function getListItemAttributes()
    {
        $attributes = '';

        if (! empty($this->navigationItem->id_attribute)) {
            $attributes .= ' id="'.$this->navigationItem->id_attribute.'"';
        }

        $classNames = $this->getHtmlClassNames();

        if (count($classNames) > 0) {
            $attributes .= ' class="'.implode(' ', $classNames).'"';
        }

        return $attributes;
    }

    /**
     * Builds html attributes string for the <a> tag
     *
     * @return string
     */
    public function getAnchorAttributes()
    {
        $attributes = '';

        if (! empty($this->navigationItem->target_attribute)) {
            $attributes .= ' target="'.$this->navigationItem->target_attribute.'"';
        }

        $attributes .= ' href="'.$this->getUrl().'"';

        return $attributes;
    }

    /**
     * Returns the navigation item model
     *
     * @return \CmsCanvas\Models\Content\Navigation\Item
     */
    public function getNavigationItem()
    {
        return $this->navigationItem;
    }

    /**
     * Generates an array of html class names for the current item
     *
     * @return array
     */
    public function getHtmlClassNames()
    {
        $classNames = [];

        if ($this->firstFlag) {
            $classNames[] = 'first';
        }

        if ($this->lastFlag) {
            $classNames[] = 'last';
        }

        if ($this->currentItemFlag) {
            $classNames[] = 'current-item';
        }

        if ($this->currentItemAncestorFlag) {
            $classNames[] = 'current-item-ancestor';
        }

        if (! empty($this->navigationItem->class_attribute)) {
            $classNames = array_merge($classNames, explode(' ', $this->navigationItem->class_attribute));
        }

        return $classNames;
    }

    /**
     * Returns the title for the item
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->navigationItem->title == null) {
            return $this->navigationItem->entry->title;
        } 

        return $this->navigationItem->title;
    }

    /**
     * Returns the full url for the item
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->navigationItem->entry != null) {
            return $this->navigationItem->entry->getUrl();
        } 

        if ($this->navigationItem->url != null) {
            $parsed = parse_url($this->navigationItem->url);

            if (empty($parsed['scheme'])) {
                return url($this->navigationItem->url);
            } else {
                return $this->navigationItem->url;
            }
        }

        return null;
    }

    /**
     * Returns the builder item children for the current item
     *
     * @return \CmsCanvas\Content\Navigation\Builder\Item|array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Returns the builder item parent for the current item
     *
     * @return \CmsCanvas\Content\Navigation\Builder\Item
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the items's position in the collection
     *
     * @param int $value
     * @return self
     */
    public function setIndex($value)
    {
        $this->index = $value;

        return $this;
    }

    /**
     * Sets the firstFlag class variable
     *
     * @param  bool $value
     * @return self
     */
    public function setFirstFlag($value)
    {
        $this->firstFlag = (bool) $value;

        return $this;
    }

    /**
     * Sets the lastFlag class variable
     *
     * @param  bool $value
     * @return self
     */
    public function setLastFlag($value)
    {
        $this->lastFlag = (bool) $value;

        return $this;
    }

    /**
     * Returns the firstFlag class property
     *
     * @return bool
     */
    public function getFirstFlag()
    {
        return $this->firstFlag;
    }

    /**
     * Returns the lastFlag class property
     *
     * @return bool
     */
    public function getLastFlag()
    {
        return $this->lastFlag;
    }

    /**
     * Returns the index class property
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Sets the currentItemFlag class variable
     *
     * @param  bool $value
     * @return self
     */
    public function setCurrentItemFlag($value)
    {
        $this->currentItemFlag = (bool) $value;

        return $this;
    }

    /**
     * Returns currentItemFlag class variable
     *
     * @return bool
     */
    public function isCurrentItem()
    {
        return $this->currentItemFlag;
    }

    /**
     * Sets the currentItemAncestorFlag class variable
     *
     * @param  bool $value
     * @return self
     */
    public function setCurrentItemAncestorFlag($value)
    {
        $this->currentItemAncestorFlag = (bool) $value;

        return $this;
    }

    /**
     * Returns currentItemAncestorFlag class variable
     *
     * @return bool
     */
    public function isCurrentItemAncestor()
    {
        return $this->currentItemAncestorFlag;
    }

    /**
     * Sets the seperator for the current item
     *
     * @param  string $seperator
     * @return self
     */
    public function setSeperator($seperator)
    {
        $this->seperator = $seperator;

        return $this;
    }

    /**
     * Returns the seperator for the current item
     *
     * @return string
     */
    public function getSeperator()
    {
        return $this->seperator;
    }

    /**
     * Unsets the children class property
     *
     * @return self
     */
    public function unsetChildren()
    {
        $this->children = [];

        return $this;
    }

    /**
     * Recursively loop through children and remove hidden navigation items
     *
     * @return self;
     */
    public function unsetHiddenChildren()
    {
        foreach ($this->children as $key => $child) {
            if ($child->isHidden()) {
                unset($this->children[$key]);
            } else {
                $child->unsetHiddenChildren();
            }
        }

        return $this;
    }

    /**
     * Unsets the parent class property
     *
     * @return self
     */
    public function unsetParent()
    {
        $this->parent = null;

        return $this;
    }

    /**
     * Checks if the current item is a reference to the home page 
     *
     * @return bool
     */
    public function isHomePage()
    {
        return $this->navigationItem->isHomePage();
    }

    /**
     * Checks if the current item is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->navigationItem->hidden_flag;
    }

}