<?php namespace CmsCanvas\Controllers\Admin\Content;

use View, Theme, Admin, Redirect, Validator, Request, Input, DB, stdClass, App, Auth, Config;
use CmsCanvas\Routing\AdminController;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;
use CmsCanvas\Models\Language;
use CmsCanvas\Models\Content\Entry\Status;
use CmsCanvas\Models\User;
use Carbon\Carbon;
use Content;

class EntryController extends AdminController {

    /**
     * Display all entries
     *
     * @return View
     */
    public function getEntries()
    {
        $content = View::make('cmscanvas::admin.content.entry.entries');

        $filter = Entry::getSessionFilter();
        $orderBy = Entry::getSessionOrderBy();

        $entries = new Entry;
        $entries = $entries->join('content_types', 'entries.content_type_id', '=', 'content_types.id')
            ->leftJoin('permissions', 'content_types.admin_view_permission_id', '=', 'permissions.id')
            ->leftJoin('role_permissions', 'content_types.admin_view_permission_id', '=', 'role_permissions.permission_id')
            ->join('entry_statuses', 'entries.entry_status_id', '=', 'entry_statuses.id')
            ->select(DB::raw('entries.*, content_types.title as content_type_title, entry_statuses.name as entry_status_name'))
            ->distinct()
            ->where(function($query) 
            {
                $query->whereNull('content_types.admin_view_permission_id');
                $roles = Auth::user()->roles;
                if (count($roles) > 0)
                {
                    $query->orWhereIn('role_permissions.role_id', $roles->lists('id'));
                }
            })
            ->applyFilter($filter)
            ->applyOrderBy($orderBy);

        $contentTypes = Type::getAvailableForNewEntry();
        $entryStatuses = Status::orderBy('id', 'asc')->get();
        $viewableContentTypes = Type::getAllViewable();

        $content->entries = $entries->paginate(50);
        $content->filter = new stdClass();
        $content->filter->filter = $filter;
        $content->orderBy = $orderBy;
        $content->contentTypes = $contentTypes;
        $content->viewableContentTypes = $viewableContentTypes;
        $content->entryStatuses = $entryStatuses;

        $this->layout->breadcrumbs = array(Request::path() => 'Entries');
        $this->layout->content = $content;

    }

    /**
     * Saves the filter request to the session
     *
     * @return View
     */
    public function postEntries()
    {
        Entry::processFilterRequest();

        return Redirect::route('admin.content.entry.entries');
    }

    /**
     * Deletes entry(s) that are posted in the selected array
     *
     * @return View
     */
    public function postDelete()
    {
        $selected = Input::get('selected');

        if (empty($selected) || ! is_array($selected)) {
            return Redirect::route('admin.content.entry.entries')
                ->with('notice', 'You must select at least one group to delete.');
        }

        $selected = array_values($selected);

        Entry::destroy($selected);

        return Redirect::route('admin.content.entry.entries')
            ->with('message', 'The selected entry(s) were sucessfully deleted.');;
    }

    /**
     * Display add entry form
     *
     * @return View
     */
    public function getAdd()
    {
        // Routed to getEdit
    }

    /**
     * Create a new entry
     *
     * @return View
     */
    public function postAdd()
    {
        // Routed to postEdit
    }

    /**
     * Display add entry form
     *
     * @return View
     */
    public function getEdit($contentType, $entry = null)
    {
        if ($entry == null)
        {
            $contentType->checkEntriesAllowed();
        }

        $content = View::make('cmscanvas::admin.content.entry.edit');

        $entryStatuses = Status::orderBy('id', 'asc')->get();
        $authors = User::getAuthors();
        $authorOptions = array('' => '');
        foreach ($authors as $author) {
            $authorOptions[$author->id] = $author->getFullName();
        }

        $content->entry = $entry;
        $content->fieldViews = $contentType->getAdminFieldViews($entry);
        $content->entryStatuses = $entryStatuses;
        $content->authorOptions = $authorOptions;
        $content->contentType = $contentType;

        $this->layout->content = $content;
    }

    /**
     * Update an existing entry
     *
     * @return View
     */
    public function postEdit($contentType, $entry = null)
    {
        if ($entry == null)
        {
            $contentType->checkEntriesAllowed();
        }
        
        $contentFields = $contentType->getAllFieldTypeInstances($entry);
        $rules = $contentFields->getValidationRules();

        $rules['title'] = 'required';

        $attributeNames = $contentFields->getAttributeNames();

        $validator = Validator::make(Input::all(), $rules, array(), $attributeNames);

        if ($validator->fails())
        {
            if ($entry == null)
            {
                return Redirect::route('admin.content.entry.add', $contentType->id)
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
            else
            {
                return Redirect::route('admin.content.entry.edit', array($contentType->id, $entry->id))
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
        }

        $entry = ($entry == null) ? new Entry : $entry;
        $entry->fill(Input::all());
        $entry->content_type_id = $contentType->id;
        $date = Carbon::createFromFormat('m/d/Y h:i:s a', Input::get('created_at'), auth::user()->timezone->identifier);
        $date->setTimezone(config::get('app.timezone'));
        $entry->created_at = $date;
        $entry->save();

        $contentFields->setEntry($entry);
        $contentFields->fill(Input::all());
        $contentFields->save();

        if (Input::get('save_exit'))
        {
            return Redirect::route('admin.content.entry.entries')
                ->with('message', "{$entry->title} was successfully updated.");
        }
        else
        {
            return Redirect::route('admin.content.entry.edit', array($contentType->id, $entry->id))
                ->with('message', "{$entry->title} was successfully updated.");
        }
    }

    /**
     * Generate a thumbnail from the specified image path
     *
     * @return string
     */
    public function postCreateThumbnail()
    {
        return Content::thumbnail(
            Input::get('image_path'), 
            150, 
            150, 
            false, 
            array('no_image' => Theme::asset('images/no_image.jpg'))
        );
    }

}