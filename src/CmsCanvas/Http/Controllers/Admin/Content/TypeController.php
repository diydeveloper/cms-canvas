<?php 

namespace CmsCanvas\Http\Controllers\Admin\Content;

use View, Theme, Admin, Validator, stdClass, Config;
use CmsCanvas\Http\Controllers\Admin\AdminController;
use CmsCanvas\Models\Content\Type;
use CmsCanvas\Models\Permission;
use CmsCanvas\Models\Content\Type\MediaType;
use Illuminate\Http\Request;

class TypeController extends AdminController {

    /**
     * Display all content types
     *
     * @return View
     */
    public function getTypes(Request $request)
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

        $this->layout->breadcrumbs = [$request->path() => 'Content Types'];
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

        return redirect()->route('admin.content.type.types');
    }

    /**
     * Deletes content type(s) that are posted in the selected array
     *
     * @return View
     */
    public function postDelete(Request $request)
    {
        $selected = $request->input('selected');

        if (empty($selected) || ! is_array($selected)) {
            return redirect()->route('admin.content.type.types')
                ->with('notice', 'You must select at least one content type to delete.');
        }

        $selected = array_values($selected);

        Type::destroy($selected);

        return redirect()->route('admin.content.type.types')
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
    public function getEdit(Request $request, $contentType = null, $revision = null)
    {
        if ($contentType == null) {
            $content = View::make('cmscanvas::admin.content.type.add');
        } else {
            Theme::addPackage('codemirror');
            $content = View::make('cmscanvas::admin.content.type.edit');

            if ($revision != null) {
                $contentType->fill($revision->data);
            }
        }

        $content->contentType = $contentType;
        $content->themeLayouts = Theme::getThemeLayouts(Config::get('cmscanvas::config.theme'));
        $content->defaultThemeLayout = Theme::getThemeLayouts(Config::get('cmscanvas::config.layout'));
        $content->permissions = Permission::orderBy('name', 'asc')->get();
        $content->mediaTypes = MediaType::orderBy('name', 'asc')->get();
        $content->revision = $revision;

        $this->layout->breadcrumbs = [
            'content/type' => 'Content Types', 
            $request->path() => (($contentType == null) ? 'Add' : 'Edit').' Content Type'
        ];

        $this->layout->content = $content;
    }

    /**
     * Update an existing content type
     *
     * @return View
     */
    public function postEdit(Request $request, $contentType = null, $revision = null)
    {
        $rules = [
            'title' => 'required|max:255',
            'short_name' => "required|alpha_dash|max:50"
                ."|unique:content_types,short_name".(($contentType == null) ? "" : ",{$contentType->id}"),
            'route' => "max:500|unique:content_types,route".(($contentType == null) ? "" : ",{$contentType->id}"),
            'entries_allowed' => 'integer',
        ];

        $messages = [];
        
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($contentType == null) {
                return redirect()->route('admin.content.type.add')
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            } else {
                return redirect()->route('admin.content.type.edit', $contentType->id)
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
        }

        $data = $request->all();
        $contentType = ($contentType == null) ? new Type() : $contentType;
        $contentType->fill($data);
        $contentType->save();

        $contentType->createRevision($data);

        if ($request->input('save_exit')) {
            return redirect()->route('admin.content.type.types')
                ->with('message', "{$contentType->title} was successfully updated.");
        } else {
            return redirect()->route('admin.content.type.edit', $contentType->id)
                ->with('message', "{$contentType->title} was successfully updated.");
        }
    }

}