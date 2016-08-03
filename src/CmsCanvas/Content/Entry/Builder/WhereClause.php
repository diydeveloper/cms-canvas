<?php 

namespace CmsCanvas\Content\Entry\Builder;

use CmsCanvas\Exceptions\Exception;

class WhereClause {

    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var mixed
     */
    protected $relation;

    /**
     * @var array
     */
    protected $nested = [];

    /**
     * Constructor
     *
     * @param  string  $column
     * @param  string  $operator
     * @return void
     */
    public function __construct($column = null, $operator = '=', $value = null, $relation = 'and')
    {
        $this->setColumn($column);
        $this->setOperator($operator);
        $this->setValue($value);
        $this->setRelation($relation);
    }

    /**
     * Return the column class property
     *
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Return the column class property
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Return the column class property
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Adds the current where clause to the provided query builder
     *
     * @param  mixed  $query
     * @return void
     */
    public function build($query)
    {
        if (count($this->nested) > 0) {
            return $this->buildNested($query);
        }

        switch ($this->operator) {
            case '=':
            case '!=':
            case '>':
            case '>=':
            case '<':
            case '<=':
            case 'like':
            case 'not like':
                if ($this->relation == 'or') {
                    $query->orWhere($this->column, $this->operator, $this->value);
                } else {
                    $query->where($this->column, $this->operator, $this->value);
                }
                break;
            
            case 'in':
                if ($this->relation == 'or') {
                    $query->orWhereIn($this->column, $this->value);
                } else {
                    $query->whereIn($this->column, $this->value);
                }
                break;

            case 'not in':
                if ($this->relation == 'or') {
                    $query->orWhereNotIn($this->column, $this->value);
                } else {
                    $query->whereNotIn($this->column, $this->value);
                }
                break;

            case 'between':
                if ($this->relation == 'or') {
                    $query->orWhereBetween($this->column, $this->value);
                } else {
                    $query->whereBetween($this->column, $this->value);
                }
                break;

            case 'not between':
                if ($this->relation == 'or') {
                    $query->orWhereNotBetween($this->column, $this->value);
                } else {
                    $query->whereNotBetween($this->column, $this->value);
                }
                break;

            case 'is null':
                if ($this->relation == 'or') {
                    $query->orWhereNull($this->column);
                } else {
                    $query->whereNull($this->column);
                }
                break;

            case 'is not null':
                if ($this->relation == 'or') {
                    $query->orWhereNotNull($this->column);
                } else {
                    $query->whereNotNull($this->column);
                }
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * Set the column class property
     *
     * @param  string  $column
     * @return self
     */
    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * Set the value class property
     *
     * @param  string  $value
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Builds the nested array
     *
     * @return void
     */
    public function buildNested($query)
    {
        $method = ($this->relation == 'or') ? 'orWhere' : 'where';

        $builder = $this;
        $query->$method(function($query) use ($builder) {
            foreach ($builder->nested as $whereClause) {
                $whereClause->build($query);
            }
        });
    }

    /**
     * Set the operator class property
     *
     * @param  string  $operator
     * @return self
     */
    public function setOperator($operator)
    {
        $operator = strtolower($operator);

        $validOperators = [
            '=', 
            '!=', 
            '>', 
            '>=', 
            '<', 
            '<=', 
            'like', 
            'not like', 
            'in', 
            'not in', 
            'between', 
            'not between', 
            'is null', 
            'is not null',
        ];

        if (! in_array($operator, $validOperators)) {
            throw new Exception("The value {$operator} is not a valid operator.");
        }

        $this->operator = $operator;

        return $this;
    }

    /**
     * Set the relation class property
     *
     * @param  string  $relation
     * @return self
     */
    public function setRelation($relation)
    {
        $relation = strtolower($relation);

        if (! in_array($relation, ['or', 'and'])) {
            throw new Exception("The value {$relation} is not a valid relation.");
        }

        $this->relation = $relation;

        return $this;
    }

    /**
     * Set the relation class property
     *
     * @param  array  $items
     * @return void
     */
    public function createNestedEntryData(array $items)
    {
        reset($items);
        $entryColumns = [
            'id',
            'title', 
            'url_title', 
            'route',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'entry_status_id',
            'author_id',
            'created_at',
            'updated_at',
        ];

        foreach ($items as $item) {
            $whereClause = new WhereClause();
            if (isset($item['relation']) && ! in_array($item['relation'], ['and', 'or'])) {
                throw new Exception('Invalid value detected for \'relation\' in where clause.');
            }
            $relation = (isset($item['relation'])) ? $item['relation'] : 'and';
            $whereClause->setRelation($relation);

            if (is_array(current($item)) || isset($item['nested'])) {
                if (isset($item['nested'])) {
                    $nestedItems = $item['nested'];
                    if (! is_array(current($nestedItems))) {
                        $nestedItems = [$nestedItems];
                    } 
                } else {
                    $nestedItems = $item;
                }
                $whereClause->createNestedEntryData($nestedItems);
            } else {
                $field = (isset($item['field'])) ? $item['field'] : null;
                $value = (isset($item['value'])) ? $item['value'] : null;
                $operator = (isset($item['operator'])) ? $item['operator'] : '=';

                if ($field == null) {
                    throw new Exception('The where clause must a \'field\' property.');
                }

                if ($value == null && ! in_array($operator, ['is null', 'is not null'])) {
                    throw new Exception('The where clause must contain a \'value\' property.');
                }

                if (in_array($field, $entryColumns)) {
                    $whereClause->nested[] = new WhereClause('entries.'.$field, $operator, $value);
                } else {
                    $whereClause->nested[] = new WhereClause('entry_data.content_type_field_short_tag', '=', $field);
                    $whereClause->nested[] = new WhereClause('entry_data.data', $operator, $value);
                }
            }

            $this->nested[] = $whereClause;
        }
    }
}