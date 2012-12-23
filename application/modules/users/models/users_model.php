<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Users_model extends DataMapper
{	
    public $table = "users";
    public $has_one = array(
        'groups' => array(
            'class' => 'groups_model',
            'other_field' => 'users',
            'join_other_as' => 'group',
            'model_path' => 'application/modules/users',
        ),
    );

    /*
     * Login
     *
     * Attempts user login
     *
     * @return bool
     */
    function login($email, $password)
    {
        $this->load->helper('security');
        $CI =& get_instance();

        // Database query to lookup email and password
        $Login_result = new Users_model();
        $Login_result->where("email", $email)
            ->where("password", do_hash($this->config->item('encryption_key') . $password, 'md5'))
            ->get();

        // if email and password found checks permissions and sets session data
        if ($Login_result->exists())
        {
            if ( ! $Login_result->enabled)
            {
                $CI->session->set_flashdata('message', '<p class="attention">Your account has been disabled.</p>');
            }
            elseif ($CI->settings->users_module->email_activation && ! $Login_result->activated)
            {
                $CI->session->set_flashdata('message', '<p class="attention">Your account has been not yet been activated.</p>');
            }
            else
            {
                $Login_result->last_login = date("Y-m-d H:i:s");
                $Login_result->create_session();
                $Login_result->save();

                if ($CI->input->post('remember_me'))
                {
                    $this->set_remember_me($Login_result);
                }

                return TRUE;
            }
        }
        else
        {
            $CI->session->set_flashdata('message', '<p class="error">No match for Email and/or Password.</p>');
        }

        return FALSE;
    }

    // --------------------------------------------------------------------

    /*
     * Full Name
     *
     * Returns user's first and last name seperated by a space
     *
     * @return string
     */
    function full_name()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // --------------------------------------------------------------------

    /*
     * Create Session
     *
     * Creates a session as user
     * If admin_id passed, creates an admin logged in as user
     *
     * @param int
     * @return void
     */
    function create_session($admin_id = null)
    {
        $CI =& get_instance();

        $User_class = new stdClass();
        $User_class->id = $this->id;
        $User_class->first_name = $this->first_name;
        $User_class->last_name = $this->last_name;
        $User_class->group_id = $this->group_id;
        $User_class->last_login = $this->last_login;
        $User_class->email = $this->email;

        $this->groups->get();

        $Group_class = new stdClass();
        $Group_class->id = $this->groups->id;
        $Group_class->namne = $this->groups->name;
        $Group_class->type = $this->groups->type;
        $Group_class->permissions = $this->groups->permissions;

        // Used to allow admin login as user
        if ( ! empty($admin_id))
        {
            $User_class->admin_id = $admin_id;
        }

        $CI->session->set_userdata('user_session', $User_class);
        $CI->session->set_userdata('group_session', $Group_class);
    }

    // --------------------------------------------------------------------

    /*
     * Check Remember Me
     *
     * Checks if user has a remember me cookie set 
     * and logs user in if validation is true
     *
     * @return bool
     */
    function check_remember_me()
    {
        $CI =& get_instance();
        $remember_me = $CI->input->cookie('remember_me');

        if ($remember_me !== FALSE)
        {
            $remember_me = @unserialize($remember_me);

            // Insure we have all the data we need
            if ( ! isset($remember_me['email']) || ! isset($remember_me['token']))
            {
                return FALSE;
            }

            // Database query to lookup email and password
            $User = new Users_model();
            $User->where("email", $remember_me['email'])->get();

            // If user found validate token and login
            if ($User->exists() && $remember_me['token'] == md5($User->last_login . $CI->config->item('encryption_key') . $User->password))
            {
                if ( ! $User->enabled || ($CI->settings->users_module->email_activation && ! $User->activated))
                {
                    return FALSE;
                }

                $User->last_login = date("Y-m-d H:i:s");
                $User->create_session();
                $User->save();
                $this->set_remember_me($User);
                return TRUE;
            }
        }

        return FALSE;
    }

    // --------------------------------------------------------------------

    /*
     * Set Remember Me
     *
     * Sets a remember  me cookie on the clients computer
     *
     * @param object
     * @return void
     */
    function set_remember_me($User)
    {
        $CI =& get_instance();

        $cookie = array(
            'name'   => 'remember_me',
            'value'  => serialize(array(
                'email' => $User->email,
                'token' => md5($User->last_login . $CI->config->item('encryption_key') . $User->password),
            )),
            'expire' => '1209600',
        );

        $CI->input->set_cookie($cookie);
    }

    // --------------------------------------------------------------------

    /*
     * Destroy Remember Me
     *
     * Destroy remember me cookie on the clients computer
     *
     * @return void
     */
    function destroy_remember_me()
    {
        $CI =& get_instance();

        $cookie = array(
            'name'   => 'remember_me',
            'value'  => '',
            'expire' => '',
        );

        $CI->input->set_cookie($cookie);
    }

    // --------------------------------------------------------------------

    /*
     * Get Session User
     *
     * Returns Session User's updated DB Record
     *
     * @return object
     */
    function get_session_user()
    {
        $CI =& get_instance();

        // Get user_id from session
        $user_id = $CI->secure->get_user_session()->id;

        $User_model = new Users_model();

        return $User_model->get_by_id($user_id);
    }
}
