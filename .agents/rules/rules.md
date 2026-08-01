---
trigger: always_on
---

# Development Rules & Guidelines

This document defines the strict development rules that the AI coding assistant must follow step-by-step during the implementation of this project.

---

## 🏛️ 1. Architectural Rules & Code Cleanliness (Clean Code & SOLID)

1. **Commitment to Modular Architecture:**
   * All new domain code, controllers, models, services, migrations, and routes must be placed inside a specific module under the `app/Modules` directory (e.g., `app/Modules/UserModule`). Writing random code outside this modular structure is strictly prohibited.
2. **Separation of Concerns (SRP - Service Separation):**
   * Controllers must remain thin, only handling request input parsing and response routing.
   * All business calculations, database operations, and domain rules must reside exclusively inside dedicated **Service Classes** (e.g., `UserService.php`).
3. **Dependency Inversion Principle (DIP):**
   * When integrating payment gateways or third-party APIs that are subject to change, always design an **Interface** and inject it via the Constructor. Avoid direct instantiation of concrete classes.
4. **Input Validation (Form Requests):**
   * Never write validation rules directly inside Controllers. All validations must be isolated in dedicated **FormRequest** classes for each module.

---

## 🌿 2. Version Control & Git Guidelines

1. **Strict Git Flow:**
   * All feature development must happen on a new branch starting with `feature/` branched from the `develop` branch.
   * Direct commits or pushes to the `production` or `main` branches are strictly forbidden.
2. **Conventional Commits:**
   * All commit messages must strictly adhere to the standard pattern:
     * `feat: ...` (new feature)
     * `fix: ...` (bug fix)
     * `refactor: ...` (code cleanup)
     * `docs: ...` (documentation updates)
     * `test: ...` (automated tests)
3. **Step-by-Step Git Commands:**
   * The AI assistant must provide the exact Git commands (creating branches, committing with proper messages, and merging) at each step to guide the developer through the workflow.

---

## 🔒 3. Security Standards

1. **Payment & Financial Security:**
   * Never store, log, or print raw customer credit card details (card numbers, CVV, expiry dates) anywhere in the application or logs.
   * Enforce digital signature (HMAC) verification for all incoming Webhooks from Stripe and Paymob before processing any transaction.
2. **Data & Output Protection:**
   * Always use Parameter Binding (PDO) for database queries to prevent SQL Injection attacks.
   * Sanitize and escape all dynamically rendered data in views to prevent Cross-Site Scripting (XSS) attacks.

---

## 📖 4. Pedagogical Workflow (Mentor Mode)

1. **No Automatic Execution:**
   * The AI assistant is strictly forbidden from creating files, directories, or running terminal commands automatically. The user is the driver, the AI is the navigator.
2. **The "Why" Masterclass (Prior Explanation):**
   * Before writing code or suggesting edits, the AI must provide a comprehensive theoretical explanation detailing:
     * **Goal:** What are we doing in this step?
     * **Rationale:** Why are we doing it this way?
     * **Best Practices:** Enterprise standards regarding naming conventions, security, and performance.
3. **Peer Review & Refactoring:**
   * Collaborate closely on code reviews, highlight potential bugs, and refactor code to maintain highest quality standards.