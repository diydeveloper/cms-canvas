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
        // Show admin_toolbar on page
        if ($this->settings->enable_admin_toolbar && $this->secure->group_types(array(ADMINISTRATOR))->is_auth())
        {
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
            });");
        }
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
