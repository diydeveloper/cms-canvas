<?php 

namespace CmsCanvas\Content\Navigation;

use Request, Cache;
use CmsCanvas\Models\Content\Navigation;
use CmsCanvas\Models\Content\Navigation\Item;
use CmsCanvas\Content\Navigation\Item\RenderCollection;

class Builder {

    /**
     * @var string
     */
    protected $shortName;

    /**
     * @var int
     */
    protected $maxDepth;

    /**
     * @var string
     */
    protected $startLevel;

    /**
     * @var int
     */
    protected $offset = 1;

    /**
     * @var int
     */
    protected $startingParentDepth = 1;

    /**
     * @var int
     */
    protected $currentItemDepth = 0;

    /**
     * @var int
     */
    protected $startItemId = 0;

    /**
     * @var bool
     */
    protected $recursiveFlag = true;

    /**
     * @var \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected $navigationTree;

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
     * Returns collection of navigation items
     *
     * @return \CmsCanvas\Content\Navigation\Item\RenderCollection
     */
    public function get()
    {
        $navigationTree = $this->removeHidden($this->getNavigationTree());
        return new RenderCollection($navigationTree, $this);
    }

    /**
     * Returns collection of navigation items
     *
     * @return \CmsCanvas\Content\Navigation\Item|collection
     */
    public function getNavigationTree()
    {
        $this->compile();

        return $this->navigationTree;
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
                case 'max_depth':
                    $this->setMaxDepth($value);
                    break;

                case 'start_level':
                    $this->setStartLevel($value);
                    break;

                case 'offset':
                    $this->setOffset($value);
                    break;

                case 'start_item_id':
                    $this->setStartItemId($value);
                    break;

                case 'recursive':
                    $this->setRecursiveFlag($value);
                    break;

                case 'id_attribute':
                    $this->setIdAttribute($value);
                    break;

                case 'class_attribute':
                    $this->setClassAttribute($value);
                    break;
            }
        } 
    }

    /**
     * Sets the level that the navigation should start from
     *
     * @param  string $startLevel
     * @return self
     */
    public function setStartLevel($startLevel)
    {
        $this->startLevel = strtolower($startLevel); 

        return $this;
    }

    /**
     * Sets the start level offset that the navigation should start from
     *
     * @param  int $offset
     * @return self
     */
    public function setOffset($offset)
    {
        $this->offset = $offset; 

        return $this;
    }

    /**
     * Sets the depth at which the navigation should stop 
     *
     * @param  int $maxDepth
     * @return self
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth; 

        return $this;
    }

    /**
     * Sets the navigation short name to build from
     *
     * @param  string $shortName
     * @return self
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName; 

        return $this;
    }

    /**
     * Sets the item id where the navigation should start
     *
     * @param  int $startItemId
     * @return self
     */
    public function setStartItemId($startItemId)
    {
        $this->startItemId = $startItemId; 

        return $this;
    }

    /**
     * Sets a flag indicating whether the navigaiton should render children
     *
     * @param  bool $recursiveFlag
     * @return self
     */
    public function setRecursiveFlag($recursiveFlag)
    {
        $this->recursiveFlag = (bool) $recursiveFlag; 

        return $this;
    }

    /**
     * Sets the id attribute string for the <ul> tag
     *
     * @param  string $idAttribute
     * @return self
     */
    public function setIdAttribute($idAttribute)
    {
        $this->idAttribute = $idAttribute; 

        return $this;
    }

    /**
     * Sets the class attribute string for the <ul> tag
     *
     * @param  string $classAttribute
     * @return self
     */
    public function setClassAttribute($classAttribute)
    {
        $this->classAttribute = $classAttribute; 

        return $this;
    }

    /**
     * Recursively Lazy load naavigation item entries and children
     *
     * @param  \CmsCanvas\Models\Content\Navigation\Item|collection $items
     * @param  int $depth
     * @return \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected function lazyLoadNavigationTree($items, $depth = 1)
    {
        $items->load('entry.contentType');

        if ($this->recursiveFlag && ($this->maxDepth === null || $this->maxDepth > $depth)) {
            $items->load(['children' => function($query) {
                $query->orderBy('sort', 'asc');
            }]);
        }

        foreach ($items as $item) {
            if ($this->recursiveFlag && count($item->getLoadedChildren()) > 0) {
                $this->lazyLoadNavigationTree($item->getLoadedChildren(), ++$depth);
            }
        }

        return $items;
    }

    /**
     * Caches and returns the navigation tree
     * 
     * @return \CmsCanvas\Content\Navigation\Builder\Item|array
     */
    protected function getCachedTree()
    {
        $cache = Cache::rememberForever($this->getCacheKey(), function() {
            $items = Item::join('navigations', 'navigation_items.navigation_id', '=', 'navigations.id')
                ->where('navigations.short_name', $this->shortName)
                ->where('parent_id', ($this->startItemId ? $this->startItemId : null))
                ->orderBy('sort', 'asc')
                ->select('navigation_items.*')
                ->get();

            return Item::newItemBuilderCollection($this->lazyLoadNavigationTree($items));
        });

        return $cache;
    }

    /**
     * A unique cache identifier
     * 
     * @return string
     */
    protected function getCacheKey()
    {
        $key = $this->shortName.'.'.$this->startItemId.'.'.$this->recursiveFlag;

        if ($this->maxDepth !== null) {
            $key .= '.'.$this->maxDepth;
        }

        return $key;
    }

    /**
     * Recursively loop through navigation tree and remove hidden navigation items
     *
     * @return \CmsCanvas\Content\Navigation\Builder\Item|array;
     */
    protected function removeHidden($items, $depth = 1)
    {
        foreach ($items as $key => $item) {
            if ($item->isHidden()) {
                unset($items[$key]);
            } else {
                $item->unsetHiddenChildren();
            }
        }

        return $items;
    }

    /**
     * Finds and identifies via URL the current navigaiton item
     *
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     * @param int
     * @return void
     */
    protected function compileCurrentItems($items, $depth = 1)
    {
        foreach ($items as $item) {
            $pattern = $item->getNavigationItem()->uri_pattern;

            if ( ! $item->getNavigationItem()->disable_current_flag 
                && (($pattern != null && (bool) @preg_match('#^'.$pattern.'\z#', Request::path())) 
                    || ($pattern == null && Request::url() == $item->getUrl())
                )
            ) {
                $item->setCurrentItemFlag(true);
                $this->currentItemDepth = $depth;
            }

            if (count($item->getChildren()) > 0) {
                $this->compileCurrentItems($item->getChildren(), ++$depth);
            }
        }
    }

    /**
     * Finds and identifies ancestors of the current navigation item
     *
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     * @param int
     * @return void
     */
    protected function compileCurrentItemAncestors($items, $depth = 1)
    {
        foreach ($items as $item) {
            if ( ! $item->getNavigationItem()->disable_current_ancestor_flag && $this->hasCurrentDescendant($item->getChildren())) {
                $item->setCurrentItemAncestorFlag(true);
            }

            if (count($item->getChildren()) > 0) {
                $this->compileCurrentItemAncestors($item->getChildren(), ++$depth);
            }
        }
    }

    /**
     * Detects if items is a ancestor of the current navigation item
     *
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     * @param int
     * @return bool
     */
    protected function hasCurrentDescendant($items, $depth = 1)
    {
        $hasDescendant = false;

        foreach ($items as $item) {
            if ($item->isCurrentItem()) {
                return true;
            }

            if (count($item->getChildren()) > 0) {
                $hasDescendant = $this->hasCurrentDescendant($item->getChildren(), ++$depth);

                if ($hasDescendant) {
                    return $hasDescendant;
                }
            }
        }

        return $hasDescendant;
    }

    /**
     * Returns a navigation subset starting with the parent of the current navigation
     * item at the nth parent depth
     *
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     * @param int
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     */
    protected function getCurrentParentSubset($items, $depth = 1)
    {
        $subset = [];

        foreach($items as $item) {
            if ($item->isCurrentItem() || $item->isCurrentItemAncestor()) {
                if ($depth == $this->startingParentDepth) {
                    $subset = $items;
                } elseif ($item->isCurrentItem()) {
                    // If we reach this point, the starting parent depth is greater
                    // than the current page's depth. Go ahead and return the children of the
                    // current item. If there are no children then return its siblings.
                    if (count($item->getChildren()) > 0) {
                        $subset = $item->getChildren();
                    } else {
                        $subset = $items;
                    }
                } else {
                    $subset = $this->getCurrentParentSubset($item->getChildren(), ++$depth);
                }

                break;
            }
        }

        return $subset;
    }

    /**
     * Returns a navigation subset starting with the siblings of the current navigation item
     *
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     * @param int
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     */
    protected function getCurrentSiblingSubset($items, $depth = 1)
    {
        $subset = [];

        foreach($items as $item) {
            if ($item->isCurrentItem()) {
                $subset = $items;

                break;
            } else {
                if (count($item->getChildren()) > 0) {
                    $subset = $this->getCurrentSiblingSubset($item->getChildren(), ++$depth);

                    if (count($subset) > 0) {
                        return $subset;
                    }
                }
            }
        }

        return $subset;
    }

    /**
     * Returns a navigation subset starting with the children of the current navigation item
     *
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     * @param int
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     */
    protected function getCurrentChildrenSubset($items, $depth = 1)
    {
        $subset = [];

        foreach($items as $item) {
            if ($item->isCurrentItem()) {
                $subset = $item->getChildren();

                return $subset;
            }

            if (count($item->getChildren()) > 0) {
                $subset = $this->getCurrentChildrenSubset($item->getChildren(), ++$depth);

                if (count($subset) > 0) {
                    return $subset;
                }
            }
        }

        return $subset;
    }

    /**
     * Calculate the starting parent depth from the configuration
     *
     * @return void
     */
    protected function compileStartingParentDepth()
    {
        $calculatedDepth = null;

        switch ($this->startLevel) {
            case 'parent_depth':
                $calculatedDepth = $this->offset;
                break;

            case 'above_current':
                $calculatedDepth = $this->currentItemDepth - $this->offset;
                break;
        }

        if ($calculatedDepth != null && $calculatedDepth > 0) {
            $this->startingParentDepth = $calculatedDepth;
        }
    }

    /**
     * Get and set a subset of the navigation tree if needed
     *
     * @return void
     */
    protected function compileSubset()
    {
        $navigationTree = $this->navigationTree;

        switch ($this->startLevel) {
            case 'parent_depth':
            case 'above_current':
                $this->compileStartingParentDepth();
                $navigationTree = $this->getCurrentParentSubset($navigationTree);
                break;

            case 'current':
                $navigationTree = $this->getCurrentSiblingSubset($navigationTree);
                break;

            case 'current_children':
                $navigationTree = $this->getCurrentChildrenSubset($navigationTree);
                break;
        }

        $this->navigationTree = $navigationTree;
    }

    /**
     * Compile the navigation tree from the current object
     *
     * @return void
     */
    protected function compile()
    {
        $this->navigationTree = $this->getCachedTree();

        $this->compileCurrentItems($this->navigationTree);
        $this->compileCurrentItemAncestors($this->navigationTree);
        $this->compileSubset();
    }

    /**
     * Builds html attributes string for the <ul> tag
     *
     * @return string
     */
    public function getAttributes()
    {
        $attributes = '';

        if (! empty($this->idAttribute)) {
            $attributes .= ' id="'.$this->idAttribute.'"';
        }

        if (! empty($this->classAttribute)) {
            $attributes .= ' class="'.$this->classAttribute.'"';
        }

        return $attributes;
    }

}