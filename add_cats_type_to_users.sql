-- Добавление поля cats_type в таблицу users
-- Поле может принимать значения: 'system' (по умолчанию) или 'user'

ALTER TABLE users 
ADD COLUMN cats_type VARCHAR(10) NOT NULL DEFAULT 'system' 
CHECK (cats_type IN ('system', 'user'));

-- Добавляем комментарий к полю для документации
COMMENT ON COLUMN users.cats_type IS 'Тип категорий: system - системные, user - пользовательские'; 