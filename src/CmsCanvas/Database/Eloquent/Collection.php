<?php 

namespace CmsCanvas\Database\Eloquent;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class Collection extends EloquentCollection {

	/**
	 * Returns a collection of items matching the value specified for the key
	 *
	 * @param  string  $key
	 * @param  mixed   $matchValue
	 * @return static
	 */
	public function getWhere($key, $matchValue)
	{
		$finalResults = [];

		foreach ($this->items as $item) {
			$results = $this->getData($item, $key);

			if (in_array($matchValue, $results->toArray())) {
				$finalResults[] = $item;
			}
		}

		return new static(array_values($finalResults));
	}

	/**
	 * Get a collection with the values of a given key
	 *
	 * @param  string  $items
	 * @param  string  $key
	 * @return static
	 */
	public function getData($items, $key)
	{
		foreach (explode('.', $key) as $segment) {
			if (! ($items instanceof Collection)) {
				$items = new static([$items]);
			}

			$newCollection = new static();

			foreach ($items as $item) {
				$result = data_get($item, $segment);

				if ($result instanceof Collection) {
					$newCollection = $newCollection->merge($result);
				} else {
					$newCollection[] = $result;
				}
			}

			$items = $newCollection;
		}

		return $items;
	}

	/**
	 * Returns the first item from a get where collection
	 *
	 * @param string $key
	 * @param mixed $matchValue
	 * @return mixed
	 */
	public function getFirstWhere($key, $matchValue)
	{
		$getWhereCollection = $this->getWhere($key, $matchValue);

		return $getWhereCollection->first();
	}

}