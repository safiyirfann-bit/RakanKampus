# RakanKampus mobile API

Base URL: `https://rakankampus-nm7u.onrender.com/api`

Send on every request:

```
Accept: application/json
Authorization: Bearer <token>      (everything except /login)
Accept-Language: ms | en | zh | ta (optional, before login)
```

Errors: `401` = not signed in / token revoked, `422` = validation (`{"message", "errors": {field: [..]}}`).

## Account
| Method | Path | Body | Returns |
|---|---|---|---|
| POST | /login | email, password, device_name? | `{token, user}` |
| POST | /logout | – | signs out this phone |
| GET | /me | – | `{user}` |
| PUT | /me | first_name, last_name, student_id, email, phone? | `{user}` |
| PUT | /me/password | current_password, password, password_confirmation, logout_other_devices? | |
| PUT | /me/settings | any of: language, theme (light/dark/system), reminder_notifications, class_notifications, dnd_until | `{user}` |
| POST | /me/photo, /me/cover (multipart `photo` / `cover`) · DELETE /me/cover | | |

`user` = id, name, first_name, last_name, student_id, email, phone, programme, photo (data URI), cover, language, theme, notification_settings.

## Home
GET /home → user, today, classes (whole week), reminders (next 3), conversations (4), quick_questions.

## Chat
- POST /chat `{message, conversation_id?, kb_id?, stream: true}`
  - stream → `application/x-ndjson`: lines `{"d":"text"}` … then `{"done":true, reply, conversation_id, message_id, suggestions, from_kb, topic}` or `{"error":true}`
  - without stream → the same final object as JSON
- GET /conversations · GET /conversations/{id} · PUT /conversations/{id} `{title}` · DELETE /conversations/{id}
- POST /messages/{id}/rate `{rating: up|down}`

## Reminders
GET /reminders · GET /reminders/history · POST /reminders `{subject, type (Exam/Assignment/Quiz/Other), due_at, lead_hours, repeat_lead_hours?}` · PUT/DELETE /reminders/{id} · POST /reminders/ai-capture (multipart `photo`) → preview items · POST /reminders/bulk-store · /bulk-delete · /restore

## Timetable
GET /timetable → schedules, programs, days · POST /timetable · PUT/DELETE /timetable/{id} · POST /timetable/ai-capture (multipart `photo`) · /bulk-store · /bulk-delete · /delete-all · POST/PUT/DELETE /programs

## Phone notifications
GET /notifications/upcoming → `{items: [{id, at (ms), title, body, url}]}` — schedule these on the phone (next 8 days).
