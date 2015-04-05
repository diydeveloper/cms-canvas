<?php namespace CmsCanvas\Content\Navigation;

use Request, Cache;
use CmsCanvas\Models\Content\Navigation;
use CmsCanvas\Models\Content\Navigation\Item;
use CmsCanvas\Content\Navigation\Item\RenderCollection;

class Builder {

    /**
     * @var int
     */
    protected $navigationId;

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
    protected $startNodeId = 0;

    /**
     * @var bool
     */
    protected $recursiveFlag = true;

    /**
     * @var \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected $navigationTree;

    /**
     * @param array $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->buildFromArray($config);
    }

    /**
     * Returns collection of navigation items
     *
     * @return \CmsCanvas\Content\Navigation\Item\RenderCollection
     */
    public function get()
    {
        $this->compile();

        return new RenderCollection($this->navigationTree);
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
        foreach ($config as $key => $value)
        {
            switch ($key) {
                case 'navigation_id':
                    $this->setNavigationId($value);
                    break;

                case 'max_depth':
                    $this->setMaxDepth($value);
                    break;

                case 'start_level':
                    $this->setStartFromParentDepth($value);
                    break;

                case 'offset':
                    $this->setOffset($value);
                    break;

                case 'start_node_id':
                    $this->setStartNodeId($value);
                    break;
            }
        } 
    }

    /**
     * Sets the level that the navigation should start from
     *
     * @param string $startLevel
     * @return void
     */
    public function setStartLevel($startLevel)
    {
        $this->startLevel = strtolower($startLevel); 
    }

    /**
     * Sets the start level offset that the navigation should start from
     *
     * @param int $offset
     * @return void
     */
    public function setOffset($offset)
    {
        $this->offset = $offset; 
    }

    /**
     * Sets the depth at which the navigation should stop 
     *
     * @param int $maxDepth
     * @return void
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth; 
    }

    /**
     * Sets the navigation id to build from
     *
     * @param int $navigationId
     * @return void
     */
    public function setNavigationId($navigationId)
    {
        $this->navigationId = $navigationId; 
    }

    /**
     * Sets the item id where the navigation should start
     *
     * @param int $startNodeId
     * @return void
     */
    public function setStartNodeId($startNodeId)
    {
        $this->startNodeId = $startNodeId; 
    }

    /**
     * Sets a flag indicating whether the navigaiton should render children
     *
     * @param bool $recursive
     * @return void
     */
    public function setRecursiveFlag($recursiveFlag)
    {
        $this->recursiveFlag = $recursiveFlag; 
    }

    /**
     * Recursively query navigation item children to build 
     * the navigation tree
     * 
     * @param \CmsCanvas\Models\Content\Navigation\Item|collection
     * @return \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected function buildNavigationTree($items, $depth = 1)
    {
        $items->load('entry');

        if ($this->recursiveFlag)
        {
            $items->load('children');
        }

        $itemCount = count($items);
        $counter = 1;

        foreach ($items as $item) 
        {
            if ($counter == 1)
            {
                $item->setFirstFlag(true);
            }

            if ($counter == $itemCount)
            {
                $item->setLastFlag(true);
            }

            if ($this->recursiveFlag && count($item->children) > 0)
            {
                $this->buildNavigationTree($item->children, $depth++);
            }

            $counter++;
        }

        return $items;
    }

    /**
     * Caches and returns the navigation tree
     * 
     * @return \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected function getCachedTree()
    {
        $cache = Cache::rememberForever($this->getCacheKey(), function()
        {
            $items = Item::where('navigation_id', $this->navigationId)
                ->where('parent_id', $this->startNodeId)
                ->orderBy('sort', 'asc')
                ->get();

            return $this->buildNavigationTree($items);
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
        return $this->navigationId.'.'.$this->startNodeId.'.'.$this->recursiveFlag;
    }

    /**
     * Finds and identifies via URL the current navigaiton item
     *
     * @param \CmsCanvas\Models\Content\Navigation\Item|collection
     * @param int
     * @return void
     */
    protected function compileCurrentItems($items, $depth = 1)
    {
        foreach ($items as $item) 
        {
            if (Request::is($item->url))
            {
                $item->setCurrentItemFlag(true);
                $item->setCurrentAncestorFlag(true);
                $this->currentItemDepth = $depth;
            }

            if (count($item->children) > 0)
            {
                $this->compileCurrentItems($item->children, $depth++);
            }
        }
    }

