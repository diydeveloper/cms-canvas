<?php 

namespace CmsCanvas\Content\Navigation;

use Request, Cache, Lang;
use CmsCanvas\Models\Content\Navigation;
use CmsCanvas\Models\Content\Navigation\Item;
use CmsCanvas\Content\Navigation\Builder\Item as ItemBuilder;
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
     * @var int
     */
    protected $minDepth = 0;

    /**
     * @var int
     */
    protected $maxQueryDepth;

    /**
     * @var int
     */
    protected $minQueryDepth = 0;

    /**
     * @var int
     */
    protected $currentItemDepth = 0;

    /**
     * @var bool
     */
    protected $recursiveFlag = true;

    /**
     * @var bool
     */
    protected $onlyCurrentBranchFlag = false;

    /**
     * @var bool
     */
    protected $startFromCurrentDepthFlag = false;

    /**
     * @var \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected $navigationTree;

    /**
     * @var string
     */
    protected $ulId;

    /**
     * @var string
     */
    protected $ulClass;

    /**
     * @var int
     */
    protected $childrenVisibility;

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
        $navigationTree = $this->getSubset($this->getNavigationTree());

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

                case 'min_depth':
                    $this->setMinDepth($value);
                    break;

                case 'start_from_current_depth':
                    $this->setStartFromCurrentDepth($value);
                    break;

                case 'only_current_branch':
                    $this->setOnlyCurrentBranch($value);
                    break;

                case 'children_visibility':
                    $this->setChildrenVisibility($value);
                    break;

                case 'recursive':
                    $this->setRecursive($value);
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
     * Sets the depth at which the navigation should start 
     *
     * @param  int $minDepth
     * @return self
     */
    public function setMinDepth($minDepth)
    {
        $this->minDepth = $minDepth; 

        return $this;
    }

    /**
     * Sets a flag indicating to only return the current branch
     *
     * @param  bool $onlyCurrentBranchFlag
     * @return self
     */
    public function setOnlyCurrentBranch($onlyCurrentBranchFlag)
    {
        $this->onlyCurrentBranchFlag = (bool) $onlyCurrentBranchFlag; 

        return $this;
    }

    /**
     * Sets a flag indicating if the minDepth should start at the current depth
     *
     * @param  bool $startFromCurrentDepthFlag
     * @return self
     */
    public function setStartFromCurrentDepth($startFromCurrentDepthFlag)
    {
        $this->startFromCurrentDepthFlag = $startFromCurrentDepthFlag;

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
     * Sets the visibility for navigation chilren
     *
     * @param  string $visibility
     * @return self
     */
    public function setChildrenVisibility($visibility)
    {
        switch ($visibility) {
            case 'show':
                $this->childrenVisibility = Item::CHILDREN_VISIBILITY_SHOW;
                break;

            case 'current_branch':
                $this->childrenVisibility = Item::CHILDREN_VISIBILITY_CURRENT_BRANCH;
                break;

            case 'hide':
                $this->childrenVisibility = Item::CHILDREN_VISIBILITY_HIDE;
                break;
        }

        return $this;
    }

    /**
     * Sets a flag indicating whether the navigaiton should render children
     *
     * @param  bool $recursiveFlag
     * @return self
     */
    public function setRecursive($recursiveFlag)
    {
        $this->recursiveFlag = (bool) $recursiveFlag; 

        return $this;
    }

    /**
     * Sets the id attribute string for the <ul> tag
     *
     * @param  string $ulId
     * @return self
     */
    public function setUlId($ulId)
    {
        $this->ulId = $ulId; 

        return $this;
    }

    /**
     * Sets the class attribute string for the <ul> tag
     *
     * @param  string $ulClass
     * @return self
     */
    public function setUlClass($ulClass)
    {
        $this->ulClass = $ulClass; 

        return $this;
    }

    /**
     * Recursively Lazy load naavigation item entries and children
     *
     * @param  \CmsCanvas\Models\Content\Navigation\Item|collection $items
     * @param  int $depth
     * @return \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    protected function lazyLoadNavigationTree($items, $depth = 0)
    {
        $items->load('entry.contentType');

        if ($this->recursiveFlag && ($this->maxQueryDepth === null || $this->maxQueryDepth > $depth)) {
            $items->load(['children' => function($query) {
                $query->orderBy('sort', 'asc');
                $query->with('data');
            }]);
        }

        foreach ($items as $item) {
            if ($this->recursiveFlag && count($item->getLoadedChildren()) > 0) {
                $this->lazyLoadNavigationTree($item->getLoadedChildren(), $depth + 1);
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
            $items = Item::with('data')
                ->join('navigations', 'navigation_items.navigation_id', '=', 'navigations.id')
                ->where('navigations.short_name', $this->shortName)
                ->where('depth', $this->minQueryDepth)
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
        $key = $this->shortName;
        $key .= '.'.Lang::getLocale();
        $key .= '.'.$this->minQueryDepth;
        $key .= '.'.($this->recursiveFlag ? 'true' : 'false');

        if ($this->maxQueryDepth !== null) {
            $key .= '.'.$this->maxQueryDepth;
        }

        return $key;
    }

    /**
     * Finds and identifies via URL the current navigaiton item
     *
     * @param \CmsCanvas\Content\Navigation\Builder\Item|array
     * @param int
     * @return void
     */
    protected function compileCurrentItems($items, $depth = 0)
    {
        foreach ($items as $item) {
            $pattern = $item->getNavigationItem()->current_uri_pattern;

            if ( ! $item->getNavigationItem()->disable_current_flag 
                && (($pattern != null && (bool) @preg_match('#^'.$pattern.'\z#', Request::path())) 
                    || ($pattern == null && Request::url() == $item->getUrl())
                )
            ) {
                $item->setCurrentItemFlag(true);

                // In cases where there are multiple current items in the navigation
                // we only want to capture the depth of the first one encountered.
                if ($this->currentItemDepth === 0) {
                    $this->currentItemDepth = $depth;
                }
            }

            $parent = $item->getParent();
            if ($parent != null && ($parent->isCurrentItem() || $parent->isCurrentItemDescendant())) {
                $item->setCurrentItemDescendantFlag(true);
            }

            if (count($item->getChildren()) > 0) {
                $this->compileCurrentItems($item->getChildren(), $depth + 1);
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
    protected function compileCurrentItemAncestors($items, $depth = 0)
    {
        foreach ($items as $item) {
            if ( ! $item->getNavigationItem()->disable_current_ancestor_flag 
                && $this->hasCurrentDescendant($item->getChildren())
            ) {
                $item->setCurrentItemAncestorFlag(true);
            }

            if (count($item->getChildren()) > 0) {
                $this->compileCurrentItemAncestors($item->getChildren(), $depth + 1);
            }
        }
    }

    /**
     * Detects if items is a ancestor of the current navigation item
     *
     * @param  \CmsCanvas\Content\Navigation\Builder\Item|array
     * @param  int $depth
     * @return bool
     */
    protected function hasCurrentDescendant($items, $depth = 0)
    {
        $hasDescendant = false;

        foreach ($items as $item) {
            if ($item->isCurrentItem()) {
                return true;
            }

            if (count($item->getChildren()) > 0) {
                $hasDescendant = $this->hasCurrentDescendant($item->getChildren(), $depth + 1);

                if ($hasDescendant) {
                    return $hasDescendant;
                }
            }
        }

        return $hasDescendant;
    }

    /**
     * Return a subset of the navigation tree processing additional filters
     *
     * @param  \CmsCanvas\Content\Navigation\Builder\Item|array $items
     * @param  int $depth
     * @return \CmsCanvas\Content\Navigation\Builder\Item|array
     */
    public function getSubset($items, $depth = 0)
    {
        $subset = [];

        foreach ($items as $item) {
            if ($depth < $this->minDepth) {
                $subset = array_merge($subset, $this->getSubset($item->getChildren(), $depth + 1));
                continue;
            }

            if ($item->isHidden()) {
                continue;
            }

            if ($this->onlyCurrentBranchFlag  && $depth == $this->minDepth
                && ($item->getParent() != null && ! $item->getParent()->isInCurrentBranch())
            ) {
                continue;
            }

            $newItem = $item->cloneWithNoChildren();
            $newItem->setDepth($depth);

            // Determine if children should be returned in the subset
            if (count($item->getChildren()) > 0 
                && ($this->maxDepth === null || $depth < $this->maxDepth)
                && $this->isItemChildrenVisible($item)
            ) {
                $children = $this->getSubset($item->getChildren(), $depth + 1);
                $newItem->setChildren($children);
            }

            $subset[] = $newItem;
        }

        return $subset;
    }

    /**
     * Determines if the provided item's children is visible
     *
     * @param  \CmsCanvas\Content\Navigation\Builder\Item $item
     * @return bool
     */
    protected function isItemChildrenVisible(ItemBuilder $item)
    {
        if ($this->childrenVisibility !== null) {
            if ($this->childrenVisibility == Item::CHILDREN_VISIBILITY_SHOW 
                || ($this->childrenVisibility == Item::CHILDREN_VISIBILITY_CURRENT_BRANCH 
                    && $item->isInCurrentBranch()
                )
            ) {
                return true;
            }
        } elseif ($item->getNavigationItem()->children_visibility_id == Item::CHILDREN_VISIBILITY_SHOW
            || ($item->getNavigationItem()->children_visibility_id == Item::CHILDREN_VISIBILITY_CURRENT_BRANCH 
                && $item->isInCurrentBranch()
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * Set the calculated depth factoring the current depth and min depth
     *
     * @return void
     */
    protected function compileCurrentDepth()
    {
        if ($this->startFromCurrentDepthFlag) {
            $calculatedDepth = $this->minDepth + $this->currentItemDepth;
            $this->minDepth = ($calculatedDepth > 0) ? $calculatedDepth : 0;
        }
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
        $this->compileCurrentDepth();
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