<?php namespace CmsCanvas\Content\Type;

abstract class FieldType {

    /**
     * The content type field
     *
     * @var \CmsCanvas\Models\Content\Type\Field
     */
    public $field;

    /**
     * The entry associated with the field and the current data
     *
     * @var \CmsCanvas\Models\Content\Entry
     */
    public $entry;

    /**
     * The data associated with the field and entry
     *
     * @var string
     */
    public $data;

    /**
     * A object containing metadata for the field type
     *
     * @var object
     */
    public $metadata;

    /**
     * A object containing additional settings for the field type
     *
     * @var object
     */
    public $settings;

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
     * Returns the rendered data for the field
     *
     * @return mixed
     */
    public function render()
    {
        return $this->data;
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

        if ($property != null)
        {
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

        if ($property != null)
        {
            $settingsKey .= '['.$property.']';
        }

        return $settingsKey;
    }

    /**
     * Returns the metadata value for the provided property
     *
     * @param string $property
     * @return string
     */
    public function getMetadata($property, $defaultValue = null)
    {
        if (isset($this->metadata->$property) && $this->metadata->$property !== '' 
            && $this->metadata->$property !== null
        )
        {
            return $this->metadata->$property;
        }
        else
        {
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

        if (isset($field->settings))
        {
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
     * @param string $data
     * @param bool $fromFormData
     * @return void
     */
    public function setData($data, $fromFormData = false)
    {
        $this->data = $data;
    }

    /**
     * Sets the settings object for the field type
     *
     * @param string $settings
     * @return void
     */
    public function setSettings($settings, $fromFormData = false)
    {
        if ($fromFormData)
        {
            $filteredSettings = array();
            foreach ($settings as $key => $value) {
                if ($value !== '' && $value !== null)
                {
                    $filteredSettings[$key] = $value;
                }
            }
            $this->settings = (count($filteredSettings) > 0) ? (object) $filteredSettings : null;
        }
        else
        {
            $settings = @json_decode($settings);
            $this->settings = (is_object($settings)) ? $settings : null;
        }
    }

    /**
     * Returns the setting value for the provided property
     *
     * @param string $property
     * @return mixed
     */
    public function getSetting($property, $defaultValue = null)
    {
        if (isset($this->settings->$property) && $this->settings->$property !== '' 
            && $this->settings->$property !== null
        )
        {
            return $this->settings->$property;
        }
        else
        {
            return $defaultValue;
        }
    }

    /**
     * Sets the settings object for the field type
     *
     * @param string $settings
     * @param bool $fromFormData
     * @return void
     */
    public function setMetadata($metadata, $fromFormData = false)
    {
        // The data is an array when being set from a form post
        if ($fromFormData)
        {
            $filteredMetadata = array();
            foreach ($metadata as $key => $value) {
                if ($value !== '' && $value !== null)
                {
                    $filteredMetadata[$key] = $value;
                }
            }
            $this->metadata = (count($filteredMetadata) > 0) ? (object) $filteredMetadata : null;
        }
        else
        {
            $metadata = @json_decode($metadata);
            $this->metadata = (is_object($metadata)) ? $metadata : null;
        }
    }

    /**
     * Returns an array of validation rules 
     *
     * @return array
     */
    public function getValidationRules()
    {
        $validationRules = array();

        if ($this->field->required)
        {
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
        return array();
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
     * Returns a serialized metadata object to be saved to the database
     *
     * @return string
     */
    public function getSaveMetadata()
    {
        if ($this->metadata !== null && $this->metadata !== '')
        {
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
        if ($this->settings !== null && $this->settings !== '')
        {
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

}