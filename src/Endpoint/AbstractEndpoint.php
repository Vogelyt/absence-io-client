<?php

namespace Vogelyt\AbsenceIoClient\Endpoint;

use Vogelyt\AbsenceIoClient\Http\HttpClient;
use Vogelyt\AbsenceIoClient\Query\QueryBuilder;

/**
 * Base class for API endpoints providing common CRUD operations.
 */
abstract class AbstractEndpoint
{
    protected string $entityName;

    public function __construct(
        protected HttpClient $http
    ) {}

    /**
     * Get all entities with optional filtering, sorting, and pagination.
     *
     * @param QueryBuilder|null $query Optional query builder with filters, sorting, etc.
     * @return array List of entities
     */
    public function getAll(?QueryBuilder $query = null): array
    {
        $payload = $query ? $query->build() : [];
        return $this->http->post($this->entityName, $payload);
    }

    /**
     * Get a single entity by ID.
     *
     * @param string|int $id
     * @return array Entity data
     */
    public function getSingle($id): array
    {
        return $this->http->get("{$this->entityName}/{$id}");
    }

    /**
     * Create a new entity.
     *
     * @param array $data Entity data
     * @return array Created entity with ID
     */
    public function create(array $data): array
    {
        return $this->http->post("{$this->entityName}/create", $data);
    }

    /**
     * Update an existing entity.
     *
     * @param string|int $id
     * @param array $data Partial data to update
     * @return array Updated entity data
     */
    public function update($id, array $data): array
    {
        return $this->http->put("{$this->entityName}/{$id}", $data);
    }

    /**
     * Delete an entity by ID.
     *
     * @param string|int $id
     * @return array Response from API (usually empty or confirmation)
     */
    public function delete($id): array
    {
        return $this->http->delete("{$this->entityName}/{$id}");
    }

    /**
     * Create a new QueryBuilder instance for complex queries.
     *
     * @return QueryBuilder
     */
    public function query(): QueryBuilder
    {
        return new QueryBuilder();
    }
}
