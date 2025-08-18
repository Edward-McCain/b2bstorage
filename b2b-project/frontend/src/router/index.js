import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../components/HomePage.vue'
import AuthPage from '../components/AuthPage.vue'
import ProductsPage from '../components/products/ProductsPage.vue'
import PurchasesPage from '../components/purchases/PurchasesPage.vue'
import SalesPage from '../components/sales/SalesPage.vue'
import AccountSettingsPage from '../components/AccountSettingsPage.vue'
import ReceiptsPage from '../components/products/ReceiptsPage.vue'
import WriteOffsPage from '../components/products/WriteOffsPage.vue'
import InventoryPage from '../components/products/InventoryPage.vue'
import InternalOrdersPage from '../components/products/InternalOrdersPage.vue'
import TransfersPage from '../components/products/TransfersPage.vue'
import TransferCreatePage from '../components/products/TransferCreatePage.vue'
import PriceListsPage from '../components/products/PriceListsPage.vue'
import BalancesPage from '../components/products/BalancesPage.vue'
import TurnoversPage from '../components/products/TurnoversPage.vue'
import SerialNumbersPage from '../components/products/SerialNumbersPage.vue'
import MarkingCodesPage from '../components/products/MarkingCodesPage.vue'
import MarkingPage from '../components/products/MarkingPage.vue'
import ProductCreatePage from '../components/products/ProductCreatePage.vue'
import ProductEditPage from '../components/products/ProductEditPage.vue'
import ReceiptCreatePage from '../components/products/ReceiptCreatePage.vue'
import ReceiptViewPage from '../components/products/ReceiptViewPage.vue'
import ReceiptEditPage from '../components/products/ReceiptEditPage.vue'
import WriteOffCreatePage from '../components/products/WriteOffCreatePage.vue'
import WriteOffViewPage from '../components/products/WriteOffViewPage.vue'
import WriteOffEditPage from '../components/products/WriteOffEditPage.vue'
import InventoryCreatePage from '../components/products/InventoryCreatePage.vue'
import InventoryViewPage from '../components/products/InventoryViewPage.vue'
import InventoryEditPage from '../components/products/InventoryEditPage.vue'
import ProductsLogsPage from '../components/products/ProductsLogs.vue'
import NotificationsPage from '../components/NotificationsPage.vue'

// Warehouse pages
import WarehousesPage from '../components/warehouses/WarehousesPage.vue'
import WarehouseCreatePage from '../components/warehouses/WarehouseCreatePage.vue'
import WarehouseEditPage from '../components/warehouses/WarehouseEditPage.vue'
import WarehouseViewPage from '../components/warehouses/WarehouseViewPage.vue'

// Purchase pages
import SupplierOrdersPage from '../components/purchases/SupplierOrdersPage.vue'
import SupplierInvoicesPage from '../components/purchases/SupplierInvoicesPage.vue'
import ReceivedInvoicesPage from '../components/purchases/ReceivedInvoicesPage.vue'
import PurchaseReceiptsPage from '../components/purchases/ReceiptsPage.vue'
import SupplierReturnsPage from '../components/purchases/SupplierReturnsPage.vue'
import PurchaseManagementPage from '../components/purchases/PurchaseManagementPage.vue'

// Sales pages
import CustomerOrdersPage from '../components/sales/CustomerOrdersPage.vue'
import CustomerInvoicesPage from '../components/sales/CustomerInvoicesPage.vue'
import ShipmentsPage from '../components/sales/ShipmentsPage.vue'
import CommissionReportsPage from '../components/sales/CommissionReportsPage.vue'
import CustomerReturnsPage from '../components/sales/CustomerReturnsPage.vue'
import IssuedInvoicesPage from '../components/sales/IssuedInvoicesPage.vue'
import ProfitabilityPage from '../components/sales/ProfitabilityPage.vue'
import ConsignmentGoodsPage from '../components/sales/ConsignmentGoodsPage.vue'
import SalesFunnelPage from '../components/sales/SalesFunnelPage.vue'
import UnitEconomicsPage from '../components/sales/UnitEconomicsPage.vue'

