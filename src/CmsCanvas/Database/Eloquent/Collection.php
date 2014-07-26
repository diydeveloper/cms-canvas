<?php namespace CmsCanvas\Database\Eloquent;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class Collection extends EloquentCollection {

	/**
	 * Returns a collection of items matching the value specified for the key
	 *
	 * @param string $key
	 * @param mixed $matchValue
	 * @return \CmsCanvas\Database\Eloquent\Collection
	 */
	public function getWhere($key, $matchValue)
	{
		$arrayItems = $this->toArray();
		$segments = explode('.', $key);
		$segmentCount = count($segments);
		$firstSegment = array_shift($segments);
		$finalResults = array();
		$depth = 1;

		foreach ($this->items as $item)
		{
			if ($segmentCount > 1)
			{
				$array = isset($item[$firstSegment]) ? $item[$firstSegment] : array();

				foreach ($segments as $segment)
		        {
		            $results = array();
		 
		            foreach ($array as $nestedArray)
		            {
		                $nestedArray = (array) $nestedArray;

		                $value = isset($nestedArray[$segment]) ? $nestedArray[$segment] : null;

		                if ($depth == $segmentCount)
		                {
			                if ($value == $matchValue)
			                {
				                $finalResults[] = $item;
			                }
			            }
			            else
			            {
			            	$results[] = $value;
			            }
		            }
		 
		            $array = array_values($results);
		            $depth++;
		        }
			}
			else 
			{
				$value = isset($item[$firstSegment]) ? $item[$firstSegment] : null;

	            if ($value == $matchValue)
	            {
	                $finalResults[] = $item;
	            }
			}
		}

		return new static(array_values($finalResults));
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

    /**
     * Returns an array of keys and cooresponding values for the current items
     *
	 * @param string $key
	 * @param string $value
	 * @param mixed $arrayPrefix
     * @return array
     */
    public function getKeyValueArray($key, $value, $arrayPrefix = array('' => ''))
    {
        $array = array();

    	if (is_array($arrayPrefix))
    	{
    		$array = $arrayPrefix;
    	}

        foreach ($this->items as $item) {
        	if ( ! isset($item->$value) && method_exists($item, $value))
        	{
	            $array[$item->$key] = $item->$value();
        	}
        	else
        	{
	            $array[$item->$key] = $item->$value;
        	}
        }

        return $array;
    }

}