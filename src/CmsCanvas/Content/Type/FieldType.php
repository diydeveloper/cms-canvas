<?php 

namespace CmsCanvas\Content\Type;

use ArrayAccess, Admin;
use CmsCanvas\Models\Content\Entry\Data as EntryData;
use CmsCanvas\Content\Type\FieldTypeCollection;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type\Field;

abstract class FieldType implements ArrayAccess {

    /**
     * The content type field
     *
     * @var \CmsCanvas\Models\Content\Type\Field
     */
    protected $field;

    /**
     * The entry associated with the field and the current data
     *
     * @var \CmsCanvas\Models\Content\Entry
     */
    protected $entry;

    /**
     * The entry data associated with the field and entry
     *
     * @var \CmsCanvas\Models\Content\Entry\Data
     */
    protected $entryData;

    /**
     * The language locale that the current field is using
     *
     * @var string
     */
    protected $locale;

    /**
     * The data associated with the field and entry
     *
     * @var string
     */
    protected $data;

    /**
     * A object containing metadata for the field type
     *
     * @var array
     */
    protected $metadata = [];

    /**
     * A object containing additional settings for the field type
     *
     * @var array
     */
    protected $settings = [];

    /**
     * Constructor
     *
     * @param \CmsCanvas\Models\Content\Type\Field $field
     * @param \CmsCanvas\Models\Content\Entry $entry
     * @param string $locale
     * @param string $data
     * @return void
     */
    public function __construct(
        \CmsCanvas\Models\Content\Type\Field $field = null, 
        \CmsCanvas\Models\Content\Entry $entry = null,
        $locale = null,
        $data = '',
        $metadata = null 
    )
    {
        $this->setField($field);
        $this->setEntry($entry);
        $this->setLocale($locale);
        $this->setData($data);
        $this->setMetadata($metadata);
    }

    /**
     * Factors the current class by type
     *
     * @param \CmsCanvas\Models\Content\Type\Field $field
     * @param \CmsCanvas\Models\Content\Entry $entry
     * @param string $locale
     * @param string $data
     * @return \CmsCanvas\Content\Type\FieldType
     */
    public static function factory(
        \CmsCanvas\Models\Content\Type\Field $field, 
        \CmsCanvas\Models\Content\Entry $entry = null,
        $locale = null,
        $data = '',
        $metadata = null 
    )
    {
        $className = '\CmsCanvas\Content\Type\FieldType\\'.ucfirst(strtolower($field->type->key_name));

        return new $className($field, $entry, $locale, $data, $metadata);
    }

    /**
     * Factors the current class using the provided type key name
     *
     * @param string $type
     * @param \CmsCanvas\Models\Content\Type\Field $field
     * @return \CmsCanvas\Content\Type\FieldType
     */
    public static function baseFactory($type, $field = null)
    {
        $className = '\CmsCanvas\Content\Type\FieldType\\'.ucfirst(strtolower($type));

        return new $className($field);
    }

    /**
     * Returns a view of the field input
     *
     * @return \Illuminate\View\View
     */
    abstract public function inputField();

    /**
     * Returns the rendered data or editable data for the field
     *
     * @return mixed
     */
    final public function render()
    {
        if ($this->isInlineEditable()) {
            return $this->renderEditableContents();
        } else {
            return $this->renderContents();
        }
    }

    /**
     * Returns the rendered data for the field
     *
     * @return mixed
     */
    public function renderContents()
    {
        return $this->data;
    }

    /**
     * Returns the editable data for the field
     *
     * @return mixed
     */
    public function renderEditableContents()
    {
        return $this->data;
    }

