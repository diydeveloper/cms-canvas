<?php namespace CmsCanvas\Content;

use CmsCanvas\Models\Content\Entry;

class Entries {

    /**
     * @var int
     */
    protected $entryId

    /**
     * @var string
     */
    protected $contentType

    /**
     * @var int
     */
    protected $limit

    /**
     * @var int
     */
    protected $offset

    /**
     * @var string
     */
    protected $orderBy

    /**
     * @var string
     */
    protected $sort

    /**
     * @return void
     */
    public function buildFromArray($config)
    {
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

    }

}