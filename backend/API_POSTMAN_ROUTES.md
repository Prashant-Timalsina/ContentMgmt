## API Routes – Postman Guide

**Base URL (local dev)**: `http://localhost:8000`  
**API prefix**: all routes below are under `/api` (e.g. `POST http://localhost:8000/api/login`)

**Auth header for protected routes**

- **Header**: `Authorization: Bearer <ACCESS_TOKEN>`
- `ACCESS_TOKEN` is returned from `/api/login` or `/api/register` in the `access_token` field.

**Refresh token cookie**

- Login / register / refresh responses also set an HTTP-only cookie: `refresh_token`
- `/api/refresh` expects this cookie; in Postman you must:
  - either copy the `refresh_token` cookie from your browser,
  - or call `/api/login`/`/api/register` in Postman once and let Postman store the cookie, then reuse it.

---

## 1. Auth Routes (`UserController`)

### 1.1 Login

- **Method**: `POST`
- **URL**: `/api/login`
- **Auth**: none
- **Body (JSON)**:

```json
{
  "email": "user@example.com",
  "password": "Password123"
}
```

- **Response (200)**:  
  - JSON: `{ "user": { ... }, "access_token": "...", "roles": [], "permissions": [] }`  
  - Sets `refresh_token` cookie.

---

### 1.2 Register

- **Method**: `POST`
- **URL**: `/api/register`
- **Auth**: none
- **Body (JSON)**:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Password123",
  "password_confirmation": "Password123"
}
```

Password must:
- be at least 8 chars
- contain at least one lowercase, one uppercase, and one digit.

- **Response (201/200)**: same structure as login, plus `refresh_token` cookie.

---

### 1.3 Refresh Access Token

- **Method**: `POST`
- **URL**: `/api/refresh`
- **Auth header**: not required
- **Cookie required**: `refresh_token=<refresh_token_from_login_or_register>`
- **Body**: _none_

- **Response (200)**:

```json
{
  "user": { ... },
  "access_token": "...",
  "roles": [],
  "permissions": []
}
```

Also updates `refresh_token` cookie.

---

### 1.4 Get Current User (`/me`)

- **Method**: `GET`
- **URL**: `/api/me`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>`
- **Body**: _none_

- **Response (200)**:

```json
{
  "user": { ... },
  "roles": ["role-name"],
  "permissions": ["perm-1", "perm-2"]
}
```

---

### 1.5 Logout

- **Method**: `POST`
- **URL**: `/api/logout`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>`
- **Body**: _none_

- **Response (200)**:

```json
{ "message": "Logged out" }
```

All tokens for the user are revoked and `refresh_token` cookie is cleared.

---

### 1.6 User Access Request (self)

- **Method**: `POST`
- **URL**: `/api/up_req`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>`
- **Body (JSON)**:

```json
{
  "type": "role",          // or "permission"
  "item_name": "editor",   // role or permission name being requested
  "reason": "Need edit access for articles"
}
```

- **Response (201)**: request created (`AccessRequest`).

---

## 2. Admin Routes (`AdminController`)

All admin routes:
- **Base URL**: `/api/admin/...`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>` (token for a user with `manage_users` permission)

### 2.1 List Users

- **Method**: `GET`
- **URL**: `/api/admin/users`
- **Body**: _none_

---

### 2.2 Update User Role

- **Method**: `PATCH`
- **URL**: `/api/admin/users/{user}/role`
  - `{user}`: user ID
- **Body (JSON)**:

```json
{
  "role": "admin"   // must exist in roles.name
}
```

- **Notes**:
  - Admin cannot change their own role.

---

### 2.3 Update User Permissions

- **Method**: `PATCH`
- **URL**: `/api/admin/users/{user}/permissions`
  - `{user}`: user ID
- **Body (JSON)**:

```json
{
  "permissions": [
    "create_articles",
    "edit_articles"
  ]
}
```

`permissions` is optional; omitting it or sending an empty array will clear permissions.

---

### 2.4 List All Permissions

- **Method**: `GET`
- **URL**: `/api/admin/permissions`
- **Body**: _none_

---

### 2.5 List All Roles

- **Method**: `GET`
- **URL**: `/api/admin/roles`
- **Body**: _none_

---

## 3. Access Request Routes (`AccessRequestController`)

These routes are for admins managing user access requests.

### 3.1 Create Access Request (duplicate of `/up_req`)

- **Method**: `POST`
- **URL**: `/api/access-request`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>`
- **Body (JSON)**: same as `/api/up_req`

