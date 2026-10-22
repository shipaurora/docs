---
paths:
  - 'tests/**'
---

# Tests

## Only the website's own tests
Test what this website adds: its layout, blocks, settings, content and seeders. The CMS's behaviour is tested in the cms repository (Orchestra Testbench); a test of the CMS itself goes there, not here.

## Test conventions
PHPUnit classes with `test_` methods named as sentences. Create users with `$this->userWithRole('Editor')` and use Laravel fakes (Storage, Http, etc.) rather than Mockery. Start queries and creates with `Model::query()`. Tests never reach the network: the base TestCase blocks every HTTP request no test faked. Run the suite with `php artisan test --parallel`.
