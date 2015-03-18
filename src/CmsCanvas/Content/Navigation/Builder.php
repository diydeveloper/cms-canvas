<?php namespace CmsCanvas\Content\Navigation;

use Request;
use CmsCanvas\Models\Content\Navigation;

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
    protected $recursive = true;

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
     * Set query limit for entries
     *
     * @param string $startLevel
     * @return void
     */
    public function setStartLevel($startLevel)
    {
        $this->startLevel = strtolower($startLevel); 
    }

    /**
     * Set query limit for entries
     *
     * @param int $startLevel
     * @return void
     */
    public function setOffset($offset)
    {
        $this->offset = $offset; 
    }

    /**
     * Set query limit for entries
     *
     * @param int $maxDepth
     * @return void
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth; 
    }

    /**
     * Set query limit for entries
     *
     * @param int $navigationId
     * @return void
     */
    public function setNavigationId($navigationId)
    {
        $this->navigationId = $navigationId; 
    }

    /**
     * Set query limit for entries
     *
     * @param int $startNodeId
     * @return void
     */
    public function setStartNodeId($startNodeId)
    {
        $this->startNodeId = $startNodeId; 
    }

    /**
     * Set query limit for entries
     *
     * @param bool $recursive
     * @return void
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive; 
    }

    /**
     * Recursively query navigation item children to build 
     * the navigation tree
     * 
     * @param \CmsCanvas\Models\Content\Navigation\Item|collection
     * @return \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected function buildNavigationTree($items = null, $depth = 1)
    {
        if ($items == null)
        {
            $items = Item::where('navigationId', $this->navigationId)
                ->where('parent_id', $this->startNodeId)
                ->orderBy('sort', 'asc')
                ->get();
        }

        if ($this->recursive)
        {
            $items->load('children', 'entry');

            foreach ($items as $item) 
            {
                if (count($item->children) > 0)
                {
                    $this->buildNavigationTree($item->children, $depth++);
                }
            }
        }

        return $items;
    }

    /**
     * Caches and returns the navigation tree
     * 
     * @return \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected function getNavigationTree()
    {
        $cache = Cache::rememberForever($this->getCacheKey(), function()
        {
            return $this->buildNavigationTree();
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
        return $this->navigationId.'.'.$this->startNodeId.'.'.$this->recursive;
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
                $item->currentItem = true;
                $item->currentAncestor = true;
                $this->currentItemDepth = $depth;
            }

            if (count($item->children) > 0)
            {
                $this->setCurrentItems($item->children, $depth++);
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
    protected function compileCurrentItemAncestors($items, $depth)
    {
        foreach ($items as $item) 
        {
            if ($this->hasCurrentDescendant($item->children))
            {
                $item->currentAncestor = true;
            }

            if (count($item->children) > 0)
            {
                $this->setCurrentItemAncestors($item->children, $depth++);
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
            if ($item->currentItem)
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
            if ($item->currentItem || $item->currentAncestor)
            {
                if ($depth == $this->startingParentDepth)
                {
                    $subset = $items;
                }
                else if ($item->currentItem)
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
            if ($item->currentItem)
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
        $navigaitonTree = $this->navigaitonTree;

        switch ($this->startLevel)
        {
            case 'parent_depth':
            case 'above_current':
                $this->compileStartingParentDepth();
                $navigaitonTree = $this->getCurrentParentSubset($navigaitonTree);
                break;

            case 'current':
                $navigaitonTree = $this->getCurrentSiblingSubset($navigaitonTree);
                break;

            case 'current_children':
                $navigaitonTree = $this->getCurrentChildrenSubset($navigaitonTree);
                break;
        }

        $this->navigaitonTree = $navigaitonTree;
    }

    /**
     * Compile the navigation tree from the current object
     *
     * @return void
     */
    protected function compile()
    {
        $this->navigaitonTree = $this->getNavigationTree();

        $this->compileCurrentItems($this->navigaitonTree);
        $this->compileCurrentItemAncestors($this->navigaitonTree);
        $this->compileSubset();
    }

}