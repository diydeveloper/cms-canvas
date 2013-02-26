<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Snippets_model extends DataMapper
{   
    public $table = "snippets";

    /*
     * Add Revision
     *
     * Adds the snippet data to the revisions table
     *
     * @return void
     */
    public function add_revision()
    {
        $CI =& get_instance();
        $CI->load->model('revisions_model');

        $_POST['layout'] = (isset($_POST['layout'])) ? $CI->input->post('layout') : '';
        $_POST['page_head'] = (isset($_POST['page_head'])) ? $CI->input->post('page_head') : '';

        // Delete old revsions so that not to exceed 5 revisions
        $Revision = new Revisions_model();
        $Revision->where_related('revision_resource_types', 'key_name', 'SNIPPET')
            ->where('resource_id', $this->id)
            ->order_by('id', 'desc')
            ->limit(25, 5 - 1)
            ->get()
            ->delete_all();
            
        // Serialize and save post data to content type revisions table
        $User = $CI->secure->get_user_session();
        $Revision = new Revisions_model();
        $Revision->revision_resource_type_id = Revision_resource_types_model::SNIPPET;
        $Revision->resource_id = $this->id;
        $Revision->author_id = $User->id;
        $Revision->author_name = $User->first_name . ' ' . $User->last_name;
        $Revision->revision_date = date('Y-m-d H:i:s');
        $Revision->revision_data = serialize($CI->input->post());
        $Revision->save();
    }

    // ------------------------------------------------------------------------

    /*
     * Get Revisions
     *
     * Gets past revisions for the snippet
     *
     * @return void
     */
    public function get_revisions() {
        $CI =& get_instance();
        $CI->load->model('revisions_model');

        $Revisions = new Revisions_model();
        $Revisions->where_related('revision_resource_types', 'key_name', 'SNIPPET')
                  ->where('resource_id', $this->id)
                  ->order_by('id', 'desc')
                  ->get();

        return $Revisions;
    }
}
