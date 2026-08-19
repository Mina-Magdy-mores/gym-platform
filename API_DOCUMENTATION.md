# 🏆 FIT CLUB Platform - Master RESTful API Documentation (v1.0)

> **Enterprise Clean Architecture & Mobile/Web Integration Standard**  
> Complete API specification for **Mobile Apps (Flutter / React Native)** and **Headless Frontends (Next.js / React / Vue)**.

---

## 📌 1. Architecture & General Standards

### 🌐 Base URL
- **Local Development:** `http://127.0.0.1:8000/api/v1`
- **Production (Laravel Cloud):** `https://fitclub.laravel.cloud/api/v1`

### 🔑 Authentication Mechanism (Laravel Sanctum)
All protected endpoints require a Bearer token passed in the HTTP Authorization header:
```http
Authorization: Bearer <your_sanctum_token>
Accept: application/json
Content-Type: application/json
```

### 🛡️ Security Rate Limits
| Scope | Throttle Limit | Response on Exceeding |
| :--- | :--- | :--- |
| **Login Attempts (`throttle:login`)** | 5 requests / minute | `429 Too Many Requests` |
| **Global APIs (`throttle:api`)** | 60 requests / minute | `429 Too Many Requests` |
| **Real-time Chat Messages (`throttle:chat`)** | 30 messages / minute | `429 Too Many Requests` |

### 📦 Standardized JSON Envelope
#### Success Response (`200 OK` / `201 Created`):
```json
{
    "success": true,
    "message": "Operation completed successfully.",
    "data": { ... }
}
```
#### Validation Error Response (`422 Unprocessable Entity`):
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email has already been taken."]
    }
}
```
#### Unauthorized / Forbidden Response (`401 / 403`):
```json
{
    "success": false,
    "message": "Unauthorized access or invalid token."
}
```

---

## 📂 2. Master Endpoints Reference by Module

---

### 01. Public & Landing Page APIs (Home, Coaches, Plans)

#### `GET /api/v1/landing`
* **Auth:** Public
* **Description:** Returns the consolidated landing page payload in a single high-speed request (Hero, stats, active plans, schedules, rules).
* **Response `200 OK`:**
```json
{
    "success": true,
    "data": {
        "hero": {
            "headline": "BUILD YOUR ULTIMATE PHYSIQUE",
            "stats": {
                "active_members": 542,
                "certified_coaches": 12,
                "modern_equipment": "150+",
                "satisfaction_rate": "99%"
            }
        },
        "plans": [ ... ],
        "schedules": { "men": [ ... ], "women": [ ... ] },
        "rules": [ ... ]
    }
}
```

#### `GET /api/v1/trainers`
* **Auth:** Public
* **Description:** List certified coaches with avatars, biographies, session rates, and specialties for booking/team showcases.
* **Response `200 OK`:**
```json
{
    "success": true,
    "data": [
        {
            "id": 2,
            "name": "Captain Ahmed",
            "avatar": "http://127.0.0.1:8000/storage/...",
            "session_rate": 250.00,
            "specialties": "Hypertrophy & Strength Training",
            "bio": "Certified IFBB coach with 8+ years of experience."
        }
    ]
}
```

#### `GET /api/v1/plans`
* **Auth:** Public
* **Description:** Public active subscription plans with full benefit breakdown (PT sessions, InBody, freeze days).

#### `GET /api/v1/schedules`
* **Auth:** Public
* **Description:** Operating hours and daily schedule slots for Men and Women.

#### `GET /api/v1/gym-rules`
* **Auth:** Public
* **Description:** Active gym code of conduct, regulations, and hygiene rules.

---

### 02. Authentication & User Profile

#### `POST /api/v1/register`
* **Auth:** Public
* **Body (JSON):**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "01012345678",
    "password": "password123",
    "password_confirmation": "password123"
}
```
* **Response `201 Created`:**
```json
{
    "success": true,
    "message": "User registered successfully.",
    "token": "1|abc123token...",
    "data": {
        "id": 5,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "member"
    }
}
```

#### `POST /api/v1/login`
* **Auth:** Public (`throttle:login`)
* **Body (JSON):**
```json
{
    "email": "mina@gym.com",
    "password": "password123"
}
```
* **Response `200 OK`:**
```json
{
    "success": true,
    "token": "2|xyz987token...",
    "data": {
        "id": 1,
        "name": "Mina Magdy",
        "email": "mina@gym.com",
        "role": "admin"
    }
}
```

#### `GET /api/v1/profile`
* **Auth:** `auth:sanctum`
* **Description:** Retrieves authenticated user details, roles, avatar, and active subscription.

#### `POST /api/v1/profile`
* **Auth:** `auth:sanctum`
* **Body (`multipart/form-data`):**
  - `name`: `string` (optional)
  - `email`: `string` (optional)
  - `phone`: `string` (optional)
  - `avatar`: `file` (image)
  - `certificates[]`: `file` (PDF/Image)

