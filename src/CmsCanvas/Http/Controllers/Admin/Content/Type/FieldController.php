<?php 

namespace CmsCanvas\Http\Controllers\Admin\Content\Type;

use View, Theme, Admin, Validator, stdClass;
use CmsCanvas\Http\Controllers\Admin\AdminController;
use CmsCanvas\Models\Content\Type\Field;
use CmsCanvas\Models\Content\Type\Field\Type;
use CmsCanvas\Content\Type\FieldType;
use Illuminate\Http\Request;

class FieldController extends AdminController {

    /**
     * Display all content types
     *
     * @return View
     */
    public function getFields(Request $request, $contentType)
    {
        Theme::addPackage('tablednd');
        $content = View::make('cmscanvas::admin.content.type.field.fields');

        $filter = Field::getSessionFilter();
        $orderBy = Field::getSessionOrderBy();

        $fields = $contentType->fields()
            ->orderByRaw('ISNULL(`sort`) asc')
            ->orderBy('sort', 'asc');

        $content->fields = $fields->paginate(50);
        $content->contentType = $contentType;
        $content->filter = new stdClass();
        $content->filter->filter = $filter;
        $content->orderBy = $orderBy;

        $this->layout->breadcrumbs = [
            '/content/type' => 'Content Types',
            $request->path() => 'Content Type Fields'
        ];
        $this->layout->content = $content;

    }

    /**
     * Saves the filter request to the session
     *
     * @return View
     */
    public function postFields(Request $request, $contentType)
    {
        Type::processFilterRequest();

        return redirect()->route('admin.content.type.field.fields', [$contentType->id]);
    }

    /**
     * Deletes content type(s) that are posted in the selected array
     *
     * @return View
     */
    public function postDelete(Request $request, $contentType)
    {
        $selected = $request->input('selected');

        if (empty($selected) || ! is_array($selected)) {
            return redirect()->route('admin.content.type.field.fields', [$contentType->id])
                ->with('notice', 'You must select at least one field to delete.');
        }

        $selected = array_values($selected);

        Field::destroy($selected);

        return redirect()->route('admin.content.type.field.fields', [$contentType->id])
            ->with('message', 'The selected content type(s) were sucessfully deleted.');;
    }

    /**
     * Display add content type form
     *
     * @return View
     */
    public function getAdd(Request $request, $contentType)
    {
        // Routed to getEdit
    }

    /**
     * Create a new content type
     *
     * @return View
     */
    public function postAdd(Request $request, $contentType)
    {
        // Routed to postEdit
    }

    /**
     * Display edit content type form
     *
     * @return View
     */
    public function getEdit(Request $request, $contentType, $contentTypeField = null)
    {
        $content = View::make('cmscanvas::admin.content.type.field.edit');
        $content->contentType = $contentType;
        $content->field = $contentTypeField;

        $types = Type::orderBy('name', 'asc')
            ->get();

        if ($contentTypeField == null) {
            $selectedType = $types->first();
        } else {
            $selectedType = $types->getFirstWhere('id', $contentTypeField->content_type_field_type_id);
        }

        $fieldType = FieldType::baseFactory($selectedType, $contentTypeField);

        $content->fieldTypeSettings = $fieldType->settings();
        $content->fieldTypes = $types;

        $this->layout->breadcrumbs = [
            '/content/type' => 'Content Types',
            '/content/type/'.$contentType->id.'/field' => 'Content Type Fields',
            $request->path() => (($contentTypeField == null) ? 'Add' : 'Edit') . 'Field'
        ];
        $this->layout->content = $content;
    }

    /**
     * Add or update a content type
     *
     * @return View
     */
    public function postEdit(Request $request, $contentType, $contentTypeField = null)
    {
        $rules = [
            'content_type_field_type_id' => 'required',
            'label' => 'required|max:50',
            'short_tag' => "required|alpha_dash|max:50"
                ."|unique:content_type_fields,short_tag,"
                .(($contentTypeField == null) ? "NULL" : $contentTypeField->id)
                .",id,content_type_id,{$contentType->id}",
            'required' => 'required',
            'translate' => 'required',
        ];

        $typeId = $request->input('content_type_field_type_id');
        if ( ! empty($typeId)) {
            $type = Type::find($typeId);
            $fieldType = FieldType::baseFactory($type, $contentTypeField);
            $settingsValidationRules = $fieldType->getSettingsValidationRules();
            $rules = array_merge($rules, $settingsValidationRules);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($contentTypeField == null) {
                return redirect()->route('admin.content.type.field.add', $contentType->id)
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            } else {
                return redirect()->route('admin.content.type.field.edit', [$contentType->id, $contentTypeField->id])
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
        }

        if ($contentTypeField == null) {
            $contentTypeField = new Field();
        }
        $contentTypeField->fill($request->except('settings'));
        $fieldType->setSettings($request->input('settings'), true);
        $saveSettings = $fieldType->getSaveSettings();
        $contentTypeField->settings = ($saveSettings !== null && $saveSettings !== '') ? $saveSettings : null;
        $contentTypeField->content_type_id = $contentType->id;
        $contentTypeField->save();

        return redirect()->route('admin.content.type.field.fields', $contentType->id)
            ->with('message', "{$contentTypeField->label} was successfully updated.");
    }

    /**
     * Updates the sort order for the content type fields
     *
     * @return void
     */
    public function postOrder(Request $request, $contentType)
    {
        $tableOrder = $request->input('fields_table');

        $sort = 1;
        foreach ($tableOrder as $id) {
            Field::where('id', $id)->update(['sort' => $sort]);
            $sort++;
        }

        return '';
    }

    /**
     * Updates the setting fields on a field type change
     *
     * @return void
     */
    public function postSettings(Request $request, $contentType)
    {
        $typeId = $request->input('content_type_field_type_id');
        $fieldId = $request->input('field_id');

        $type = Type::find($typeId);
        $contentTypeField = Field::find($fieldId);

        return FieldType::baseFactory($type, $contentTypeField)
            ->settings();
    }

}