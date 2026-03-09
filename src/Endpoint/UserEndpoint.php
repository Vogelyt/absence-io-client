<?php

namespace Vogelyt\AbsenceIoClient\Endpoint;

use Vogelyt\AbsenceIoClient\Query\QueryBuilder;

/**
 * User endpoint for managing users (CRUD, invites).
 */
class UserEndpoint extends AbstractEndpoint
{
    protected string $entityName = 'users';

    /**
     * Send an invite to a user.
     *
     * The API accepts a small JSON body with the new user's email plus
     * optional metadata such as firstName, lastName, roleId, etc. If you only
     * provide the email, the server will still process the invite but may
     * complain about missing required fields.
     *
     * @param string $email      Email address to invite
     * @param array  $metadata   Additional invite fields (firstName, lastName, roleId, ...)
     * @return array Invite response data
     */
    public function invite(string $email, array $metadata = []): array
    {
        $payload = array_merge(['email' => $email], $metadata);
        return $this->http->post('users/invite', $payload);
    }

    /**
     * Get all users with optional filtering, sorting, and pagination.
     *
     * @param QueryBuilder|null $query Optional query builder with filters, sorting, etc.
     * @return array List of users
     */
    public function getAll(?QueryBuilder $query = null): array
    {
        $payload = $query ? $query->build() : [];
        return $this->http->post($this->entityName, $payload);
    }

    /**
     * Override the generic create method because the API does not allow
     * direct user creation.  New users must be invited instead.
     *
     * @param array $data
     * @throws \BadMethodCallException
     */
    public function create(array $data): array
    {
        throw new \BadMethodCallException(
            'Users cannot be created directly; use invite() instead.'
        );
    }
}