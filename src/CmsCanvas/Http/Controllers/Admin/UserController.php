<?php 

namespace CmsCanvas\Http\Controllers\Admin;

use View, Theme, Admin, DB, Validator, Auth, Hash, stdClass, Session, Content, Config, Storage;
use CmsCanvas\Models\User;
use CmsCanvas\Models\Role;
use CmsCanvas\Models\Timezone;
use CmsCanvas\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;

class UserController extends AdminController {

    /**
     * Display login screen
     *
     * @return View
     */
    public function getLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.index');
        }

        $this->layout->content = View::make('cmscanvas::admin.user.login');
        $this->layout->disableNotifications = true;
    }

    /**
     * Attempt to login
     *
     * @return Redirect
     */
    public function postLogin(Request $request)
    {
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'active' => 1,
        ];

        $rememberMe = ($request->input('remember_me')) ? true : false;

        if (Auth::attempt($credentials, $rememberMe)) {
            return redirect()->intended(route('admin.index'));
        }

        return redirect()->route('admin.user.login')
            ->withInput($request->except('password'))
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
        Session::flush();

        return redirect()->route('admin.user.login');
    }

    /**
     * Display all users
     *
     * @return View
     */
    public function getUsers(Request $request)
    {
        $content = View::make('cmscanvas::admin.user.users');

        $filter = User::getSessionFilter();
        $orderBy = User::getSessionOrderBy();

        $users = new User;
        $users = $users->with('roles')
            ->applyFilter($filter)
            ->applyOrderBy($orderBy);

        $roles = Role::all();

        $content->users = $users->paginate(50);
        $content->filter = new stdClass();
        $content->filter->filter = $filter;
        $content->orderBy = $orderBy;
        $content->roles = $roles;

        $this->layout->breadcrumbs = [$request->path() => 'Users'];
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

        return redirect()->route('admin.user.users');
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
            return redirect()->route('admin.user.users')
                ->with('notice', 'You must select at least one user to delete.');
        }

        $selected = array_values($selected);

        if (in_array(Auth::user()->id, $selected)) {
            return redirect()->route('admin.user.users')
                ->with('error', 'Failed to delete user(s) because you cannot delete yourself.');
        }

        User::destroy($selected);

        return redirect()->route('admin.user.users')
            ->with('message', 'The selected user(s) were sucessfully deleted.');;
    }

    /**
     * Display add user form
     *
     * @return View
     */
    public function getAdd()
    {
        // Routed to getEdit
    }

    /**
     * Create a new user
     *
     * @return View
     */
    public function postAdd()
    {
        // Routed to postEdit
    }

    /**
     * Display add user form
     *
     * @return View
     */
    public function getEdit(Request $request, $user = null)
    {
        Theme::addPackage('avatar_image_field');

        $roles = Role::all();
        $timezones = Timezone::all();
        
        $content = View::make('cmscanvas::admin.user.edit');
        $content->editMode = true;
        $content->roles = $roles;
        $content->timezones = $timezones;
        $content->user = $user;

        $this->layout->breadcrumbs = [
            'user' => 'Users', 
            $request->path() => (empty($user) ? 'Add' : 'Edit').' User'
        ];
        $this->layout->content = $content;
    }

    /**
     * Update an existing user
     *
     * @return View
     */
    public function postEdit(Request $request, $user = null)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => "required|email|unique:users,email".(($user == null) ? "" : ",{$user->id}"),
            'phone' => 'regex:/[0-9]{10,11}/'
        ];

        // Require password to be set for a new user
        if ($user == null || $request->input('password')) {
            $rules['password'] = 'required|confirmed|min:6';
            $rules['password_confirmation'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($user == null) {
                return redirect()->route('admin.user.add')
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            } else {
                return redirect()->route('admin.user.edit', $user->id)
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
        }

        $user = ($user == null) ? new User : $user;
        $user->fill($request->all());

        if ($request->input('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();
        $user->roles()->sync($request->input('user_roles', []));

        return redirect()->route('admin.user.users')
            ->with('message', "{$user->getFullName()} was successfully updated.");
    }

    /**
     * Generate a thumbnail from the specified image path
     *
     * @return string
     */
    public function postCreateAvatarThumbnail(Request $request)
    {
        return Content::thumbnail(
            $request->input('image_path'), 
            ['width' => 100, 'height' => 100, 'crop' => true, 'no_image' => Theme::asset('images/portrait.jpg')]
        );
    }

    /**
     * View a users's profile
     *
     * @return View
     */
    public function getProfile(Request $request, $user)
    {
        $content = View::make('cmscanvas::admin.user.profile');
        $content->user = $user;

        $this->layout->content = $content;
    }

    /**
     * Edit authenticated users's profile
     *
     * @return View
     */
    public function getEditProfile()
    {
        $content = View::make('cmscanvas::admin.user.account.editProfile');
        $content->user = Auth::user();
        $content->timezones = Timezone::all();;

        $this->layout->content = $content;
    }

    /**
     * Update an authenticated user's profile
     *
     * @return View
     */
    public function postEditProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'regex:/[0-9]{10,11}/'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('admin.user.account.editProfile')
                ->withInput()
                ->with('error', $validator->messages()->all());
        }

        $user->fill($request->all());
        $user->save();

        return redirect()->route('admin.user.account.editProfile')
            ->with('message', "Profile was successfully updated.");
    }

    /**
     * Update authenticated users's avatar
     *
     * @return View
     */
    public function getUpdateAvatar()
    {
        $content = View::make('cmscanvas::admin.user.account.updateAvatar');
        $content->user = Auth::user();

        $this->layout->content = $content; 
    }

    /**
     * Update authenticated users's avatar
     *
     * @return View
     */
    public function postUpdateAvatar(Request $request)
    {
        $rules = [
            'image_upload' => 'required|max:2048|mimes:jpeg,gif,png',
        ];

        $validator = Validator::make($request->all(), $rules);

        if (empty($request->input('remove_image')) && $validator->fails()) {
            return redirect()->route('admin.user.account.updateAvatar')
                ->withInput()
                ->with('error', $validator->messages()->all());
        }

        $user = Auth::user();

        // Delete the old avatar
        if (strpos($user->avatar, 'uploads/avatars/'.$user->id) !== false) {
            @unlink(public_path($user->avatar));
        }

        if (empty($request->input('remove_image'))) {
            $path = trim(Config::get('cmscanvas::config.avatars'), '/');
            $extension = $request->file('image_upload')->getClientOriginalExtension();
            $fileName = $user->id.'.'.$extension;
            $request->file('image_upload')->move(public_path($path), $fileName);

            $user->avatar = $path.'/'.$fileName;
        } else {
            $user->avatar = null;
        }

        $user->save();

        return redirect()->route('admin.user.account.updateAvatar')
            ->with('message', "Avatar was successfully updated.");
    }

    /**
     * Change authenticated users's password
     *
     * @return View
     */
    public function getChangePassword()
    {
        $content = View::make('cmscanvas::admin.user.account.changePassword');

        $this->layout->content = $content; 
    }

    /**
     * Change authenticated users's password
     *
     * @return View
     */
    public function postChangePassword(Request $request)
    {
        $rules = [
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('admin.user.account.changePassword')
                ->withInput()
                ->with('error', $validator->messages()->all());
        }

        $user = Auth::user();
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('admin.user.account.changePassword')
            ->with('message', "Password was successfully updated.");
    }

}