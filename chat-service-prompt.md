# Промпт для разработки чат-сервиса

## Задача

Создать отдельный микросервис чата для экосистемы из 4 проектов на разных доменах и технологических стеках. Сервис должен работать как независимое приложение и предоставлять API для интеграции в другие проекты.

## Технические требования

### Backend
- **Node.js + Express** - основной сервер
- **WebSocket (Socket.io)** - real-time коммуникация
- **PostgreSQL** - хранение сообщений, пользователей, проектов
- **Redis** - кэш, сессии, временные данные
- **JWT** - аутентификация и авторизация
- **TypeScript** - типизация

### Frontend SDK
- **JavaScript/TypeScript библиотека** - для интеграции
- **Vue компоненты** - готовые UI компоненты
- **iframe вариант** - для простой интеграции
- **PostMessage API** - безопасная коммуникация между доменами

## Архитектура

### База данных (PostgreSQL)

Нужно создать следующие таблицы:
- **projects** - проекты экосистемы (id, name, domain, api_key, settings)
- **users** - пользователи из разных проектов (id, external_id, project_id, username, email, avatar_url, metadata)
- **chats** - чаты (id, project_id, type, title, metadata)
- **chat_participants** - участники чатов (id, chat_id, user_id, role)
- **messages** - сообщения (id, chat_id, user_id, content, type, metadata)

### API Endpoints

#### Аутентификация
- POST /api/auth/temp-token - Генерация временного токена
- POST /api/auth/validate - Валидация токена
- POST /api/auth/refresh - Обновление токена

#### Проекты
- GET /api/projects/:id - Получить информацию о проекте
- POST /api/projects - Создать новый проект
- PUT /api/projects/:id - Обновить проект

#### Пользователи
- POST /api/users - Создать/обновить пользователя
- GET /api/users/:id - Получить пользователя
- GET /api/users/:id/chats - Чаты пользователя

#### Чаты
- POST /api/chats - Создать чат
- GET /api/chats/:id - Получить чат
- GET /api/chats/:id/messages - Сообщения чата
- POST /api/chats/:id/participants - Добавить участника
- DELETE /api/chats/:id/participants/:userId - Удалить участника

#### Сообщения
- POST /api/chats/:id/messages - Отправить сообщение
- GET /api/chats/:id/messages - Получить сообщения
- DELETE /api/messages/:id - Удалить сообщение

### WebSocket Events

#### Клиент -> Сервер
- join-chat (chatId: string)
- leave-chat (chatId: string)
- send-message (data: { chatId: string; content: string; type?: string })
- typing (data: { chatId: string; isTyping: boolean })
- read-messages (data: { chatId: string; messageIds: string[] })

#### Сервер -> Клиент
- message-received (message: Message)
- user-joined (data: { chatId: string; user: User })
- user-left (data: { chatId: string; userId: string })
- typing (data: { chatId: string; userId: string; isTyping: boolean })
- message-read (data: { chatId: string; messageIds: string[]; userId: string })

## Frontend SDK

### JavaScript/TypeScript библиотека

Создать класс ChatSDK с методами:
- constructor(config: ChatConfig)
- connect(): Promise<void>
- joinChat(chatId: string): void
- sendMessage(chatId: string, content: string, type = 'text'): void
- onMessageReceived(handler: (message: Message) => void): void

### Vue компоненты

Создать компоненты:
- ChatWidget.vue - основной виджет чата
- MessageBubble.vue - компонент сообщения
- ChatInput.vue - поле ввода сообщения
- ChatHeader.vue - заголовок чата

### iframe интеграция

Создать класс ChatIframe для безопасной интеграции через iframe с использованием PostMessage API.

## Структура проекта

```
chat-service/
├── backend/
│   ├── src/
│   │   ├── controllers/
│   │   ├── models/
│   │   ├── routes/
│   │   ├── services/
│   │   ├── middleware/
│   │   ├── websocket/
│   │   └── utils/
│   ├── package.json
│   └── tsconfig.json
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   ├── sdk/
│   │   ├── types/
│   │   └── utils/
│   ├── package.json
│   └── vite.config.ts
├── database/
│   ├── migrations/
│   └── seeds/
├── docs/
│   ├── api.md
│   ├── integration.md
│   └── deployment.md
└── docker-compose.yml
```

## Функциональные требования

### Основные функции
1. **Мультитенантность** - изоляция данных между проектами
2. **Real-time сообщения** - мгновенная доставка через WebSocket
3. **Типы чатов** - личные, групповые, поддержка
4. **Типы сообщений** - текст, изображения, файлы, системные
5. **Статусы** - онлайн/оффлайн, печатает, прочитано
6. **История сообщений** - загрузка и пагинация
7. **Уведомления** - push-уведомления для новых сообщений

### Безопасность
1. **CORS** - настройка для всех доменов проектов
2. **CSP** - Content Security Policy для iframe
3. **Временные токены** - короткий срок жизни для iframe
4. **Валидация origin** - проверка источника сообщений
5. **Rate limiting** - ограничение запросов
6. **Шифрование** - HTTPS, шифрование чувствительных данных

### Масштабируемость
1. **Горизонтальное масштабирование** - несколько инстансов
2. **Redis кластер** - для сессий и кэша
3. **PostgreSQL репликация** - для чтения
4. **CDN** - для статических ресурсов
5. **Load balancer** - для распределения нагрузки

## Этапы разработки

### Этап 1: MVP (2-3 недели)
- Базовая структура проекта
- Простая аутентификация
- Основные API endpoints
- WebSocket подключение
- Простой чат без iframe

### Этап 2: SDK и интеграция (1-2 недели)
- JavaScript/TypeScript SDK
- Vue компоненты
- iframe интеграция
- PostMessage API

### Этап 3: Админка и аналитика (2-3 недели)
- Административная панель
- Управление проектами
- Модерация сообщений
- Аналитика использования

### Этап 4: Продвинутые функции (1-2 недели)
- Push-уведомления
- Файлы и изображения
- Групповые чаты
- Поиск сообщений

## Технические детали

### Аутентификация через iframe
1. Основной сайт генерирует временный токен
2. Передача токена через PostMessage API
3. Валидация токена на сервере чата
4. Инициализация чата с данными пользователя

### Мультитенантность
1. Изоляция данных по project_id
2. Отдельные API ключи для каждого проекта
3. Настройки проекта в JSONB поле
4. Логирование действий с привязкой к проекту

### Производительность
1. Кэширование в Redis
2. Пагинация сообщений
3. Lazy loading изображений
4. Оптимизация WebSocket соединений

## Документация

Создать документацию:
1. **API Reference** - полное описание всех endpoints
2. **Integration Guide** - инструкции по интеграции
3. **SDK Documentation** - описание SDK методов
4. **Deployment Guide** - инструкции по развертыванию
5. **Security Guide** - рекомендации по безопасности

## Тестирование

1. **Unit тесты** - для всех сервисов и утилит
2. **Integration тесты** - для API endpoints
3. **WebSocket тесты** - для real-time функционала
4. **E2E тесты** - для полного flow
5. **Load тесты** - для проверки производительности

## Мониторинг

1. **Логирование** - структурированные логи
2. **Метрики** - количество сообщений, пользователей, ошибок
3. **Алерты** - уведомления о проблемах
4. **Health checks** - проверка состояния сервиса
5. **Tracing** - отслеживание запросов

Начни с создания базовой структуры проекта и настройки окружения для разработки. 