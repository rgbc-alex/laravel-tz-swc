# Менеджер Задач API

Простое API для управления задачами с токен-аутентификацией.

## API Эндпоинты

### Аутентификация

#### Регистрация

- **Метод:** `POST`
- **URL:** `/api/register`
- **Тело запроса:**
  ```json
  {
      "name": "Your Name",
      "email": "your.email@example.com",
      "password": "your_password",
      "password_confirmation": "your_password"
  }
  ```
- **Ответ:**
  ```json
  {
      "name": "Your Name",
      "email": "your.email@example.com",
      "updated_at": "2025-12-05T12:00:00.000000Z",
      "created_at": "2025-12-05T12:00:00.000000Z",
      "id": 1
  }
  ```

#### Логин

- **Метод:** `POST`
- **URL:** `/api/login`
- **Тело запроса:**
  ```json
  {
      "email": "your.email@example.com",
      "password": "your_password"
  }
  ```
- **Ответ:**
  ```json
  {
      "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
  ```

#### Выход

- **Метод:** `POST`
- **URL:** `/api/logout`
- **Заголовки:**
  - `Authorization: Bearer <token>`
- **Ответ:**
  ```json
  {
      "message": "Выход выполнен успешно"
  }
  ```

### Задачи

Для всех эндпоинтов задач требуется заголовок `Authorization: Bearer <token>`.

#### Получение списка задач

- **Метод:** `GET`
- **URL:** `/api/tasks`
- **Параметры запроса (фильтры):**
  - `status` (string, опционально): `planned`, `in_progress`, `done`
  - `assignee_id` (integer, опционально): ID пользователя
  - `due_date` (date, опционально): Дата в формате `YYYY-MM-DD`
- **Пример ответа:**
  ```json
  [
      {
          "id": 1,
          "title": "Первая задача",
          "description": "Описание задачи",
          "status": "planned",
          "due_date": null,
          "assignee_id": 1,
          "created_at": "2025-12-05T12:00:00.000000Z",
          "updated_at": "2025-12-05T12:00:00.000000Z",
          "assignee": { ... },
          "attachment_url": "http://localhost/storage/1/file.jpg"
      }
  ]
  ```

#### Создание новой задачи

- **Метод:** `POST`
- **URL:** `/api/tasks`
- **Тело запроса (form-data):**
  - `title` (string, обязательно)
  - `description` (string, обязательно)
  - `status` (string, опционально)
  - `due_date` (date, опционально)
  - `assignee_id` (integer, опционально)
  - `attachment` (file, опционально)
- **Пример ответа (201 Created):**
  ```json
  {
      "id": 2,
      "title": "Новая задача",
      "description": "...",
      // ...
      "attachment_url": "http://localhost/storage/2/new_file.pdf"
  }
  ```

#### Получение информации о задаче

- **Метод:** `GET`
- **URL:** `/api/tasks/{id}`
- **Пример ответа:**
  ```json
  {
      "id": 1,
      "title": "Первая задача",
      // ...
  }
  ```

#### Обновление данных задачи

- **Метод:** `PUT`
- **URL:** `/api/tasks/{id}`
- **Тело запроса (x-www-form-urlencoded или form-data для файла):**
  - `title` (string, опционально)
  - `description` (string, опционально)
  - `status` (string, опционально)
  - `due_date` (date, опционально)
  - `assignee_id` (integer, опционально)
  - `attachment` (file, опционально)
- **Пример ответа:**
  ```json
  {
      "id": 1,
      "title": "Обновленный заголовок",
      // ...
  }
  ```

#### Удаление задачи

- **Метод:** `DELETE`
- **URL:** `/api/tasks/{id}`
- **Ответ:** `204 No Content`

## Тестирование

Для запуска набора тестов выполните:
```bash
php artisan test
```
