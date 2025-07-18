export const userMethods = [
  {
    id: 'me',
    method: 'GET',
    path: '/api/me',
    description: 'Получение данных текущего пользователя',
    auth: true,
    parameters: [],
    requestExample: `curl -X GET http://localhost:8000/api/me \\
  -H "Authorization: Bearer 1|abc123def456..."`,
    successResponse: `{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "first_name": "Иван Иванов",
      "last_name": "",
      "user_name": "Иван Иванов",
      "email": "user@example.com",
      "phone_number": "",
      "position": "Пользователь",
      "country": "Россия",
      "city": "Москва",
      "avatar_url": "",
      "currency": "RUB",
      "language": "ru",
      "timezone": "UTC",
      "last_logged_in": "2024-01-15T10:30:00.000000Z",
      "is_online": true,
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  }
}`,
    errorResponses: [
      {
        code: 401,
        status: 'Unauthorized',
        description: 'Пользователь не авторизован',
        response: `{
  "success": false,
  "message": "User not authenticated"
}`
      }
    ]
  },
  {
    id: 'user',
    method: 'GET',
    path: '/api/user',
    description: 'Получение профиля пользователя',
    auth: true,
    parameters: [],
    requestExample: `curl -X GET http://localhost:8000/api/user \\
  -H "Authorization: Bearer 1|abc123def456..."`,
    successResponse: `{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "first_name": "Иван Иванов",
      "last_name": "Петров",
      "user_name": "Иван Иванов",
      "email": "user@example.com",
      "phone_number": "+7 (999) 123-45-67",
      "position": "Менеджер",
      "country": "Россия",
      "city": "Москва",
      "avatar_url": "uploads/avatars/user1.jpg",
      "currency": "RUB",
      "language": "ru",
      "timezone": "UTC",
      "company_name": "ООО Компания",
      "company_address": "ул. Примерная, 123",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  }
}`,
    errorResponses: [
      {
        code: 401,
        status: 'Unauthorized',
        description: 'Пользователь не авторизован',
        response: `{
  "success": false,
  "message": "User not authenticated"
}`
      }
    ]
  },
  {
    id: 'profile',
    method: 'PUT',
    path: '/api/profile',
    description: 'Обновление профиля пользователя',
    auth: true,
    parameters: [
      {
        name: 'first_name',
        type: 'string',
        required: false,
        description: 'Имя пользователя'
      },
      {
        name: 'last_name',
        type: 'string',
        required: false,
        description: 'Фамилия пользователя'
      },
      {
        name: 'phone_number',
        type: 'string',
        required: false,
        description: 'Номер телефона'
      },
      {
        name: 'country',
        type: 'string',
        required: false,
        description: 'Страна'
      },
      {
        name: 'city',
        type: 'string',
        required: false,
        description: 'Город'
      }
    ],
    requestExample: `curl -X PUT http://localhost:8000/api/profile \\
  -H "Authorization: Bearer 1|abc123def456..." \\
  -H "Content-Type: application/json" \\
  -d '{
    "first_name": "Иван",
    "last_name": "Петров",
    "phone_number": "+7 (999) 123-45-67",
    "country": "Россия",
    "city": "Москва"
  }'`,
    successResponse: `{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "user": {
      "id": 1,
      "first_name": "Иван",
      "last_name": "Петров",
      "user_name": "Иван Иванов",
      "email": "user@example.com",
      "phone_number": "+7 (999) 123-45-67",
      "position": "Пользователь",
      "country": "Россия",
      "city": "Москва",
      "avatar_url": "",
      "currency": "RUB",
      "language": "ru",
      "timezone": "UTC",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  }
}`,
    errorResponses: [
      {
        code: 401,
        status: 'Unauthorized',
        description: 'Пользователь не авторизован',
        response: `{
  "success": false,
  "message": "User not authenticated"
}`
      },
      {
        code: 422,
        status: 'Unprocessable Entity',
        description: 'Ошибка валидации данных',
        response: `{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "phone_number": ["The phone number format is invalid."]
  }
}`
      }
    ]
  },
  {
    id: 'user-avatar',
    method: 'POST',
    path: '/api/user/avatar',
    description: 'Загрузка аватара пользователя',
    auth: true,
    parameters: [
      {
        name: 'avatar',
        type: 'file',
        required: true,
        description: 'Файл изображения (jpg, png, gif, max 2MB)'
      }
    ],
    requestExample: `curl -X POST http://localhost:8000/api/user/avatar \\
  -H "Authorization: Bearer 1|abc123def456..." \\
  -F "avatar=@user-avatar.jpg"`,
    successResponse: `{
  "success": true,
  "message": "Avatar uploaded successfully",
  "data": {
    "avatar_url": "uploads/avatars/user1_avatar.jpg",
    "updated_at": "2024-01-15T10:30:00.000000Z"
  }
}`,
    errorResponses: [
      {
        code: 401,
        status: 'Unauthorized',
        description: 'Пользователь не авторизован',
        response: `{
  "success": false,
  "message": "User not authenticated"
}`
      },
      {
        code: 422,
        status: 'Unprocessable Entity',
        description: 'Ошибка валидации файла',
        response: `{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "avatar": ["The avatar must be an image.", "The avatar may not be greater than 2048 kilobytes."]
  }
}`
      }
    ]
  },
  {
    id: 'user-settings',
    method: 'GET',
    path: '/api/user/settings',
    description: 'Получение настроек пользователя',
    auth: true,
    parameters: [],
    requestExample: `curl -X GET http://localhost:8000/api/user/settings \\
  -H "Authorization: Bearer 1|abc123def456..."`,
    successResponse: `{
  "success": true,
  "data": {
    "settings": {
      "notifications": {
        "email": true,
        "sms": false,
        "push": true
      },
      "privacy": {
        "profile_visible": true,
        "show_online_status": true
      },
      "interface": {
        "theme": "light",
        "language": "ru",
        "timezone": "UTC"
      }
    }
  }
}`,
    errorResponses: [
      {
        code: 401,
        status: 'Unauthorized',
        description: 'Пользователь не авторизован',
        response: `{
  "success": false,
  "message": "User not authenticated"
}`
      }
    ]
  },
  {
    id: 'user-personal',
    method: 'PUT',
    path: '/api/user/personal',
    description: 'Обновление персональных данных пользователя',
    auth: true,
    parameters: [
      {
        name: 'first_name',
        type: 'string',
        required: false,
        description: 'Имя'
      },
      {
        name: 'last_name',
        type: 'string',
        required: false,
        description: 'Фамилия'
      },
      {
        name: 'birth_date',
        type: 'string',
        required: false,
        description: 'Дата рождения (YYYY-MM-DD)'
      },
      {
        name: 'gender',
        type: 'string',
        required: false,
        description: 'Пол (male, female, other)'
      }
    ],
    requestExample: `curl -X PUT http://localhost:8000/api/user/personal \\
  -H "Authorization: Bearer 1|abc123def456..." \\
  -H "Content-Type: application/json" \\
  -d '{
    "first_name": "Иван",
    "last_name": "Петров",
    "birth_date": "1990-05-15",
    "gender": "male"
  }'`,
    successResponse: `{
  "success": true,
  "message": "Personal data updated successfully",
  "data": {
    "user": {
      "id": 1,
      "first_name": "Иван",
      "last_name": "Петров",
      "birth_date": "1990-05-15",
      "gender": "male",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  }
}`,
    errorResponses: [
      {
        code: 401,
        status: 'Unauthorized',
        description: 'Пользователь не авторизован',
        response: `{
  "success": false,
  "message": "User not authenticated"
}`
      },
      {
        code: 422,
        status: 'Unprocessable Entity',
        description: 'Ошибка валидации данных',
        response: `{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "birth_date": ["The birth date must be a valid date."],
    "gender": ["The selected gender is invalid."]
  }
}`
      }
    ]
  },
  {
    id: 'user-company',
    method: 'PUT',
    path: '/api/user/company',
    description: 'Обновление данных компании пользователя',
    auth: true,
    parameters: [
      {
        name: 'company_name',
        type: 'string',
        required: false,
        description: 'Название компании'
      },
      {
        name: 'company_address',
        type: 'string',
        required: false,
        description: 'Адрес компании'
      },
      {
        name: 'company_phone',
        type: 'string',
        required: false,
        description: 'Телефон компании'
      },
      {
        name: 'position',
        type: 'string',
        required: false,
        description: 'Должность в компании'
      }
    ],
    requestExample: `curl -X PUT http://localhost:8000/api/user/company \\
  -H "Authorization: Bearer 1|abc123def456..." \\
  -H "Content-Type: application/json" \\
  -d '{
    "company_name": "ООО Компания",
    "company_address": "ул. Примерная, 123, Москва",
    "company_phone": "+7 (495) 123-45-67",
    "position": "Менеджер по продажам"
  }'`,
    successResponse: `{
  "success": true,
  "message": "Company data updated successfully",
  "data": {
    "user": {
      "id": 1,
      "company_name": "ООО Компания",
      "company_address": "ул. Примерная, 123, Москва",
      "company_phone": "+7 (495) 123-45-67",
      "position": "Менеджер по продажам",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  }
}`,
    errorResponses: [
      {
        code: 401,
        status: 'Unauthorized',
        description: 'Пользователь не авторизован',
        response: `{
  "success": false,
  "message": "User not authenticated"
}`
      },
      {
        code: 422,
        status: 'Unprocessable Entity',
        description: 'Ошибка валидации данных',
        response: `{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "company_phone": ["The company phone format is invalid."]
  }
}`
      }
    ]
  },
  {
    id: 'user-password',
    method: 'PUT',
    path: '/api/user/password',
    description: 'Изменение пароля пользователя',
    auth: true,
    parameters: [
      {
        name: 'current_password',
        type: 'string',
        required: true,
        description: 'Текущий пароль'
      },
      {
        name: 'new_password',
        type: 'string',
        required: true,
        description: 'Новый пароль (минимум 8 символов)'
      },
      {
        name: 'new_password_confirmation',
        type: 'string',
        required: true,
        description: 'Подтверждение нового пароля'
      }
    ],
    requestExample: `curl -X PUT http://localhost:8000/api/user/password \\
  -H "Authorization: Bearer 1|abc123def456..." \\
  -H "Content-Type: application/json" \\
  -d '{
    "current_password": "oldpassword123",
    "new_password": "newpassword456",
    "new_password_confirmation": "newpassword456"
  }'`,
    successResponse: `{
  "success": true,
  "message": "Password changed successfully"
}`,
    errorResponses: [
      {
        code: 401,
        status: 'Unauthorized',
        description: 'Пользователь не авторизован',
        response: `{
  "success": false,
  "message": "User not authenticated"
}`
      },
      {
        code: 422,
        status: 'Unprocessable Entity',
        description: 'Ошибка валидации данных',
        response: `{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "current_password": ["The current password is incorrect."],
    "new_password": ["The new password must be at least 8 characters."],
    "new_password_confirmation": ["The new password confirmation does not match."]
  }
}`
      }
    ]
  }
] 