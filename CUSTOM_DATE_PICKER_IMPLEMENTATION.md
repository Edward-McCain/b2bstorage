# 📅 Внедрение кастомного календаря выбора даты

## 🎯 Цель
Заменить стандартные поля выбора даты `input[type="date"]` на кастомный компонент `CustomDatePicker` с улучшенным дизайном, соответствующим стилю сайта.

## ✅ Что уже сделано

### 1. Создан кастомный компонент
- **Файл**: `b2b-project/frontend/src/components/CustomDatePicker.vue`
- **Функции**: Выбор даты, навигация по месяцам, кнопки "Сегодня" и "Удалить"
- **Дизайн**: Соответствует стилю сайта (шрифт Inter, закругленные углы, легкая тень)

### 2. Добавлены кастомные стили
- **Файл**: `b2b-project/frontend/src/assets/custom-date-picker.css`
- **Особенности**: Убрана стандартная рамка браузера, добавлены плавные переходы
- **Подключение**: Импортирован в `base.css`

### 3. Пример внедрения
- **Файл**: `b2b-project/frontend/src/components/products/ReceiptsPage.vue`
- **Изменения**: Заменены стандартные поля даты на `CustomDatePicker`

## 📋 Список файлов для обновления

### Страницы с полями выбора даты:

#### 1. Страницы продуктов
- [ ] `b2b-project/frontend/src/components/products/ReceiptsPage.vue` ✅ (уже обновлено)
- [ ] `b2b-project/frontend/src/components/products/WriteOffsPage.vue`
- [ ] `b2b-project/frontend/src/components/products/InventoryPage.vue`
- [ ] `b2b-project/frontend/src/components/products/TransfersPage.vue`
- [ ] `b2b-project/frontend/src/components/products/ProductsLogs.vue`
- [ ] `b2b-project/frontend/src/components/products/TransferCreatePage.vue`
- [ ] `b2b-project/frontend/src/components/products/BalancesPage.vue`

#### 2. Админские страницы
- [ ] `b2b-project/frontend/src/components/admin/products/AdminReceiptsPage.vue`
- [ ] `b2b-project/frontend/src/components/admin/products/AdminWriteOffsPage.vue`
- [ ] `b2b-project/frontend/src/components/admin/products/AdminInventoryPage.vue`
- [ ] `b2b-project/frontend/src/components/admin/products/AdminTransfersPage.vue`
- [ ] `b2b-project/frontend/src/components/admin/products/AdminBalanceMovementsModal.vue`

#### 3. Страницы продаж
- [ ] `b2b-project/frontend/src/components/sales/CustomerOrdersPage.vue`
- [ ] `b2b-project/frontend/src/components/purchases/SupplierInvoicesPage.vue`
- [ ] `b2b-project/frontend/src/components/purchases/SupplierOrdersPage.vue`
- [ ] `b2b-project/frontend/src/components/purchases/ReceiptsPage.vue`
- [ ] `b2b-project/frontend/src/components/purchases/SupplierReturnsPage.vue`

#### 4. Модальные окна
- [ ] `b2b-project/frontend/src/components/products/MovementsModal.vue`

## 🔧 Инструкция по замене

### Шаг 1: Добавить импорт
```vue
<script setup>
import CustomDatePicker from '../CustomDatePicker.vue'
// ... остальные импорты
</script>
```

### Шаг 2: Заменить поле ввода
**Было:**
```vue
<input 
  v-model="filters.date_from" 
  type="date" 
  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
/>
```

**Стало:**
```vue
<CustomDatePicker
  v-model="filters.date_from"
  placeholder="Выберите дату от"
  class="w-full"
/>
```

### Шаг 3: Обновить все поля даты на странице
Заменить все `input[type="date"]` на `CustomDatePicker` с соответствующими placeholder'ами.

## 🎨 Особенности дизайна

### Кастомные стили включают:
- ✅ **Шрифт Inter** - соответствует дизайну сайта
- ✅ **Закругленные углы** - `border-radius: 8px`
- ✅ **Легкая тень** - `box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05)`
- ✅ **Отсутствие рамки** - убрана стандартная рамка браузера
- ✅ **Плавные переходы** - `transition: all 0.2s ease-in-out`
- ✅ **Состояние фокуса** - синяя рамка и тень
- ✅ **Состояние при наведении** - изменение цвета рамки

### Функциональность:
- ✅ **Выбор даты** - клик по дню месяца
- ✅ **Навигация по месяцам** - кнопки предыдущий/следующий
- ✅ **Выбор сегодняшней даты** - кнопка "Сегодня"
- ✅ **Очистка даты** - кнопка "Удалить"
- ✅ **Закрытие по клику вне календаря** - оверлей
- ✅ **Русские названия** - месяцы и дни недели на русском языке

## 📱 Адаптивность

### Мобильные устройства:
- ✅ **Размер шрифта 16px** - предотвращает зум на iOS
- ✅ **Увеличенные отступы** - для удобства на сенсорных экранах
- ✅ **Оптимизированные размеры кнопок** - минимум 44px

### Темная тема:
- ✅ **Поддержка prefers-color-scheme** - автоматическое переключение
- ✅ **Адаптированные цвета** - для темного режима

## ♿ Доступность

### Соответствие стандартам:
- ✅ **Клавиатурная навигация** - Tab, Enter, Escape
- ✅ **ARIA атрибуты** - role, aria-label
- ✅ **Фокус на элементах** - визуальная индикация
- ✅ **Контрастность** - соответствие стандартам WCAG
- ✅ **Размер кликабельных областей** - минимум 44px
- ✅ **Экранные читалки** - поддержка screen readers

## 🧪 Тестирование

### Автоматические тесты:
- ✅ **Стили календаря** - проверка всех CSS свойств
- ✅ **Функциональность** - проверка всех функций компонента
- ✅ **Интеграция с сайтом** - проверка совместимости
- ✅ **Доступность** - проверка соответствия стандартам

### Ручное тестирование:
- [ ] Проверить работу на всех страницах
- [ ] Протестировать на мобильных устройствах
- [ ] Проверить работу с клавиатурой
- [ ] Убедиться в корректности отображения дат

## 🚀 Быстрое внедрение

### Команда для массовой замены:
```bash
# Найти все файлы с input[type="date"]
find b2b-project/frontend/src/components -name "*.vue" -exec grep -l "type=\"date\"" {} \;

# Заменить в каждом файле
# 1. Добавить импорт CustomDatePicker
# 2. Заменить input на CustomDatePicker
# 3. Добавить placeholder
```

### Пример замены для всех файлов:
```vue
<!-- Заменить все вхождения -->
<input 
  v-model="filters.date_from" 
  type="date" 
  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
/>

<!-- На -->
<CustomDatePicker
  v-model="filters.date_from"
  placeholder="Выберите дату от"
  class="w-full"
/>
```

## 📊 Результат

После внедрения кастомного календаря:
- ✅ **Единообразный дизайн** - все календари выглядят одинаково
- ✅ **Улучшенный UX** - более удобный интерфейс выбора даты
- ✅ **Соответствие дизайну сайта** - шрифты и стили как на остальных элементах
- ✅ **Лучшая доступность** - поддержка клавиатуры и экранных читалок
- ✅ **Адаптивность** - корректная работа на всех устройствах

## 🎯 Следующие шаги

1. **Внедрить на всех страницах** - заменить все `input[type="date"]`
2. **Протестировать** - проверить работу на всех устройствах
3. **Оптимизировать** - при необходимости улучшить производительность
4. **Документировать** - добавить в документацию проекта 