<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Page_url_field extends Field_type
{
    function display_field()
    {
        $data = get_object_vars($this);
        
        // Get all entries for link dropdown
        $this->load->model('content/entries_model');

        $Pages = new Entries_model();
        $Pages->where('status', 'published')
            ->where('slug !=', 'NULL')
            ->or_where('id =', $this->settings->content_module->site_homepage)
            ->order_by('title')
            ->get();

        $page_array = array('' => '');

        foreach($Pages as $Page)
        {
            $page_array['/' . $Page->slug] = $Page->title;
        }

        $data['Pages'] = $page_array;

        return $this->load->view('page_url', $data, TRUE);
    }
}
