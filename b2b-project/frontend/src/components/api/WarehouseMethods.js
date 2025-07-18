export const warehouseMethods = [
  {
    id: 'warehouses',
    path: '/warehouses',
    method: 'GET',
    title: 'Получение списка складов',
    description: 'Возвращает список всех складов пользователя с пагинацией'
  },
  {
    id: 'warehouse-detail',
    path: '/warehouses/{id}',
    method: 'GET',
    title: 'Получение информации о складе',
    description: 'Возвращает детальную информацию о конкретном складе'
  },
  {
    id: 'warehouse-create',
    path: '/warehouses',
    method: 'POST',
    title: 'Создание нового склада',
    description: 'Создает новый склад с указанными параметрами'
  },
  {
    id: 'warehouse-update',
    path: '/warehouses/{id}',
    method: 'PUT',
    title: 'Обновление информации о складе',
    description: 'Обновляет информацию о существующем складе'
  },
  {
    id: 'warehouse-delete',
    path: '/warehouses/{id}',
    method: 'DELETE',
    title: 'Удаление склада',
    description: 'Удаляет склад по его ID'
  }
] 