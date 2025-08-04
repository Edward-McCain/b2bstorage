# SEO Настройка для B2B SKLAD

## 📋 Обзор

Данный документ описывает настройку SEO тегов для главной страницы и страниц авторизации/регистрации системы B2B SKLAD.

## 🎯 Созданные файлы

### Главная страница
- **`index.html`** - главная страница с SEO тегами на русском языке

### Страницы авторизации
- **`login.html`** - страница входа на русском языке
- **`login-uz.html`** - страница входа на узбекском языке

### Страницы регистрации
- **`register.html`** - страница регистрации на русском языке
- **`register-uz.html`** - страница регистрации на узбекском языке

## 🔍 SEO Теги

### Meta Description
**Русский:**
- Главная: "B2B SKLAD - профессиональная система складского учета нового поколения..."
- Авторизация: "Вход в систему B2B SKLAD. Авторизация в профессиональной системе..."
- Регистрация: "Регистрация в системе B2B SKLAD. Создайте аккаунт для доступа..."

**Узбекский:**
- Авторизация: "B2B SKLAD tizimiga kirish. Professional ombor hisobini boshqarish..."
- Регистрация: "B2B SKLAD tizimida ro'yxatdan o'tish. Professional ombor hisobini..."

### Meta Keywords
**Русский:**
- Главная: "складской учет, система учета, B2B, автоматизация склада..."
- Авторизация: "вход в систему, авторизация, B2B SKLAD, складской учет..."
- Регистрация: "регистрация, создание аккаунта, B2B SKLAD, складской учет..."

**Узбекский:**
- Авторизация: "tizimga kirish, avtorizatsiya, B2B SKLAD, ombor hisobi..."
- Регистрация: "ro'yxatdan o'tish, hisob yaratish, B2B SKLAD, ombor hisobi..."

### Open Graph Теги
Все страницы включают:
- `og:title` - заголовок страницы
- `og:description` - описание для социальных сетей
- `og:type` - тип контента (website)
- `og:url` - URL страницы
- `og:image` - изображение для превью
- `og:site_name` - название сайта
- `og:locale` - локаль (ru_RU или uz_UZ)

### Twitter Card Теги
Все страницы включают:
- `twitter:card` - тип карточки (summary_large_image)
- `twitter:title` - заголовок для Twitter
- `twitter:description` - описание для Twitter
- `twitter:image` - изображение для Twitter

## 🌐 Многоязычность

### Поддерживаемые языки
- **Русский (ru)** - основной язык
- **Узбекский (uz)** - для узбекского рынка

### Локализация
- `lang="ru"` для русских страниц
- `lang="uz"` для узбекских страниц
- `og:locale="ru_RU"` для русских страниц
- `og:locale="uz_UZ"` для узбекских страниц

## 🔧 Настройка сервера

### Nginx конфигурация
Для правильной работы SEO тегов необходимо настроить Nginx:

```nginx
# Главная страница
location = / {
    try_files /index.html =404;
}

# Страницы авторизации
location = /login {
    try_files /login.html =404;
}

location = /login-uz {
    try_files /login-uz.html =404;
}

# Страницы регистрации
location = /register {
    try_files /register.html =404;
}

location = /register-uz {
    try_files /register-uz.html =404;
}
```

### Apache конфигурация
Для Apache (.htaccess):

```apache
RewriteEngine On

# Главная страница
RewriteRule ^$ /index.html [L]

# Страницы авторизации
RewriteRule ^login$ /login.html [L]
RewriteRule ^login-uz$ /login-uz.html [L]

# Страницы регистрации
RewriteRule ^register$ /register.html [L]
RewriteRule ^register-uz$ /register-uz.html [L]
```

## 📊 Аналитика

### Google Analytics
Для отслеживания SEO эффективности рекомендуется добавить Google Analytics:

```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

### Yandex Metrika
Для российского рынка также рекомендуется Yandex Metrika:

```html
<!-- Yandex Metrika -->
<script type="text/javascript">
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(METRIKA_ID, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true
   });
</script>
```

## 🚀 Рекомендации

### Для улучшения SEO:
1. **Создайте sitemap.xml** - карта сайта для поисковых систем
2. **Настройте robots.txt** - инструкции для поисковых роботов
3. **Добавьте структурированные данные** - Schema.org разметка
4. **Оптимизируйте изображения** - используйте WebP формат
5. **Настройте кеширование** - для улучшения скорости загрузки

### Для социальных сетей:
1. **Создайте og-image.jpg** - изображение для превью в соцсетях
2. **Настройте Twitter Cards** - для красивого отображения в Twitter
3. **Добавьте Facebook Pixel** - для рекламной аналитики

## 📝 Примечания

- Страницы авторизации и регистрации имеют `robots: noindex, nofollow` для предотвращения индексации
- Главная страница имеет `robots: index, follow` для индексации
- Все URL указаны как `https://b2bstorage.ru` - замените на ваш домен
- Изображение `og-image.jpg` должно быть создано в размере 1200x630 пикселей 