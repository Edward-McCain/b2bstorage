-- Проверка товаров в базе данных
SELECT 
    id,
    name,
    article,
    user_id,
    created_at
FROM products_sklad 
ORDER BY created_at DESC 
LIMIT 10;

-- Проверка последних позиций оприходований
SELECT 
    rp.id,
    rp.receipt_id,
    rp.product_id,
    rp.name,
    rp.article,
    rp.quantity,
    r.number as receipt_number,
    r.created_at
FROM receipt_positions rp
JOIN receipts r ON rp.receipt_id = r.id
ORDER BY rp.created_at DESC
LIMIT 10; 