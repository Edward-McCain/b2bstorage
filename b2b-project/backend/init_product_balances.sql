-- Инициализация остатков товаров на основе существующих оприходований
-- Этот скрипт нужно выполнить после создания таблиц

-- Вставляем остатки на основе оприходований
INSERT INTO product_balances (product_id, warehouse_id, quantity, created_at, updated_at)
SELECT 
    rp.product_id,
    r.warehouse_id,
    SUM(rp.quantity) as total_quantity,
    NOW() as created_at,
    NOW() as updated_at
FROM receipt_positions rp
JOIN receipts r ON rp.receipt_id = r.id
WHERE r.status = 'posted' OR r.is_posted = true
GROUP BY rp.product_id, r.warehouse_id
ON CONFLICT (product_id, warehouse_id) 
DO UPDATE SET 
    quantity = product_balances.quantity + EXCLUDED.quantity,
    updated_at = NOW();

-- Вставляем операции на основе существующих оприходований
INSERT INTO product_operations (product_id, warehouse_id, operation_type, quantity, reference_type, reference_id, notes, created_by, created_at, updated_at)
SELECT 
    rp.product_id,
    r.warehouse_id,
    'receipt' as operation_type,
    rp.quantity,
    'receipt' as reference_type,
    r.id as reference_id,
    CONCAT('Оприходование №', r.number) as notes,
    r.created_by,
    r.created_at,
    r.updated_at
FROM receipt_positions rp
JOIN receipts r ON rp.receipt_id = r.id
WHERE r.status = 'posted' OR r.is_posted = true;

-- Вставляем операции на основе существующих списаний
INSERT INTO product_operations (product_id, warehouse_id, operation_type, quantity, reference_type, reference_id, notes, created_by, created_at, updated_at)
SELECT 
    wp.product_id,
    w.warehouse_id,
    'write_off' as operation_type,
    -wp.quantity as quantity, -- Отрицательное количество для списания
    'write_off' as reference_type,
    w.id as reference_id,
    CONCAT('Списание №', w.number) as notes,
    w.created_by,
    w.created_at,
    w.updated_at
FROM write_off_positions wp
JOIN write_offs w ON wp.write_off_id = w.id
WHERE w.status = 'posted' OR w.is_posted = true; 