#### `DELETE /api/v1/profile/media/{mediaId}`
* **Auth:** `auth:sanctum`
* **Description:** Deletes a specific media attachment or certificate from the user's profile.

#### `POST /api/v1/logout`
* **Auth:** `auth:sanctum`
* **Description:** Revokes and destroys the current Sanctum access token.

---

### 03. Subscriptions & Member Benefits Dashboard

#### `GET /api/v1/member/dashboard`
* **Auth:** `auth:sanctum` (`Role: member`)
* **Description:** Real-time quota balance for the 7 benefit counters.
* **Response `200 OK`:**
```json
{
    "success": true,
    "data": {
        "has_active_subscription": true,
        "plan_name": "VIP Platinum 12 Months",
        "days_remaining": 340,
        "status": "active",
        "benefits": {
            "remaining_pt_sessions": 12,
            "remaining_inbody_scans": 24,
            "remaining_kickboxing_classes": 12,
            "remaining_invitations": 6,
            "remaining_freeze_days": 60,
            "remaining_nutrition_plans": 12
        }
    }
}
```

#### `POST /api/v1/subscriptions`
* **Auth:** `auth:sanctum`
* **Description:** Purchases a new plan or upgrades an existing plan within 7 days (Atomic Lock protected).
* **Body (JSON):**
```json
{
    "subscription_plan_id": 1
}
```

#### `GET /api/v1/notifications`
* **Auth:** `auth:sanctum`
* **Description:** List member notifications (Session booked, quota warnings, plan upgrades).

#### `PATCH /api/v1/notifications/{id}/read`
* **Auth:** `auth:sanctum`
* **Description:** Mark single notification as read.

#### `POST /api/v1/notifications/mark-all-read`
* **Auth:** `auth:sanctum`
* **Description:** Mark all notifications as read in bulk.

---

### 04. Private Trainer Sessions & Bookings

#### `GET /api/v1/bookings`
* **Auth:** `auth:sanctum`
* **Description:** List upcoming and past bookings for the logged-in user or coach.

#### `POST /api/v1/bookings`
* **Auth:** `auth:sanctum` (`Role: member`)
* **Description:** Books a private coach session with **Pessimistic Lock** preventing double booking and deducting 1 PT session from active quota.
* **Body (JSON):**
```json
{
    "trainer_id": 2,
    "booking_date": "2026-08-25",
    "start_time": "14:00:00",
    "end_time": "15:00:00",
    "notes": "Leg hypertrophy and squat mobility."
}
```

#### `POST /api/v1/bookings/{id}/cancel`
* **Auth:** `auth:sanctum`
* **Description:** Cancels booking. If > 24 hours prior to session, 1 PT session is 100% refunded to quota.

#### `POST /api/v1/bookings/{id}/complete`
* **Auth:** `auth:sanctum` (`Role: trainer|admin`)
* **Description:** Marks session as attended and credits the coach's wallet with 85% net earnings.

---

### 05. Trainer Wallet & Payout Operations

#### `GET /api/v1/trainer/wallet`
* **Auth:** `auth:sanctum` (`Role: trainer`)
* **Description:** Trainer wallet ledger, available balance, total earnings, pending payouts, and transaction history.

#### `POST /api/v1/trainer/wallet/payout`
* **Auth:** `auth:sanctum` (`Role: trainer`)
* **Description:** Requests earnings payout (InstaPay / Vodafone Cash) with pessimistic wallet balance freeze.
* **Body (JSON):**
```json
{
    "amount": 500.00,
    "payment_method": "instapay",
    "account_details": "coach_ahmed@instapay"
}
```

#### `GET /api/v1/payouts/{id}/voucher`
* **Auth:** `auth:sanctum` (`Role: trainer|admin`)
* **Description:** Fetches digital payout voucher receipt details.

---

### 06. Workout Routines & Nutrition Diets

#### `GET /api/v1/workouts/routines`
* **Auth:** `auth:sanctum`
* **Description:** Member sees assigned routines; Coach sees created routines with exercises.

#### `POST /api/v1/workouts/routines`
* **Auth:** `auth:sanctum` (`Role: trainer|admin`)
* **Description:** Coach assigns a custom workout routine with nested exercise sets and reps.
* **Body (JSON):**
```json
{
    "user_id": 3,
    "title": "Hypertrophy Push Day",
    "goal": "Chest & Deltoid Growth",
    "status": "active",
    "exercises": [
        {
            "day_name": "Day 1 - Push",
            "exercise_name": "Incline Dumbbell Press",
            "target_muscle": "Upper Chest",
            "sets": 4,
            "reps": "10-12",
            "rest_seconds": 90
        }
    ]
}
```

#### `GET /api/v1/workouts/diets`
* **Auth:** `auth:sanctum`
* **Description:** List diet plans with meal breakdown and macronutrients.

