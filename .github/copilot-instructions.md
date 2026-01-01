# Copilot / AI agent instructions for hyuka-psikotes 🔧

Short, focused instructions to help an AI coding agent be productive in this repository.

## Project at-a-glance
- Laravel (framework v12) PHP app for psychometric tests ("psikotes"). Main domain models live in `app/Models/` (e.g. `PauliTest`, `PapiQuestion`, `RmibQuestion`, `AlatTes`, `TestResult`, `User`).
- Frontend assets built with Vite + Tailwind; scripts in `package.json`. Server entry is standard Laravel (`public/index.php`).

## Quick dev environment (commands) ✅
- Install PHP deps: `composer install`
- Install JS deps: `npm install`
- Create env: `copy .env.example .env` (PowerShell: `Copy-Item .env.example .env`)
- Generate key: `php artisan key:generate`
- Create SQLite DB (if needed): `New-Item -Path database\database.sqlite -ItemType File` (Windows PowerShell) or `touch database/database.sqlite`
- Migrate + seed: `php artisan migrate --seed`
- Dev mode (runs server, queue, logs, vite): `composer dev` (uses `concurrently` to run `php artisan serve`, `php artisan queue:listen`, `php artisan pail`, and `npm run dev`)
- Frontend only: `npm run dev` / `npm run build`
- Run the test suite: `composer test` or `php artisan test` (phpunit is configured in `phpunit.xml` to use sqlite in-memory)

## Tests & CI notes 🧪
- `phpunit.xml` config sets `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:` so tests run without an external DB service.
- Unit tests: `tests/Unit` | Feature tests: `tests/Feature`.
- Run a single test: `php artisan test --filter ExampleTest` or `vendor/bin/phpunit --filter ExampleTest`

## Project conventions & patterns (important) 📌
- Domain-specific terminology is in Indonesian: `AlatTes`, `Pauli`, `Papi`, `Rmib`, etc. Look in `app/Models` and corresponding migrations for canonical fields and relationships.
- Questions and test logic: `app/Models/Question.php`, `app/Models/Test.php`, and related migration files in `database/migrations` (e.g. `create_questions_table.php`, `add_type_to_questions_table.php`). When changing question types, update both migrations and logic in models/controllers.
- Seeds / factories: check `database/seeders/` and `database/factories/` for example data used in tests and local dev.
- Queues: default queue connection in `.env.example` is `database`. The dev script uses `php artisan queue:listen`; consider `queue:work` for production-like behavior.

## Integration points & external deps 🔗
- Packages of note (see `composer.json`): `dompdf/dompdf` (PDF exports), `phpoffice/phpspreadsheet`, `laravel/pail` (used in dev scripts), `laravel/breeze` (auth scaffolding).
- Optional services you may need locally: Redis, Mail dev server (Mailhog) if you change mailer settings. By default `.env.example` uses `DB_CONNECTION=sqlite` and `MAIL_MAILER=log` so these are not required.

## Where to look for examples & implementation details 🔎
- Models: `app/Models/` (domain logic)
- Controllers and HTTP endpoints: `app/Http/Controllers/`
- Jobs: `app/Jobs/` (background processing)
- Migrations and schema evolution: `database/migrations/` (numerous small migrations; prefer adding new migrations over editing old ones)
- Tests: `tests/Feature/` (API/behavior examples) and `tests/Unit/` (unit-level expectations)

## Debugging & logs 🐞
- Application logs: `storage/logs/laravel.log`
- Failed jobs / queue errors: check `failed_jobs` table and database records
- When running locally, use `composer dev` to start server + queue watchers + vite

## Style & tooling 🔧
- Code formatting uses Laravel Pint (present in `require-dev`) — run `./vendor/bin/pint` as needed.
- Follow existing patterns in controllers and models (use Form Requests in `app/Http/Requests/` for validation, return resources/JSON in controllers for APIs).

## Helpful examples to reference
- DB-in-memory test config: `phpunit.xml`
- Dev process defined: `composer.json` -> `scripts.dev` (runs `php artisan serve`, `php artisan queue:listen`, `php artisan pail`, and `npm run dev`)
- Domain examples: `app/Models/PapiQuestion.php`, `app/Models/PauliTest.php`, `database/migrations/*create_questions_table.php`

---
If anything here is unclear or you'd like more detail (e.g., example workflows for adding a new question type, or how to add an API endpoint + tests), tell me which area and I will expand it with concrete step-by-step examples. 

Please review and tell me which sections to clarify or expand. 🙏