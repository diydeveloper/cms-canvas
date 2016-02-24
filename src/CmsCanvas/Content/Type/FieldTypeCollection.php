<?php 

namespace CmsCanvas\Content\Type;

use View;
use CmsCanvas\Database\Eloquent\Collection as CmsCanvasCollection;
use CmsCanvas\Models\Language;

class FieldTypeCollection extends CmsCanvasCollection {

	/**
	 * Sets data to the fields types from an array
	 *
	 * @param  array $array
	 * @param  bool $rawRequestData
	 * @return void
	 */
	public function fill(array $array, $rawRequestData = true)
	{
		foreach ($this->items as $item) {
			if (isset($array[$item->getKey()])) {
				$item->setData($array[$item->getKey()], $rawRequestData);
			}

			if (isset($array[$item->getMetadataKey()])) {
				$item->setMetadata($array[$item->getMetadataKey()], $rawRequestData);
			}

			if (isset($array[$item->getInlineEditableKey()])) {
				$item->setData($array[$item->getInlineEditableKey()], $rawRequestData);
			}
		}
	}

	/**
	 * Deletes field type's existing data from the database
	 * and saves the new data.
	 *
	 * @return void
	 */
	public function save()
	{
		$entries = [];

		foreach ($this->items as $item) {
			$item->save();
		}
	}

	/**
	 * Returns an array of each field type's validation rules
	 *
	 * @return array
	 */
	public function getValidationRules()
	{
		$rules = [];

		foreach ($this->items as $item) {
			$itemRules = $item->getValidationRules();

			if (! empty($itemRules)) {
				$rules = array_merge($rules, $itemRules);
			}
		}

		return $rules;
	}

	/**
	 * Returns an array of each field type's key and attribute name
	 *
	 * @return array
	 */
	public function getAttributeNames()
	{
		$attributeNames = [];

		foreach ($this->items as $item) {
			$attributeNames[$item->getKey()] = $item->getField()->label;
		}

		return $attributeNames;
	}

	/**
	 * Sets and entry to all field types
	 *
	 * @param \CmsCanvas\Models\Content\Entry
	 * @param void
	 */
	public function setEntry(\CmsCanvas\Models\Content\Entry $entry)
	{
		foreach ($this->items as $item) {
			$item->setEntry($entry);
		}
	}

    /**
     * Builds an array of views for administrative editing
     *
     * @return \Illuminate\View\View|array
     */
	public function getAdminViews()
	{
		$fieldIds = [];
		$fieldViews = [];
		$languages = Language::where('active', 1)->get();

		// Make a list of unique field ids
		foreach ($this->items as $item) {
			$fieldIds[$item->getField()->id] = $item->getField()->id;
		}

		foreach ($fieldIds as $fieldId) {
			$relatedFieldTypes = $this->getWhere('field.id', $fieldId);
			$fieldType = $relatedFieldTypes->first();

			if ($fieldType->inputField() !== null) {
	            $fieldViews[] = View::make('cmscanvas::admin.content.entry.editField')
	                ->with('languages', $languages)
	                ->with('fieldType', $fieldType)
	                ->with('relatedFieldTypes', $relatedFieldTypes);
            }
		}

		return $fieldViews;
	}

	/**
	 * Loops through items and creates revisions for each unique entry
	 *
	 * @param  array $array
	 * @return void
	 */
	public function createRevisions(array $array)
	{
		$entries = [];
		$data = [];

		// Group the request data by entry
		foreach ($this->items as $item) {
			$entry = $item->getEntry();

			if ($entry != null) {
				if (! isset($entries[$entry->id])) {
					$entries[$entry->id] = $entry;
				}

				if (isset($array[$item->getKey()])) {
					$data[$entry->id][$item->getKey()] = $array[$item->getKey()];
				}

				if (isset($array[$item->getMetadataKey()])) {
					$data[$entry->id][$item->getMetadataKey()] = $array[$item->getMetadataKey()];
				}

				if (isset($array[$item->getInlineEditableKey()])) {
					$data[$entry->id][$item->getInlineEditableKey()] = $array[$item->getInlineEditableKey()];
				}
			}
		}

		// Create a revision for each entry that has request data
		foreach ($entries as $entry) {
			if (isset($data[$entry->id])) {
				$entry->createRevision($data[$entry->id]);
			}
		}
	}

}