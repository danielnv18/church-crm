# Church CRM Development Guidelines

This document provides essential information for developers working on the Church CRM project.

## Build/Configuration Instructions

### Prerequisites
- PHP 8.4 or higher
- Composer
- Node.js and npm
- SQLite (for development/testing)

### Initial Setup
1. Clone the repository
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install JavaScript dependencies:
   ```bash
   npm install
   ```
4. Create environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. Set up the database:
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

### Development Environment
Run the development server with:
```bash
composer dev
```

This command starts:
- Laravel development server
- Queue worker
- Log watcher
- Vite development server

For server-side rendering, use:
```bash
composer dev:ssr
```

## Testing Information

### Testing Framework
The project uses Pest PHP for testing, which is a testing framework built on top of PHPUnit with a more expressive syntax.

### Running Tests
Run all tests:
```bash
composer test
```

Run specific test suites:
```bash
# Run unit tests with coverage
composer test:unit

# Run type coverage tests
composer test:type-coverage

# Run static analysis
composer test:types

# Run code style checks
composer test:lint

# Run refactoring checks
composer test:refactor
```

Run a specific test file:
```bash
./vendor/bin/pest tests/path/to/TestFile.php
```

### Creating Tests
1. Tests are organized in the `tests` directory:
   - `tests/Feature/Console` - For console command tests
   - `tests/Feature/Http` - For HTTP-related tests
   - `tests/Unit/Actions` - For action classes
   - `tests/Unit/Models` - For model tests
   - `tests/Unit/Jobs` - For job tests
   - `tests/Unit/Policies` - For policy tests
   - `tests/Unit/Services` - For service classes

2. Test files should follow the naming convention `*Test.php`. Use Pest for all the tests.

3. Example of a basic test using Pest PHP:
```php
<?php

declare(strict_types=1);

use App\Models\YourModel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can perform some action', function (): void {
    // Arrange
    $data = [
        'field' => 'value',
    ];

    // Act
    $result = YourModel::factory()->create($data);

    // Assert
    expect($result)->toBeInstanceOf(YourModel::class)
        ->and($result->field)->toBe('value');
});
```

4. Use the `RefreshDatabase` trait for tests that interact with the database

5. Follow the Arrange-Act-Assert pattern in your tests.
   - **Arrange**: Set up the necessary preconditions and inputs. Use factories to create test data.
   - **Act**: Execute the code under test.
   - **Assert**: Verify that the expected outcomes occur.

6. Generate a {Model}Factory with each model.

## Code Style & Development Practices

### PHP Code Style
- The project uses Laravel Pint (pint.json) for PHP code style enforcement
- Strict types declaration is required
- Classes should be final when possible
- Follow PSR-12 coding standards with Laravel-specific additions
- Use PHP v8.4 features.
- Enforce strict types and array shapes via PHPStan.

Run code style checks:
```bash
composer test:lint
```

Fix code style issues:
```bash
composer lint
```

### JavaScript/TypeScript Code Style
- Uses Prettier for code formatting
- ESLint for linting
- TypeScript for type checking

Configuration:
- Single quotes
- Semicolons required
- 4 spaces for indentation (except YAML files which use 2 spaces)
- 150 character line length

### Styling & UI
- Use Shadcn for UI components
- Use Tailwind CSS for styling
- Keep UI minimal

### Static Analysis
- PHPStan (via Larastan) is used for static analysis
- Type coverage is enforced at 100%

Run static analysis:
```bash
composer test:types
```

### Code Refactoring
- Rector is used for automated code refactoring

Run refactoring checks:
```bash
composer test:refactor
```

Apply refactoring:
```bash
composer refactor
```

### Actions
- Use Action classes for business logic
- Actions pattern and naming verbs. Example:
```php
public function store(CreateTodoRequest $request, CreateTodoAction $action)
{
    $user = $request->user();

    $action->handle($user, $request->validated());
}
```

### Requests
- Use FormRequest for validation
- Name with Create, Update, Delete.

### Database
- Migrations should be created for all database changes
- Use Laravel's Eloquent ORM for database interactions
- Use enums for fields with a fixed set of values

### Architecture
- Follow Laravel's MVC architecture
- Use Actions classes for business logic
- Use Policies for authorization
- Use Enums for type-safe constants
- Delete .gitkeep when adding a file.
- Stick to existing structure—no new folders.
- Avoid DB::; use Model::query() only.
- No dependency changes without approval.
