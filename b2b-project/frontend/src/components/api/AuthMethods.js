export const authMethods = [
  {
    id: 'register',
    method: 'POST',
    path: '/api/register',
    description: 'Регистрация нового пользователя в системе',
    auth: false,
    parameters: [
      {
        name: 'email',
        type: 'string',
        required: true,
        description: 'Email пользователя (должен быть уникальным)'
      },
      {
        name: 'user_name',
        type: 'string',
        required: true,
        description: 'Имя пользователя'
      },
      {
        name: 'password',
        type: 'string',
        required: true,
        description: 'Пароль (минимум 8 символов)'
      },
      {
        name: 'password_confirmation',
        type: 'string',
        required: true,
        description: 'Подтверждение пароля'
      }
    ],
    requestExample: `curl -X POST http://localhost:8000/api/register \\
  -H "Content-Type: application/json" \\
  -d '{
    "email": "user@example.com",
    "user_name": "Иван Иванов",
    "password": "password123",
    "password_confirmation": "password123"
  }'`,
    successResponse: `{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "first_name": "Иван Иванов",
      "email": "user@example.com",
      "user_name": "Иван Иванов",
      "position": "Пользователь",
      "country": "Россия",
      "city": "Москва",
      "currency": "RUB",
      "language": "ru",
      "timezone": "UTC",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    "token": "1|abc123def456...",
    "token_type": "Bearer"
  }
}`,
    errorResponses: [
      {
        code: 422,
        status: 'Unprocessable Entity',
        description: 'Ошибка валидации данных',
        response: `{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}`
      },
      {
        code: 500,
        status: 'Internal Server Error',
        description: 'Внутренняя ошибка сервера',
        response: `{
  "success": false,
  "message": "Registration failed",
  "error": "Database connection failed"
}`
      }
    ]
  },
  {
    id: 'login',
    method: 'POST',
    path: '/api/login',
    description: 'Вход пользователя в систему',
    auth: false,
    parameters: [
      {
        name: 'email',
        type: 'string',
        required: true,
        description: 'Email пользователя'
      },
      {
        name: 'password',
        type: 'string',
        required: true,
        description: 'Пароль пользователя'
      }
    ],
    requestExample: `curl -X POST http://localhost:8000/api/login \\
  -H "Content-Type: application/json" \\
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'`,
    successResponse: `{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "first_name": "Иван Иванов",
      "email": "user@example.com",
      "user_name": "Иван Иванов",
      "position": "Пользователь",
      "country": "Россия",
      "city": "Москва",
      "currency": "RUB",
      "language": "ru",
      "timezone": "UTC",
      "last_logged_in": "2024-01-15T10:30:00.000000Z",
      "is_online": true
    },
    "token": "1|abc123def456...",
    "token_type": "Bearer"
  }
}`,
    errorResponses: [
      {
        code: 422,
        status: 'Unprocessable Entity',
        description: 'Ошибка валидации данных',
        response: `{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}`
      },
      {
        code: 401,
        status: 'Unauthorized',
        description: 'Неверные учетные данные',
        response: `{
  "success": false,
  "message": "Invalid credentials"
}`
      },
      {
        code: 403,
        status: 'Forbidden',
        description: 'Аккаунт заблокирован',
        response: `{
  "success": false,
  "message": "Account is banned or inactive"
}`
      }
    ]
  },
  {
    id: 'logout',
    method: 'POST',
    path: '/api/logout',
    description: 'Выход пользователя из системы',
    auth: true,
    parameters: [],
    requestExample: `curl -X POST http://localhost:8000/api/logout \\
  -H "Authorization: Bearer 1|abc123def456..." \\
  -H "Content-Type: application/json"`,
    successResponse: `{
  "success": true,
  "message": "Logged out successfully"
}`,
    errorResponses: [
      {
        code: 401,
        status: 'Unauthorized',
        description: 'Пользователь не авторизован',
        response: `{
  "message": "Unauthenticated."
}`
      }
    ]
  }
] 