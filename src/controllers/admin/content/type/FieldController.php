<?php namespace CmsCanvas\Controllers\Admin\Content\Type;

use View, Theme, Admin, Redirect, Validator, Request, Input, stdClass;
use CmsCanvas\Controllers\Admin\AdminController;
use CmsCanvas\Models\Content\Type\Field;
use CmsCanvas\Models\Content\Type\Field\Type;
use CmsCanvas\Content\Type\FieldType;

class FieldController extends AdminController {

    /**
     * Display all content types
     *
     * @return View
     */
    public function getFields($contentType)
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

        $this->layout->breadcrumbs = array(
            '/content/type' => 'Content Types',
            Request::path() => 'Content Type Fields'
        );
        $this->layout->content = $content;

    }

    /**
     * Saves the filter request to the session
     *
     * @return View
     */
    public function postFields($contentType)
    {
        Type::processFilterRequest();

        return Redirect::route('admin.content.type.field.fields', array($contentType->id));
    }

    /**
     * Deletes content type(s) that are posted in the selected array
     *
     * @return View
     */
    public function postDelete($contentType)
    {
        $selected = Input::get('selected');

        if (empty($selected) || ! is_array($selected)) {
            return Redirect::route('admin.content.type.field.fields', array($contentType->id))
                ->with('notice', 'You must select at least one field to delete.');
        }

        $selected = array_values($selected);

        Field::destroy($selected);

        return Redirect::route('admin.content.type.field.fields', array($contentType->id))
            ->with('message', 'The selected content type(s) were sucessfully deleted.');;
    }

    /**
     * Display add content type form
     *
     * @return View
     */
    public function getAdd($contentType)
    {
        // Routed to getEdit
    }

    /**
     * Create a new content type
     *
     * @return View
     */
    public function postAdd($contentType)
    {
        // Routed to postEdit
    }

    /**
     * Display edit content type form
     *
     * @return View
     */
    public function getEdit($contentType, $contentTypeField = null)
    {
        $content = View::make('cmscanvas::admin.content.type.field.edit');
        $content->contentType = $contentType;
        $content->field = $contentTypeField;

        $types = Type::orderBy('name', 'asc')
            ->get();

        if ($contentTypeField == null)
        {
            $selectedType = $types->first();
        }
        else
        {
            $selectedType = $types->getFirstWhere('id', $contentTypeField->content_type_field_type_id);
        }

        $fieldType = FieldType::baseFactory($selectedType->key_name, $contentTypeField);

        $content->fieldTypeSettings = $fieldType->settings();
        $content->fieldTypeSelectOptions = $types->getKeyValueArray('id', 'name', false);

        $this->layout->breadcrumbs = array(
            '/content/type' => 'Content Types',
            '/content/type/'.$contentType->id.'/field' => 'Content Type Fields',
            Request::path() => (($contentTypeField == null) ? 'Add' : 'Edit') . 'Field'
        );
        $this->layout->content = $content;
    }

    /**
     * Add or update a content type
     *
     * @return View
     */
    public function postEdit($contentType, $contentTypeField = null)
    {
        $rules = array(
            'content_type_field_type_id' => 'required',
            'label' => 'required',
            'short_tag' => 'required',
            'required' => 'required',
            'translate' => 'required',
        );

        $typeId = Input::get('content_type_field_type_id');
        if ( ! empty($typeId))
        {
            $type = Type::find($typeId);
            $fieldType = FieldType::baseFactory($type->key_name, $contentTypeField);
            $settingsValidationRules = $fieldType->getSettingsValidationRules();
            $rules = array_merge($rules, $settingsValidationRules);
        }

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            if ($contentTypeField == null)
            {
                return Redirect::route('admin.content.type.field.add', $contentType->id)
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
            else
            {
                return Redirect::route('admin.content.type.field.edit', array($contentType->id, $contentTypeField->id))
                    ->withInput()
                    ->with('error', $validator->messages()->all());
            }
        }

        if ($contentTypeField == null)
        {
            $contentTypeField = new Field();
        }
        $contentTypeField->fill(Input::except('settings'));
        $fieldType->setSettings(Input::get('settings'), true);
        $saveSettings = $fieldType->getSaveSettings();
        $contentTypeField->settings = ($saveSettings !== null && $saveSettings !== '') ? $saveSettings : null;
        $contentTypeField->content_type_id = $contentType->id;
        $contentTypeField->save();

        return Redirect::route('admin.content.type.field.fields', $contentType->id)
            ->with('message', "{$contentTypeField->label} was successfully updated.");
    }

    /**
     * Updates the sort order for the content type fields
     *
     * @return void
     */
    public function postOrder($contentType)
    {
        $tableOrder = Input::get('fields_table');

        $sort = 1;
        foreach ($tableOrder as $id)
        {
            Field::where('id', $id)->update(array('sort' => $sort));
            $sort++;
        }

        return '';
    }

    /**
     * Updates the setting fields on a field type change
     *
     * @return void
     */
    public function postSettings($contentType)
    {
        $typeId = Input::get('content_type_field_type_id');
        $fieldId = Input::get('field_id');

        $type = Type::find($typeId);
        $contentTypeField = Field::find($fieldId);

        return FieldType::baseFactory($type->key_name, $contentTypeField)
            ->settings();
    }

}