# LearnSphere LMS — Modernization Audit

## Scope and confidence

This evidence-based snapshot follows the main application configuration, route entry point, module service providers, representative controllers/services/views, migrations, tests, and user-facing locale/brand references. The repository contains many independently routed modules; any absence finding below is limited to the searched application and package code and should be rechecked before a major integration is commissioned.

## Architecture

- PHP 8.2+ and Laravel 12, with Composer path packages under `packages/Mindigo`, `packages/Teacher`, and `packages/Students`.
- Blade is the primary UI, with Tailwind CSS 4 and Vite 6; package providers register module routes, translations, views, and assets.
- Eloquent migrations and services support modular course/catalog/enrollment, classroom, exam/question-bank, assignment, discussion, calendar, practice, reporting, notification, and live-session domains.
- Authentication and role checks are package middleware; `bootstrap/app.php` installs locale middleware on web requests, and the modules define their own web routes.
- The project includes PHPUnit feature coverage and Playwright end-to-end coverage, including a focused live-session accessibility test.

## Existing module status

| Area | Status | Evidence |
|---|---|---|
| Course discovery and learning | Implemented | `packages/Teacher/TeacherCourse/src/Services/CourseDiscoveryService.php`, `CourseEnrollmentService.php`, `StudentCourseLearningService.php`; corresponding module routes and feature tests. |
| Exam authoring, delivery, grading, and analytics | Implemented | `packages/Mindigo/ExamManagement/src/Services/ExamService.php`, `ExamAnalyticsService.php`; `packages/Teacher/TeacherExam/` and `packages/Students/StudentExam/`. |
| Student adaptive practice | Implemented | `packages/Students/StudentPractice/src/Services/PracticeRecommendationService.php`, `PracticeAnalyticsService.php`, and adaptive practice routes/views. |
| Assignments and grading | Implemented | Teacher and student assignment package routes/services; submission and grading feature coverage exists. |
| Calendar and reminders | Implemented | `packages/Mindigo/AcademicCalendar/src/Services/AcademicCalendarService.php`, `CalendarReminderService.php`, and reminder notification/job infrastructure. |
| Ratings, course reviews, replies, moderation | Implemented | `packages/Teacher/TeacherCourse/src/Services/CourseReviewService.php`, review routes, course review/history migrations, and review workflow tests. |
| Live classrooms and resilience | Implemented | Teacher/student live-session packages, provider/gateway code, backup/restore commands, and extensive live-session tests. |
| English locale and language switching | Implemented as the sole supported locale by this change | `config/app.php`, `app/Http/Middleware/SetLocale.php`, `routes/web.php`, and English package translations. Some older module views still contain hardcoded Vietnamese labels and need a follow-up translation pass. |

## Audit findings and recommended improvements

Priorities are ranked by learner impact and practical implementation value. “Missing” means no corresponding completion-certificate, plagiarism, or learning-standard integration workflow was found in the reviewed application code; confirm this against any separately hosted integrations before planning delivery.

