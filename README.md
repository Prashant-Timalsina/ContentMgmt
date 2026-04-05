# ContentMgmt

A high-performance Content Management System (CMS) built with a decoupled architecture featuring a **Laravel 11 API** and a **Vue.js 3 (Quasar Framework)** frontend.

## Project Philosophy
This project is designed with a "Security-First" approach, ensuring that every content interaction is gated by server-side policies while maintaining a reactive user experience on the frontend.

---

## Tech Stack

| Layer | Technology | Key Usage |
| :--- | :--- | :--- |
| **Backend** | Laravel 11 | RESTful API, Service Layer logic |
| **Frontend** | Vue.js 3 / Quasar | Admin Dashboard, Reactive UI components |
| **Auth** | Laravel Sanctum | SPA Authentication & Token Refresh logic |
| **Security** | Spatie Permissions | Role-Based Access Control (RBAC) |
| **Database** | SQL / Eloquent | Relational data management & Seeding |

---

## Key Features

### Authentication & Security
* **Token Separation:** Implemented custom logic for Refresh and Access token separation to enhance session security.
* **Bouncer & Guards:** Custom middleware to handle role-specific routing and prevent unauthorized API access.
* **Secure Logout:** Backend invalidation of tokens to ensure session termination.

### Role-Based Access Control (RBAC)
* **Granular Permissions:** Define specific actions (e.g., edit-articles, manage-users) using Spatie's permission library.
* **Dynamic Frontend Syncing:** User permissions are synced to the Quasar state, allowing the UI to toggle the Admin Panel and action buttons dynamically.
* **Automated Seeding:** Includes comprehensive seeders for creating Default Admins and standard Permission sets.

### Content Lifecycle Management
* **State Handling:** Articles support multiple states, including a "Rejected" state for moderation workflows.
* **Article Type Schema:** Flexible data structures for different categories of content.
* **Policy Authorization:** Every request is filtered through Laravel Policies to ensure users only modify authorized data.

---

## Architecture Overview

### Backend (Laravel)
* **Models:** Content, ArticleType, User (extended with Spatie traits).
* **Controllers:** Separated into Auth, Content, and Admin namespaces.
* **Middleware:** Custom implementations for CORS and Role verification.

### Frontend (Quasar)
* **Auth Store:** Manages login state and permission syncing.
* **Components:** Modular UI elements for article management and user administration.
* **Axios Interceptors:** Handles automatic token attachment and 401/403 error redirection.

---

## Getting Started

### 1. Backend Setup
```bash
# Clone the repository
git clone [https://github.com/Prashant-Timalsina/ContentMgmt.git](https://github.com/Prashant-Timalsina/ContentMgmt.git)
cd ContentMgmt

# Install dependencies
composer install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Database setup & seeding
php artisan migrate --seed
```

### 2. Frontend Setup
```bash
# Navigate to the frontend directory
cd frontend

# Install packages
npm install

# Run in development mode
quasar dev
```

---

## Recent Milestones

* **Feb 13-15, 2026:** Finalized Content Policies and fixed redundant update requests.
* **Feb 11-12, 2026:** Completed Article Type schemas and Admin UI button refinements.
* **Feb 8-10, 2026:** Integrated Bouncer logic and Role syncing between backend and frontend.
* **Feb 4-6, 2026:** Implemented token-based Auth (Access/Refresh), Quasar Notify integration, and login/logout fixes.
* **Feb 2-3, 2026:** Initial setup with Laravel Sanctum and Spatie Roles/Permissions.

---

**Developed by [Prashant-Timalsina](https://github.com/Prashant-Timalsina)**
