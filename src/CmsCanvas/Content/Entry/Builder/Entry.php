<?php 

namespace CmsCanvas\Content\Entry\Builder;

use Auth, StringView, Route;
use CmsCanvas\Models\Content\Entry\Status;
use CmsCanvas\Models\Content\Entry as EntryModel;
use CmsCanvas\Content\Entry\Render;
use CmsCanvas\Content\Entry\RenderCollection;

class Entry {

    /**
     * @var \CmsCanvas\Models\Content\Entry
     */
    protected $entry;

    /**
     * Parameters added to the route
     *
     * @var array
     */
    protected $parameters;

    /**
     * Rendered data
     *
     * @var array
     */
    protected $renderedData;

    /**
     * Rendered content
     *
     * @var \TwigBridge\StringView\StringView
     */
    protected $renderContents;

    /**
     * Set true if the entry is solo or first in a collection
     *
     * @var bool
     */
    protected $firstFlag = true;

    /**
     * Set true if the entry is solo or last in a collection
     *
     * @var bool
     */
    protected $lastFlag = true;

    /**
     * The position the entry is in a collection
     *
     * @var bool
     */
    protected $index = 0;

    /**
     * Constructor
     *
     * @param  \CmsCanvas\Models\Content\Entry  $entry
     * @param  array $parameters
     * @return void
     */
    public function __construct(EntryModel $entry, $parameters = [])
    {
        $this->entry = $entry;
        $this->setParameters($parameters);
        $this->renderedData = $this->entry->getRenderedData();
    }

    /**
     * Returns the entry model instance
     *
     * @return \CmsCanvas\Models\Content\Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Returns a render instance
     *
     * @return \CmsCanvas\Content\Entry\Render
     */
    public function render()
    {
        if ($this->entry->entry_status_id == Status::DISABLED) {
            return abort(404);
        }

        if ($this->entry->entry_status_id == Status::DRAFT) {
            $user = Auth::user();

            if ($user == null || ! $user->can('ADMIN_ENTRY_VIEW')) {
                return abort(404);
            }
        }

        $render = new Render($this);
        $this->setParameter('self', $render);

        return $render;
    }

    /**
     * Generates a view with the entry's data
     *
     * @return string
     */
    public function renderContents()
    {
        $data = array_merge($this->renderedData, $this->parameters);

        $template = ($this->entry->contentType->layout === null) ? '' : $this->entry->contentType->layout;
        $content = StringView::make($template)
            ->cacheKey($this->entry->contentType->getRouteName())
            ->updatedAt($this->entry->contentType->updated_at->timestamp)
            ->with($data);

        if ($this->entry->template_flag) {
            $content = StringView::make((string) $content)
                ->cacheKey($this->entry->getRouteName())
                ->updatedAt(max($this->entry->updated_at->timestamp, $this->entry->contentType->updated_at->timestamp))
                ->with($data);
        }

        return $content;
    }

    /**
     * Returns parameters and rendered data
     *
     * @param  string  $key
     * @return mixed
     */
    public function getData($key)
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        if (isset($this->renderedData[$key])) {
            return $this->renderedData[$key];
        }        

        return null;
    }

    /**
     * Adds a parameter to the paramaters array
     *
     * @param  string  $key
     * @param  string  $value
     * @return self
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }


    /**
     * Set the paramaters array
     *
     * @param  array  $value
     * @return self
     */
    protected function setParameters(array $parameters)
    {
        $route = Route::current();
        if ($route != null) {
            $this->parameters = array_merge($route->parameters(), $parameters);
        } else {
            $this->parameters = $parameters;
        }

        return $this;
    }

    /**
     * Sets the entry's position in the collection
     *
     * @param int $value
     * @return void
     */
    public function setIndex($value)
    {
        $this->index = $value;
    }

    /**
     * Sets the firstFlag class variable
     *
     * @param bool $value
     * @return void
     */
    public function setFirstFlag($value)
    {
        $this->firstFlag = (bool) $value;
    }

    /**
     * Sets the lastFlag class variable
     *
     * @param bool $value
     * @return void
     */
    public function setLastFlag($value)
    {
        $this->lastFlag = (bool) $value;
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

}