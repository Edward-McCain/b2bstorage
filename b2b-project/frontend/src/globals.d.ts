// Глобальные декларации для Vue.js проекта

declare global {
  // Расширение глобального объекта Window
  interface Window {
    // Добавляем типы для localStorage и других браузерных API
  }

  // Типы для Vue refs
  interface VueRef<T> {
    value: T
  }

  // Типы для изображений
  interface ImageData {
    id: number
    image_url: string
    alt_text?: string
    product_id: number
    created_at: string
    updated_at: string
  }

  // Типы для API ответов
  interface ApiResponse<T = any> {
    ok: boolean
    data: T
    message?: string
    errors?: Record<string, string[]>
  }

  // Типы для категорий
  interface Category {
    category_id: number
    name: string
    name_ru?: string
    description?: string
  }

  // Типы для подкатегорий
  interface Subcategory {
    subcategory_id: number
    category_id: number
    name: string
    name_ru?: string
    description?: string
  }

  // Типы для продукта
  interface Product {
    id?: number
    name: string
    description?: string
    category?: string
    subcategory?: string
    country?: string
    supplier?: string
    article?: string
    code?: string
    external_code?: string
    unit?: string
    weight?: number | null
    volume?: number | null
    vat?: string
    min_stock?: number | null
    stock_type?: 'sum' | 'same' | 'by_warehouse'
    packing?: string
    accounting_type?: string
    traceable?: boolean
    marking?: string
    product_type?: string
    barcode_type?: string
    barcode?: string
    cash_register_tax?: string
    cash_register_type?: string
  }

  // Типы для опций селектов
  interface SelectOption {
    label: string
    value: string | number
    raw?: any
  }
}

// Экспорт пустого объекта для модуля
export {} 