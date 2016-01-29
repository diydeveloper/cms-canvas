<?php 

namespace CmsCanvas\Http\Controllers\Admin\User;

use View, Theme, Admin, Session, Validator, stdClass;
use CmsCanvas\Http\Controllers\Admin\AdminController;
use CmsCanvas\Models\Role;
use CmsCanvas\Models\Permission;
use CmsCanvas\Container\Database\OrderBy;
use Illuminate\Http\Request;

class RoleController extends AdminController {

    /**
     * Display all roles
     *
     * @return View
     */
    public function getRoles(Request $request)
    {
        $content = View::make('cmscanvas::admin.user.role.roles');

        $filter = Role::getSessionFilter();
        $orderBy = Role::getSessionOrderBy();

        $roles = new Role;
        $roles = $roles->applyFilter($filter)
            ->applyOrderBy($orderBy);

        $content->roles = $roles->paginate(50);
        $content->filter = new stdClass();
        $content->filter->filter = $filter;
        $content->orderBy = $orderBy;

        $this->layout->breadcrumbs = ['user' => 'Users', $request->path() => 'Role'];
        $this->layout->content = $content;
    }

    /**
     * Saves the filter request to the session
     *
     * @return View
     */
    public function postRoles()
    {
        Role::processFilterRequest();

        return redirect()->route('admin.user.role.roles');
    }

    /**
     * Deletes user(s) that are posted in the selected array
     *
     * @return View
     */
    public function postDelete(Request $request)
    {
        $selected = $request->input('selected');

        if (empty($selected) || ! is_array($selected)) {
            return redirect()->route('admin.user.role.roles')
                ->with('notice', 'You must select at least one role to delete.');
        }

        $selected = array_values($selected);

        $roles = Role::whereIn('id', $selected)
            ->get();

        foreach ($roles as $role) {
            if ($role->users()->count() > 0) {
                return redirect()->route('admin.user.role.roles')
                    ->with('error', 'Failed to delete role(s) because one or more of the selected has users still assigned.');
            }
        }

        foreach ($roles as $role) {
            $role->delete();
        }

        return redirect()->route('admin.user.role.roles')
            ->with('message', 'The selected role(s) were sucessfully deleted.');;
    }

    /**
     * Display add role form
     *
     * @return View
     */
    public function getAdd()
    {
        // Routed to getEdit
    }

    /**
     * Create a new role
     *
     * @return View
     */
    public function postAdd()
    {
        // Routed to postEdit
    }

    /**
     * Display add role form
     *
     * @return View
     */
    public function getEdit(Request $request, $role = null)
    {
        $permissions = Permission::orderBy('name', 'asc')->get();

        $content = View::make('cmscanvas::admin.user.role.edit');
        $content->role = $role;
        $content->permissions = $permissions;

        $this->layout->breadcrumbs = [
            'user/role' => 'Roles', 
            $request->path() => (empty($role) ? 'Add' : 'Edit').' Role'
        ];
        $this->layout->content = $content;
    }

    /**
     * Update an existing user
     *
     * @return View
     */
    public function postEdit(Request $request, $role = null)
    {
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($role == null) {
                return redirect()->route('admin.user.role.add')
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            } else {
                return redirect()->route('admin.user.role.edit', $role->id)
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
        }

        $role = ($role == null) ? new Role : $role;
        $role->fill($request->all());
        $role->save();
        $role->permissions()->sync($request->input('role_permissions', []));

        return redirect()->route('admin.user.role.roles')
            ->with('message', "{$role->name} was successfully updated.");
    }

}