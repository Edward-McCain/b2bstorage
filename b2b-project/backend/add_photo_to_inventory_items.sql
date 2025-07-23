-- Добавление поля photo в таблицу inventory_items
ALTER TABLE inventory_items 
ADD COLUMN photo VARCHAR(500) NULL COMMENT 'URL фотографии товара при расхождении';

-- Создание индекса для оптимизации поиска по фото (опционально)
CREATE INDEX idx_inventory_items_photo ON inventory_items(photo);

-- Проверка структуры таблицы после изменений
DESCRIBE inventory_items; 