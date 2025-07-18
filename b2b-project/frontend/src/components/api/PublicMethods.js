export const publicMethods = [
  {
    id: 'categories',
    method: 'GET',
    path: '/categories',
    title: 'Получение категорий',
    description: 'Получение списка всех категорий товаров'
  },
  {
    id: 'category-subcategories',
    method: 'GET',
    path: '/categories/{id}/subcategories',
    title: 'Подкатегории категории',
    description: 'Получение подкатегорий для конкретной категории'
  },
  {
    id: 'subcategories',
    method: 'GET',
    path: '/subcategories',
    title: 'Получение подкатегорий',
    description: 'Получение списка всех подкатегорий'
  }
] 