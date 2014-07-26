<?php namespace CmsCanvas\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Relations\Pivot as EloquentPivot;

class Pivot extends EloquentPivot {

    /**
     * Returns parent model of the relationship.
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getParent()
    {
        return $this->parent;
    }

}
