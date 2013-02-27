<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Entry URL
 *
 * Returns the URL of a CMS page
 *
 * @param int
 * @return string
 */
if ( ! function_exists('entry_url'))
{
    function entry_url($id)
    {
        $CI =& get_instance();
        $CI->load->library('cache');

        $Page = $CI->cache->model('entries_cache_model', 'cacheable_get_by', array('id' => $id), 'entries');

        if ( $Page->exists() )
        {
            return site_url($Page->slug);
        }

        return site_url();
    }
}

// ------------------------------------------------------------------------

/*
 * Entry Name
 *
 * Returns the title of a CMS page
 *
 * @param int
 * @return string
 */
if ( ! function_exists('entry_name'))
{
    function entry_name($id)
    {
        $CI =& get_instance();
        $CI->load->library('cache');

        $Page = $CI->cache->model('entries_cache_model', 'cacheable_get_by', array('id' => $id), 'entries');

        if ( $Page->exists() )
        {
            return $Page->title;
        }
    }
}

// ------------------------------------------------------------------------

/*
 * Entries
 *
 * Queries and returns entries based on passed config settings
 *
 * @param array
 * @return array
 */
if ( ! function_exists('entries'))
{
    function entries($config)
    {
        $CI =& get_instance();
        $CI->load->library('content/entries_library');

        $Entries_library = new Entries_library();
        $Entries_library->initialize($config);
        return $Entries_library->entries();
    }
}

// ------------------------------------------------------------------------

/*
 * Categories
 *
 * Queries and returns categories based on passed config settings
 *
 * @param array
 * @return string
 */
if ( ! function_exists('categories'))
{
    function categories($config)
    {
        $CI =& get_instance();

        $CI->load->library('content/categories_library');

        // Set the attribute id as the tag id
        if (isset($config['id']))
        {
            $config['tag_id'] = $config['id'];
            unset($config['id']);
        }

        // Set the id as the config attribute id;
        $config['id'] = $config['category_group_id'];

        return $CI->categories_library->list_categories($config);
    }
}

// ------------------------------------------------------------------------

/*
 * Archive
 *
 * Queries month year combinations for content types provided
 *
 * @return array
 */
if ( ! function_exists('archive'))
{
    function archive($config)
    {
        $CI =& get_instance();
        $CI->load->library('content/Archives_library');

        // Set the attribute id as the tag id
        if (isset($config['id']))
        {
            $config['tag_id'] = $config['id'];
            unset($config['id']);
        }

        $Archives_library = new Archives_library();
        $Archives_library->initialize($config);
        return $Archives_library->archive();
    }
}

// ------------------------------------------------------------------------

/*
 * Is Home
 *
 * Checks if the current page matches that of the selected hompage
 *
 * @return bool
 */
if ( ! function_exists('is_home'))
{
    function is_home()
    {
        $CI =& get_instance();

        if (isset($CI->template->template_data['entry_id']) && $CI->template->template_data['entry_id'] == $CI->settings->content_module->site_homepage)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
}

// ------------------------------------------------------------------------

/*
 * Snippets
 *
 * Returns a reusable code snippet
 *
 * @return string
 */
if ( ! function_exists('snippets'))
{
    function snippets($config, $data)
    {
        $CI =& get_instance();
        $CI->load->library('parser');

        $Snippet = $CI->cache->model('snippets_cache_model', 'cacheable_get_by_short_name', array('short_name' => $config['snippet']), 'snippets');

        // Refactor data array renaming parent tags to snippet tag names
        $rename_tags = array_flip($config);

        foreach ($data as $tag => $value)
        {
            if (isset($rename_tags[$tag]))
            {
                $refactord_data[$rename_tags[$tag]] = $value;
                unset($rename_tags[$tag]);
            }
            else
            {
                $refactord_data[$tag] = $value;
            }
        }

        // Add literal values to the refactored data array
        $literals = array_flip($rename_tags);

        foreach ($literals as $tag => $literal_value)
        {
            $literal_value = trim($literal_value, '"');
            $literal_value = trim($literal_value, "'");
            $refactord_data[$tag] = $literal_value;
        }

        $parsed_content = $CI->parser->parse_string($Snippet->snippet, $refactord_data, TRUE);

        return $parsed_content;
    }
}
