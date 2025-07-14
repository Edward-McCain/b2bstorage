-- Создание таблицы инвентаризаций
CREATE TABLE inventories (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    warehouse_id BIGINT REFERENCES warehouses(id) ON DELETE CASCADE,
    status VARCHAR(50) DEFAULT 'draft', -- draft, in_progress, completed, cancelled
    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    notes TEXT
);

-- Создание таблицы товаров инвентаризации
CREATE TABLE inventory_items (
    id BIGSERIAL PRIMARY KEY,
    inventory_id BIGINT REFERENCES inventories(id) ON DELETE CASCADE,
    product_id BIGINT REFERENCES products(id) ON DELETE CASCADE,
    calculated_quantity DECIMAL(15,3) NOT NULL DEFAULT 0, -- расчетный остаток
    actual_quantity DECIMAL(15,3) NOT NULL DEFAULT 0, -- фактический остаток
    difference_quantity DECIMAL(15,3) GENERATED ALWAYS AS (actual_quantity - calculated_quantity) STORED, -- разница
    excess_shortage VARCHAR(20) GENERATED ALWAYS AS (
        CASE 
            WHEN (actual_quantity - calculated_quantity) > 0 THEN 'excess'
            WHEN (actual_quantity - calculated_quantity) < 0 THEN 'shortage'
            ELSE 'normal'
        END
    ) STORED,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы файлов инвентаризации
CREATE TABLE inventory_files (
    id BIGSERIAL PRIMARY KEY,
    inventory_id BIGINT REFERENCES inventories(id) ON DELETE CASCADE,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT,
    mime_type VARCHAR(100),
    uploaded_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создание индексов для оптимизации
CREATE INDEX idx_inventories_warehouse_id ON inventories(warehouse_id);
CREATE INDEX idx_inventories_status ON inventories(status);
CREATE INDEX idx_inventories_created_by ON inventories(created_by);
CREATE INDEX idx_inventories_created_at ON inventories(created_at);

CREATE INDEX idx_inventory_items_inventory_id ON inventory_items(inventory_id);
CREATE INDEX idx_inventory_items_product_id ON inventory_items(product_id);
CREATE INDEX idx_inventory_items_excess_shortage ON inventory_items(excess_shortage);

CREATE INDEX idx_inventory_files_inventory_id ON inventory_files(inventory_id);
CREATE INDEX idx_inventory_files_uploaded_by ON inventory_files(uploaded_by);

-- Добавление комментариев к таблицам
COMMENT ON TABLE inventories IS 'Таблица инвентаризаций';
COMMENT ON TABLE inventory_items IS 'Таблица товаров инвентаризации';
COMMENT ON TABLE inventory_files IS 'Таблица файлов инвентаризации';

-- Добавление комментариев к полям
COMMENT ON COLUMN inventories.status IS 'Статус инвентаризации: draft, in_progress, completed, cancelled';
COMMENT ON COLUMN inventory_items.calculated_quantity IS 'Расчетный остаток товара до инвентаризации';
COMMENT ON COLUMN inventory_items.actual_quantity IS 'Фактический остаток товара после инвентаризации';
COMMENT ON COLUMN inventory_items.difference_quantity IS 'Разница между фактическим и расчетным остатком';
COMMENT ON COLUMN inventory_items.excess_shortage IS 'Тип расхождения: excess (избыток), shortage (недостача), normal (норма)'; 