<?php

namespace Vogelyt\AbsenceIoClient\Query;

/**
 * QueryBuilder for constructing complex API queries with filters, sorting, relations, and pagination.
 */
class QueryBuilder
{
    private array $query = [];

    public function __construct()
    {
        $this->query = [
            'skip' => 0,
            'limit' => 50,
        ];
    }

    /**
     * Add a filter condition to the query.
     *
     * @param string $field Field name
     * @param mixed $value Value or operator array
     * @param string|null $operator Operator (e.g., '$eq', '$in', '$gt', '$like', '$lte')
     * @return self
     */
    public function where(string $field, mixed $value, ?string $operator = null): self
    {
        if ($operator === null) {
            $this->query['filter'][$field] = $value;
        } else {
            $this->query['filter'][$field] = [$operator => $value];
        }
        return $this;
    }

    /**
     * Add an 'in' filter (checks if field is in the given array).
     *
     * @param string $field
     * @param array $values
     * @return self
     */
    public function whereIn(string $field, array $values): self
    {
        return $this->where($field, $values, '$in');
    }

    /**
     * Add a 'not in' filter.
     *
     * @param string $field
     * @param array $values
     * @return self
     */
    public function whereNotIn(string $field, array $values): self
    {
        return $this->where($field, $values, '$nin');
    }

    /**
     * Add a 'greater than' filter.
     *
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function whereGreaterThan(string $field, mixed $value): self
    {
        return $this->where($field, $value, '$gt');
    }

    /**
     * Add a 'greater than or equal' filter.
     *
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function whereGreaterThanOrEqual(string $field, mixed $value): self
    {
        return $this->where($field, $value, '$gte');
    }

    /**
     * Add a 'less than' filter.
     *
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function whereLessThan(string $field, mixed $value): self
    {
        return $this->where($field, $value, '$lt');
    }

    /**
     * Add a 'less than or equal' filter.
     *
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function whereLessThanOrEqual(string $field, mixed $value): self
    {
        return $this->where($field, $value, '$lte');
    }

    /**
     * Add a 'like' filter (case-insensitive substring match).
     *
     * @param string $field
     * @param string $value
     * @return self
     */
    public function whereLike(string $field, string $value): self
    {
        return $this->where($field, $value, 'like');
    }

    /**
     * Add an OR condition (multiple conditions, any can match).
     *
     * @param array $conditions Array of [field => value] pairs
     * @return self
     */
    public function orWhere(array $conditions): self
    {
        $this->query['filter']['$or'][] = $conditions;
        return $this;
    }

    /**
     * Add an AND condition (multiple conditions, all must match).
     *
     * @param array $conditions Array of [field => value] pairs
     * @return self
     */
    public function andWhere(array $conditions): self
    {
        $this->query['filter']['$and'][] = $conditions;
        return $this;
    }

    /**
     * Set sorting by a field.
     *
     * @param string $field
     * @param int $direction 1 for ascending, -1 for descending
     * @return self
     */
    public function sortBy(string $field, int $direction = 1): self
    {
        $this->query['sortBy'][$field] = $direction;
        return $this;
    }

    /**
     * Alias for ascending sort.
     *
     * @param string $field
     * @return self
     */
    public function orderBy(string $field): self
    {
        return $this->sortBy($field, 1);
    }

    /**
     * Alias for descending sort.
     *
     * @param string $field
     * @return self
     */
    public function orderByDesc(string $field): self
    {
        return $this->sortBy($field, -1);
    }

    /**
     * Set pagination offset.
     *
     * @param int $skip Number of records to skip
     * @return self
     */
    public function skip(int $skip): self
    {
        $this->query['skip'] = $skip;
        return $this;
    }

    /**
     * Alias for offset (page * limit).
     *
     * @param int $page Page number (1-indexed)
     * @param int $perPage Records per page
     * @return self
     */
    public function page(int $page, int $perPage = 50): self
    {
        $this->query['skip'] = ($page - 1) * $perPage;
        $this->query['limit'] = $perPage;
        return $this;
    }

    /**
     * Set pagination limit.
     *
     * @param int $limit Number of records to return (max 100)
     * @return self
     */
    public function limit(int $limit): self
    {
        // Cap at 100 per API documentation
        $this->query['limit'] = min($limit, 100);
        return $this;
    }

    /**
     * Add relations to resolve (e.g., ['assignedToId', 'departmentId']).
     *
     * @param array|string $relations Field names or relationship names
     * @return self
     */
    public function with($relations): self
    {
        if (is_string($relations)) {
            $relations = [$relations];
        }
        if (!isset($this->query['relations'])) {
            $this->query['relations'] = [];
        }
        $this->query['relations'] = array_merge($this->query['relations'], $relations);
        return $this;
    }

    /**
     * Build the final query array for API requests.
     *
     * @return array
     */
    public function build(): array
    {
        // Clean up empty filter sections
        if (isset($this->query['filter']) && empty($this->query['filter'])) {
            unset($this->query['filter']);
        }

        return $this->query;
    }

    /**
     * Reset the query builder.
     *
     * @return self
     */
    public function reset(): self
    {
        $this->query = [
            'skip' => 0,
            'limit' => 50,
        ];
        return $this;
    }
}
