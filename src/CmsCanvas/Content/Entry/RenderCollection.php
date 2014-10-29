<?php namespace CmsCanvas\Content\Entry;

use CmsCanvas\Database\Eloquent\Collection as CmsCanvasCollection;

class RenderCollection extends CmsCanvasCollection {

    /**
     * @var \Illuminate\Pagination\Paginator
     */
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
            $render->setIndex($counter - 1);

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

    /**
     * Get the current page for the request.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        if ($this->paginator != null)
        {
            return $this->paginator->getCurrentPage();
        }
    }

    /**
     * Get the last page that should be available.
     *
     * @return int
     */
    public function getLastPage()
    {
        if ($this->paginator != null)
        {
            return $this->paginator->getLastPage();
        }
    }

    /**
     * Get the number of items to be displayed per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        if ($this->paginator != null)
        {
            return $this->paginator->getPerPage();
        }
    }

    /**
     * Get the total number of items in the collection.
     *
     * @return int
     */
    public function getTotal()
    {
        if ($this->paginator != null)
        {
            return $this->paginator->getTotal();
        }
    }

    /**
     * Get the number of items for the current page.
     *
     * @return int
     */
    public function count()
    {
        if ($this->paginator != null)
        {
            return $this->paginator->count();
        }
    }

    /**
     * Get the number of the last item on the paginator.
     *
     * @return int
     */
    public function getTo()
    {
        if ($this->paginator != null)
        {
            return $this->paginator->getTo();
        }
    }

    /**
     * Get the number of the first item on the paginator.
     *
     * @return int
     */
    public function getFrom()
    {
        if ($this->paginator != null)
        {
            return $this->paginator->getFrom();
        }
    }

}