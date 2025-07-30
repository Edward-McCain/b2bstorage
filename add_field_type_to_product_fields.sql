-- Добавление колонки field_type в таблицу product_fields
ALTER TABLE product_fields 
ADD COLUMN field_type VARCHAR(255); 