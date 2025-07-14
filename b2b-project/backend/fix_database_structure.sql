-- Исправление структуры базы данных
-- Выполняйте эти запросы после анализа структуры

-- 1. Добавление недостающих внешних ключей для складов
DO $$ 
BEGIN
    -- receipts.warehouse -> warehouses.id
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'fk_receipts_warehouse'
    ) THEN
        ALTER TABLE receipts 
        ADD CONSTRAINT fk_receipts_warehouse 
        FOREIGN KEY (warehouse) REFERENCES warehouses(id) ON DELETE CASCADE;
    END IF;
    
    -- write_offs.warehouse -> warehouses.id
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'fk_write_offs_warehouse'
    ) THEN
        ALTER TABLE write_offs 
        ADD CONSTRAINT fk_write_offs_warehouse 
        FOREIGN KEY (warehouse) REFERENCES warehouses(id) ON DELETE CASCADE;
    END IF;
    
    -- inventories.warehouse -> warehouses.id
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'fk_inventories_warehouse'
    ) THEN
        ALTER TABLE inventories 
        ADD CONSTRAINT fk_inventories_warehouse 
        FOREIGN KEY (warehouse) REFERENCES warehouses(id) ON DELETE CASCADE;
    END IF;
END $$;

-- 2. Добавление недостающих внешних ключей для пользователей
DO $$ 
BEGIN
    -- receipts.user_id -> users.id
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'fk_receipts_user_id'
    ) THEN
        ALTER TABLE receipts 
        ADD CONSTRAINT fk_receipts_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
    END IF;
    
    -- write_offs.user_id -> users.id
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'fk_write_offs_user_id'
    ) THEN
        ALTER TABLE write_offs 
        ADD CONSTRAINT fk_write_offs_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
    END IF;
    
    -- inventories.user_id -> users.id
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'fk_inventories_user_id'
    ) THEN
        ALTER TABLE inventories 
        ADD CONSTRAINT fk_inventories_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
    END IF;
END $$;

-- 3. Добавление недостающих индексов
CREATE INDEX IF NOT EXISTS idx_receipts_user_id ON receipts(user_id);
CREATE INDEX IF NOT EXISTS idx_receipts_warehouse ON receipts(warehouse);
CREATE INDEX IF NOT EXISTS idx_receipts_status ON receipts(status);
CREATE INDEX IF NOT EXISTS idx_receipts_date ON receipts(date);

CREATE INDEX IF NOT EXISTS idx_write_offs_user_id ON write_offs(user_id);
CREATE INDEX IF NOT EXISTS idx_write_offs_warehouse ON write_offs(warehouse);
CREATE INDEX IF NOT EXISTS idx_write_offs_status ON write_offs(status);
CREATE INDEX IF NOT EXISTS idx_write_offs_date ON write_offs(date);

CREATE INDEX IF NOT EXISTS idx_inventories_user_id ON inventories(user_id);
CREATE INDEX IF NOT EXISTS idx_inventories_warehouse ON inventories(warehouse);
CREATE INDEX IF NOT EXISTS idx_inventories_status ON inventories(status);
CREATE INDEX IF NOT EXISTS idx_inventories_date ON inventories(date);

CREATE INDEX IF NOT EXISTS idx_products_sklad_user_id ON products_sklad(user_id);
CREATE INDEX IF NOT EXISTS idx_products_sklad_category ON products_sklad(category);
CREATE INDEX IF NOT EXISTS idx_products_sklad_subcategory ON products_sklad(subcategory);

-- 4. Добавление уникальных ограничений
DO $$ 
BEGIN
    -- Уникальные номера документов для пользователя
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'unique_receipt_number_per_user'
    ) THEN
        ALTER TABLE receipts 
        ADD CONSTRAINT unique_receipt_number_per_user 
        UNIQUE (user_id, number);
    END IF;
    
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'unique_write_off_number_per_user'
    ) THEN
        ALTER TABLE write_offs 
        ADD CONSTRAINT unique_write_off_number_per_user 
        UNIQUE (user_id, number);
    END IF;
    
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'unique_inventory_number_per_user'
    ) THEN
        ALTER TABLE inventories 
        ADD CONSTRAINT unique_inventory_number_per_user 
        UNIQUE (user_id, number);
    END IF;
END $$;

