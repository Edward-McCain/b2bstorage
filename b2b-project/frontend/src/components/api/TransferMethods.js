export const transferMethods = [
  {
    id: 'transfers',
    path: '/transfers',
    method: 'GET',
    title: 'Получение списка перемещений',
    description: 'Возвращает список всех перемещений товаров между складами'
  },
  {
    id: 'transfer-filter',
    path: '/transfers/filter',
    method: 'POST',
    title: 'Фильтрация перемещений',
    description: 'Расширенная фильтрация перемещений с использованием сложных критериев'
  },
  {
    id: 'transfer-available-products',
    path: '/transfers/available-products',
    method: 'POST',
    title: 'Получение доступных товаров',
    description: 'Возвращает список товаров, доступных для перемещения с указанного склада'
  },
  {
    id: 'transfer-all-products',
    path: '/transfers/all-products',
    method: 'GET',
    title: 'Получение всех товаров',
    description: 'Возвращает полный список всех товаров с информацией о количестве на складах'
  },
  {
    id: 'transfer-create',
    path: '/transfers',
    method: 'POST',
    title: 'Создание перемещения',
    description: 'Создает новое перемещение товаров между складами'
  },
  {
    id: 'transfer-detail',
    path: '/transfers/{id}',
    method: 'GET',
    title: 'Получение информации о перемещении',
    description: 'Возвращает детальную информацию о конкретном перемещении'
  },
  {
    id: 'transfer-update',
    path: '/transfers/{id}',
    method: 'PUT',
    title: 'Обновление перемещения',
    description: 'Обновляет информацию о существующем перемещении'
  },
  {
    id: 'transfer-delete',
    path: '/transfers/{id}',
    method: 'DELETE',
    title: 'Удаление перемещения',
    description: 'Удаляет перемещение по его ID'
  },
  {
    id: 'transfer-confirm',
    path: '/transfers/{id}/confirm',
    method: 'POST',
    title: 'Подтверждение перемещения',
    description: 'Подтверждает перемещение товаров'
  },
  {
    id: 'transfer-complete',
    path: '/transfers/{id}/complete',
    method: 'POST',
    title: 'Завершение перемещения',
    description: 'Завершает перемещение товаров'
  },
  {
    id: 'transfer-cancel',
    path: '/transfers/{id}/cancel',
    method: 'POST',
    title: 'Отмена перемещения',
    description: 'Отменяет перемещение товаров'
  }
] 