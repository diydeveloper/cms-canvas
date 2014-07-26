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
        $data = ''
    )
    {
        $this->setField($field);
        $this->setEntry($entry);
        $this->setLocale($locale);
        $this->setData($data);
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
        $data = ''
    )
    {
        $className = '\CmsCanvas\Content\Type\FieldType\\'.ucfirst(strtolower($field->type->key_name));

        return new $className($field, $entry, $locale, $data);
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
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Sets the settings object for the field type
     *
     * @param string $settings
     * @return void
     */
    public function setSettings($settings)
    {
        $settings = @json_decode($settings);

        if (is_object($settings))
        {
            $this->settings = $settings;
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
     * Returns the data to be saved to the database
     *
     * @return string
     */
    public function getSaveData()
    {
        return $this->data;
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