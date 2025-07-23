-- Обновление валюты пользователей с RUB на UZS
-- 
-- Этот скрипт изменяет валюту по умолчанию для всех пользователей
-- с RUB на UZS в соответствии с требованиями проекта

-- Показываем текущее состояние
SELECT 
    'Пользователей с валютой RUB:' as description,
    COUNT(*) as count
FROM users 
WHERE currency = 'RUB';

-- Обновляем валюту с RUB на UZS
UPDATE users 
SET currency = 'UZS', 
    updated_at = NOW()
WHERE currency = 'RUB';

-- Показываем результат
SELECT 
    'Пользователей с валютой UZS после обновления:' as description,
    COUNT(*) as count
FROM users 
WHERE currency = 'UZS';

-- Проверяем что RUB больше нет
SELECT 
    'Пользователей с валютой RUB после обновления:' as description,
    COUNT(*) as count
FROM users 
WHERE currency = 'RUB';

-- Показываем распределение по валютам
SELECT 
    currency,
    COUNT(*) as user_count
FROM users 
GROUP BY currency
ORDER BY user_count DESC; 