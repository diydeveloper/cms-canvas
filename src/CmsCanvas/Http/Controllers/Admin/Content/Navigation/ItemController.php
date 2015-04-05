<?php namespace CmsCanvas\Http\Controllers\Admin\Content\Navigation;

use View, Admin, Redirect, Validator, Request, Input, stdClass;
use CmsCanvas\Http\Controllers\Admin\AdminController;
use CmsCanvas\Models\Content\Navigation;
use CmsCanvas\Models\Content\Navigation\Item;

class ItemController extends AdminController {

    /**
     * Display all navigations
     *
     * @return View
     */
    public function tree($navigation)
    {
        $content = View::make('cmscanvas::admin.content.navigation.navigations');

        $this->layout->breadcrumbs = array(
            'content/navigation' => 'Navigations', 
            Request::path() => 'Navigation Tree'
        );
        $this->layout->content = $content;
    }

    /**
     * Saves the filter request to the session
     *
     * @return View
     */
    public function postNavigations()
    {
        Navigation::processFilterRequest();

        return Redirect::route('admin.content.navigation.navigations');
    }

    /**
     * Deletes navigation(s) that are posted in the selected array
     *
     * @return View
     */
    public function postDelete()
    {
        $selected = Input::get('selected');

        if (empty($selected) || ! is_array($selected)) {
            return Redirect::route('admin.content.navigation.navigations')
                ->with('notice', 'You must select at least one group to delete.');
        }

        $selected = array_values($selected);

        Navigation::destroy($selected);

        return Redirect::route('admin.content.navigation.navigations')
            ->with('message', 'The selected navigation(s) were sucessfully deleted.');;
    }

    /**
     * Display add navigation form
     *
     * @return View
     */
    public function getAdd()
    {
        // Routed to getEdit
    }

    /**
     * Create a new navigation
     *
     * @return View
     */
    public function postAdd()
    {
        // Routed to postEdit
    }

    /**
     * Display add navigation form
     *
     * @return View
     */
    public function getEdit($navigation = null)
    {
        $content = View::make('cmscanvas::admin.content.navigation.edit');

        $content->navigation = $navigation;

        $this->layout->content = $content;
    }

    /**
     * Update an existing navigation
     *
     * @return View
     */
    public function postEdit($navigation = null)
    {
        $rules['title'] = 'required';

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            if ($navigation == null)
            {
                return Redirect::route('admin.content.navigation.add', $contentType->id)
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
            else
            {
                return Redirect::route('admin.content.navigation.edit', array($navigation->id))
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
        }

        $navigation = ($navigation == null) ? new Navigation : $navigation;
        $navigation->fill(Input::all());
        $navigation->save();

        return Redirect::route('admin.content.navigation.navigations')
            ->with('message', "{$navigation->title} was successfully updated.");
    }

}