    /**
     * Finds and identifies ancestors of the current navigation item
     *
     * @param \CmsCanvas\Models\Content\Navigation\Item|collection
     * @param int
     * @return void
     */
    protected function compileCurrentItemAncestors($items, $depth = 1)
    {
        foreach ($items as $item) 
        {
            if ($this->hasCurrentDescendant($item->children))
            {
                $item->currentAncestorFlag = true;
            }

            if (count($item->children) > 0)
            {
                $this->compileCurrentItemAncestors($item->children, $depth++);
            }
        }
    }

    /**
     * Detects if items is a ancestor of the current navigation item
     *
     * @param \CmsCanvas\Models\Content\Navigation\Item|collection
     * @param int
     * @return bool
     */
    protected function hasCurrentDescendant($items, $depth = 1)
    {
        $hasDescendant = false;

        foreach ($items as $item) 
        {
            if ($item->currentItemFlag)
            {
                return true;
            }

            if (count($item->children) > 0)
            {
                $hasDescendant = $this->hasCurrentDescendant($item->children, $depth++);

                if ($hasDescendant)
                {
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
     * @param \CmsCanvas\Models\Content\Navigation\Item|collection
     * @param int
     * @param \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected function getCurrentParentSubset($items, $depth = 1)
    {
        $subset = array();

        foreach($items as $item)
        {
            if ($item->currentItemFlag || $item->currentAncestorFlag)
            {
                if ($depth == $this->startingParentDepth)
                {
                    $subset = $items;
                }
                else if ($item->currentItemFlag)
                {
                    // If we reach this point, the starting parent depth is greater
                    // than the current page's depth. Go ahead and return the children of the
                    // current item. If there are no children then return its siblings.
                    if (count($item->children) > 0)
                    {
                        $subset = $item->children;
                    }
                    else
                    {
                        $subset = $items;
                    }
                }
                else
                {
                    $subset = $this->getCurrentParentSubset($item->children, $depth++);
                }

                break;
            }
        }

        return $subset;
    }

    /**
     * Returns a navigation subset starting with the siblings of the current navigation item
     *
     * @param \CmsCanvas\Models\Content\Navigation\Item|collection
     * @param int
     * @param \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected function getCurrentSiblingSubset($items, $depth = 1)
    {
        $subset = array();

        foreach($items as $item)
        {
            if ($item->currentItem)
            {
                $subset = $items;

                break;
            }
            else
            {
                if (count($item->children) > 0)
                {
                    $subset = $this->getCurrentSiblingSubset($item->children, $depth++);

                    if (count($subset) > 0)
                    {
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
     * @param \CmsCanvas\Models\Content\Navigation\Item|collection
     * @param int
     * @param \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected function getCurrentChildrenSubset($items, $depth = 1)
    {
        $subset = array();

        foreach($items as $item)
        {
            if ($item->currentItemFlag)
            {
                $subset = $item->children;

                return $subset;
            }

            if (count($item->children) > 0)
            {
                $subset = $this->getCurrentChildrenSubset($Item->children, $depth++);

                if (count($subset) > 0)
                {
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

        switch ($this->startLevel)
        {
            case 'parent_depth':
                $calculatedDepth = $this->offset;
                break;

            case 'above_current':
                $calculatedDepth = $this->currentItemDepth - $this->offset;
                break;
        }

        if ($calculatedDepth != null && $calculatedDepth > 0)
        {
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

        switch ($this->startLevel)
        {
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

        if (!empty($this->idAttribute))
        {
            $attributes .= ' id="'.$this->idAttribute.'"';
        }

        if (!empty($this->classAttribute))
        {
            $attributes .= ' class="'.$this->classAttribute.'"';
        }

        return $attributes;
    }

}