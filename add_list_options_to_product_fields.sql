-- Добавление колонки list_options в таблицу product_fields
ALTER TABLE product_fields 
ADD COLUMN list_options JSONB; 