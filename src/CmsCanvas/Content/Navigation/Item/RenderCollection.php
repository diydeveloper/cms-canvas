<?php 

namespace CmsCanvas\Content\Navigation\Item;

use CmsCanvas\Database\Eloquent\Collection as CmsCanvasCollection;

class RenderCollection extends CmsCanvasCollection {

    /**
     * @var mixed
     */
    protected $builder;

    /**
     * Contructor to set collection of navigation items
     *
     * @param  \CmsCanvas\Content\Navigation\Builder\Item|array  $itemBuilders
     * @param  mixed  $builder
     * @return void
     */
    public function __construct(array $itemBuilders, $builder = null)
    {
        $this->builder = $builder;

        foreach ($itemBuilders as $itemBuilder) {
            $this->items[] = $itemBuilder->render();
        }
    }

    /**
     * Magic method to render the items as a string
     *
     * @return string
     */
    public function __toString()
    {
        $attributes = '';
        $contents = '';

        if ($this->builder != null) {
            $attributes = $this->builder->getAttributes();
        }

        if (count($this->items) > 0) {
            $contents .= '<ul'.$attributes.'>';

            foreach ($this->items as $item) {
                $contents .= (string) $item;
            }

            $contents .= '</ul>';
        }

        return $contents;
    }

}