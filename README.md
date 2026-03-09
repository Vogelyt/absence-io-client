# Absence.io PHP Client

A comprehensive, production-ready PHP client library for the [absence.io API](https://docs.absence.io/) with full CRUD operations, Hawk and OAuth 2.0 authentication, and advanced querying capabilities.

## Features

- **Full API Coverage**: CRUD operations for all absence.io entities (absences, users, teams, departments, etc.)
- **Dual Authentication**: Support for both Hawk and OAuth 2.0 authentication
- **Advanced Querying**: Powerful query builder with filters, sorting, relations, and pagination
- **Type-Safe**: Built with PHP 8.1+ with strict types
- **Well-Tested**: Comprehensive test suite with 59+ unit tests
- **Error Handling**: Custom exceptions for different API error scenarios
- **Easy to Use**: Simple, fluent API for developers

## Installation

### Using Composer (Recommended)

```bash
composer require vogelyt/absence-io-client
```

### Manual Installation

Clone the repository and install dependencies:

```bash
git clone https://github.com/vogelyt/absence-io-client.git
cd absence-io-client
composer install
```

## Quick Start

### Using Hawk Authentication

```php
<?php

use Vogelyt\AbsenceIoClient\AbsenceClient;
use Vogelyt\AbsenceIoClient\Config\Config;

// Initialize with Hawk credentials
$config = new Config('your-hawk-id', 'your-hawk-key');
$client = new AbsenceClient($config);

// Fetch all users
$users = $client->users()->getAll();

foreach ($users as $user) {
    echo "User: " . $user['name'] . "\n";
}
?>
```

### Using OAuth 2.0

```php
<?php

use Vogelyt\AbsenceIoClient\AbsenceClient;
use Vogelyt\AbsenceIoClient\Config\Config;

// Initialize with OAuth credentials
$config = Config::withOAuth('your-client-id', 'your-client-secret');
$client = new AbsenceClient($config);

// Use the client as normal (token fetching is automatic)
$absences = $client->absences()->getAll();
?>
```

## Authentication

### Hawk Authentication

Hawk is a built-in authentication method that signs each request. You'll need your Hawk ID and Key:

```php
$config = new Config('hawk-id', 'hawk-key');
$client = new AbsenceClient($config);
```

### OAuth 2.0

OAuth 2.0 provides token-based authentication. The client automatically handles token fetching and renewal:

```php
$config = Config::withOAuth('client-id', 'client-secret');
$client = new AbsenceClient($config);

// Bearer token is automatically added to requests
```

## API Endpoints

The client provides access to all absence.io entities (paths are plural):

```php
$client->absences();        // Absences  (/api/v2/absences)
$client->users();           // Users    (/api/v2/users)
$client->teams();           // Teams    (/api/v2/teams)
$client->departments();     // Departments (/api/v2/departments)
$client->locations();       // Locations (/api/v2/locations)
$client->reasons();         // Reasons  (/api/v2/reasons)
$client->allowanceTypes();  // Allowance types (/api/v2/allowanceTypes)
$client->timespans();       // Timespans (/api/v2/timespans)
$client->holidays();        // Holidays (/api/v2/holidays)
```

## CRUD Operations

Each endpoint supports full CRUD operations:

### Create

**Users** cannot be created directly via the API; you must send an invitation instead.
The invite endpoint accepts extra fields such as firstName, lastName, roleId,
company id, etc. Example payload that passes server validation:

```php
$invite = $client->users()->invite('foo2.bar@bar.com', [
    'firstName' => 'foo',
    'lastName' => 'foo',
    'roleId' => '000000000000000000001000',
]);
echo "Invite sent to " . $invite['email'];
```

For other entities (e.g. absences) use the standard `create` method:

```php
$absence = $client->absences()->create([
    'assignedToId' => '...',
    'start' => '2026-03-10',
    'end' => '2026-03-12',
    'reasonId' => '...'
]);
```


### Read (Single)

```php
$user = $client->users()->getSingle(123);
echo $user['name'];
```

### Read (List)

```php
$users = $client->users()->getAll();
```

### Update

```php
$updated = $client->users()->update(123, [
    'name' => 'Jane Doe'
]);
```

### Delete

```php
$client->users()->delete(123);
```

## Query Builder

The query builder provides a fluent interface for constructing complex queries:

### Basic Filtering

```php
$query = $client->absences()->query()
    ->where('status', 'approved')
    ->whereGreaterThan('created_at', '2024-01-01');

$absences = $client->absences()->getAll($query);
```

### Filter Operators

```php
$query = $client->users()->query()
    ->whereIn('id', [1, 2, 3])                  // IN
    ->whereNotIn('status', ['inactive'])        // NOT IN
    ->whereGreaterThan('salary', 50000)         // Greater than
    ->whereGreaterThanOrEqual('age', 18)        // Greater or equal
    ->whereLessThan('experience', 5)            // Less than
    ->whereLessThanOrEqual('days_left', 10)     // Less or equal
    ->whereLike('name', 'John');                // Case-insensitive substring
```

### Sorting

```php
$query = $client->absences()->query()
    ->orderBy('created_at')                      // Ascending
    ->orderByDesc('name');                       // Descending
```

### Pagination

```php
// Using skip/limit
$query = $client->users()->query()
    ->skip(20)
    ->limit(10);

// Or using page/perPage
$query = $client->users()->query()
    ->page(3, 50);  // Page 3, 50 items per page
```

### Relations

Resolve related entity IDs to full objects:

```php
$query = $client->absences()->query()
    ->with(['assignedToId', 'departmentId']);

$absences = $client->absences()->getAll($query);
```

### Complex Queries

Chain multiple conditions:

```php
$query = $client->absences()->query()
    ->where('status', 'pending')
    ->whereGreaterThan('start_date', '2024-01-01')
    ->whereLike('reason', 'vacation')
    ->orderByDesc('created_at')
    ->page(1, 25)
    ->with(['assignedToId', 'teamId']);

$absences = $client->absences()->getAll($query);
```

## User-Specific Operations

The users endpoint provides additional operations:

### Invite a User

```php
$result = $client->users()->invite('newuser@example.com');
echo "Invitation sent to: " . $result['email'];
```

## Error Handling

The client throws specific exceptions for different error scenarios:

```php
<?php

use Vogelyt\AbsenceIoClient\Exception\AuthException;
use Vogelyt\AbsenceIoClient\Exception\ValidationException;
use Vogelyt\AbsenceIoClient\Exception\NotFoundException;
use Vogelyt\AbsenceIoClient\Exception\ApiException;

try {
    $user = $client->users()->getSingle(999);
} catch (NotFoundException $e) {
    echo "User not found";
} catch (AuthException $e) {
    echo "Authentication failed: " . $e->getMessage();
} catch (ValidationException $e) {
    echo "Validation error: " . $e->getMessage();
} catch (ApiException $e) {
    echo "API error: " . $e->getMessage();
}
?>
```

## Configuration

Customize the client configuration:

```php
$config = new Config('hawk-id', 'hawk-key');

// Set custom base URL (optional, defaults to absence.io)
$config->setBaseUrl('https://custom.absence.io/api/v2');

$client = new AbsenceClient($config);
```

## Testing

### Run Tests

```bash
composer test
```

or directly:

```bash
./vendor/bin/phpunit
```

### Docker Development

Build and start Docker environment:

```bash
docker compose build
docker compose run --rm php composer install
```

Run tests in Docker:

```bash
docker compose run --rm php ./vendor/bin/phpunit
```

## Examples

### Fetch All Absences for a User

```php
$query = $client->absences()->query()
    ->where('assignedToId', 123)
    ->orderByDesc('start_date');

$absences = $client->absences()->getAll($query);
```

### Create an Absence

```php
$absence = $client->absences()->create([
    'assignedToId' => 123,
    'start' => '2024-02-01',
    'end' => '2024-02-05',
    'reasonId' => 1
]);
```

### Update an Absence Status

```php
$updated = $client->absences()->update(456, [
    'status' => 'approved'
]);
```

### Get Users from a Specific Team

```php
$query = $client->users()->query()
    ->where('teamId', 789)
    ->orderBy('name')
    ->page(1, 50);

$users = $client->users()->getAll($query);
```

### Find Departments

```php
$query = $client->departments()->query()
    ->whereLike('name', 'engineering')
    ->with(['parentId']);

$departments = $client->departments()->getAll($query);
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For issues, questions, or feature requests, please use the [GitHub Issues](https://github.com/vogelyt/absence-io-client/issues).

## API Documentation

For complete API documentation, visit: [https://docs.absence.io/](https://docs.absence.io/)
