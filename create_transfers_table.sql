-- Создание таблицы для перемещений товаров между складами
CREATE TABLE product_transfers (
    id BIGSERIAL PRIMARY KEY,
    from_warehouse_id BIGINT NOT NULL REFERENCES warehouses(id) ON DELETE RESTRICT,
    to_warehouse_id BIGINT NOT NULL REFERENCES warehouses(id) ON DELETE RESTRICT,
    transfer_date DATE NOT NULL DEFAULT CURRENT_DATE,
    status VARCHAR(50) NOT NULL DEFAULT 'draft', -- draft, confirmed, completed, cancelled
    notes TEXT,
    created_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    completed_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    
    -- Ограничения
    CONSTRAINT check_different_warehouses CHECK (from_warehouse_id != to_warehouse_id),
    CONSTRAINT check_valid_status CHECK (status IN ('draft', 'confirmed', 'completed', 'cancelled'))
);

-- Создание таблицы для позиций перемещения
CREATE TABLE product_transfer_positions (
    id BIGSERIAL PRIMARY KEY,
    transfer_id BIGINT NOT NULL REFERENCES product_transfers(id) ON DELETE CASCADE,
    product_id BIGINT NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
    quantity INTEGER NOT NULL CHECK (quantity > 0),
    actual_quantity INTEGER NULL, -- фактическое количество при выполнении
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Индексы для оптимизации
CREATE INDEX idx_product_transfers_from_warehouse ON product_transfers(from_warehouse_id);
CREATE INDEX idx_product_transfers_to_warehouse ON product_transfers(to_warehouse_id);
CREATE INDEX idx_product_transfers_status ON product_transfers(status);
CREATE INDEX idx_product_transfers_date ON product_transfers(transfer_date);
CREATE INDEX idx_product_transfer_positions_transfer_id ON product_transfer_positions(transfer_id);
CREATE INDEX idx_product_transfer_positions_product_id ON product_transfer_positions(product_id);

-- Комментарии к таблицам
COMMENT ON TABLE product_transfers IS 'Перемещения товаров между складами';
COMMENT ON TABLE product_transfer_positions IS 'Позиции перемещений товаров';
COMMENT ON COLUMN product_transfers.status IS 'Статус: draft - черновик, confirmed - подтвержден, completed - выполнен, cancelled - отменен';
COMMENT ON COLUMN product_transfer_positions.actual_quantity IS 'Фактическое количество при выполнении перемещения'; 