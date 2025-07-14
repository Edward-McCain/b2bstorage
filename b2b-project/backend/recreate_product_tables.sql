-- Удаление существующих таблиц (если они есть)
DROP TABLE IF EXISTS product_operations CASCADE;
DROP TABLE IF EXISTS product_balances CASCADE;

-- Создание таблицы product_balances
CREATE TABLE product_balances (
    id BIGSERIAL PRIMARY KEY,
    product_id BIGINT NOT NULL,
    warehouse_id BIGINT NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE(product_id, warehouse_id)
);

-- Создание таблицы product_operations
CREATE TABLE product_operations (
    id BIGSERIAL PRIMARY KEY,
    product_id BIGINT NOT NULL,
    warehouse_id BIGINT NOT NULL,
    operation_type VARCHAR(255) NOT NULL,
    quantity INTEGER NOT NULL,
    reference_type VARCHAR(255) NULL,
    reference_id BIGINT NULL,
    notes TEXT NULL,
    created_by BIGINT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Создание индексов для product_balances
CREATE INDEX idx_product_balances_product_warehouse ON product_balances(product_id, warehouse_id);

-- Создание индексов для product_operations
CREATE INDEX idx_product_operations_product_warehouse ON product_operations(product_id, warehouse_id);
CREATE INDEX idx_product_operations_type ON product_operations(operation_type);
CREATE INDEX idx_product_operations_reference ON product_operations(reference_type, reference_id);

-- Добавление внешних ключей для product_balances
ALTER TABLE product_balances 
ADD CONSTRAINT fk_product_balances_product_id 
FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE;

ALTER TABLE product_balances 
ADD CONSTRAINT fk_product_balances_warehouse_id 
FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE;

-- Добавление внешних ключей для product_operations
ALTER TABLE product_operations 
ADD CONSTRAINT fk_product_operations_product_id 
FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE;

ALTER TABLE product_operations 
ADD CONSTRAINT fk_product_operations_warehouse_id 
FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE;

ALTER TABLE product_operations 
ADD CONSTRAINT fk_product_operations_created_by 
FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE; 