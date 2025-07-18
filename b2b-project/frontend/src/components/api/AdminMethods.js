export const adminMethods = [
  // Пользователи
  {
    id: 'admin-users',
    method: 'GET',
    path: '/admin/users',
    title: 'Список пользователей',
    description: 'Получение списка всех пользователей системы'
  },
  {
    id: 'admin-user-details',
    method: 'GET',
    path: '/admin/users/{id}',
    title: 'Детали пользователя',
    description: 'Получение подробной информации о пользователе'
  },
  {
    id: 'admin-recent-users',
    method: 'GET',
    path: '/admin/recent-users',
    title: 'Недавние пользователи',
    description: 'Получение списка недавно зарегистрированных пользователей'
  },
  {
    id: 'admin-stats',
    method: 'GET',
    path: '/admin/stats',
    title: 'Статистика системы',
    description: 'Получение общей статистики системы'
  },
  
  // Товары
  {
    id: 'admin-products',
    method: 'GET',
    path: '/admin/products',
    title: 'Список товаров',
    description: 'Получение списка всех товаров в системе'
  },
  {
    id: 'admin-products-search',
    method: 'POST',
    path: '/admin/products/search',
    title: 'Поиск товаров',
    description: 'Поиск товаров по различным критериям'
  },
  
  // Подкатегории
  {
    id: 'admin-subcategories',
    method: 'GET',
    path: '/admin/subcategories',
    title: 'Список подкатегорий',
    description: 'Получение списка всех подкатегорий'
  },
  
  // Склады
  {
    id: 'admin-warehouses',
    method: 'GET',
    path: '/admin/warehouses',
    title: 'Список складов',
    description: 'Получение списка всех складов'
  },
  {
    id: 'admin-warehouse-details',
    method: 'GET',
    path: '/admin/warehouses/{id}',
    title: 'Детали склада',
    description: 'Получение подробной информации о складе'
  },
  
  // Оприходования
  {
    id: 'admin-receipts',
    method: 'GET',
    path: '/admin/receipts',
    title: 'Список оприходований',
    description: 'Получение списка всех оприходований'
  },
  {
    id: 'admin-receipt-details',
    method: 'GET',
    path: '/admin/receipts/{id}',
    title: 'Детали оприходования',
    description: 'Получение подробной информации об оприходовании'
  },
  
  // Списания
  {
    id: 'admin-write-offs',
    method: 'GET',
    path: '/admin/write-offs',
    title: 'Список списаний',
    description: 'Получение списка всех списаний'
  },
  {
    id: 'admin-write-off-details',
    method: 'GET',
    path: '/admin/write-offs/{id}',
    title: 'Детали списания',
    description: 'Получение подробной информации о списании'
  },
  
  // Инвентаризации
  {
    id: 'admin-inventories',
    method: 'GET',
    path: '/admin/inventories',
    title: 'Список инвентаризаций',
    description: 'Получение списка всех инвентаризаций'
  },
  {
    id: 'admin-inventory-details',
    method: 'GET',
    path: '/admin/inventories/{id}',
    title: 'Детали инвентаризации',
    description: 'Получение подробной информации об инвентаризации'
  },
  
  // Остатки
  {
    id: 'admin-balances',
    method: 'GET',
    path: '/admin/balances',
    title: 'Список остатков',
    description: 'Получение списка всех остатков товаров'
  },
  {
    id: 'admin-balances-post',
    method: 'POST',
    path: '/admin/balances',
    title: 'Фильтр остатков',
    description: 'Фильтрация остатков товаров'
  },
  {
    id: 'admin-balance-movements',
    method: 'POST',
    path: '/admin/balances/movements',
    title: 'Движения остатков',
    description: 'Получение движений остатков товаров'
  },
  
  // Перемещения
  {
    id: 'admin-transfers',
    method: 'GET',
    path: '/admin/transfers',
    title: 'Список перемещений',
    description: 'Получение списка всех перемещений'
  },
  {
    id: 'admin-transfer-details',
    method: 'GET',
    path: '/admin/transfers/{id}',
    title: 'Детали перемещения',
    description: 'Получение подробной информации о перемещении'
  }
] 