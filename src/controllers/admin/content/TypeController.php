<?php namespace CmsCanvas\Controllers\Admin\Content;

use View, Theme, Admin, Redirect, Validator, Request, Input, stdClass;
use CmsCanvas\Routing\AdminController;
use CmsCanvas\Models\Content\Type;

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
        $content = View::make('cmscanvas::admin.content.type.add');

        $this->layout->breadcrumbs = array(
            'content/type' => 'Content Types', 
            Request::path() => 'Add Content Type'
        );
        $this->layout->content = $content;
    }

    /**
     * Create a new content type
     *
     * @return View
     */
    public function postAdd()
    {
        $rules = array(
            'title' => 'required',
            'short_name' => 'required',
            'entries_allowed' => 'integer',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            return Redirect::route('admin.content.type.add')
                ->withInput()
                ->with('error', $validator->messages()->all());
        }

        $contentType = new Type();
        $contentType->fill(Input::all());
        $contentType->save();

        return Redirect::route('admin.content.type.edit', $contentType->id);
    }

    /**
     * Display add content type form
     *
     * @return View
     */
    public function getEdit($contentType)
    {
        Theme::addPackage('codemirror');

        $content = View::make('cmscanvas::admin.content.type.edit');
        $content->contentType = $contentType;

        $this->layout->breadcrumbs = array(
            'content/type' => 'Content Types', 
            Request::path() => 'Edit Content Type'
        );
        $this->layout->content = $content;
    }

    /**
     * Update an existing content type
     *
     * @return View
     */
    public function postEdit($contentType)
    {
        $rules = array(
            'title' => 'required',
            'short_name' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            return Redirect::route('admin.content.type.edit', $contentType->id)
                ->withInput()
                ->with('error', $validator->messages()->all());
        }

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