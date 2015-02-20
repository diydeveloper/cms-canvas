<?php namespace CmsCanvas\Http\Controllers\Admin\User;

use View, Theme, Admin, Session, Redirect, Validator, Request, Input, stdClass;
use CmsCanvas\Http\Controllers\Admin\AdminController;
use CmsCanvas\Models\Permission;
use CmsCanvas\Models\Role;
use CmsCanvas\Container\Database\OrderBy;

class PermissionController extends AdminController {

    /**
     * Display all permissions
     *
     * @return View
     */
    public function getPermissions()
    {
        $content = View::make('cmscanvas::admin.user.permission.permissions');

        $filter = Permission::getSessionFilter();
        $orderBy = Permission::getSessionOrderBy();

        $permissions = new Permission;
        $permissions = $permissions->applyFilter($filter)
            ->applyOrderBy($orderBy);

        $content->permissions = $permissions->paginate(50);
        $content->filter = new stdClass();
        $content->filter->filter = $filter;
        $content->orderBy = $orderBy;

        $this->layout->breadcrumbs = array('user' => 'Users', Request::path() => 'Permissions');
        $this->layout->content = $content;
    }

    /**
     * Saves the filter request to the session
     *
     * @return View
     */
    public function postPermissions()
    {
        Permission::processFilterRequest();

        return Redirect::route('admin.user.permission.permissions');
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
            return Redirect::route('admin.user.permission.permissions')
                ->with('notice', 'You must select at least one permission to delete.');
        }

        $selected = array_values($selected);

        $permissions = Permission::whereIn('id', $selected)
            ->get();

        foreach ($permissions as $permission)
        {
            $permission->delete();
        }

        return Redirect::route('admin.user.permission.permissions')
            ->with('message', 'The selected permission(s) were sucessfully deleted.');;
    }

    /**
     * Display add permission form
     *
     * @return View
     */
    public function getAdd()
    {
        // Routed to getEdit
    }

    /**
     * Create a new permission
     *
     * @return View
     */
    public function postAdd()
    {
        // Routed to postEdit
    }

    /**
     * Display add permission form
     *
     * @return View
     */
    public function getEdit($permission = null)
    {
        $roles = Role::orderBy('name', 'asc')->get();

        $content = View::make('cmscanvas::admin.user.permission.edit');
        $content->permission = $permission;
        $content->roles = $roles;

        $this->layout->content = $content;
    }

    /**
     * Update an existing permission
     *
     * @return View
     */
    public function postEdit($permission = null)
    {
        $rules = array(
            'name' => 'required|max:255',
            'key_name' => 'required|max:50'
                ."|unique:permissions,key_name".(($permission == null) ? "" : ",{$permission->id}"),
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            if ($permission == null)
            {
                return Redirect::route('admin.user.permission.add')
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
            else
            {
                return Redirect::route('admin.user.permission.edit', $permission->id)
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
        }

        $permission = ($permission == null) ? new Permission : $permission;
        $permission->fill(Input::all());
        $permission->key_name = strtoupper($permission->key_name);
        $permission->save();
        $permission->roles()->sync(Input::get('role_permissions', array()));

        return Redirect::route('admin.user.permission.permissions')
            ->with('message', "{$permission->name} was successfully updated.");
    }

}