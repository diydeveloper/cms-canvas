<?php namespace CmsCanvas\Controllers\Admin;

use View, Theme, Admin, Request, Input, Redirect, DB, Validator, Auth, Hash, stdClass;
use CmsCanvas\Models\User;
use CmsCanvas\Models\User\Group;
use CmsCanvas\Models\Timezone;

class UserController extends AdminController {

    /**
     * Display login screen
     *
     * @return View
     */
    public function getLogin()
    {
        if (Auth::check())
        {
            return Redirect::route('admin.index');
        }

        $this->layout->content = View::make('cmscanvas::admin.user.login');
        $this->layout->disableNotifications = true;
    }

    /**
     * Attempt to login
     *
     * @return Redirect
     */
    public function postLogin()
    {
        $credentials = array(
            'email' => Input::get('email'),
            'password' => Input::get('password'),
            'active' => 1,
        );

        $rememberMe = (Input::get('remember_me')) ? true : false;

        if (Auth::attempt($credentials, $rememberMe))
        {
            return Redirect::route('admin.index');
        }

        return Redirect::route('admin.user.login')->withInput(Input::except('password'))
            ->with('error', 'Login failed!');
    }

    /**
     * Log a user out
     *
     * @return Redirect
     */
    public function getLogout()
    {
        Auth::logout();

        return Redirect::route('admin.user.login');
    }

    /**
     * Display all users
     *
     * @return View
     */
    public function getUsers()
    {
        $content = View::make('cmscanvas::admin.user.users');

        $filter = User::getSessionFilter();
        $orderBy = User::getSessionOrderBy();

        $users = new User;
        $users = $users->join('user_groups', 'users.user_group_id', '=', 'user_groups.id')
            ->select(DB::raw('users.*, user_groups.name as group_name'))
            ->applyFilter($filter)
            ->applyOrderBy($orderBy);

        $groups = Group::all();

        $content->users = $users->paginate(50);
        $content->filter = new stdClass();
        $content->filter->filter = $filter;
        $content->orderBy = $orderBy;
        $content->groupSelectOptions = $groups->getKeyValueArray('id', 'name');

        $this->layout->breadcrumbs = array(Request::path() => 'Users');
        $this->layout->content = $content;

    }

    /**
     * Saves filter and order by requests to the current user's session
     *
     * @return View
     */
    public function postUsers()
    {
        User::processFilterRequest();

        return Redirect::route('admin.user.users');
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
            return Redirect::route('admin.user.users')
                ->with('notice', 'You must select at least one user to delete.');
        }

        $selected = array_values($selected);

        if (in_array(Auth::user()->id, $selected))
        {
            return Redirect::route('admin.user.users')
                ->with('error', 'Failed to delete user(s) because you cannot delete yourself.');
        }

        User::destroy($selected);

        return Redirect::route('admin.user.users')
            ->with('message', 'The selected user(s) were sucessfully deleted.');;
    }

    /**
     * Display add user form
     *
     * @return View
     */
    public function getAdd()
    {
        $groups = Group::all();
        $timezones = Timezone::all();

        $content = View::make('cmscanvas::admin.user.edit');
        $content->editMode = false;
        $content->groupSelectOptions = $groups->getKeyValueArray('id', 'name');
        $content->timezoneSelectOptions = $timezones->getKeyValueArray('id', 'name');

        $this->layout->content = $content;
    }

    /**
     * Display add user form
     *
     * @return View
     */
    public function getEdit($user)
    {
        $groups = Group::all();
        $timezones = Timezone::all();
        
        $content = View::make('cmscanvas::admin.user.edit');
        $content->editMode = true;
        $content->groupSelectOptions = $groups->getKeyValueArray('id', 'name');
        $content->timezoneSelectOptions = $timezones->getKeyValueArray('id', 'name');
        $content->user = $user;

        $this->layout->content = $content;
    }

    /**
     * Create a new user
     *
     * @return View
     */
    public function postAdd()
    {
        $rules = array(
            'user_group_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'regex:/[0-9]{10,11}/'
        );

        $attributeNames = array(
            'user_group_id' => 'group'
        );

        $validator = Validator::make(Input::all(), $rules, array(), $attributeNames);

        if ($validator->fails())
        {
            return Redirect::route('admin.user.add')
                ->withInput()
                ->with('error', $validator->messages()->all());
        }

        $user = new User();
        $user->fill(Input::all());
        $user->password = Hash::make(Input::get('password'));
        $user->save();

        return Redirect::route('admin.user.users')
            ->with('message', "{$user->getFullName()} was successfully added.");
    }

    /**
     * Update an existing user
     *
     * @return View
     */
    public function postEdit($user)
    {
        $rules = array(
            'user_group_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'regex:/[0-9]{10,11}/'
        );

        if (Input::get('password'))
        {
            $rules['password'] = 'required|confirmed|min:6';
            $rules['password_confirmation'] = 'required';
        }

        $attributeNames = array(
            'user_group_id' => 'group'
        );

        $validator = Validator::make(Input::all(), $rules, array(), $attributeNames);

        if ($validator->fails())
        {
            return Redirect::route('admin.user.edit', $user->id)
                ->withInput()
                ->with('error', $validator->messages()->all());
        }

        $user->fill(Input::all());

        if (Input::get('password'))
        {
            $user->password = Hash::make(Input::get('password'));
        }

        $user->save();

        return Redirect::route('admin.user.users')
            ->with('message', "{$user->getFullName()} was successfully updated.");
    }

}