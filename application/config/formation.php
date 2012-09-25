<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Formation
 *
 * A CodeIgniter library that creates forms via a config file.  It
 * also contains functions to allow for creation of forms on the fly.
 *
 * @package     Formation
 * @author      Dan Horrigan <http://dhorrigan.com>
 * @license     Apache License v2.0
 * @copyright   2010 Dan Horrigan
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
| -------------------------------------------------------------------
| FORMATION CONFIG
| -------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Form Wrapper tags
|--------------------------------------------------------------------------
|
| These tags will wrap the different elements in the form.
|
| Example:
| $formation['form_wrapper_open']   = '<ul>';
| $formation['form_wrapper_close']  = '</ul>';
|
| $formation['input_wrapper_open']  = '<li>';
| $formation['input_wrapper_close'] = '</li>';
|
| $formation['label_wrapper_open']  = '<label for="%s">';
| $formation['label_wrapper_close'] = '</label>';
|
| $formation['required_location']   = 'after';
| $formation['required_tag']        = '<span class="required">*</span>';
|
| Would result in the following form:
| <form action="" method="post">
| <ul>
|     <li>
|         <label for="first_name">First Name</label>
|         <input type="text" name="first_name" id="first_name" value="" />
|     </li>
| </ul>
| </form>
*/
$formation['form_wrapper_open']     = '';
$formation['form_wrapper_close']    = '';

$formation['input_wrapper_open']    = '<div>';
$formation['input_wrapper_close']   = '</div>';

$formation['label_wrapper_open']    = '<label for="%s">';
$formation['label_wrapper_close']   = '</label>';

$formation['required_location']     = 'label_before';
$formation['required_tag']          = '<span class="required">*</span> ';

$formation['forms']['create_user'] = array(
    //'action'    => 'users/create',
    'fields'    => array(
        'id'    => array(
            'type'      => 'hidden',
            'value'     => ''
        ),
        'myusername'   => array(
            'label'     => 'Username',
            'type'      => 'text',
            'size'      => '40',
            'validation' => 'required|trim',
        ),
        'first_name' => array(
            'label'     => 'First Name',
            'type'      => 'text',
            'size'      => '40',
            'validation' => 'required|trim',
        ),
        'last_name'  => array(
            'id' => 'sucky',
            'class' => 'mysucky',
            'label'     => 'Last Name',
            'type'      => 'text',
            'size'      => '40',
            'value'     => ''
        ),
        'password'   => array(
            'label'     => 'Password',
            'type'      => 'password',
            'size'      => '40',
            'value'     => ''
        ),
        'public' => array(
            'type'      => 'radio',
            'label'     => 'Public?',
            'items'     => array(
                array(
                    'label'     => 'Yes',
                    'checked'   => 'checked',
                    'value'     => '1',
                ),
                array(
                    'label'     => 'No',
                    'value'     => '0',
                )
            )
        ),
        'display_options' => array(
            'type'      => 'checkbox',
            'label'     => 'Display Options',
            'items'     => array(
                array(
                    'label'     => 'Display Email',
                    'checked'   => 'checked',
                    'value'     => '1',
                ),
                array(
                    'label'     => 'Display Real Name',
                    'checked'   => 'checked',
                    'value'     => '2',
                ),
            )
        ),
        'bio'   => array(
            'label'     => 'Bio',
            'type'      => 'textarea',
            'rows'      => '4',
            'cols'      => '50',
            'value'     => ''
        ),
        'plan' => array(
            'type'      => 'select',
            'label'     => 'Plan',
            'selected'  => '2',
            'options'   => array(
                '1' => 'Basic',
                '2' => 'Standard',
                '3' => 'Advanced'
            )
        ),
        'action'         => array(
            'label'     => '',
            'type'      => 'submit',
            'value'     => 'Create'
        )
    )
);

/* End of file formation.php */
