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
import PriceListsPage from '../components/products/PriceListsPage.vue'
import BalancesPage from '../components/products/BalancesPage.vue'
import TurnoversPage from '../components/products/TurnoversPage.vue'
import SerialNumbersPage from '../components/products/SerialNumbersPage.vue'
import MarkingCodesPage from '../components/products/MarkingCodesPage.vue'
import MarkingPage from '../components/products/MarkingPage.vue'
import ProductCreatePage from '../components/products/ProductCreatePage.vue'

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
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router 