    /**
     * Returns true if the current field is inline editable and inline editing 
     * is enabled
     *
     * @return bool
     */
    public function isInlineEditable()
    {
        if ($this->field == null || $this->entry == null) {
            return false;
        }

        if ($this->getSetting('inline_editable', false) == false) {
            return false;
        }

        if (! Admin::isInlineEditingEnabled()) {
            return false;
        }

        try {
            $this->entry->contentType->checkAdminEntryEditPermissions();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Returns a unique identifier for the field type
     *
     * @return string
     */
    public function getKey()
    {
        return 'field_id_'.$this->field->id.'_'.$this->locale;
    }

    /**
     * Returns a unique metadata identifier for the field type
     *
     * @param string $property|null
     * @return string
     */
    public function getMetadataKey($property = null)
    {
        $metadataKey = 'field_id_'.$this->field->id.'_'.$this->locale.'_metadata';

        if ($property != null) {
            $metadataKey .= '['.$property.']';
        }

        return $metadataKey;
    }

    /**
     * Returns a unique setting identifier for the field type
     *
     * @param string $property|null
     * @return string
     */
    public function getSettingsKey($property = null)
    {
        $settingsKey = 'settings';

        if ($property != null) {
            $settingsKey .= '['.$property.']';
        }

        return $settingsKey;
    }

    /**
     * Returns a unique identifier for inline editing the field type
     *
     * @return string
     */
    public function getInlineEditableKey()
    {
        return 'cc_field_'.$this->entry->id.'_'.$this->field->id.'_'.$this->locale;
    }

    /**
     * Returns the metadata value for the provided property
     *
     * @param string $property
     * @return string
     */
    public function getMetadata($property, $defaultValue = null)
    {
        if (isset($this->metadata[$property]) && $this->metadata[$property] !== '' 
            && $this->metadata[$property] !== null
        ) {
            return $this->metadata[$property];
        } else {
            return $defaultValue;
        }
    }

    /**
     * Sets the entry class variable
     *
     * @param \CmsCanvas\Models\Content\Type\Field $field
     * @return void
     */
    public function setField($field)
    {
        $this->field = $field;

        if (isset($field->settings)) {
            $this->setSettings($field->settings);
        }
    }

    /**
     * Sets the entry class variable
     *
     * @param \CmsCanvas\Models\Content\Entry $entry
     * @return void
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

    /**
     * Sets the locale class variable
     *
     * @param string $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Sets the data class variable
     *
     * @param  string $data
     * @param  bool $rawRequestData
     * @return void
     */
    public function setData($data, $rawRequestData = false)
    {
        $this->data = $data;
    }

    /**
     * Sets the settings array for the field type
     *
     * @param  mixed $settings
     * @param  bool $rawRequestData
     * @return void
     */
    public function setSettings($settings, $rawRequestData = false)
    {
        if ($rawRequestData) {
            if (! is_array($settings)) {
                return null;
            }

            $settingsArray = [];
            foreach ($settings as $key => $value) {
                if ($value !== '' && $value !== null) {
                    $settingsArray[$key] = $value;
                }
            }
        } else {
            $settingsArray = @json_decode($settings, true);
        }

        if (is_array($settingsArray) && count($settingsArray) > 0) {
            $this->settings = $settingsArray;
        } else {
            $this->settings = [];
        }
    }

    /**
     * Set a setting value to the settings array
     *
     * @param  string $key
     * @param  string $value
     * @return self
     */
    public function setSettingValue($key, $value)
    {
        $this->settings[$key] = $value;

        return $this;
    }

    /**
     * Returns the setting value for the provided property
     *
     * @param string $property
     * @return mixed
     */
    public function getSetting($property, $defaultValue = null)
    {
        if (isset($this->settings[$property]) && $this->settings[$property] !== '' 
            && $this->settings[$property] !== null
        ) {
            return $this->settings[$property];
        } else {
            return $defaultValue;
        }
    }

    /**
     * Sets the settings object for the field type
     *
     * @param  mixed $settings
     * @param  bool $rawRequestData
     * @return void
     */
    public function setMetadata($metadata, $rawRequestData = false)
    {
        // The data is an array when being set from a form post
        if ($rawRequestData) {
            if (! is_array($metadata)) {
                return null;
            }

            $metadataArray = [];
            foreach ($metadata as $key => $value) {
                if ($value !== '' && $value !== null) {
                    $metadataArray[$key] = $value;
                }
            }
        } else {
            $metadataArray = @json_decode($metadata, true);
        }

        if (is_array($metadataArray) && count($metadataArray) > 0) {
            $this->metadata = $metadataArray;
        } else {
            $this->metadata = [];
        }
    }

    /**
     * Set a metadata value to the metadata array
     *
     * @param  string $key
     * @param  string $value
     * @return self
     */
    public function setMetadataValue($key, $value)
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * Returns an array of validation rules 
     *
     * @return array
     */
    public function getValidationRules()
    {
        $validationRules = [];

        if ($this->field->required) {
            $validationRules[$this->getKey()] = 'required';
        }

        return $validationRules;
    }

    /**
     * Returns an array of validation rules for settings
     *
     * @return array
     */
    public function getSettingsValidationRules()
    {
        return [];
    }

    /**
     * Returns the data to be saved to the database
     *
     * @return string
     */
    public function getSaveData()
    {
        return $this->data;
    }

    /**
     * Returns the data for the current field type
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the entry for the current field type
     *
     * @return \CmsCanvas\Models\Content\Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Returns the field for the current field type
     *
     * @return \CmsCanvas\Models\Content\Type\Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Returns a serialized metadata object to be saved to the database
     *
     * @return string
     */
    public function getSaveMetadata()
    {
        if (count($this->metadata) > 0) {
            return @json_encode($this->metadata);
        }

        return null;
    }

    /**
     * Returns a serialized settings object to be saved to the database
     *
     * @return string
     */
    public function getSaveSettings()
    {
        if (count($this->settings) > 0) {
            return @json_encode($this->settings);
        }

        return null;
    }

    /**
     * Returns a view of additional settings for the field
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return '';
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    /**
     * Populates the current field type with data from the provided entry data
     *
     * @param  \CmsCanvas\Models\Content\Entry\Data $entryData
     * @return self
     */
    public function setEntryData(EntryData $entryData = null)
    {
        $this->entryData = $entryData;

        return $this;
    }

    /**
     * Populates the current field type with data from the provided entry data
     *
     * @param  \CmsCanvas\Models\Content\Entry\Data $entryData
     * @return self
     */
    public function populateFromEntryData(EntryData $entryData)
    {
        $this->setEntryData($entryData);
        $this->setData($entryData->data, false);
        $this->setMetadata($entryData->metadata);
        $this->setLocale($entryData->language_locale);

        return $this;
    }

    /**
     * Saves the current entry field data to the database
     *
     * @return void
     */
    public function save()
    {
        if ($this->field == null) {
            throw new \CmsCanvas\Exceptions\Exception('A field must be specified to save the entry data.');
        }
        
        if ($this->entry == null) {
            throw new \CmsCanvas\Exceptions\Exception('A entry must be specified to save the entry data.');
        }

        $data = $this->getSaveData();
        $metadata = $this->getSaveMetadata();

        // Only insert data if it is not an empty string and not null
        if (($data !== '' && $data !== null) || ($metadata !== '' && $metadata !== null)) {
            $entryData = ($this->entryData != null) ? $this->entryData : new EntryData;
            $entryData->entry_id = $this->entry->id;
            $entryData->content_type_field_id = $this->field->id;
            $entryData->content_type_field_short_tag = $this->field->short_tag;
            $entryData->language_locale = $this->locale;
            $entryData->data = ($data === '' || $data === null) ? null : $data;
            $entryData->metadata = ($metadata === '' || $metadata === null) ? null : $metadata;
            $entryData->save();

            $this->setEntryData($entryData);
        } else {
            // Delete the entry data record if there is no data
            if ($this->entryData != null) {
                $this->entryData->delete();
                $this->setEntryData(null);
            }
        }
    }

    /**
     * Returns collection of field types using an array of inline editqble keys
     *
     * @param  array $keys
     * @return \CmsCanvas\Content\Type\FieldTypeCollection
     */
    public static function findByInlineEditableKeys(array $keys)
    {
        $entries = [];
        $fields = [];
        $fieldTypeCollection = new FieldTypeCollection;

        foreach ($keys as $key) {
            if (preg_match("/cc_field_(\d+)_(\d+)_(\w+)/", $key, $matches)) {
                $entryId = $matches[1];
                $fieldId = $matches[2];
                $locale = $matches[3];

                if (isset($entries[$entryId])) {
                    $entry = $entries[$entryId];
                } else {
                    $entry = Entry::find($entryId);
                    if ($entry == null) {
                        throw new \CmsCanvas\Exceptions\Exception("Unable to find entry id# {$entryId}.");
                    }
                    $entries[$entryId] = $entry;
                }

                if (isset($fields[$fieldId])) {
                    $field = $fields[$fieldId];
                } else {
                    $field = Field::with('type')->find($fieldId);
                    if ($field == null) {
                        throw new \CmsCanvas\Exceptions\Exception("Unable to find content field id# {$fieldId}.");
                    }
                    $fields[$fieldId] = $field;
                }

                $fieldType = FieldType::factory($field, $entry, $locale);

                $entryData = EntryData::where('entry_id', $entryId)
                    ->where('content_type_field_id', $fieldId)
                    ->where('language_locale', $locale)
                    ->first();

                if ($entryData != null) {
                    $fieldType->populateFromEntryData($entryData);
                }

                $fieldTypeCollection[] = $fieldType;
            }
        }

        return $fieldTypeCollection;
    }

    /**
     * Returns collection of field types using an array of inline editqble keys
     *
     * @param  string $key
     * @return \CmsCanvas\Content\Type\FieldType
     */
    public static function findByInlineEditableKey($key)
    {
        $contentFields = self::findByInlineEditableKeys([$key]);
        return $contentFields->first();
    }

}