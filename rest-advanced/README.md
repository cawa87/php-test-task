## REST advanced test task

## Task

You need to create a system that is allowing to update (edit) any json-document with PATCH method (follow [RFC-7396](https://tools.ietf.org/html/rfc7396).). Client can create a draft of the document. Till the document is in draft, it can be updated anytime. Draft can be published, and once published, it can not be edited anymore.

## Requirements

|                  |                                                         |
|------------------|---------------------------------------------------------|
| Estimation       | up to 20 hours                                          |
| Framework        | Phalcon, Laravel, Symfony, Yii2 or any other framework and/or components from any of it with your PSR-* core.
| Other packages   | any                                                     |
| PHP version      | >=7.3                                                   |
| Db               | any relational                                          |

1. Publish your result in any git repo
2. Fill down README file how to execute the project
3. Follow code-style within the project
4. Strict typing is mandatory
5. First commit must be setting up and configuring the skeleton (don't finish the whole project just in 1 commit)
6. Report about difficulties, solutions, questions, and etc.
7. Write unit and API tests (phpunit, behat, codeception, etc)

## API

- `POST /api/v1/document/` - credting draft of the document
- `GET /api/v1/document/{id}` - getting document by id
- `PATCH /api/v1/document/{id}` - edit document
- `POST /api/v1/document/{id}/publish` - publish document
- `GET /api/v1/document/?page=1&perPage=20` - get last document with pagination, sorting (new added are on the top)


- If document is not found, 404 NOT Found must be returned
- If document is already published, and user tries to update it, return 400.
- Try to publish arelady published document should return 200
- `PATCH` is sending in the body of JSON document, all fields except `payload` are ignored. If `payload` is not sent/defined, then return 400.

### Document object

```js
document = {
  id: "some-uuid-string",
  status: "draft|published",
  payload: Object,
  createAt: "iso-8601 date time with time zone",
  modifyAt: "iso-8601 date time with time zone"
}
```

## Example

### 1. Client is creating document

Request:

```http
POST /api/v1/document HTTP/1.1
accept: application/json
```

Response:

```http
HTTP/1.1 200 OK
content-type: application/json

{
    "document": {
        "id": "718ce61b-a669-45a6-8f31-32ba41f94784",
        "status": "draft",
        "payload": {},
        "createAt": "2018-09-01 20:00:00+07:00",
        "modifyAt": "2018-09-01 20:00:00+07:00"
    }
}
```

### 2. Client is editing document for the first time

Request:

```http
PATCH /api/v1/document/718ce61b-a669-45a6-8f31-32ba41f94784 HTTP/1.1
accept: application/json
content-type: application/json

{
    "document": {
        "payload": {
            "actor": "The fox",
            "meta": {
                "type": "quick",
                "color": "brown"
            },
            "actions": [
                {
                    "action": "jump over",
                    "actor": "lazy dog"
                }
            ]
        }
    }
}
```

Response:

```http
HTTP/1.1 200 OK
content-type: application/json

{
    "document": {
        "id": "718ce61b-a669-45a6-8f31-32ba41f94784",
        "status": "draft",
        "payload": {
            "actor": "The fox",
            "meta": {
                "type": "quick",
                "color": "brown"
            },
            "actions": [
                {
                    "action": "jump over",
                    "actor": "lazy dog"
                }
            ]
        },
        "createAt": "2018-09-01 20:00:00+07:00",
        "modifyAt": "2018-09-01 20:01:00+07:00"
    }
}
```

### 3. Client editing document

Request:

```http
PATCH /api/v1/document/718ce61b-a669-45a6-8f31-32ba41f94784 HTTP/1.1
accept: application/json
content-type: application/json

{
    "document": {
        "payload": {
            "meta": {
                "type": "cunning",
                "color": null
            },
            "actions": [
                {
                    "action": "eat",
                    "actor": "blob"
                },
                {
                    "action": "run away"
                }
            ]
        }
    }
}
```

Response:

```http
HTTP/1.1 200 OK
content-type: application/json

{
    "document": {
        "id": "718ce61b-a669-45a6-8f31-32ba41f94784",
        "status": "draft",
        "payload": {
            "actor": "The fox",
            "meta": {
                "type": "cunning",
            },
            "actions": [
                {
                    "action": "eat",
                    "actor": "blob"
                },
                {
                    "action": "run away"
                }
            ]
        },
        "createAt": "2018-09-01 20:00:00+07:00",
        "modifyAt": "2018-09-01 20:02:00+07:00"
    }
}
```

### 4. Client is publishing document

Request:

```http
POST /api/v1/document/718ce61b-a669-45a6-8f31-32ba41f94784/publish HTTP/1.1
accept: application/json
```

Response:

```http
HTTP/1.1 200 OK
content-type: application/json

{
    "document": {
        "id": "718ce61b-a669-45a6-8f31-32ba41f94784",
        "status": "published",
        "payload": {
            "actor": "The fox",
            "meta": {
                "type": "cunning",
            },
            "actions": [
                {
                    "action": "eat",
                    "actor": "blob"
                },
                {
                    "action": "run away"
                }
            ]
        },
        "createAt": "2018-09-01 20:00:00+07:00",
        "modifyAt": "2018-09-01 20:03:00+07:00"
    }
}
```

### 5. Client is getting records in list

Request:

```http
GET /api/v1/document/?page=1 HTTP/1.1
accept: application/json
```

Response:

```http
HTTP/1.1 200 OK
content-type: application/json

{
    "document": [
        {
            "id": "718ce61b-a669-45a6-8f31-32ba41f94784",
            "status": "published",
            "payload": {
                "actor": "The fox",
                "meta": {
                    "type": "cunning",
                },
                "actions": [
                    {
                        "action": "eat",
                        "actor": "blob"
                    },
                    {
                        "action": "run away"
                    }
                ]
            },
            "createAt": "2018-09-01 20:00:00+07:00",
            "modifyAt": "2018-09-01 20:03:00+07:00"
        }
    ],
    "pagination": {
        "page": 1,
        "perPage": 20,
        "total": 1
    }
}
```

## Options tasks (additional points)

Optional tasks are not mandatory, however, if you do not experience difficulties with their implementation, then you are probably an experienced developer. If difficulties nevertheless arise, then there is a reason to overcome them and gain new knowledge. The tasks below describe sample tasks. The completeness and method of their execution is entirely a product of your creativity, because a developer is a creative profession.

Try to implement at least one task. Even if you do not succeed, we will be glad that you tried. Optional tasks will enhance you in our eyes as a developer.

### Tasks decomposition

Make a scope of work / modules to be implemented. Give an estimate of the time that will be spent on each of the points. Make each item as a separate commit. At the end of the task, write down how much real time was spent.

As a result, get something like this table:

| #  | Task               | Estimation | Spent | Comment               |
|:--:|:---------------------|:------:|:---------:|:--------------------------|
| 1  | Env setup  | 1h     | 40m       | Find nice instruction  |
| 2  | Framework setup | 20m    | 30m       | Forgot to install composer |
| 3  | ............         | ...    | ...       | ...                       |

### Docker

Wrap your application in a docker container. Write an application deployment script with one command.
The most convenient way to do this, of course, is docker-compose.

### Add frontend

- Add the ability to get a list of documents with a paginator along the path `/`
- View a specific document along the path `/document/{id}`.

UI/UX does not interest us - therefore, do not try to do everything perfectly. The implementation method and the time you spend on it are important. Better use ready-made solutions and frameworks. There are no restrictions - complete freedom of creativity.

### Implement own patching

In your application, you could use a ready-made module for the patch. If so, then try implementing the patch algorithm yourself.

### Add user authentication/authorization

Implement auhtorization without password `POST /api/v1/login`
Request:

```http
POST /api/v1/login HTTP/1.1
accept: application/json
content-type: application/json

{
    "login": "root"
}
```

Response:

```http
HTTP/1.1 200 OK
content-type: application/json

{
    "user": "root",
    "token": "q56lVCW9aIW6Gs01F5N9raaQordCb8HW",
    "until": 1537352295
}
```

token - random string
until - unix timestamp

Authentication should be proceed via token header. Example:

```http
GET /api/v1/document/718ce61b-a669-45a6-8f31-32ba41f94784 HTTP/1.1
accept: application/json
Authorization: bearer q56lVCW9aIW6Gs01F5N9raaQordCb8HW
```

Requirements:

1. Each time a user logs in, he receives a new token.
2. If the user sends any request with a nonexistent token or with an expired token, then the response with the code 401 should be returned.
3. An anonymous person can see a list of published documents, as well as download a specific published document.
4. An anonymous person will receive an error 401 when trying to access PATCH and POST requests.
5. Only an authorized user can create a document.
6. Only the user who created it can edit and publish the document.
7. The user in the list of documents sees only his unpublished documents and published documents of other users, but does not see the unpublished documents of others.
8. The user, when trying to access a foreign, unpublished document, gets 403.
9. The duration of the token is 1 hour.

### Race condition

How will your application behave taking into account attempts to update one document by several clients at the same time? If there are problems, they must be eliminated.