| # | Opportunity / status | Code evidence and current limitation | User value | Dependencies, complexity, safeguards | Priority |
|---|---|---|---|---|---|
| 1 | **Verifiable course-completion certificates — Missing** | Course enrollment and lesson progress exist in `packages/Teacher/TeacherCourse/src/Models/CourseEnrollment.php` and `CourseLessonProgress.php`; the reviewed course routes/migrations do not show a certificate issuance or verification workflow. | Gives learners portable proof of completion and institutions a way to validate it. | Medium: completion rules, immutable certificate IDs, PDF generation, public verification endpoint. Avoid exposing student details; make verification opt-in/minimal. | High |
| 2 | **Course recommendations — Implemented; improve** | `packages/Teacher/TeacherCourse/src/Services/CourseRecommendationService.php` scores public courses using a learner’s history, subject mastery, exams, ratings, and engagement. It is rule-based, not a validated predictive model. | Helps learners find relevant next courses without duplicating existing recommendation logic. | Low–medium: explain recommendation signals, let learners dismiss/reset, evaluate relevance. Enforce published-course visibility and use only authorized learner signals. | High |
| 3 | **Early academic-support alerts — Partially implemented** | Adaptive practice tracks skills and recommendations in `packages/Students/StudentPractice/src/Services/PracticeAnalyticsService.php`; exam/course analytics exist, but the reviewed evidence does not establish a unified teacher-facing early-intervention workflow. | Helps teachers offer support before learners fall further behind. | Medium–high: clear thresholds, teacher dashboards, consent and retention policy, bias review; restrict individual data to authorized teachers. | High |
| 4 | **Calendar exports and external sync — Partially implemented** | Calendar aggregation and reminders exist in `packages/Mindigo/AcademicCalendar/src/Services/CalendarReminderService.php` and `app/Console/Commands/AcademicCalendar/SendCalendarReminders.php`; no iCalendar/ICS or Google/Outlook sync endpoint was found in the reviewed application code. | Reduces missed lessons, exams, and deadlines in learners’ existing calendars. | Medium: begin with signed, revocable ICS feeds; OAuth sync is higher complexity. Protect feed URLs as credentials and limit exported student data. | High |
| 5 | **Learning analytics and progress reporting — Implemented; improve** | `packages/Teacher/TeacherCourse/src/Services/CourseAnalyticsService.php`, `packages/Students/StudentPractice/src/Services/PracticeAnalyticsService.php`, and exam analytics already report activity/performance. | Improves actionable progress visibility and teacher follow-up. | Medium: establish consistent metric definitions and accessible exports; aggregate by default, enforce class/teacher authorization, and avoid misleading comparisons. | High |
| 6 | **Plagiarism/similarity review — Missing** | Assignment submissions and grading exist in `packages/Teacher/TeacherAssignment/` and `packages/Students/StudentAssignment/`; no similarity comparison or plagiarism-review domain was found in the reviewed code. | Helps teachers triage suspicious similarity while preserving human judgment. | High: text extraction, privacy/retention controls, false-positive review, and likely significant compute or an explicitly approved external service. Never auto-penalize based on a score alone. | Medium |
| 7 | **Offline learning — Partially implemented** | Learning resources and file-backed lesson content exist under `packages/Mindigo/LearningTools/` and `packages/Teacher/TeacherCourse/`; no learner offline cache/service-worker workflow was found in the reviewed frontend. | Supports learners with unreliable connectivity. | High: service worker, storage quotas, versioning, conflict handling, and carefully scoped downloads. Do not cache exam answers, personal data, or restricted course files indiscriminately. | Medium |
| 8 | **Course pathways and prerequisites — Partially implemented** | The course schema supports `prerequisite_lesson_id` in `database/migrations/2026_06_27_000006_create_courses_chapters_lessons_tables.php`; full cross-course pathways and enrollment enforcement were not established by this evidence. | Makes long-term learning sequences clearer and prevents learners starting advanced material unprepared. | Medium: pathway model/UI and cycle-safe prerequisite validation. Preserve existing course/lesson access behavior when adding enforcement. | Medium |
| 9 | **Course ratings and feedback — Implemented; improve** | Eligible enrolled learners can review courses, teachers can reply, and administrators can moderate in `packages/Teacher/TeacherCourse/src/Services/CourseReviewService.php` and its review routes. | Builds trust and supports course quality improvement. | Low–medium: improve moderation turnaround and aggregate reporting. Retain eligibility checks, audit moderation, and prevent reviewer identity leakage. | Medium |
| 10 | **Discussion reporting/moderation — Needs verification** | Student/teacher class discussions exist in `packages/Students/StudentDiscussion/` and `packages/Teacher/TeacherDiscussion/`; a complete user-facing report/appeal/moderation workflow was not verified in this audit. | Gives learners a safe route to flag abuse, spam, or privacy violations. | Medium: report reasons, moderator queue, action history, appeals, and retention controls. Confirm no existing admin workflow before implementation. | Medium |
| 11 | **Learning-standard interoperability (LTI/SCORM/xAPI) — Missing in reviewed code** | The reviewed route, package, and migration searches showed no LTI, SCORM, or xAPI integration. | Allows institutions to connect existing courseware and learning record systems. | High: select a standard and hosting model first; signing keys, launch validation, tenant isolation, and learner-consent requirements are significant. | Low |
| 12 | **Accessibility coverage — Partially implemented** | Keyboard/accessibility assertions and axe checks exist in `tests/E2E/live-session-accessibility.spec.js`; equivalent automated coverage across public, auth, student, teacher, and admin screens was not found. | Makes learning usable to people with disabilities and improves keyboard/mobile UX for all learners. | Medium: expand axe/keyboard smoke tests by role and manually verify critical flows; fix contrast, labels, focus management, and reduced-motion issues as evidence identifies them. | High |

## UI, maintainability, and operational observations

- Public, authentication, and dashboard experiences already use a consistent Blade/Tailwind foundation; this change updates the product name and removes remaining Vietnamese language selectors without rebuilding existing screens.
- The public layout previously loaded Tailwind from its CDN despite an existing Tailwind/Vite build. It now uses the existing Vite CSS entry, removing that production warning and avoiding a new dependency.
- The homepage loads all news articles before slicing a small display window in `packages/Mindigo/Core/src/resources/views/home.blade.php`; paginate/limit at the query when the news table grows.
- Course discovery/recommendation and analytics should be load-tested with realistic data volumes. Verify database indexes and query plans as course/enrollment history grows.
- Preserve a consistent UTC storage convention and render user-facing dates in the learner’s configured timezone. English interface localization does not imply that existing course content or user-entered text must be translated.
- The repository contains English translation files for each of the 60 Vietnamese package/vendor translation files before their removal; broader automated missing-key checks should remain part of localization changes.
- No database migration or user-data deletion was made for the branding/localization changes.

