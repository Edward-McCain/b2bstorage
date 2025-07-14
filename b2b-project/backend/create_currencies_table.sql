-- Создание таблицы валют для PostgreSQL
CREATE TABLE IF NOT EXISTS currencies (
    id BIGSERIAL PRIMARY KEY,
    currency_id CHAR(36) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    currency_type VARCHAR(10) NOT NULL,
    rate DECIMAL(10,2) NOT NULL,
    date TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Создание индексов
CREATE INDEX IF NOT EXISTS idx_currencies_currency_type ON currencies(currency_type);
CREATE INDEX IF NOT EXISTS idx_currencies_date ON currencies(date);

-- Добавление поля currency в таблицу users (если его нет)
DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'currency') THEN
        ALTER TABLE users ADD COLUMN currency VARCHAR(10) DEFAULT 'USD';
    END IF;
END $$;

-- Создание индекса для поля currency в таблице users
CREATE INDEX IF NOT EXISTS idx_users_currency ON users(currency); 