<?php namespace CmsCanvas\Content;

use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Content\Entry\RenderCollection;

class Entries {

    /**
     * @var int
     */
    protected $entry;

    /**
     * @var int
     */
    protected $paginate;

    /**
     * @var int
     */
    protected $simplePaginate;

    /**
     * @param array $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->entry = new Entry;

        foreach ($config as $key => $value)
        {
            switch ($key) {
                case 'entry_id':
                    $this->setEntryId($value);
                    break;

                case 'content_type':
                    $this->setContentType($value);
                    break;

                case 'order_by':
                    $this->setOrderBy($value);
                    break;
                
                case 'limit':
                    $this->setLimit($value);
                    break;

                case 'offset':
                    $this->setOffset($value);
                    break;

                case 'paginate':
                    $this->paginate = $value;
                    break;

                case 'simple_paginate':
                    $this->simplePaginate = $value;
                    break;
            }
        }
    }

    public function get()
    {
        if ($this->paginate !== null)
        {
            $entries = $this->entry->paginate($this->paginate);
        }
        else if ($this->simplePaginate !== null)
        {
            $entries = $this->entry->simplePaginate($this->simplePaginate);
        }
        else
        {
            $entries = $this->entry->get();
        }


        return new RenderCollection($entries);
    }

    public function setContentType($contentType)
    {
        $this->entry = $this->entry->whereHas('contentType', function($query) use($contentType)
        {
            $query->where('short_name', $contentType);

        });
    }

    public function setLimit($limit)
    {
        $this->entry = $this->entry->take($limit);
    }

    public function setOffset($offset)
    {
        $this->entry = $this->entry->skip($offset);
    }

    public function setEntryId($entryId)
    {
        $this->entry = $this->entry->where('id', $entryId);
    }

}