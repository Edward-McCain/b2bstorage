# Настройка Favicon для B2B SKLAD

## ✅ Что сделано:

1. **Добавлены все иконки** в `frontend/src/assets/fav/`
2. **Обновлен `index.html`** с правильными путями к favicon
3. **Обновлен `manifest.json`** с корректными путями и метаданными

## 📁 Структура файлов:

```
frontend/src/assets/fav/
├── favicon.ico (основная иконка)
├── favicon-16x16.png
├── favicon-32x32.png
├── favicon-96x96.png
├── apple-icon-57x57.png
├── apple-icon-60x60.png
├── apple-icon-72x72.png
├── apple-icon-76x76.png
├── apple-icon-114x114.png
├── apple-icon-120x120.png
├── apple-icon-144x144.png
├── apple-icon-152x152.png
├── apple-icon-180x180.png
├── android-icon-36x36.png
├── android-icon-48x48.png
├── android-icon-72x72.png
├── android-icon-96x96.png
├── android-icon-144x144.png
├── android-icon-192x192.png
├── ms-icon-70x70.png
├── ms-icon-144x144.png
├── ms-icon-150x150.png
├── ms-icon-310x310.png
├── manifest.json
└── browserconfig.xml
```

## 🔧 Что добавлено в `index.html`:

```html
<link rel="icon" href="/src/assets/fav/favicon.ico">
<link rel="apple-touch-icon" sizes="57x57" href="/src/assets/fav/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/src/assets/fav/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/src/assets/fav/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/src/assets/fav/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="/src/assets/fav/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="/src/assets/fav/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="/src/assets/fav/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/src/assets/fav/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/src/assets/fav/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="/src/assets/fav/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="/src/assets/fav/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="/src/assets/fav/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="/src/assets/fav/favicon-16x16.png">
<link rel="manifest" href="/src/assets/fav/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/src/assets/fav/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
```

## 🎯 Поддержка устройств:

- ✅ **iOS** (iPhone, iPad) - apple-touch-icon
- ✅ **Android** - android-icon и manifest.json
- ✅ **Windows** - ms-icon и browserconfig.xml
- ✅ **Браузеры** - favicon.ico и favicon-*.png

## 🚀 Как проверить:

1. **Откройте сайт** в браузере
2. **Проверьте вкладку** - должен появиться favicon
3. **Добавьте в закладки** - иконка должна отображаться
4. **На мобильных устройствах** - при добавлении на главный экран должна появиться иконка

## 📱 PWA поддержка:

Manifest.json настроен для работы как Progressive Web App:
- Название: "B2B SKLAD"
- Описание: "B2B система управления складом"
- Цвета: белый фон и тема
- Режим отображения: standalone

## 🔄 Обновление favicon:

Если нужно обновить favicon:
1. Замените файлы в `frontend/src/assets/fav/`
2. Убедитесь, что имена файлов совпадают
3. Очистите кэш браузера для проверки изменений 