-- 5. Проверка и создание таблиц для остатков (если их нет)
DO $$ 
BEGIN
    -- Создание таблицы product_balances если её нет
    IF NOT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'product_balances') THEN
        CREATE TABLE product_balances (
            id BIGSERIAL PRIMARY KEY,
            product_id BIGINT NOT NULL,
            warehouse_id BIGINT NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            UNIQUE(product_id, warehouse_id)
        );
        
        CREATE INDEX idx_product_balances_product_warehouse ON product_balances(product_id, warehouse_id);
        
        ALTER TABLE product_balances 
        ADD CONSTRAINT fk_product_balances_product_id 
        FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE;
        
        ALTER TABLE product_balances 
        ADD CONSTRAINT fk_product_balances_warehouse_id 
        FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE;
    END IF;
    
    -- Создание таблицы product_operations если её нет
    IF NOT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'product_operations') THEN
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
        
        CREATE INDEX idx_product_operations_product_warehouse ON product_operations(product_id, warehouse_id);
        CREATE INDEX idx_product_operations_type ON product_operations(operation_type);
        CREATE INDEX idx_product_operations_reference ON product_operations(reference_type, reference_id);
        
        ALTER TABLE product_operations 
        ADD CONSTRAINT fk_product_operations_product_id 
        FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE;
        
        ALTER TABLE product_operations 
        ADD CONSTRAINT fk_product_operations_warehouse_id 
        FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE;
        
        ALTER TABLE product_operations 
        ADD CONSTRAINT fk_product_operations_created_by 
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE;
    END IF;
    
    -- Создание таблицы product_transfers если её нет
    IF NOT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'product_transfers') THEN
        CREATE TABLE product_transfers (
            id BIGSERIAL PRIMARY KEY,
            from_warehouse_id BIGINT NOT NULL,
            to_warehouse_id BIGINT NOT NULL,
            transfer_date DATE NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'draft',
            notes TEXT NULL,
            created_by BIGINT NOT NULL,
            completed_by BIGINT NULL,
            completed_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        );
        
        ALTER TABLE product_transfers 
        ADD CONSTRAINT fk_product_transfers_from_warehouse 
        FOREIGN KEY (from_warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE;
        
        ALTER TABLE product_transfers 
        ADD CONSTRAINT fk_product_transfers_to_warehouse 
        FOREIGN KEY (to_warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE;
        
        ALTER TABLE product_transfers 
        ADD CONSTRAINT fk_product_transfers_created_by 
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE;
        
        ALTER TABLE product_transfers 
        ADD CONSTRAINT fk_product_transfers_completed_by 
        FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE CASCADE;
    END IF;
    
    -- Создание таблицы product_transfer_positions если её нет
    IF NOT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'product_transfer_positions') THEN
        CREATE TABLE product_transfer_positions (
            id BIGSERIAL PRIMARY KEY,
            transfer_id BIGINT NOT NULL,
            product_id BIGINT NOT NULL,
            quantity INTEGER NOT NULL,
            actual_quantity INTEGER NULL,
            notes TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        );
        
        ALTER TABLE product_transfer_positions 
        ADD CONSTRAINT fk_product_transfer_positions_transfer_id 
        FOREIGN KEY (transfer_id) REFERENCES product_transfers(id) ON DELETE CASCADE;
        
        ALTER TABLE product_transfer_positions 
        ADD CONSTRAINT fk_product_transfer_positions_product_id 
        FOREIGN KEY (product_id) REFERENCES products_sklad(id) ON DELETE CASCADE;
    END IF;
END $$;

-- 6. Проверка целостности данных
-- Удаление записей с несуществующими внешними ключами
DELETE FROM receipts WHERE warehouse NOT IN (SELECT id FROM warehouses);
DELETE FROM write_offs WHERE warehouse NOT IN (SELECT id FROM warehouses);
DELETE FROM inventories WHERE warehouse NOT IN (SELECT id FROM warehouses);

DELETE FROM receipts WHERE user_id NOT IN (SELECT id FROM users);
DELETE FROM write_offs WHERE user_id NOT IN (SELECT id FROM users);
DELETE FROM inventories WHERE user_id NOT IN (SELECT id FROM users);

-- 7. Создание представления для удобного просмотра остатков
CREATE OR REPLACE VIEW product_balances_view AS
SELECT 
    pb.id,
    pb.product_id,
    ps.name as product_name,
    ps.article as product_article,
    pb.warehouse_id,
    w.name as warehouse_name,
    pb.quantity,
    pb.updated_at
FROM product_balances pb
JOIN products_sklad ps ON pb.product_id = ps.id
JOIN warehouses w ON pb.warehouse_id = w.id
ORDER BY w.name, ps.name; 