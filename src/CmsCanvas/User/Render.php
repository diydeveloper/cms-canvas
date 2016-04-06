<?php 

namespace CmsCanvas\User;

use CmsCanvas\Models\User as UserModel;
use CmsCanvas\Support\Contracts\View\Render as ViewRender;

class Render implements ViewRender {

    /**
     * The entry to render from
     *
     * @var \CmsCanvas\Models\User
     */
    protected $user;

    /**
     * Constructor fo rthe entry render
     *
     * @param  \CmsCanvas\Models\User  $user
     * @return void
     */
    public function __construct(UserModel $user)
    {
        $this->user = $user;
    }

    /**
     * Magic method to retrive rendered data
     *
     * @param  string $name
     * @return null
     */
    public function __get($name)
    {
        return $this->user->getAttributeValue($name);
    }

    /**
     * Magic method to trigger twig to call __get
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        return true;
    }

    /**
     * Magic method to render the entry as a string
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->fullName();
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Returns the users first and last name sperated by a space
     *
     * @return string
     */
    public function fullName()
    {
        return $this->user->getFullName();
    }

    /**
     * Returns the resource type for this render
     *
     * @return string
     */
    public function getResourceType()
    {
        return 'USER';
    }

}