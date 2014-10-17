<?php namespace CmsCanvas\Content;

use CmsCanvas\Models\Content\Entry;

class Entries {

    /**
     * @var int
     */
    protected $entry;

    /**
     * @return void
     */
    public function __construct($config)
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
            }
        }
    }

    public function get()
    {
        $entries = $this->entry->get();

        // $renderings = '';
        // foreach ($entries as $entry) {
        //     $renderings .= $entry->render();
        // }
        return $entries;
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