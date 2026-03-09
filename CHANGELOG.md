# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-03-09

### Added

- Full API coverage for all absence.io entities (absences, users, teams, departments, locations, reasons, allowance types, timespans, holidays)
- Complete CRUD operations (Create, Read, getSingle, Update, Delete) for all endpoints
- Advanced QueryBuilder with support for:
  - Filtering with multiple operators ($eq, $in, $nin, $gt, $gte, $lt, $lte, like)
  - Sorting by multiple fields (ascending/descending)
  - Pagination with skip/limit and page/perPage helpers
  - Relations resolution
  - Complex queries with OR/AND conditions
- Dual authentication support:
  - Hawk authentication (request signing)
  - OAuth 2.0 with automatic token fetching and renewal
- User-specific operations (invite users)
- Comprehensive error handling with custom exceptions:
  - AuthException (401 Unauthorized)
  - ValidationException (422 Validation errors)
  - NotFoundException (404 Not found)
  - ApiException (generic API errors)
- Extensive test suite (59+ unit tests)
- Complete documentation with examples

### Security

- Hawk authentication implemented with request signing
- OAuth 2.0 client credentials flow with automatic token refresh
- Proper exception handling for authentication failures

## [0.1.0] - 2026-02-01

### Initial Release

- Basic project structure
- Hawk authentication support
- HttpClient with GET and POST methods
- Users endpoint (basic listing)
- PHPUnit integration

[unreleased]: https://github.com/vogelyt/absence-io-client/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/vogelyt/absence-io-client/compare/v0.1.0...v1.0.0
[0.1.0]: https://github.com/vogelyt/absence-io-client/releases/tag/v0.1.0
