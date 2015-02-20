<?php namespace CmsCanvas\Http\Controllers\Admin\Content;

use View, Theme, Admin, Redirect, Validator, Request, Input, DB, stdClass;
use CmsCanvas\Http\Controllers\Admin\AdminController;
use CmsCanvas\Models\Content\Navigation;

class NavigationController extends AdminController {

    /**
     * Display all navigations
     *
     * @return View
     */
    public function getNavigations()
    {
        $content = View::make('cmscanvas::admin.content.navigation.navigations');

        $filter = Navigation::getSessionFilter();
        $orderBy = Navigation::getSessionOrderBy();

        $content->navigations = Navigation::applyFilter($filter)
            ->applyOrderBy($orderBy)
            ->paginate(50);
        $content->filter = new stdClass();
        $content->filter->filter = $filter;
        $content->orderBy = $orderBy;

        $this->layout->breadcrumbs = array(Request::path() => 'Navigations');
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