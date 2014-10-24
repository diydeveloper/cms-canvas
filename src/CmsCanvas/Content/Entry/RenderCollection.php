<?php namespace CmsCanvas\Content\Entry;

use CmsCanvas\Database\Eloquent\Collection as CmsCanvasCollection;

class RenderCollection extends CmsCanvasCollection {

    protected $paginator;

    /**
     * Defines the order in which to sort.
     *
     * @param \CmsCanvas\Models\Content\Entry|collection $entries
     * @return void
     */
    public function __construct($entries)
    {
        if ($entries instanceof \Illuminate\Pagination\Paginator)
        {
            $this->paginator = $entries;
        }

        $entryCount = count($entries);
        $counter = 1;

        foreach ($entries as $entry) {
            $render = $entry->render();

            if ($counter !== 1)
            {
                $render->setFirstFlag(false);
            }

            if ($counter !== $entryCount)
            {
                $render->setLastFlag(false);
            }

            $this->items[] = $render;
            $counter++;
        }
    }

    /**
     * Magic method to render the entries as a string
     *
     * @return string
     */
    public function __toString()
    {
        $contents = '';

        foreach ($this->items as $item) 
        {
            $contents .= (string) $item;
        }

        return $contents;
    }

    /**
     * Returns pagination links
     *
     * @return string
     */
    public function links()
    {
        if ($this->paginator != null)
        {
            return $this->paginator->links();
        }
    }

}