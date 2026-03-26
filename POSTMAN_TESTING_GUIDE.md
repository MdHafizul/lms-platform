# LMS Platform - Postman Testing Guide

## 📋 Overview
This guide will walk you through testing the LMS API system using Postman. All endpoints are now ready for testing with sample data pre-populated in the database.

## 🎯 Quick Start

### 1. Start the Laravel Development Server
```bash
cd c:\Users\hafiz\OneDrive\Desktop\FREELANCE\lms-platform
php artisan serve
```
Server will run on `http://localhost:8000`

### 2. Import Postman Collection
- Open Postman
- Click **Import** → **Upload Files**
- Select `LMS_API_Postman_Collection.json` from the project root
- Collection will be imported with all endpoints and environment variables

### 3. Configure Environment Variables
The collection comes with these pre-configured variables:
- `base_url`: http://localhost:8000
- `token`: (auto-populated after login)
- `course_id`: 1
- `enrollment_id`: 1
- `assessment_id`: 1
- `attempt_id`: (auto-populated during exam)

---

## 🔐 Authentication Testing

### Step 1: Login (MUST DO FIRST)
**Request:** `POST /api/v1/auth/login`

```json
{
  "email": "student1@lms.test",
  "password": "password123"
}
```

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 3,
      "name": "Student User 1",
      "email": "student1@lms.test",
      "roles": ["student"]
    },
    "token": "1|xxxxx..."
  }
}
```

✅ **What Happens:**
- Token is AUTO-SAVED to `{{token}}` variable
- You can now use this token for all subsequent requests
- The token is valid for 24 hours

---

### Test All Authentication Endpoints:

#### 2. Get Current User (Me)
```
GET /api/v1/me
Header: Authorization: Bearer {{token}}
```
Expected: Returns your user profile with roles

#### 3. Refresh Token
```
POST /api/v1/auth/refresh
Header: Authorization: Bearer {{token}}
```
Expected: New token generated

#### 4. Logout
```
POST /api/v1/auth/logout
Header: Authorization: Bearer {{token}}
```
Expected: Token deleted, you're logged out

---

## 📚 Course Testing

### Get All Courses
```
GET /api/v1/courses
Header: Authorization: Bearer {{token}}
```

**Expected Response:**
- List of published courses
- Each course with ID, title, code, description, status
- Pagination: 15 courses per page

### Get Specific Course
```
GET /api/v1/courses/1
Header: Authorization: Bearer {{token}}
```

### Get Course Students
```
GET /api/v1/courses/1/students
Header: Authorization: Bearer {{token}}
```

### Get Course Activities
```
GET /api/v1/courses/1/activities
Header: Authorization: Bearer {{token}}
```

### Get Course Enrollments
```
GET /api/v1/courses/1/enrollments
Header: Authorization: Bearer {{token}}
```

---

## 👥 Enrollment Testing

### Get My Enrollments
```
GET /api/v1/enrollments
Header: Authorization: Bearer {{token}}
```

**Expected:**
- Shows all courses you're enrolled in
- Status, enrolled date, progress info

### Get Enrollment Progress
```
GET /api/v1/enrollments/{{enrollment_id}}/progress
Header: Authorization: Bearer {{token}}
```

---

## 📝 Assessment Testing

### 1. Start an Exam (IMPORTANT)
```
POST /api/v1/assessments/1/start-attempt
Header: Authorization: Bearer {{token}}
```

✅ **Result:** 
- `attempt_id` is AUTO-SAVED to `{{attempt_id}}`
- Shows all questions with options
- Timer starts now

### 2. View Assessment Details
```
GET /api/v1/assessments/1
Header: Authorization: Bearer {{token}}
```

### 3. Submit Assessment Answers
```
POST /api/v1/exam-attempts/{{attempt_id}}/submit
Header: Authorization: Bearer {{token}}
Content-Type: application/json
```

**Request Body:**
```json
{
  "answers": [
    {
      "question_id": 1,
      "selected_option_id": 1,
      "text_answer": null
    },
    {
      "question_id": 2,
      "selected_option_id": 4,
      "text_answer": null
    },
    {
      "question_id": 3,
      "selected_option_id": 7,
      "text_answer": null
    },
    {
      "question_id": 4,
      "selected_option_id": 10,
      "text_answer": null
    }
  ]
}
```

✅ **Expected Response (Correct Answers = 100%):**
```json
{
  "success": true,
  "message": "Attempt submitted successfully",
  "data": {
    "attempt": { ... },
    "score": 100,
    "percentage": 100,
    "passed": true
  }
}
```

### 4. View My Assessment Attempts
```
GET /api/v1/assessments/1/my-attempts
Header: Authorization: Bearer {{token}}
```

---

## 🧪 Complete Testing Workflow

### Scenario 1: Student Takes Assessment

**Step 1:** Login as student
```
POST /api/v1/auth/login
{
  "email": "student1@lms.test",
  "password": "password123"
}
```

**Step 2:** View available courses
```
GET /api/v1/courses
```

**Step 3:** View course details
```
GET /api/v1/courses/1
```

**Step 4:** View course activities
```
GET /api/v1/courses/1/activities
```

**Step 5:** Start assessment
```
POST /api/v1/assessments/1/start-attempt
```

**Step 6:** Submit answers
```
POST /api/v1/exam-attempts/{{attempt_id}}/submit
```

**Step 7:** Check results
```
GET /api/v1/assessments/1/my-attempts
```

---

## 🔑 Test User Credentials

### Students (Can register courses, take assessments, submit assignments)
- **student1@lms.test** / password123
- **student2@lms.test** / password123  
- **student3@lms.test** / password123

### Lecturer (Can create courses, manage assessments, grade submissions)
- **lecturer@lms.test** / password123

### Admin (Full system access)
- **admin@lms.test** / password123

---

## 📊 Database Sample Data

### Courses Created
1. **Web Development Fundamentals** (WEB101) - PUBLISHED
   - 3 students enrolled
   - 4 HTML assessment questions
   - 1 assignment

2. **Advanced Laravel** (LAR201) - DRAFT
   - 2 students enrolled

### Assessment Sample Questions
- Q1: What does HTML stand for? → **HyperText Markup Language** ✅
- Q2: Largest heading tag? → **&lt;h1&gt;** ✅
- Q3: Paragraph syntax? → **&lt;p&gt;...&lt;/p&gt;** ✅
- Q4: Styling attribute? → **style** ✅

Each question is worth **25 points** (Total: 100)
**Passing score:** 70%

---

## ✅ Expected Behavior

### Successful Auth Flow
1. User logs in → Returns token + user data ✅
2. Can access endpoints with valid token ✅
3. Invalid token → 401 Unauthorized ⚠️
4. No token → 401 Unauthorized ⚠️

### Assessment Workflow
1. Starts fresh attempt → Status = `in_progress` ✅
2. Submit answers → Status = `submitted` ✅
3. Auto-grading: Compare selected options with is_correct flag ✅
4. Final score calculated as percentage ✅
5. Pass/Fail determined by passing_score (70%) ✅

### Enrollment Tracking
1. Student enrolled in course → Status = `ACTIVE` ✅
2. Can track progress through activities ✅
3. Can view assessment attempts ✅
4. Can submit assignments ✅

---

## 🐛 Troubleshooting

### "Token is invalid or expired"
→ Re-login using the Login endpoint

### "Course not found"
→ Ensure course_id is correct (start with ID 1)

### "Not enrolled in this course"
→ You must be enrolled to attempt assessments. Try different course.

### 403 Forbidden
→ This endpoint requires a specific role (lecturer/admin). Try with lecturer_token.

### CORS Error
→ Ensure `APP_URL=http://localhost:8000` in .env

---

## 📌 POSTMAN Pro Tips

### Auto-save Variables
Tests automatically save:
- `token` ← After login
- `attempt_id` ← After starting assessment
- `lecturer_token` ← After lecturer login

### Pre-request Scripts
Some requests have pre-request scripts that:
- Validate required variables
- Set headers automatically
- For mat data before sending

### Test Scripts
Responses run test scripts that:
- Extract and save tokens
- Validate response structure
- Check HTTP status codes

---

## 🚀 Next Steps

1. **Test all endpoints systematically**
2. **Verify role-based access** (try endpoints with different users)
3. **Check error responses** (invalid data, wrong IDs, etc.)
4. **Load test** (create multiple attempts to verify system stability)
5. **Validate business logic** (grading, progression, certificates)

---

## 📞 Support

If you encounter issues:
1. Check database seeding: `php artisan migrate:refresh --seed`
2. Verify server is running: `php artisan serve`
3. Check .env database config
4. Review Laravel logs: `storage/logs/laravel.log`

---

**Happy Testing! 🎯**
