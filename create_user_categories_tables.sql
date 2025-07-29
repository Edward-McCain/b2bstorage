-- Создание таблицы user_categories
CREATE TABLE user_categories (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    category_id VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Создание таблицы user_subcategories
CREATE TABLE user_subcategories (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    subcategory_id VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255),
    category_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES user_categories(category_id) ON DELETE CASCADE
);

-- Добавление комментариев к таблицам
COMMENT ON TABLE user_categories IS 'Пользовательские категории товаров';
COMMENT ON TABLE user_subcategories IS 'Пользовательские подкатегории товаров';

-- Добавление комментариев к полям
COMMENT ON COLUMN user_categories.user_id IS 'ID пользователя (внешний ключ на users.id)';
COMMENT ON COLUMN user_categories.category_id IS 'Уникальный идентификатор категории';
COMMENT ON COLUMN user_categories.name IS 'Название категории';

COMMENT ON COLUMN user_subcategories.user_id IS 'ID пользователя (внешний ключ на users.id)';
COMMENT ON COLUMN user_subcategories.subcategory_id IS 'Уникальный идентификатор подкатегории';
COMMENT ON COLUMN user_subcategories.name IS 'Название подкатегории';
COMMENT ON COLUMN user_subcategories.category_id IS 'ID родительской категории (внешний ключ на user_categories.category_id)'; 