-- SQL запросы для очистки данных из таблиц оприходований
-- Выполняйте эти запросы в правильном порядке из-за внешних ключей

-- 1. Сначала удаляем файлы оприходований (если таблица существует)
DELETE FROM receipt_files;

-- 2. Удаляем позиции оприходований
DELETE FROM receipt_positions;

-- 3. Удаляем сами оприходования
DELETE FROM receipts;

-- 4. Сброс автоинкрементных счетчиков (если нужно)
-- ALTER SEQUENCE receipts_id_seq RESTART WITH 1;
-- ALTER SEQUENCE receipt_positions_id_seq RESTART WITH 1;
-- ALTER SEQUENCE receipt_files_id_seq RESTART WITH 1;

-- Проверка что данные удалены
SELECT 'receipts' as table_name, COUNT(*) as count FROM receipts
UNION ALL
SELECT 'receipt_positions' as table_name, COUNT(*) as count FROM receipt_positions
UNION ALL
SELECT 'receipt_files' as table_name, COUNT(*) as count FROM receipt_files; 