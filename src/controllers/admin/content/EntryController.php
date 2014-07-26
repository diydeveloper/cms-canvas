<?php namespace CmsCanvas\Controllers\Admin\Content;

use View, Theme, Admin, Redirect, Validator, Request, Input, DB, stdClass;
use CmsCanvas\Controllers\Admin\AdminController;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;
use CmsCanvas\Models\Language;
use CmsCanvas\Models\Content\Entry\Status;
use CmsCanvas\Models\User;

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
            ->join('entry_statuses', 'entries.entry_status_id', '=', 'entry_statuses.id')
            ->select(DB::raw('entries.*, content_types.title as content_type_title, entry_statuses.name as entry_status_name'))
            ->applyFilter($filter)
            ->applyOrderBy($orderBy);

        $contentTypes = Type::getAvailableForNewEntry();
        $queries = DB::getQueryLog();
        $entryStatuses = Status::orderBy('id', 'asc')->get();
        $contentTypesAll = Type::orderBy('title', 'asc')->get();

        $content->entries = $entries->paginate(50);
        $content->filter = new stdClass();
        $content->filter->filter = $filter;
        $content->orderBy = $orderBy;
        $content->contentTypes = $contentTypes;
        $content->contentTypeSelectOptions = $contentTypesAll->getKeyValueArray('id', 'title');
        $content->entryStatusSelectOptions = $entryStatuses->getKeyValueArray('id', 'name');

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
        $content = View::make('cmscanvas::admin.content.entry.edit');

        $entryStatuses = Status::orderBy('id', 'asc')->get();
        $authors = User::getAuthors();

        $content->entry = $entry;
        $content->fieldViews = $contentType->getAdminFieldViews($entry);
        $content->entryStatusSelectOptions = $entryStatuses->getKeyValueArray('id', 'name', false);
        $content->authors = $authors->getKeyValueArray('id', 'getFullName');
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

}