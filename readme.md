## API Documentation

### Add module reminder tag

Used to add module reminder tags to Infusionsoft API 

**URL** : `/api/module_reminder_assigner`

**Method** : `POST`

**Auth required** : NO

**Parameters**

```text
email : User email ID | Required
```

#### Success Response

**Code** : `200 OK`

**Content example**

```json
{
  "success":true,
  "message":"Start IPA Module 6 Reminders",
 }
```

#### Error Response
Error responses will always return `success` as `false` with non 2XX HTTP Code

Example:

**Condition** : If invalid email address is provided.

**Code** : `422 Unprocessable Entity`

**Content** :

```json
{
  "success":false,
  "message":"Valid email address is required",
}
```