```json
{
  "type": "permission",
  "item_name": "publish_articles",
  "reason": "Need to publish special posts"
}
```

---

### 3.2 Approve Access Request

- **Method**: `POST`
- **URL**: `/api/access-request/{accessRequest}/approve`
  - `{accessRequest}`: access request ID
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>` (with `manage_users`)
- **Body (JSON)** (optional):

```json
{
  "admin_note": "Approved for project X"
}
```

If `admin_note` is omitted, defaults to `"Approved by Admin"`.

---

### 3.3 Reject Access Request

- **Method**: `POST`
- **URL**: `/api/access-request/{accessRequest}/reject`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>` (with `manage_users`)
- **Body (JSON)**:

```json
{
  "admin_note": "Not needed for your role"
}
```

---

## 4. Content Routes (`ContentController`)

### 4.1 List Published Articles (Public)

- **Method**: `GET`
- **URL**: `/api/articles`
- **Auth**: none
- **Body**: _none_

---

### 4.2 Get Single Article (Public if Published)

- **Method**: `GET`
- **URL**: `/api/articles/{content}`
  - `{content}`: article ID
- **Auth**:
  - **Published**: none required
  - **Draft / submitted / rejected**: `Authorization: Bearer <ACCESS_TOKEN>` and user must be author or have appropriate permission (per policy).

---

### 4.3 List Article Types

- **Method**: `GET`
- **URL**: `/api/articles_type`
- **Auth**: none
- **Body**: _none_

---

### 4.4 Get Single Article Type

- **Method**: `GET`
- **URL**: `/api/articles_type/{type}`
  - `{type}`: article type ID
- **Auth**: none

---

### 4.5 List My Articles (Editor/Admin)

- **Method**: `GET`
- **URL**: `/api/my-articles`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>`
- **Permissions required**: `create_articles`
- **Body**: _none_

---

### 4.6 Create Article (Draft)

- **Method**: `POST`
- **URL**: `/api/articles`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>`
- **Permissions required**: `create_articles`
- **Body (JSON)**:

```json
{
  "title": "My first article",
  "body": "Full article content...",
  "type_id": 1
}
```

`type_id` must exist in `article_types.id`. The article will be created as a **draft**.

---

### 4.7 Update Article

- **Method**: `PUT`
- **URL**: `/api/articles/{content}`
  - `{content}`: article ID
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>`
- **Permissions required**: `edit_articles` or being the author (plus checks in policy)
- **Body (JSON)** (all fields optional – only send what you want to change):

```json
{
  "title": "Updated title",
  "body": "Updated body",
  "type_id": 2
}
```

---

### 4.8 Delete Article

- **Method**: `DELETE`
- **URL**: `/api/articles/{content}`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>`
- **Permissions required**: `delete_articles` or being author with appropriate rights
- **Body**: _none_

---

### 4.9 Submit Article for Review

- **Method**: `POST`
- **URL**: `/api/articles/{content}/submit`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>`
- **Body**: _none_

Changes article status to `submitted`.

---

### 4.10 Publish Article (Admin)

- **Method**: `POST`
- **URL**: `/api/articles/{content}/publish`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>`
- **Permissions required**: `publish_articles`
- **Body**: _none_

Sets status to `published` and `published_at` to current time.

---

### 4.11 Reject Article (Admin)

- **Method**: `POST`
- **URL**: `/api/articles/{content}/reject`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>`
- **Permissions required**: `publish_articles`
- **Body (JSON)** (optional):

```json
{
  "rejection_reason": "Needs more sources"
}
```

Sets status to `rejected` and optionally stores `rejection_reason`.

---

## 5. Admin-only routes

### 5.1 List access requests

- **Method**: `GET`
- **URL**: `/api/admin/access-requests`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>` (with `manage_users`)
- **Response**: List of all access requests (role/permission) with `user`.

### 5.2 List all articles (admin)

- **Method**: `GET`
- **URL**: `/api/admin/articles`
- **Auth**: `Authorization: Bearer <ACCESS_TOKEN>` (with `manage_users`)
- **Response**: All articles with `type` and `author` (for pending/review list).

---

## 6. Quick Postman Setup Tips

- **Environment variable**:
  - `base_url = http://localhost:8000`
- **Authorization (per request)**:
  - Add header `Authorization: Bearer {{access_token}}`
  - Update `{{access_token}}` after each login / refresh.
- **Cookies**:
  - After calling `/api/login` in Postman, check the **Cookies** tab and ensure `refresh_token` is stored for the host.
  - Subsequent `/api/refresh` calls will reuse that cookie automatically.

