export const inventoryMethods = [
  {
    path: '/inventories',
    method: 'GET',
    title: 'Получение списка инвентаризаций',
    description: 'Возвращает список всех инвентаризаций пользователя с возможностью фильтрации и пагинации'
  },
  {
    path: '/inventories/{id}',
    method: 'GET',
    title: 'Получение инвентаризации по ID',
    description: 'Возвращает детальную информацию об инвентаризации по ее уникальному идентификатору'
  },
  {
    path: '/inventories',
    method: 'POST',
    title: 'Создание инвентаризации',
    description: 'Создает новую инвентаризацию с указанным складом и описанием'
  },
  {
    path: '/inventories/{id}',
    method: 'PUT',
    title: 'Обновление инвентаризации',
    description: 'Обновляет существующую инвентаризацию'
  },
  {
    path: '/inventories/{id}',
    method: 'DELETE',
    title: 'Удаление инвентаризации',
    description: 'Удаляет инвентаризацию по указанному идентификатору'
  },
  {
    path: '/inventories/{id}/export',
    method: 'GET',
    title: 'Экспорт инвентаризации',
    description: 'Экспортирует инвентаризацию в формате Excel или PDF'
  },
  {
    path: '/inventories/calculate-balances',
    method: 'POST',
    title: 'Расчет балансов инвентаризации',
    description: 'Выполняет расчет балансов для инвентаризации на основе данных о товарах'
  },
  {
    path: '/inventory-files/upload',
    method: 'POST',
    title: 'Загрузка файлов инвентаризации',
    description: 'Загружает файлы для инвентаризации'
  },
  {
    path: '/inventory-files/upload-draft',
    method: 'POST',
    title: 'Загрузка черновика файлов',
    description: 'Загружает файлы в режиме черновика без привязки к инвентаризации'
  },
  {
    path: '/inventory-files/{id}',
    method: 'GET',
    title: 'Получение файла инвентаризации',
    description: 'Возвращает информацию о файле инвентаризации'
  },
  {
    path: '/inventory-files/{id}',
    method: 'DELETE',
    title: 'Удаление файла инвентаризации',
    description: 'Удаляет файл инвентаризации по его уникальному идентификатору'
  }
] 