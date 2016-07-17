<?php 

namespace CmsCanvas\Content\Entry;

use CmsCanvas\Database\Eloquent\Collection as CmsCanvasCollection;
use Illuminate\Pagination\AbstractPaginator;

class RenderCollection extends CmsCanvasCollection {

    /**
     * @var \Illuminate\Pagination\AbstractPaginator
     */
    protected $paginator;

    /**
     * Defines the order in which to sort.
     *
     * @param \CmsCanvas\Models\Content\Entry\Builder\Entry|array $entryBuilders
     * @param \Illuminate\Pagination\AbstractPaginator  $paginator
     * @return void
     */
    public function __construct(array $entryBuilders, AbstractPaginator $paginator = null)
    {
        $this->paginator = $paginator;

        foreach ($entryBuilders as $entryBuilder) {
            $this->items[] = $entryBuilder->render();
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

        foreach ($this->items as $item) {
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
        if ($this->paginator != null) {
            return $this->paginator->render();
        }
    }

    /**
     * Get the current page for the request.
     *
     * @return int
     */
    public function currentPage()
    {
        if ($this->paginator != null) {
            return $this->paginator->currentPage();
        }
    }

    /**
     * Get the last page that should be available.
     *
     * @return int
     */
    public function lastPage()
    {
        if ($this->paginator != null) {
            return $this->paginator->lastPage();
        }
    }

    /**
     * Get the number of items to be displayed per page.
     *
     * @return int
     */
    public function perPage()
    {
        if ($this->paginator != null) {
            return $this->paginator->perPage();
        }
    }

    /**
     * Get the total number of items in the collection.
     *
     * @return int
     */
    public function total()
    {
        if ($this->paginator != null) {
            return $this->paginator->total();
        }
    }

    /**
     * Get the number of items for the current page.
     *
     * @return int
     */
    public function count()
    {
        if ($this->paginator != null) {
            return $this->paginator->count();
        }
    }

    /**
     * Get the number of the first item on the paginator.
     *
     * @return int
     */
    public function firstItem()
    {
        if ($this->paginator != null) {
            return $this->paginator->firstItem();
        }
    }

    /**
     * Get the number of the last item on the paginator.
     *
     * @return int
     */
    public function lastItem()
    {
        if ($this->paginator != null) {
            return $this->paginator->lastItem();
        }
    }

    /**
     * Get the URL for the next page.
     *
     * @return int
     */
    public function nextPageUrl()
    {
        if ($this->paginator != null) {
            return $this->paginator->nextPageUrl();
        }
    }

    /**
     * Get the URL for the previous page.
     *
     * @return int
     */
    public function previousPageUrl()
    {
        if ($this->paginator != null) {
            return $this->paginator->previousPageUrl();
        }
    }

    /**
     * Determines if there are more pages after the current page.
     *
     * @return int
     */
    public function hasMorePages()
    {
        if ($this->paginator != null) {
            return $this->paginator->hasMorePages();
        }
    }

    /**
     * Get the URL for the provided page.
     *
     * @return int
     */
    public function url($page)
    {
        if ($this->paginator != null) {
            return $this->paginator->url($page);
        }
    }

}