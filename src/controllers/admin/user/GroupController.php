<?php namespace CmsCanvas\Controllers\Admin\User;

use View, Theme, Admin, Session, Redirect, Validator, Request, Input, stdClass;
use CmsCanvas\Controllers\Admin\AdminController;
use CmsCanvas\Models\User\Group;
use CmsCanvas\Container\Database\OrderBy;

class GroupController extends AdminController {

    /**
     * Display all groups
     *
     * @return View
     */
    public function getGroups()
    {
        $content = View::make('cmscanvas::admin.user.group.groups');

        $filter = Group::getSessionFilter();
        $orderBy = Group::getSessionOrderBy();

        $groups = new Group;
        $groups = $groups->applyFilter($filter)
            ->applyOrderBy($orderBy);

        $content->groups = $groups->paginate(50);
        $content->filter = new stdClass();
        $content->filter->filter = $filter;
        $content->orderBy = $orderBy;

        $this->layout->breadcrumbs = array('user' => 'Users', Request::path() => 'Groups');
        $this->layout->content = $content;
    }

    /**
     * Saves the filter request to the session
     *
     * @return View
     */
    public function postGroups()
    {
        Group::processFilterRequest();

        return Redirect::route('admin.user.group.groups');
    }

    /**
     * Deletes user(s) that are posted in the selected array
     *
     * @return View
     */
    public function postDelete()
    {
        $selected = Input::get('selected');

        if (empty($selected) || ! is_array($selected)) {
            return Redirect::route('admin.user.group.groups')
                ->with('notice', 'You must select at least one group to delete.');
        }

        $selected = array_values($selected);

        $userGroups = Group::whereIn('id', $selected)
            ->get();

        foreach ($userGroups as $group) 
        {
            if ($group->users()->count() > 0)
            {
                return Redirect::route('admin.user.group.groups')
                    ->with('error', 'Failed to delete group(s) because one or more of the selected has users still assigned.');
            }
        }

        foreach ($userGroups as $group)
        {
            $group->delete();
        }

        return Redirect::route('admin.user.group.groups')
            ->with('message', 'The selected group(s) were sucessfully deleted.');;
    }

    /**
     * Display add group form
     *
     * @return View
     */
    public function getAdd()
    {
        $content = View::make('cmscanvas::admin.user.group.edit');

        $this->layout->content = $content;
    }

    /**
     * Display add group form
     *
     * @return View
     */
    public function getEdit($userGroup)
    {
        $content = View::make('cmscanvas::admin.user.group.edit');
        $content->userGroup = $userGroup;

        $this->layout->content = $content;
    }

    /**
     * Create a new group
     *
     * @return View
     */
    public function postAdd()
    {
        $rules = array(
            'name' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            return Redirect::route('admin.user.group.add')
                ->withInput()
                ->with('error', $validator->messages()->all());
        }

        $userGroup = new Group();
        $userGroup->fill(Input::all());
        $userGroup->save();

        return Redirect::route('admin.user.group.groups')
            ->with('message', "{$userGroup->name} was successfully added.");
    }

    /**
     * Update an existing user
     *
     * @return View
     */
    public function postEdit($userGroup)
    {
        $rules = array(
            'name' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            return Redirect::route('admin.user.group.edit', $userGroup->id)
                ->withInput()
                ->with('error', $validator->messages()->all());
        }

        $userGroup->fill(Input::all());
        $userGroup->save();

        return Redirect::route('admin.user.group.groups')
            ->with('message', "{$userGroup->name} was successfully updated.");
    }

}