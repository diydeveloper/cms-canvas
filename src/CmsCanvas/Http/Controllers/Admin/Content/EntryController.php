<?php 

namespace CmsCanvas\Http\Controllers\Admin\Content;

use View, Theme, Admin, Validator, DB, stdClass, App, Auth;
use CmsCanvas\Http\Controllers\Admin\AdminController;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;
use CmsCanvas\Models\Language;
use CmsCanvas\Models\Content\Entry\Status;
use CmsCanvas\Models\User;
use CmsCanvas\Models\Content\Entry\Data as EntryData;
use CmsCanvas\Content\Type\FieldType;
use Carbon\Carbon;
use Content;
use Illuminate\Http\Request;

class EntryController extends AdminController {

    /**
     * Display all entries
     *
     * @return View
     */
    public function getEntries(Request $request)
    {
        $content = View::make('cmscanvas::admin.content.entry.entries');

        $filter = Entry::getSessionFilter();
        $orderBy = Entry::getSessionOrderBy();

        $entries = new Entry;
        $entries = $entries->join('content_types', 'entries.content_type_id', '=', 'content_types.id')
            ->leftJoin('permissions', 'content_types.admin_entry_view_permission_id', '=', 'permissions.id')
            ->leftJoin('role_permissions', 'content_types.admin_entry_view_permission_id', '=', 'role_permissions.permission_id')
            ->join('entry_statuses', 'entries.entry_status_id', '=', 'entry_statuses.id')
            ->select(DB::raw('entries.*, content_types.title as content_type_title, entry_statuses.name as entry_status_name'))
            ->distinct()
            ->where(function($query) {
                $query->whereNull('content_types.admin_entry_view_permission_id');
                $roles = Auth::user()->roles;
                if (count($roles) > 0) {
                    $query->orWhereIn('role_permissions.role_id', $roles->pluck('id')->all());
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

        $this->layout->breadcrumbs = [$request->path() => 'Entries'];
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

        return redirect()->route('admin.content.entry.entries');
    }

    /**
     * Prompts for entry delete verfication for entries posted in the selected array
     *
     * @return View
     */
    public function postDeleteVerify(Request $request)
    {
        $selected = $request->input('selected');

        if (empty($selected) || ! is_array($selected)) {
            return redirect()->route('admin.content.entry.entries');
        }

        $entries = Entry::whereIn('id', $selected)->get();

        $content = View::make('cmscanvas::admin.content.entry.deleteVerify');
        $content->entries = $entries;

        $this->layout->breadcrumbs = [$request->path() => 'Entries'];
        $this->layout->content = $content;
    }

    /**
     * Deletes entry(s) that are posted in the selected array
     *
     * @return View
     */
    public function postDelete(Request $request)
    {
        $selected = $request->input('selected');
        $deleteSuccessfulFlag = false;
        $errors = [];

        if (empty($selected) || ! is_array($selected)) {
            return redirect()->route('admin.content.entry.entries')
                ->with('notice', 'You must select at least one group to delete.');
        }

        $selected = array_values($selected);

        foreach ($selected as $entryId) {
            $entry = Entry::find($entryId);

            if ($entry != null) {
                try {
                    $entry->delete();
                    $deleteSuccessfulFlag = true;
                } catch (\CmsCanvas\Exceptions\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }

        $redirect = redirect()->route('admin.content.entry.entries');

        if (count($errors) > 0) {
            $redirect->with('error', $errors);
        }

        if ($deleteSuccessfulFlag) {
            if (count($errors) > 0) {
                $message = 'Some of the selected entry(s) were sucessfully deleted.';
            } else {
                $message = 'The selected entry(s) were sucessfully deleted.';
            }

            $redirect->with('message', $message);
        }

        return $redirect;
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
    public function getEdit(Request $request, $contentType, $entry = null, $revision = null)
    {
        if ($entry == null) {
            $contentType->checkEntriesAllowed();
            $contentType->checkAdminEntryCreatePermissions();
        } else {
            $entry->contentType->checkAdminEntryEditPermissions();
        }

        $content = View::make('cmscanvas::admin.content.entry.edit');

        $entryStatuses = Status::orderBy('id', 'asc')->get();
        $authors = User::getAuthors();
        $authorOptions = ['' => ''];
        foreach ($authors as $author) {
            $authorOptions[$author->id] = $author->getFullName();
        }

        $contentFields = $contentType->getAllFieldTypeInstances($entry);

        if ($revision != null) {
            $entry->fill($revision->data);
            $contentFields->fill($revision->data);
        }

        $availableContentTypes = Type::getAvailableForNewEntry();

        $content->entry = $entry;
        $content->fieldViews = $contentFields->getAdminViews();
        $content->entryStatuses = $entryStatuses;
        $content->authorOptions = $authorOptions;
        $content->contentType = $contentType;
        $content->revision = $revision;
        $content->availableContentTypes = $availableContentTypes;

        $this->layout->breadcrumbs = [
            'content/entry' => 'Entries', 
            $request->path() => (empty($entry) ? 'Add' : 'Edit').' Entry'
        ];
        $this->layout->content = $content;
    }

    /**
     * Update an existing entry
     *
     * @return View
     */
    public function postEdit(Request $request, $contentType, $entry = null, $revision = null)
    {
        if ($entry == null) {
            $contentType->checkEntriesAllowed();
            $contentType->checkAdminEntryCreatePermissions();
        } else {
            $entry->contentType->checkAdminEntryEditPermissions();
        }
        
        $contentFields = $contentType->getAllFieldTypeInstances($entry);
        $rules = $contentFields->getValidationRules();

        $rules['title'] = 'required';

        if ($contentType->url_title_flag) {
            $rules['url_title'] = "required|alpha_dash|max:500"
                ."|unique:entries,url_title,".(($entry == null) ? "NULL" : "{$entry->id}").",id"
                .",content_type_id,{$contentType->id}";
        }

        $attributeNames = $contentFields->getAttributeNames();

        $validator = Validator::make($request->all(), $rules, [], $attributeNames);

        if ($validator->fails()) {
            if ($entry == null) {
                return redirect()->route('admin.content.entry.add', $contentType->id)
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            } else {
                return redirect()->route('admin.content.entry.edit', [$contentType->id, $entry->id])
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
        }

        $createdAt = Carbon::createFromFormat(
            'd/M/Y h:i:s a', 
            $request->input('created_at'), 
            Auth::user()->getTimezoneIdentifier()
        );
        $createdAt->setTimezone(config('app.timezone'));

        $data = $request->all();
        $data['created_at'] = $createdAt;
        $data['created_at_local'] = $createdAt->copy()
            ->setTimezone(config('cmscanvas.config.default_timezone'));

        $entry = ($entry == null) ? new Entry : $entry;
        $entry->fill($data);
        $entry->content_type_id = $contentType->id;
        $entry->save();

        $contentFields->setEntry($entry);
        $contentFields->fill($data);
        $contentFields->save();

        $contentFields->createRevisions($data);

        if ($request->input('save_exit')) {
            return redirect()->route('admin.content.entry.entries')
                ->with('message', "{$entry->title} was successfully updated.");
        } else {
            return redirect()->route('admin.content.entry.edit', [$contentType->id, $entry->id])
                ->with('message', "{$entry->title} was successfully updated.");
        }
    }

    /**
     * Generate a thumbnail from the specified image path
     *
     * @return string
     */
    public function postCreateThumbnail(Request $request)
    {
        return Content::thumbnail(
            $request->input('image_path'), 
            [
                'width' => 150, 
                'height' => 150, 
                'no_image' => Theme::asset('images/no_image.jpg')
            ]
        );
    }

    /**
     * Save inline content
     *
     * Called by AJAX to save inline content elements
     * 
     * @return void
     */
    public function postSaveInlineContent(Request $request)
    {
        try {
            $data = $request->all();
            $contentFields = FieldType::findByInlineEditableKeys(array_keys($data));

            $rules = $contentFields->getInlineEditableValidationRules($data);
            $attributeNames = $contentFields->getAttributeNames();
            $validator = Validator::make($data, $rules, [], $attributeNames);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => implode(', ', $validator->messages()->all())]);
            }

            $contentFields->fill($data);
            $contentFields->save();
            
            $contentFields->touchTemplateEntries();
            $contentFields->createRevisions($data);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'content' => $e->getMessage()]);
        }
    }

    /**
     * Pre process inline content
     *
     * Called by AJAX to process inline content before saving
     * 
     * @return void
     */
    public function postPreProcessInlineContent(Request $request)
    {
        try {
            $contentField = FieldType::findByInlineEditableKey($request->input('editable_id'));
            if ($contentField == null) {
                throw new \Exception('Unable to find a field type matching key '.$request->input('editable_id'));
            }
            $contentField->setData($request->input('content'), true);
            return response()->json([
                'status' => 'success',
                'content' => $contentField->render()
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'content' => $e->getMessage()]);
        }
    }

}