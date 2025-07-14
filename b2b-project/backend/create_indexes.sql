-- Создание индексов для product_balances
CREATE INDEX IF NOT EXISTS idx_product_balances_product_warehouse ON product_balances(product_id, warehouse_id);

-- Создание индексов для product_operations
CREATE INDEX IF NOT EXISTS idx_product_operations_product_warehouse ON product_operations(product_id, warehouse_id);
CREATE INDEX IF NOT EXISTS idx_product_operations_type ON product_operations(operation_type);
CREATE INDEX IF NOT EXISTS idx_product_operations_reference ON product_operations(reference_type, reference_id); 