import AnalyticsPage from '../components/AnalyticsPage.vue'
import CounterpartiesPage from '../components/CounterpartiesPage.vue'
import AnalyticsSalesPage from '../components/analytics/AnalyticsSalesPage.vue'
import AnalyticsMoneyPage from '../components/analytics/AnalyticsMoneyPage.vue'
import AnalyticsOverdueOrdersPage from '../components/analytics/AnalyticsOverdueOrdersPage.vue'
import AnalyticsOverdueInvoicesPage from '../components/analytics/AnalyticsOverdueInvoicesPage.vue'
import CounterpartiesBuyersPage from '../components/counterparties/CounterpartiesBuyersPage.vue'
import CounterpartiesSuppliersPage from '../components/counterparties/CounterpartiesSuppliersPage.vue'
import CounterpartiesGroupsPage from '../components/counterparties/CounterpartiesGroupsPage.vue'

// Admin pages
import AdminDashboardPage from '../components/admin/AdminDashboardPage.vue'
import AdminProductsPage from '../components/admin/products/AdminProductsPage.vue'
import AdminUsersPage from '../components/admin/AdminUsersPage.vue'
import AdminSettingsPage from '../components/admin/AdminSettingsPage.vue'

// Admin products pages
import AdminProductsMainPage from '../components/admin/products/AdminProductsPage.vue'
import AdminReceiptsPage from '../components/admin/products/AdminReceiptsPage.vue'
import AdminReceiptViewPage from '../components/admin/products/AdminReceiptViewPage.vue'
import AdminWriteOffsPage from '../components/admin/products/AdminWriteOffsPage.vue'
import AdminWriteOffViewPage from '../components/admin/products/AdminWriteOffViewPage.vue'
import AdminInventoryViewPage from '../components/admin/products/AdminInventoryViewPage.vue'
import AdminInventoryPage from '../components/admin/products/AdminInventoryPage.vue'
import AdminTransfersPage from '../components/admin/products/AdminTransfersPage.vue'
import AdminBalancesPage from '../components/admin/products/AdminBalancesPage.vue'
import AdminWarehousesPage from '../components/admin/products/AdminWarehousesPage.vue'
import ApiDocumentationPage from '../components/ApiDocumentationPage.vue'
import WebRTCTestPage from '../components/WebRTCTestPage.vue'
import UsersListPage from '../components/UsersListPage.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage
  },
  {
    path: '/auth',
    name: 'Auth',
    component: AuthPage
  },
  {
    path: '/products',
    name: 'Products',
    component: ProductsPage
  },
  {
    path: '/purchases',
    name: 'Purchases',
    component: PurchasesPage
  },
  {
    path: '/sales',
    name: 'Sales',
    component: SalesPage
  },
  {
    path: '/account-settings',
    name: 'AccountSettings',
    component: AccountSettingsPage
  },
  // Product routes
  {
    path: '/products/receipts',
    name: 'Receipts',
    component: ReceiptsPage
  },
  {
    path: '/products/write-offs',
    name: 'WriteOffs',
    component: WriteOffsPage
  },
  {
    path: '/products/inventory',
    name: 'Inventory',
    component: InventoryPage
  },
  {
    path: '/products/internal-orders',
    name: 'InternalOrders',
    component: InternalOrdersPage
  },
  {
    path: '/products/transfers',
    name: 'Transfers',
    component: TransfersPage
  },
  {
    path: '/products/transfers/create',
    name: 'TransferCreate',
    component: TransferCreatePage
  },
  {
    path: '/products/price-lists',
    name: 'PriceLists',
    component: PriceListsPage
  },
  {
    path: '/products/balances',
    name: 'Balances',
    component: BalancesPage
  },
  {
    path: '/products/logs',
    name: 'ProductsLogs',
    component: ProductsLogsPage
  },
  {
    path: '/notifications',
    name: 'Notifications',
    component: NotificationsPage
  },
  {
    path: '/products/turnovers',
    name: 'Turnovers',
    component: TurnoversPage
  },
  {
    path: '/products/serial-numbers',
    name: 'SerialNumbers',
    component: SerialNumbersPage
  },
  {
    path: '/products/marking-codes',
    name: 'MarkingCodes',
    component: MarkingCodesPage
  },
  {
    path: '/products/marking',
    name: 'Marking',
    component: MarkingPage
  },
  {
    path: '/products/create',
    name: 'ProductCreate',
    component: ProductCreatePage
  },
  {
    path: '/products/edit/:id',
    name: 'ProductEdit',
    component: ProductEditPage,
    props: true
  },
  {
    path: '/products/receipts/create',
    name: 'ReceiptCreate',
    component: ReceiptCreatePage
  },
  {
    path: '/products/receipts/:id',
    name: 'ReceiptView',
    component: ReceiptViewPage
  },
  {
    path: '/products/receipts/edit/:id',
    name: 'ReceiptEdit',
    component: ReceiptEditPage
  },
  // Write-off routes
  {
    path: '/products/write-offs/create',
    name: 'WriteOffCreate',
    component: WriteOffCreatePage
  },
  {
    path: '/products/write-offs/:id',
    name: 'WriteOffView',
    component: WriteOffViewPage
  },
  {
    path: '/products/write-offs/edit/:id',
    name: 'WriteOffEdit',
    component: WriteOffEditPage
  },
  // Inventory routes
  {
    path: '/products/inventory/create',
    name: 'InventoryCreate',
    component: InventoryCreatePage
  },
  {
    path: '/products/inventory/:id',
    name: 'InventoryView',
    component: InventoryViewPage
  },
  {
    path: '/products/inventory/edit/:id',
    name: 'InventoryEdit',
    component: InventoryEditPage
  },
  // Warehouse routes
  {
    path: '/warehouses',
    name: 'Warehouses',
    component: WarehousesPage
  },
  {
    path: '/warehouses/:id',
    name: 'WarehouseView',
    component: WarehouseViewPage
  },
  {
    path: '/warehouses/create',
    name: 'WarehouseCreate',
    component: WarehouseCreatePage
  },
  {
    path: '/warehouses/edit/:id',
    name: 'WarehouseEdit',
    component: WarehouseEditPage
  },
  // Purchase routes
  {
    path: '/purchases/supplier-orders',
    name: 'SupplierOrders',
    component: SupplierOrdersPage
  },
  {
    path: '/purchases/supplier-invoices',
    name: 'SupplierInvoices',
    component: SupplierInvoicesPage
  },
  {
    path: '/purchases/received-invoices',
    name: 'ReceivedInvoices',
    component: ReceivedInvoicesPage
  },
  {
    path: '/purchases/receipts',
    name: 'PurchaseReceipts',
    component: PurchaseReceiptsPage
  },
  {
    path: '/purchases/supplier-returns',
    name: 'SupplierReturns',
    component: SupplierReturnsPage
  },
  {
    path: '/purchases/purchase-management',
    name: 'PurchaseManagement',
    component: PurchaseManagementPage
  },
  // Sales routes
  {
    path: '/sales/customer-orders',
    name: 'CustomerOrders',
    component: CustomerOrdersPage
  },
  {
    path: '/sales/customer-invoices',
    name: 'CustomerInvoices',
    component: CustomerInvoicesPage
  },
  {
    path: '/sales/shipments',
    name: 'Shipments',
    component: ShipmentsPage
  },
  {
    path: '/sales/commission-reports',
    name: 'CommissionReports',
    component: CommissionReportsPage
  },
  {
    path: '/sales/customer-returns',
    name: 'CustomerReturns',
    component: CustomerReturnsPage
  },
  {
    path: '/sales/issued-invoices',
    name: 'IssuedInvoices',
    component: IssuedInvoicesPage
  },
  {
    path: '/sales/profitability',
    name: 'Profitability',
    component: ProfitabilityPage
  },
  {
    path: '/sales/consignment-goods',
    name: 'ConsignmentGoods',
    component: ConsignmentGoodsPage
  },
  {
    path: '/sales/sales-funnel',
    name: 'SalesFunnel',
    component: SalesFunnelPage
  },
  {
    path: '/sales/unit-economics',
    name: 'UnitEconomics',
    component: UnitEconomicsPage
  },
  {
    path: '/analytics',
    name: 'Analytics',
    component: AnalyticsPage
  },
  {
    path: '/counterparties',
    name: 'Counterparties',
    component: CounterpartiesPage
  },
  {
    path: '/analytics/sales',
    name: 'AnalyticsSales',
    component: AnalyticsSalesPage
  },
  {
    path: '/analytics/money',
    name: 'AnalyticsMoney',
    component: AnalyticsMoneyPage
  },
  {
    path: '/analytics/overdue-orders',
    name: 'AnalyticsOverdueOrders',
    component: AnalyticsOverdueOrdersPage
  },
  {
    path: '/analytics/overdue-invoices',
    name: 'AnalyticsOverdueInvoices',
    component: AnalyticsOverdueInvoicesPage
  },
  {
    path: '/counterparties/buyers',
    name: 'CounterpartiesBuyers',
    component: CounterpartiesBuyersPage
  },
  {
    path: '/counterparties/suppliers',
    name: 'CounterpartiesSuppliers',
    component: CounterpartiesSuppliersPage
  },
  {
    path: '/counterparties/groups',
    name: 'CounterpartiesGroups',
    component: CounterpartiesGroupsPage
  },
  // Admin routes
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboardPage
  },
  {
    path: '/admin/products',
    name: 'AdminProducts',
    component: AdminProductsPage
  },
  {
    path: '/admin/products/receipts',
    name: 'AdminReceipts',
    component: AdminReceiptsPage
  },
  {
    path: '/admin/receipts/:id',
    name: 'AdminReceiptView',
    component: AdminReceiptViewPage
  },
  {
    path: '/admin/products/write-offs',
    name: 'AdminWriteOffs',
    component: AdminWriteOffsPage
  },
  {
    path: '/admin/write-offs/:id',
    name: 'AdminWriteOffView',
    component: AdminWriteOffViewPage
  },
  {
    path: '/admin/inventories/:id',
    name: 'AdminInventoryView',
    component: AdminInventoryViewPage
  },
  {
    path: '/admin/products/inventory',
    name: 'AdminInventory',
    component: AdminInventoryPage
  },
  {
    path: '/admin/products/transfers',
    name: 'AdminTransfers',
    component: AdminTransfersPage
  },
  {
    path: '/admin/products/balances',
    name: 'AdminBalances',
    component: AdminBalancesPage
  },
  {
    path: '/admin/warehouses',
    name: 'AdminWarehouses',
    component: AdminWarehousesPage
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsersPage
  },
  {
    path: '/admin/settings',
    name: 'AdminSettings',
    component: AdminSettingsPage
  },
  {
    path: '/docs_api',
    name: 'ApiDocumentation',
    component: ApiDocumentationPage
  },
  {
    path: '/webrtc-test',
    name: 'WebRTCTest',
    component: WebRTCTestPage
  },
  {
    path: '/users-list',
    name: 'UsersList',
    component: UsersListPage
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Навигационные guards для защиты роутов
router.beforeEach((to, from, next) => {
  console.log('Router: Переход с', from.path, 'на', to.path)
  
  // Проверяем, что путь не undefined
  if (!to.path || to.path === '/undefined') {
    console.log('Router: Обнаружен undefined путь, перенаправляем на /')
    next('/')
    return
  }
  
  const isAuthenticated = localStorage.getItem('auth_token')
  
  // Список роутов, требующих авторизации
  const protectedRoutes = [
    '/products', '/purchases', '/sales', '/analytics', '/counterparties', '/account-settings', '/warehouses', '/admin', '/users-list'
  ]
  
  // Проверяем, требует ли роут авторизации
  const requiresAuth = protectedRoutes.some(route => to.path.startsWith(route))
  
  if (requiresAuth && !isAuthenticated) {
    // Если роут требует авторизации, но пользователь не авторизован
    next('/')
  } else if (to.path === '/auth' && isAuthenticated) {
    // Если пользователь авторизован и пытается зайти на страницу авторизации
    next('/')
  } else {
    next()
  }
})

// Обработка ошибок роутера
router.onError((error) => {
  console.error('Router Error:', error)
  if (error.message.includes('undefined')) {
    router.push('/')
  }
})

export default router 