#### `POST /api/v1/workouts/diets`
* **Auth:** `auth:sanctum` (`Role: trainer|admin`)
* **Description:** Coach assigns custom nutrition plan with calories, protein, carbs, fats, and meal schedule.
* **Body (JSON):**
```json
{
    "user_id": 3,
    "title": "Clean Bulk 3000 Kcal",
    "daily_calories": 3000,
    "protein_grams": 200,
    "carbs_grams": 350,
    "fats_grams": 70,
    "status": "active",
    "meals": [
        {
            "meal_name": "Breakfast",
            "meal_time": "08:30 AM",
            "food_items": "5 Egg Whites + 100g Oats + 1 Banana",
            "calories": 650
        }
    ]
}
```

#### `GET /api/v1/workouts/athletes`
* **Auth:** `auth:sanctum` (`Role: trainer|admin`)
* **Description:** Roster of active members assigned to the coach.

---

### 07. Real-Time Chat & Messaging (WebSockets)

#### `GET /api/v1/chats`
* **Auth:** `auth:sanctum`
* **Description:** Active conversations and available contactable coaches/members.

#### `POST /api/v1/chats/start`
* **Auth:** `auth:sanctum`
* **Body (JSON):** `{"user_id": 2}`
* **Description:** Finds or creates a 1-on-1 chat room between athlete and coach.

#### `GET /api/v1/chats/{id}`
* **Auth:** `auth:sanctum`
* **Description:** Message history for a chat conversation.

#### `POST /api/v1/chats/{id}/messages`
* **Auth:** `auth:sanctum` (`throttle:chat`)
* **Body (`multipart/form-data`):**
  - `message`: `string`
  - `attachment`: `file` (optional image)
* **Real-time Event Broadcast:** Dispatches `MessageSent` on private channel `private-chat.{conversationId}`.

---

### 08. Invoices & Financial Ledger

#### `GET /api/v1/invoices/{paymentId}`
* **Auth:** `auth:sanctum`
* **Description:** Tax invoice details, tax number, items, and paid amounts.

#### `GET /api/v1/invoices/{paymentId}/download`
* **Auth:** `auth:sanctum`
* **Description:** Streams binary PDF invoice for download.

#### `GET /api/v1/admin/payments`
* **Auth:** `auth:sanctum` (`Role: admin`)
* **Description:** Master revenue ledger and payment records.

---

### 09. Admin Moderation & Infrastructure

#### `GET /api/v1/admin/activity-logs`
* **Auth:** `auth:sanctum` (`Role: admin`)
* **Description:** Immutable security audit trail with changes diff (`old` vs `attributes`).
* **Query Params:** `?log_name=users&event=updated&search=admin&page=1`

#### `GET /api/v1/admin/users`
* **Auth:** `auth:sanctum` (`Role: admin`)
* **Description:** Paginated user directory with role and status filtering.

#### `PATCH /api/v1/admin/users/{id}/toggle-active`
* **Auth:** `auth:sanctum` (`Role: admin`)
* **Description:** Freezes or unfreezes a member account.

#### `POST /api/v1/admin/users/{id}/block`
* **Auth:** `auth:sanctum` (`Role: admin`)
* **Body (JSON):** `{"reason": "Terms violation"}`
* **Description:** Instantly blocks an abusive account and terminates active sessions.

#### `POST /api/v1/admin/users/{id}/unblock`
* **Auth:** `auth:sanctum` (`Role: admin`)
* **Description:** Restores blocked account.

#### `POST /api/v1/admin/trainers`
* **Auth:** `auth:sanctum` (`Role: admin`)
* **Description:** Recruits a new certified trainer.

#### `POST /api/v1/admin/payouts/{id}/approve`
* **Auth:** `auth:sanctum` (`Role: admin`)
* **Description:** Settles pending payout and records transaction in ledger.

#### `POST /api/v1/admin/bookings/{id}/refund`
* **Auth:** `auth:sanctum` (`Role: admin`)
* **Description:** Manual refund resolution for disputed bookings.

---

## 💻 3. Client Integration Code Examples

### 📱 Flutter / Dart Example (Login & Store Token)
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

final storage = FlutterSecureStorage();
const String baseUrl = 'https://fitclub.laravel.cloud/api/v1';

Future<bool> loginUser(String email, String password) async {
  final response = await http.post(
    Uri.parse('$baseUrl/login'),
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
    body: jsonEncode({
      'email': email,
      'password': password,
    }),
  );

  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    final String token = data['token'];
    await storage.write(key: 'auth_token', value: token);
    return true;
  }
  return false;
}
```

### ⚛️ Next.js / Axios Example (Authenticated Request)
```typescript
import axios from 'axios';

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || 'https://fitclub.laravel.cloud/api/v1',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use((config) => {
  const token = typeof window !== 'undefined' ? localStorage.getItem('fitclub_token') : null;
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const getMemberDashboard = async () => {
  const response = await api.get('/member/dashboard');
  return response.data.data;
};
```

---
*Generated with Enterprise Clean Architecture for FIT CLUB Production Platform.*
