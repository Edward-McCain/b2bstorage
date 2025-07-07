const XLSX = require('xlsx');

// Данные для генерации товаров
const categories = [
  'Электроника', 'Одежда', 'Спорт и отдых', 'Книги', 'Продукты питания',
  'Строительные материалы', 'Автозапчасти', 'Мебель', 'Игрушки', 'Косметика'
];

const subcategories = {
  'Электроника': ['Смартфоны', 'Ноутбуки', 'Планшеты', 'Аксессуары'],
  'Одежда': ['Мужская одежда', 'Женская одежда', 'Детская одежда', 'Обувь'],
  'Спорт и отдых': ['Фитнес оборудование', 'Велосипеды', 'Туристическое снаряжение'],
  'Книги': ['Художественная литература', 'Учебники', 'Пособия'],
  'Продукты питания': ['Молочные продукты', 'Мясные продукты', 'Напитки'],
  'Строительные материалы': ['Кирпич', 'Цемент', 'Инструменты'],
  'Автозапчасти': ['Двигатель', 'Тормозная система', 'Электрика'],
  'Мебель': ['Кухонная мебель', 'Спальная мебель', 'Офисная мебель'],
  'Игрушки': ['Конструкторы', 'Куклы', 'Настольные игры'],
  'Косметика': ['Уход за лицом', 'Уход за телом', 'Декоративная косметика']
};

const countries = ['Россия', 'Китай', 'Германия', 'США', 'Япония', 'Италия', 'Франция', 'Южная Корея'];
const suppliers = [
  'ООО "ТехноСнаб"', 'ИП Иванов А.А.', 'ООО "ГлобалТрейд"', 'ООО "ИмпортЭкспорт"',
  'ООО "СтройМаркет"', 'ООО "СпортТовары"', 'ООО "КнижныйМир"', 'ООО "ПродуктСервис"'
];

const units = ['Штука', 'Килограмм', 'Грамм', 'Литр', 'Метр', 'Квадратный метр', 'Упаковка', 'Комплект'];
const packingTypes = ['Штучная', 'Весовая', 'Разливная'];
const accountingTypes = ['Без специализированного учета', 'Алкогольный товар', 'Учет по серийным номерам', 'СИЗ'];
const productTypes = ['Не маркируется', 'Табачная продукция', 'Обувь', 'Одежда', 'Молочная продукция', 'Упакованная вода'];

const barcodeTypes = ['EAN8', 'EAN13', 'Code128', 'GTIN', 'UPC'];

// Функция для генерации случайного числа в диапазоне
function randomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

// Функция для генерации случайного элемента из массива
function randomChoice(array) {
  return array[randomInt(0, array.length - 1)];
}

// Функция для генерации случайного веса
function randomWeight() {
  return (Math.random() * 10).toFixed(2);
}

// Функция для генерации случайного объема
function randomVolume() {
  return (Math.random() * 5).toFixed(3);
}

// Функция для генерации штрихкода
function generateBarcode() {
  const length = randomInt(8, 13);
  let barcode = '';
  for (let i = 0; i < length; i++) {
    barcode += randomInt(0, 9);
  }
  return barcode;
}

// Функция для генерации артикула
function generateArticle(category) {
  const prefix = category.substring(0, 3).toUpperCase();
  const number = randomInt(1000, 9999);
  return `${prefix}-${number}`;
}

// Функция для генерации кода
function generateCode() {
  const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  const prefix = letters[randomInt(0, letters.length - 1)] + letters[randomInt(0, letters.length - 1)];
  const number = randomInt(1000, 9999);
  return `${prefix}-${number}`;
}

// Генерация 10 товаров
const products = [];

const productNames = [
  'Смартфон Samsung Galaxy S21',
  'Ноутбук Dell Inspiron 15',
  'Кроссовки Nike Air Max',
  'Футболка хлопковая мужская',
  'Велосипед горный Stels',
  'Книга "Война и мир"',
  'Молоко пастеризованное 3.2%',
  'Кирпич строительный красный',
  'Масло моторное 5W-30',
  'Диван угловой "Комфорт"'
];

for (let i = 0; i < 10; i++) {
  const category = randomChoice(categories);
  const subcategory = randomChoice(subcategories[category] || ['Общее']);
  
  const product = {
    'Наименование': productNames[i],
    'Описание': `Описание товара ${i + 1} для складского учета`,
    'Категория': category,
    'Подкатегория': subcategory,
    'Страна': randomChoice(countries),
    'Поставщик': randomChoice(suppliers),
    'Артикул': generateArticle(category),
    'Код': generateCode(),
    'Внешний код': `EXT-${randomInt(1000, 9999)}`,
    'Единица измерения': randomChoice(units),
    'Вес (кг)': randomWeight(),
    'Объем (л)': randomVolume(),
    'НДС (%)': randomChoice(['0%', '10%', '20%']),
    'Фасовка': randomChoice(packingTypes),
    'Тип учета': randomChoice(accountingTypes),
    'Тип продукции': randomChoice(productTypes),
    'Тип штрихкода': randomChoice(barcodeTypes),
    'Штрихкод': generateBarcode(),
    'Система налогообложения': randomChoice(['ОСН', 'УСН', 'ЕНВД']),
    'Признак предмета расчета': 'Товар'
  };
  
  products.push(product);
}

// Создание рабочей книги
const workbook = XLSX.utils.book_new();

// Создание листа с данными
const worksheet = XLSX.utils.json_to_sheet(products);

// Настройка ширины столбцов
const columnWidths = [
  { wch: 30 }, // Наименование
  { wch: 40 }, // Описание
  { wch: 15 }, // Категория
  { wch: 20 }, // Подкатегория
  { wch: 12 }, // Страна
  { wch: 25 }, // Поставщик
  { wch: 12 }, // Артикул
  { wch: 10 }, // Код
  { wch: 12 }, // Внешний код
  { wch: 15 }, // Единица измерения
  { wch: 10 }, // Вес
  { wch: 10 }, // Объем
  { wch: 8 },  // НДС
  { wch: 12 }, // Фасовка
  { wch: 25 }, // Тип учета
  { wch: 20 }, // Тип продукции
  { wch: 12 }, // Тип штрихкода
  { wch: 15 }, // Штрихкод
  { wch: 20 }, // Система налогообложения
  { wch: 20 }  // Признак предмета расчета
];

worksheet['!cols'] = columnWidths;

// Добавление листа в книгу
XLSX.utils.book_append_sheet(workbook, worksheet, 'Товары');

// Сохранение файла
XLSX.writeFile(workbook, 'sample_products.xlsx');

console.log('✅ Excel файл "sample_products.xlsx" успешно создан с 10 товарами для складского учета!');
console.log('\n📋 Содержимое файла:');
console.log('- Наименование товара');
console.log('- Описание');
console.log('- Категория и подкатегория');
console.log('- Страна происхождения');
console.log('- Поставщик');
console.log('- Артикул, код и внешний код');
console.log('- Единица измерения');
console.log('- Вес и объем');
console.log('- НДС');
console.log('- Фасовка');
console.log('- Тип учета');
console.log('- Тип продукции');
console.log('- Штрихкод и его тип');
console.log('- Данные для кассового чека'); 