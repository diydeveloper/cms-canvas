<?php 

namespace CmsCanvas\Models\Content\Navigation;

use CmsCanvas\Database\Eloquent\Model;
use CmsCanvas\Content\Navigation\Item\Render;
use CmsCanvas\Content\Navigation\Item\RenderCollection;

class Item extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'navigation_items';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'entry_id',
        'url',
        'type',
        'id_attribute',
        'class_attribute',
        'target_attribute',
        'children_visibility_id',
        'disable_current_flag',
        'disable_current_ancestor_flag',
        'hidden_flag',
    ];

    /**
     * The columns that can NOT be mass-assigned.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Set true if the item links to the current url
     *
     * @var bool
     */
    protected $currentItemFlag = false;

    /**
     * Set true if the item is an ancestor of the current item 
     *
     * @var bool
     */
    protected $currentItemAncestorFlag = false;

    /**
     * Set true if the item is solo or first in a collection
     *
     * @var bool
     */
    protected $firstFlag = false;

    /**
     * Set true if the item is solo or last in a collection
     *
     * @var bool
     */
    protected $lastFlag = false;

    /**
     * Defines a one to many relationship with navigation items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('CmsCanvas\Models\Content\Navigation\Item', 'parent_id', 'id');
    }

    /**
     * Defines a one to one relationship with navigation items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent()
    {
        return $this->hasOne('CmsCanvas\Models\Content\Navigation\Item', 'id', 'parent_id');
    }

    /**
     * Defines a one to many relationship with entries
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function entry()
    {
        return $this->hasOne('CmsCanvas\Models\Content\Entry', 'id', 'entry_id');
    }

    /**
     * Returns a render instance
     *
     * @return \CmsCanvas\Content\Navigation\Item\Render
     */
    public function render()
    {
        return new Render($this);
    }

    /**
     * Returns a render collection instance of the children
     *
     * @return \CmsCanvas\Content\Navigation\Item\RenderCollection
     */
    public function renderChildren()
    {
        return new RenderCollection($this->children);
    }

    /**
     * Generates a view with the navigation item
     *
     * @return string
     */
    public function renderContents()
    {
        $contents = '<li'.$this->getListItemAttributes().'>';
        $contents .= '<a'.$this->getAnchorAttributes().'>';
        $contents .= $this->getTitle();
        $contents .= '</a>';

        if ($this->isChildrenLoaded() && count($this->children) > 0) {
            $contents .= $this->renderChildren();
        }

        $contents .= '</li>';

        return $contents;
    }

    /**
     * Builds html attributes string for the <li> tag
     *
     * @return string
     */
    public function getListItemAttributes()
    {
        $attributes = '';

        if (!empty($this->id_attribute)) {
            $attributes .= ' id="'.$this->id_attribute.'"';
        }

        $classNames = $this->getHtmlClassNames();

        if (count($classNames) > 0) {
            $attributes .= ' class="'.implode(' ', $classNames).'"';
        }

        return $attributes;
    }

    /**
     * Builds html attributes string for the <a> tag
     *
     * @return string
     */
    public function getAnchorAttributes()
    {
        $attributes = '';

        if (!empty($this->target_attribute)) {
            $attributes .= ' target="'.$this->target_attribute.'"';
        }

        $attributes .= ' href="'.$this->getUrl().'"';

        return $attributes;
    }

    /**
     * Generates an array of html class names for the current item
     *
     * @return array
     */
    public function getHtmlClassNames()
    {
        $classNames = [];

        if ($this->firstFlag) {
            $classNames[] = 'first';
        }

        if ($this->lastFlag) {
            $classNames[] = 'last';
        }

        if ($this->currentItemFlag) {
            $classNames[] = 'current-item';
        }

        if ($this->currentItemAncestorFlag) {
            $classNames[] = 'current-item-ancestor';
        }

        if (! empty($this->class_attribute)) {
            $classNames = array_merge($classNames, explode(' ', $this->class_attribute));
        }

        return $classNames;
    }

    /**
     * Returns the title for the item
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->title == null) {
            return $this->entry->title;
        } 

        return $this->title;
    }

    /**
     * Returns the full url for the item
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->entry != null) {
            return url($this->entry->getPreferredRoute());
        } 

        if ($this->url != null) {
            $parsed = parse_url($this->url);

            if (empty($parsed['scheme'])) {
                return url($this->url);
            } else {
                return $this->url;
            }
        }

        return null;
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
     * Sets the currentItemFlag class variable
     *
     * @param bool $value
     * @return void
     */
    public function setCurrentItemFlag($value)
    {
        $this->currentItemFlag = (bool) $value;
    }

    /**
     * Returns currentItemFlag class variable
     *
     * @return bool
     */
    public function isCurrentItem()
    {
        return $this->currentItemFlag;
    }

    /**
     * Sets the currentItemAncestorFlag class variable
     *
     * @param bool $value
     * @return void
     */
    public function setCurrentItemAncestorFlag($value)
    {
        $this->currentItemAncestorFlag = (bool) $value;
    }

    /**
     * Returns currentItemAncestorFlag class variable
     *
     * @return bool
     */
    public function isCurrentItemAncestor()
    {
        return $this->currentItemAncestorFlag;
    }

    /**
     * Returns children only if it has already been loaded
     *
     * @return \CmsCanvas\Models\Content\Navigation\Item|collection
     */
    public function getLoadedChildren()
    {
        if ($this->isChildrenLoaded()) {
            return $this->children;
        }

        return $this->newCollection(); 
    }

    /**
     * Checks if children has been loaded
     *
     * @return bool
     */
    public function isChildrenLoaded() 
    {
        return isset($this->relations['children']);
    }

}