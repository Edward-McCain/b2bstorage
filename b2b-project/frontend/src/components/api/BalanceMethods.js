export const balanceMethods = [
  {
    id: 'balances',
    method: 'GET',
    path: '/balances',
    title: 'Получение остатков',
    description: 'Получение списка всех остатков товаров'
  },
  {
    id: 'balance-create',
    method: 'POST',
    path: '/balances',
    title: 'Создание остатка',
    description: 'Создание нового остатка товара'
  },
  {
    id: 'balance-summary',
    method: 'GET',
    path: '/balances/summary',
    title: 'Сводка остатков',
    description: 'Получение сводной информации по остаткам'
  },
  {
    id: 'balance-summary-post',
    method: 'POST',
    path: '/balances/summary',
    title: 'Создание сводки остатков',
    description: 'Создание сводной информации по остаткам'
  },
  {
    id: 'balance-by-warehouse',
    method: 'GET',
    path: '/balances/by-warehouse',
    title: 'Остатки по складу',
    description: 'Получение остатков товаров по конкретному складу'
  },
  {
    id: 'balance-by-product',
    method: 'GET',
    path: '/balances/by-product',
    title: 'Остатки по товару',
    description: 'Получение остатков конкретного товара'
  },
  {
    id: 'balance-low-stock',
    method: 'GET',
    path: '/balances/low-stock',
    title: 'Товары с низким остатком',
    description: 'Получение товаров с низким количеством остатков'
  },
  {
    id: 'balance-out-of-stock',
    method: 'GET',
    path: '/balances/out-of-stock',
    title: 'Товары без остатка',
    description: 'Получение товаров без остатков'
  },
  {
    id: 'balance-movements',
    method: 'GET',
    path: '/balances/movements',
    title: 'Движения остатков',
    description: 'Получение истории движений остатков'
  },
  {
    id: 'balance-movements-post',
    method: 'POST',
    path: '/balances/movements',
    title: 'Создание движения остатков',
    description: 'Создание нового движения остатков'
  }
] 