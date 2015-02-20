<?php namespace CmsCanvas\Http\Controllers\Admin\Content;

use View, Theme, Admin, Redirect, Validator, Request, Input, stdClass, Config;
use CmsCanvas\Http\Controllers\Admin\AdminController;
use CmsCanvas\Models\Content\Type;
use CmsCanvas\Models\Permission;

class TypeController extends AdminController {

    /**
     * Display all content types
     *
     * @return View
     */
    public function getTypes()
    {
        $content = View::make('cmscanvas::admin.content.type.types');

        $filter = Type::getSessionFilter();
        $orderBy = Type::getSessionOrderBy();

        $contentTypes = new Type;
        $contentTypes = $contentTypes->applyFilter($filter)
            ->applyOrderBy($orderBy);

        $content->contentTypes = $contentTypes->paginate(50);
        $content->filter = new stdClass();
        $content->filter->filter = $filter;
        $content->orderBy = $orderBy;

        $this->layout->breadcrumbs = array(Request::path() => 'Content Types');
        $this->layout->content = $content;

    }

    /**
     * Saves the filter request to the session
     *
     * @return View
     */
    public function postTypes()
    {
        Type::processFilterRequest();

        return Redirect::route('admin.content.type.types');
    }

    /**
     * Deletes content type(s) that are posted in the selected array
     *
     * @return View
     */
    public function postDelete()
    {
        $selected = Input::get('selected');

        if (empty($selected) || ! is_array($selected)) {
            return Redirect::route('admin.content.type.types')
                ->with('notice', 'You must select at least one content type to delete.');
        }

        $selected = array_values($selected);

        Type::destroy($selected);

        return Redirect::route('admin.content.type.types')
            ->with('message', 'The selected content type(s) were sucessfully deleted.');;
    }

    /**
     * Display add content type form
     *
     * @return View
     */
    public function getAdd()
    {
        // Routed to getEdit
    }

    /**
     * Create a new content type
     *
     * @return View
     */
    public function postAdd()
    {
        // Routed to postEdit
    }

    /**
     * Display add content type form
     *
     * @return View
     */
    public function getEdit($contentType = null)
    {
        if ($contentType == null)
        {
            $content = View::make('cmscanvas::admin.content.type.add');
        }
        else
        {
            Theme::addPackage('codemirror');
            $content = View::make('cmscanvas::admin.content.type.edit');
        }

        $content->contentType = $contentType;
        $content->themeLayouts = Theme::getThemeLayouts(Config::get('cmscanvas::config.theme'));
        $content->defaultThemeLayout = Theme::getThemeLayouts(Config::get('cmscanvas::config.layout'));
        $content->permissions = Permission::orderBy('name', 'asc')->get();

        $this->layout->breadcrumbs = array(
            'content/type' => 'Content Types', 
            Request::path() => (($contentType == null) ? 'Add' : 'Edit').' Content Type'
        );

        $this->layout->content = $content;
    }

    /**
     * Update an existing content type
     *
     * @return View
     */
    public function postEdit($contentType = null)
    {
        $rules = array(
            'title' => 'required|max:255',
            'short_name' => "required|alpha_dash|max:50"
                ."|unique:content_types,short_name".(($contentType == null) ? "" : ",{$contentType->id}"),
            'entries_allowed' => 'integer',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            if ($contentType == null)
            {
                return Redirect::route('admin.content.type.add')
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
            else
            {
                return Redirect::route('admin.content.type.edit', $contentType->id)
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
        }

        $contentType = ($contentType == null) ? new Type() : $contentType;
        $contentType->fill(Input::all());
        $contentType->save();

        if (Input::get('save_exit'))
        {
            return Redirect::route('admin.content.type.types')
                ->with('message', "{$contentType->title} was successfully updated.");
        }
        else
        {
            return Redirect::route('admin.content.type.edit', $contentType->id)
                ->with('message', "{$contentType->title} was successfully updated.");
        }
    }

}