## Implemented modernization changes

- Set Laravel’s application locale and fallback to English, made the locale middleware force English even when old sessions/cookies or `?lang=vi` are present, and retained the legacy locale route name while rejecting unsupported locales.
- Removed Vietnamese selectors from public, legal, news, authentication, and system-setting screens; set generated auth and PDF documents to English.
- Removed the 60 Vietnamese translation files after verifying matching English files existed, and retained course-language/content fields as data rather than treating existing learner-created content as interface translation.
- Updated LearnSphere branding across primary user-facing Blade views and English translations, mail copy, environment defaults, README, and relevant architecture docs. Internal namespaces, Composer package names, route names, database identifiers, provider integration names, and repository URLs remain technical identifiers.
- Added regression coverage for rejecting Vietnamese locale selection, enforcing English on a legacy Vietnamese session/request, and restricting the system language setting to English.
- Converted the teacher exam print sheet and authentication emails to English.
- Changed the public home layout to use its existing compiled Vite/Tailwind stylesheet instead of the Tailwind CDN.

### Main files and resource groups

- Locale defaults and behavior: `config/app.php`, `app/Http/Middleware/SetLocale.php`, `routes/web.php`.
- Branding and public/auth/dashboard UI: `packages/Mindigo/Core/src/resources/views/partials/home/`, `packages/Mindigo/Core/src/resources/views/legal/`, `packages/Mindigo/Auth/src/resources/views/`, and `packages/Mindigo/Dashboard/src/resources/views/`.
- Generated auth email copy: `packages/Mindigo/Auth/src/resources/views/otp.blade.php` (password-reset OTP only).
- English system/course language choices: `packages/Mindigo/SystemSetting/src/Services/SystemSettingService.php`, `packages/Mindigo/SystemSetting/src/resources/lang/en/app.php`, and `packages/Teacher/TeacherCourse/src/resources/views/partials/form.blade.php`.
- Branding in English translation resources: `packages/**/src/resources/lang/en/` (including core, auth, dashboard, and system settings).
- Removed Vietnamese locale resources: the 59 `packages/**/src/resources/lang/vi/*.php` files and `lang/vendor/core/vi/app.php`, across the AcademicCalendar, AuditLog, Auth, BlogManagement, Core, Dashboard, ExamManagement, LearningTools, Notification, Profile, QuestionBank, Report, RolePermission, SubjectManagement, SupportManagement, SystemSetting, UserManagement, Students, and Teacher packages.
- Tests: `tests/Feature/HomepageTest.php` and `tests/Feature/Admin/NavigationTest.php`.
- Project documentation/configuration: `README.md`, `composer.json`, `.env.example`, and `docs/1-installation.md`, `docs/2-architecture.md`, and this audit.

## Validation and known limitations

- `npm.cmd run build` — passed.
- PHP syntax checks for changed runtime PHP files — passed.
- `php artisan route:list --path=lang --except-vendor` — passed; the locale route resolves.
- `php artisan test tests/Feature/HomepageTest.php tests/Feature/Admin/NavigationTest.php` — could not execute because the active PHP 8.4 CLI lacks the PDO SQLite driver (`could not find driver`); the test suite is configured for in-memory SQLite. The available XAMPP SQLite extension is for PHP 8.0 and cannot be loaded into the PHP 8.4 runtime.
- Browser-visible application and authenticated student/teacher/admin workflows should be retested after the runtime environment has the required database extensions and seeded accounts.
- English-only copy is not yet complete across all legacy module screens: static Vietnamese text remains in views such as `packages/Teacher/TeacherQuestion/src/resources/views/index.blade.php`, `packages/Teacher/TeacherAssignment/src/resources/views/create.blade.php`, and `packages/Teacher/TeacherResult/src/resources/views/by-exam.blade.php`. The official Vietnamese locale files and selectors are removed, but these view strings need a dedicated translation pass before claiming every workflow is consistently English.

## Suggested next-phase roadmap

1. Translate remaining hardcoded Vietnamese labels in module screens; add an English-only smoke test across public, student, teacher, and administrator routes.
2. Restore the SQLite PDO extension in the test runtime and run focused localization/branding tests.
3. Add learner-facing certificates with revocable, privacy-minimizing verification.
4. Expand responsive, accessibility, and role-based end-to-end regression checks.
5. Add a signed iCalendar export, then evaluate OAuth-based provider sync only if users need it.
6. Build a teacher early-support view on existing analytics, with explicit access/retention policies.
7. Load-test course catalog and analytics queries before expanding recommendation signals.
8. Evaluate offline access, plagiarism review, and learning-standard integration independently with stakeholders before selecting infrastructure or external providers.
