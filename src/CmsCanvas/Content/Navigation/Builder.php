<?php namespace CmsCanvas\Content\Navigation;

use CmsCanvas\Models\Content\Navigation;
use CmsCanvas\Content\Entry\RenderCollection;

class Builder {

    /**
     * @var int
     */
    protected $maxDepth;

    /**
     * @var int
     */
    protected $startOnCurrentLevel;

    /**
     * @var int
     */
    protected $startWithCurrentChildren;

    /**
     * @var int
     */
    protected $startFromParentDepth;

    /**
     * @var int
     */
    protected $startingParentDepth;

    /**
     * @var int
     */
    protected $currentDepth;

    /**
     * @var int
     */
    protected $disableCurrent;

    /**
     * @param array $config
     * @return void
     */
    public function __construct(array $config)
    {
        //$this->buildFromArray($config);
    }

    protected function buildTree($items = null)
    {
        if ($items == null)
        {
            $items = Item::where('navigationId', $this->navigationId)
                ->where('parent_id', $this->startNodeId)
                ->get();
        }

        foreach ($items as $item) 
        {
            if (count($item->children) > 0)
            {
                $this->buildTree($item->children);
            }
        }

    }

}