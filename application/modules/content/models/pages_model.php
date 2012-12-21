<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Pages_model extends CI_Model
{
    /*
     * Admin Toolbar
     *
     * Displays the admin toolbar on pages if the user 
     * is an administrator and it is enabled in settings
     *
     * @param object
     * @return void
     */
    function admin_toolbar($content_type_id, $entry_id = null)
    {
        // Update the inline editing setting if user submitted setting
        if ($this->input->post('admin_toggle_inline_editing'))
        {
            $this->load->model('settings/settings_model'); 
            $Settings_model = new Settings_model();
            $Settings_model->where('slug', 'enable_inline_editing')->where('module IS NULL')->update('value', (($this->settings->enable_inline_editing) ? '0' : '1'));

            // Clear the settings cache
            $this->load->library('cache');
            $this->cache->delete_all('settings');

            // Redirect to the current url so that it clears the post
            redirect(current_url());
        }

        // Show admin_toolbar on page
        if ($this->settings->enable_admin_toolbar && $this->secure->group_types(array(ADMINISTRATOR))->is_auth())
        {
            $this->template->add_script("var ADMIN_PATH = '" . site_url(ADMIN_PATH) . "';");
            $admin_toolbar = $this->load->view('admin/admin_toolbar', array('entry_id' => $entry_id, 'content_type_id' => $content_type_id), TRUE);
            $this->template->add_stylesheet('/application/modules/content/assets/css/admin_toolbar.css');
            $this->template->add_package(array('jquery', 'superfish'));
            $this->template->add_script("var jq_admin_toolbar = jQuery.noConflict(true); 
            jq_admin_toolbar(document).ready( function() {  
                jq_admin_toolbar(document.body).prepend(" . json_encode($admin_toolbar) . "); 
                jq_admin_toolbar('#admin-toolbar > ul').superfish({
                    hoverClass   : 'sfHover',
                    pathClass    : 'overideThisToUse',
                    delay        : 0,
                    animation    : {height: 'show'},
                    speed        : 'normal',
                    autoArrows   : false,
                    dropShadows  : false, 
                    disableHI    : false, /* set to true to disable hoverIntent detection */
                    onInit       : function(){},
                    onBeforeShow : function(){},
                    onShow       : function(){},
                    onHide       : function(){}
                });

                jq_admin_toolbar('#admin-toggle-inline-editing').click(function() {
                    jq_admin_toolbar('<form method=\"post\"><input type=\"hidden\" name=\"admin_toggle_inline_editing\" value=\"1\" /></form>').appendTo('body').submit();
                });
            });");

            $this->_content_editable();
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Content Editable
     *
     * Validates that the user's groups has permissions
     * to view this content type
     *
     * @return void
     */
    private function _content_editable()
    {
        $this->template->add_package('ckeditor');
        $this->template->add_javascript('/application/modules/content/assets/js/content_editable.js');
    }

    // ------------------------------------------------------------------------

    /*
     * Check Permissions
     *
     * Validates that the user's groups has permissions
     * to view this content type
     *
     * @param object
     * @return void
     */
    function check_permissions($Content_type)
    {
        // Check and enforce access permissions
        if ($Content_type->access == 1)
        {
            $this->secure->require_auth();
        }
        else if ($Content_type->access == 2)
        {
            // This might cause a problem if some dumbass
            // names one of his groups false
            $group_access = @unserialize($Content_type->restrict_to);
            $this->secure->groups($group_access)->require_auth();
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Content Type Tempalte
     *
     * Sets the content type's theme layout, css, and javascript
     * to the template
     *
     * @param object
     * @return void
     */
    function content_type_template($Content_type)
    {
        // Set content type's theme layout, css and javascript
        if (is_null($Content_type->theme_layout))
        {
            $this->template->set_layout('');
        }
        else
        {
            $this->template->set_layout($Content_type->theme_layout);
        }

        if ( ! empty($Content_type->page_head))
        {
            $this->template->add_page_head($Content_type->page_head);
        }
    }
}
