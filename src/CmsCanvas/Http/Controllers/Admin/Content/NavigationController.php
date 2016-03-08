<?php 

namespace CmsCanvas\Http\Controllers\Admin\Content;

use View, Theme, Admin, Validator, DB, stdClass;
use CmsCanvas\Http\Controllers\Admin\AdminController;
use CmsCanvas\Models\Content\Navigation;
use CmsCanvas\Models\Content\Navigation\Item;
use CmsCanvas\Content\Navigation\Builder;
use Illuminate\Http\Request;

class NavigationController extends AdminController {

    /**
     * Display all navigations
     *
     * @return View
     */
    public function getNavigations(Request $request)
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

        $this->layout->breadcrumbs = [$request->path() => 'Navigations'];
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

        return redirect()->route('admin.content.navigation.navigations');
    }

    /**
     * Deletes navigation(s) that are posted in the selected array
     *
     * @return View
     */
    public function postDelete(Request $request)
    {
        $selected = $request->input('selected');

        if (empty($selected) || ! is_array($selected)) {
            return redirect()->route('admin.content.navigation.navigations')
                ->with('notice', 'You must select at least one group to delete.');
        }

        $selected = array_values($selected);

        Navigation::destroy($selected);

        return redirect()->route('admin.content.navigation.navigations')
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
    public function postEdit(Request $request, $navigation = null)
    {
        $rules = [
            'title' => 'required|max:255',
            'short_name' => "required|alpha_dash|max:50"
                ."|unique:content_types,short_name".(($navigation == null) ? "" : ",{$navigation->id}"),
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($navigation == null) {
                return redirect()->route('admin.content.navigation.add', $contentType->id)
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            } else {
                return redirect()->route('admin.content.navigation.edit', [$navigation->id])
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
        }

        $navigation = ($navigation == null) ? new Navigation : $navigation;
        $navigation->fill($request->all());
        $navigation->save();

        return redirect()->route('admin.content.navigation.navigations')
            ->with('message', "{$navigation->title} was successfully updated.");
    }

    /**
     * Display navigation tree
     *
     * @return View
     */
    public function getTree(Request $request, $navigation)
    {
        Theme::addPackage('nestedSortable');
        $builder = new Builder($navigation->short_name);
        $navigationTree = $builder->getNavigationTree();

        $content = View::make('cmscanvas::admin.content.navigation.tree');
        $content->navigation = $navigation;
        $content->navigationTree = $navigationTree;

        $this->layout->breadcrumbs = [
            'content/navigation' => 'Navigations', 
            $request->path() => 'Navigation Tree'
        ];
        $this->layout->content = $content;
    }

    /**
     * Post navigation tree
     *
     * @return string
     */
    public function postTree(Request $request)
    {
        $list = $request->input('list');

        if (! is_array($list)) {
            $list = [];
        }

        $order = 0;

        foreach($list as $id => $parentId) {
            $parentId = ($parentId == 'root') ? null : $parentId;

            Item::where('id', $id)
                ->update(
                    [
                        'sort' => $order, 
                        'parent_id' => $parentId
                    ]
                );

            $order++;
        }

        return '';
    }

}