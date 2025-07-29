import jsPDF from 'jspdf'
import 'jspdf-autotable'

/**
 * Генерирует PDF документ из HTML элемента
 * @param {HTMLElement} element - HTML элемент для конвертации
 * @param {string} filename - Имя файла для скачивания
 * @param {string} title - Заголовок документа
 */
export function generatePDF(element, filename, title) {
  const pdf = new jsPDF('p', 'mm', 'a4')
  
  // Добавляем заголовок
  pdf.setFontSize(18)
  pdf.text(title, 20, 20)
  
  // Добавляем дату
  pdf.setFontSize(10)
  pdf.text(`Дата создания: ${new Date().toLocaleString('ru-RU')}`, 20, 30)
  
  // Создаем временный элемент для очистки стилей
  const tempDiv = document.createElement('div')
  tempDiv.innerHTML = element.innerHTML
  
  // Удаляем все кнопки и иконки
  const buttons = tempDiv.querySelectorAll('button, .print-hide')
  buttons.forEach(button => button.remove())
  
  // Удаляем иконки Lucide
  const icons = tempDiv.querySelectorAll('svg')
  icons.forEach(icon => icon.remove())
  
  // Очищаем проблемные CSS стили
  const allElements = tempDiv.querySelectorAll('*')
  allElements.forEach(el => {
    // Удаляем проблемные CSS свойства
    if (el.style) {
      el.style.removeProperty('color')
      el.style.removeProperty('background-color')
      el.style.removeProperty('border-color')
      el.style.removeProperty('outline-color')
    }
  })
  
  // Добавляем базовые стили для PDF
  const style = document.createElement('style')
  style.textContent = `
    * {
      color: #000 !important;
      background-color: transparent !important;
      border-color: #ccc !important;
    }
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 10px 0;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f5f5f5 !important;
      font-weight: bold;
    }
  `
  tempDiv.appendChild(style)
  
  // Добавляем контент в PDF
  pdf.html(tempDiv, {
    callback: function(pdf) {
      pdf.save(filename)
    },
    x: 10,
    y: 40,
    width: 190
  })
}

/**
 * Генерирует PDF документ из HTML элемента (альтернативная версия)
 * @param {HTMLElement} element - HTML элемент для конвертации
 * @param {string} filename - Имя файла для скачивания
 * @param {string} title - Заголовок документа
 */
export function generatePDFSimple(element, filename, title) {
  // Создаем PDF с поддержкой кириллицы
  const pdf = new jsPDF('p', 'mm', 'a4')
  
  // Используем встроенный шрифт с поддержкой кириллицы
  pdf.setFont('helvetica')
  
  // Добавляем заголовок
  pdf.setFontSize(18)
  pdf.text(title, 20, 20)
  
  // Добавляем дату
  pdf.setFontSize(10)
  pdf.text(`Дата создания: ${new Date().toLocaleString('ru-RU')}`, 20, 30)
  
  // Извлекаем текстовое содержимое
  const textContent = extractTextContent(element)
  
  // Разбиваем на строки
  const lines = textContent.split('\n').filter(line => line.trim())
  
  let yPosition = 45
  pdf.setFontSize(10)
  
  lines.forEach(line => {
    if (yPosition > 270) {
      pdf.addPage()
      yPosition = 20
    }
    pdf.text(line, 20, yPosition)
    yPosition += 5
  })
  
  pdf.save(filename)
}

/**
 * Генерирует PDF документ из HTML элемента с поддержкой кириллицы
 * @param {HTMLElement} element - HTML элемент для конвертации
 * @param {string} filename - Имя файла для скачивания
 * @param {string} title - Заголовок документа
 */
export async function generatePDFWithCanvas(element, filename, title) {
  try {
    // Импортируем html2canvas динамически
    const html2canvas = (await import('html2canvas')).default
    
    // Создаем временный элемент для конвертации
    const tempDiv = document.createElement('div')
    tempDiv.innerHTML = element.innerHTML
    
    // Удаляем кнопки и иконки
    const buttons = tempDiv.querySelectorAll('button, .print-hide, svg')
    buttons.forEach(button => button.remove())
    
    // Очищаем проблемные CSS стили
    const allElements = tempDiv.querySelectorAll('*')
    allElements.forEach(el => {
      if (el.style) {
        el.style.removeProperty('color')
        el.style.removeProperty('background-color')
        el.style.removeProperty('border-color')
        el.style.removeProperty('outline-color')
      }
    })
    
    // Добавляем базовые стили
    tempDiv.style.fontFamily = 'Arial, sans-serif'
    tempDiv.style.color = '#000'
    tempDiv.style.backgroundColor = '#fff'
    tempDiv.style.padding = '20px'
    tempDiv.style.lineHeight = '1.6'
    
    // Добавляем заголовок
    const header = document.createElement('h1')
    header.textContent = title
    header.style.fontSize = '18px'
    header.style.fontWeight = 'bold'
    header.style.marginBottom = '10px'
    tempDiv.insertBefore(header, tempDiv.firstChild)
    
    // Добавляем дату
    const dateDiv = document.createElement('div')
    dateDiv.textContent = `Дата создания: ${new Date().toLocaleString('ru-RU')}`
    dateDiv.style.fontSize = '12px'
    dateDiv.style.marginBottom = '20px'
    dateDiv.style.color = '#666'
    tempDiv.insertBefore(dateDiv, tempDiv.firstChild.nextSibling)
    
    // Добавляем элемент в DOM временно
    document.body.appendChild(tempDiv)
    tempDiv.style.position = 'absolute'
    tempDiv.style.left = '-9999px'
    tempDiv.style.top = '0'
    
    // Конвертируем в canvas
    const canvas = await html2canvas(tempDiv, {
      scale: 2,
      useCORS: true,
      allowTaint: true,
      backgroundColor: '#ffffff'
    })
    
    // Удаляем временный элемент
    document.body.removeChild(tempDiv)
    
    // Создаем PDF
    const pdf = new jsPDF('p', 'mm', 'a4')
    const imgData = canvas.toDataURL('image/png')
    
    // Вычисляем размеры для PDF
    const pdfWidth = pdf.internal.pageSize.getWidth()
    const pdfHeight = pdf.internal.pageSize.getHeight()
    const imgWidth = canvas.width
    const imgHeight = canvas.height
    const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight)
    
    const imgX = (pdfWidth - imgWidth * ratio) / 2
    const imgY = 10
    
    pdf.addImage(imgData, 'PNG', imgX, imgY, imgWidth * ratio, imgHeight * ratio)
    pdf.save(filename)
    
  } catch (error) {
    console.error('Ошибка генерации PDF:', error)
    // Fallback к простой текстовой версии
    generatePDFSimple(element, filename, title)
  }
}

/**
 * Генерирует простой PDF документ с правильной кодировкой
 * @param {HTMLElement} element - HTML элемент для конвертации
 * @param {string} filename - Имя файла для скачивания
 * @param {string} title - Заголовок документа
 */
export function generateSimplePDF(element, filename, title) {
  // Создаем PDF
  const pdf = new jsPDF('p', 'mm', 'a4')
  
  // Устанавливаем шрифт
  pdf.setFont('helvetica')
  
  // Добавляем заголовок
  pdf.setFontSize(16)
  pdf.text(title, 20, 20)
  
  // Добавляем дату
  pdf.setFontSize(10)
  pdf.text(`Дата создания: ${new Date().toLocaleString('ru-RU')}`, 20, 30)
  
  // Извлекаем данные из элемента
  const data = extractStructuredData(element)
  
  let yPosition = 45
  pdf.setFontSize(10)
  
  // Добавляем основную информацию
  if (data.basicInfo) {
    pdf.setFontSize(12)
    pdf.text('Основная информация:', 20, yPosition)
    yPosition += 10
    
    pdf.setFontSize(10)
    Object.entries(data.basicInfo).forEach(([key, value]) => {
      if (yPosition > 270) {
        pdf.addPage()
        yPosition = 20
      }
      pdf.text(`${key}: ${value}`, 20, yPosition)
      yPosition += 6
    })
    yPosition += 5
  }
  
  // Добавляем позиции если есть
  if (data.positions && data.positions.length > 0) {
    pdf.setFontSize(12)
    pdf.text('Позиции:', 20, yPosition)
    yPosition += 10
    
    pdf.setFontSize(8)
    data.positions.forEach((position, index) => {
      if (yPosition > 270) {
        pdf.addPage()
        yPosition = 20
      }
      pdf.text(`${index + 1}. ${position.name} - ${position.quantity} шт.`, 20, yPosition)
      yPosition += 5
    })
  }
  
  pdf.save(filename)
}

/**
 * Генерирует PDF для оприходования с правильной поддержкой кириллицы
 * @param {Object} receiptData - Данные оприходования
 * @param {string} filename - Имя файла для скачивания
 * @param {string} userCurrency - Валюта пользователя
 */
export function generateReceiptPDFWithCanvas(receiptData, filename, userCurrency = 'UZS') {
  // Создаем временный canvas для рендеринга текста
  const canvas = document.createElement('canvas')
  const ctx = canvas.getContext('2d')
  
  // Устанавливаем размер canvas
  canvas.width = 800
  canvas.height = 1200
  
  // Устанавливаем стили для правильного рендеринга
  ctx.fillStyle = '#ffffff'
  ctx.fillRect(0, 0, canvas.width, canvas.height)
  
  ctx.fillStyle = '#000000'
  ctx.font = '16px Arial'
  
  let yPosition = 30
  
  // Заголовок
  ctx.font = 'bold 20px Arial'
  ctx.fillText(`Оприходование №${receiptData.number}`, 40, yPosition)
  yPosition += 30
  
  // Дата создания
  ctx.font = '12px Arial'
  ctx.fillText(`Дата создания: ${new Date().toLocaleString('ru-RU')}`, 40, yPosition)
  yPosition += 30
  
  // Основная информация
  ctx.font = 'bold 14px Arial'
  ctx.fillText('Основная информация:', 40, yPosition)
  yPosition += 20
  
  ctx.font = '12px Arial'
  
  // Функция для отображения строки "тип: значение"
  const addInfoRow = (label, value) => {
    if (value) {
      ctx.fillText(`${label}: ${value}`, 40, yPosition)
      yPosition += 18
    }
  }
  
  // Номер
  addInfoRow('Номер', receiptData.number)
  
  // Дата
  if (receiptData.date) {
    const date = new Date(receiptData.date).toLocaleDateString('ru-RU')
    addInfoRow('Дата', date)
  }
  
  // Организация
  addInfoRow('Организация', receiptData.organization)
  
  // Склад
  addInfoRow('Склад', receiptData.warehouse_name)
  
  // Статус
  if (receiptData.status) {
    const statusText = receiptData.status === 'posted' ? 'Проведено' : 'Черновик'
    addInfoRow('Статус', statusText)
  }
  
  // Создано
  addInfoRow('Создано', receiptData.created_by)
  
  // Валюта
  addInfoRow('Валюта', userCurrency)
  
  yPosition += 20
  
  // Позиции в виде таблицы
  if (receiptData.positions && receiptData.positions.length > 0) {
    ctx.font = 'bold 14px Arial'
    ctx.fillText('Позиции:', 40, yPosition)
    yPosition += 20
    
    // Заголовки таблицы
    ctx.font = 'bold 11px Arial'
    ctx.fillText('№', 40, yPosition)
    ctx.fillText('Наименование', 80, yPosition)
    ctx.fillText('Количество', 350, yPosition)
    ctx.fillText('Цена', 450, yPosition)
    ctx.fillText('Сумма', 550, yPosition)
    yPosition += 15
    
    // Разделительная линия
    ctx.strokeStyle = '#000000'
    ctx.lineWidth = 1
    ctx.beginPath()
    ctx.moveTo(40, yPosition)
    ctx.lineTo(700, yPosition)
    ctx.stroke()
    yPosition += 10
    
    ctx.font = '10px Arial'
    let totalAmount = 0
    
    receiptData.positions.forEach((position, index) => {
      if (yPosition > 1100) {
        // Если достигли конца страницы, начинаем новую
        ctx.fillStyle = '#ffffff'
        ctx.fillRect(0, 0, canvas.width, canvas.height)
        ctx.fillStyle = '#000000'
        yPosition = 30
      }
      
      const productName = position.product_name || position.name || 'Товар'
      const quantity = position.quantity || 0
      const price = position.price || 0
      const amount = quantity * price
      totalAmount += amount
      
      // Номер
      ctx.fillText(`${index + 1}`, 40, yPosition)
      
      // Наименование (с переносом строки если длинное)
      const nameLines = splitTextToFit(productName, 250)
      nameLines.forEach((line, lineIndex) => {
        ctx.fillText(line, 80, yPosition + (lineIndex * 12))
      })
      
      // Количество
      ctx.fillText(quantity.toString(), 350, yPosition)
      
      // Цена
      ctx.fillText(`${price} ${userCurrency}`, 450, yPosition)
      
      // Сумма
      ctx.fillText(`${amount} ${userCurrency}`, 550, yPosition)
      
      yPosition += Math.max(15, nameLines.length * 12)
    })
    
    // Итоговая линия
    ctx.strokeStyle = '#000000'
    ctx.lineWidth = 1
    ctx.beginPath()
    ctx.moveTo(40, yPosition)
    ctx.lineTo(700, yPosition)
    ctx.stroke()
    yPosition += 15
    
    // Итоговая сумма
    ctx.font = 'bold 12px Arial'
    ctx.fillText(`Итого: ${totalAmount} ${userCurrency}`, 550, yPosition)
  }
  
  // Конвертируем canvas в PDF
  const pdf = new jsPDF('p', 'mm', 'a4')
  const imgData = canvas.toDataURL('image/png')
  
  // Вычисляем размеры для PDF
  const pdfWidth = pdf.internal.pageSize.getWidth()
  const pdfHeight = pdf.internal.pageSize.getHeight()
  const imgWidth = canvas.width
  const imgHeight = canvas.height
  const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight)
  
  const imgX = (pdfWidth - imgWidth * ratio) / 2
  const imgY = 10
  
  pdf.addImage(imgData, 'PNG', imgX, imgY, imgWidth * ratio, imgHeight * ratio)
  pdf.save(filename)
}

/**
 * Генерирует PDF для списания с правильной поддержкой кириллицы
 * @param {Object} writeOffData - Данные списания
 * @param {string} filename - Имя файла для скачивания
 * @param {string} userCurrency - Валюта пользователя
 */
export function generateWriteOffPDFWithCanvas(writeOffData, filename, userCurrency = 'UZS') {
  // Создаем временный canvas для рендеринга текста
  const canvas = document.createElement('canvas')
  const ctx = canvas.getContext('2d')
  
  // Устанавливаем размер canvas
  canvas.width = 800
  canvas.height = 1200
  
  // Устанавливаем стили для правильного рендеринга
  ctx.fillStyle = '#ffffff'
  ctx.fillRect(0, 0, canvas.width, canvas.height)
  
  ctx.fillStyle = '#000000'
  ctx.font = '16px Arial'
  
  let yPosition = 30
  
  // Заголовок
  ctx.font = 'bold 20px Arial'
  ctx.fillText(`Списание №${writeOffData.number}`, 40, yPosition)
  yPosition += 30
  
  // Дата создания
  ctx.font = '12px Arial'
  ctx.fillText(`Дата создания: ${new Date().toLocaleString('ru-RU')}`, 40, yPosition)
  yPosition += 30
  
  // Основная информация
  ctx.font = 'bold 14px Arial'
  ctx.fillText('Основная информация:', 40, yPosition)
  yPosition += 20
  
  ctx.font = '12px Arial'
  
  // Функция для отображения строки "тип: значение"
  const addInfoRow = (label, value) => {
    if (value) {
      ctx.fillText(`${label}: ${value}`, 40, yPosition)
      yPosition += 18
    }
  }
  
  // Номер
  addInfoRow('Номер', writeOffData.number)
  
  // Дата
  if (writeOffData.date) {
    const date = new Date(writeOffData.date).toLocaleDateString('ru-RU')
    addInfoRow('Дата', date)
  }
  
  // Организация
  addInfoRow('Организация', writeOffData.organization)
  
  // Склад
  addInfoRow('Склад', writeOffData.warehouse_name)
  
  // Статус
  if (writeOffData.status) {
    const statusText = writeOffData.status === 'posted' ? 'Проведено' : 'Черновик'
    addInfoRow('Статус', statusText)
  }
  
  // Создано
  addInfoRow('Создано', writeOffData.created_by)
  
  // Валюта
  addInfoRow('Валюта', userCurrency)
  
  yPosition += 20
  
  // Позиции в виде таблицы
  if (writeOffData.positions && writeOffData.positions.length > 0) {
    ctx.font = 'bold 14px Arial'
    ctx.fillText('Позиции:', 40, yPosition)
    yPosition += 20
    
    // Заголовки таблицы
    ctx.font = 'bold 11px Arial'
    ctx.fillText('№', 40, yPosition)
    ctx.fillText('Наименование', 80, yPosition)
    ctx.fillText('Количество', 350, yPosition)
    ctx.fillText('Цена', 450, yPosition)
    ctx.fillText('Сумма', 550, yPosition)
    yPosition += 15
    
    // Разделительная линия
    ctx.strokeStyle = '#000000'
    ctx.lineWidth = 1
    ctx.beginPath()
    ctx.moveTo(40, yPosition)
    ctx.lineTo(700, yPosition)
    ctx.stroke()
    yPosition += 10
    
    ctx.font = '10px Arial'
    let totalAmount = 0
    
    writeOffData.positions.forEach((position, index) => {
      if (yPosition > 1100) {
        // Если достигли конца страницы, начинаем новую
        ctx.fillStyle = '#ffffff'
        ctx.fillRect(0, 0, canvas.width, canvas.height)
        ctx.fillStyle = '#000000'
        yPosition = 30
      }
      
      const productName = position.product_name || position.name || 'Товар'
      const quantity = position.quantity || 0
      const price = position.price || 0
      const amount = quantity * price
      totalAmount += amount
      
      // Номер
      ctx.fillText(`${index + 1}`, 40, yPosition)
      
      // Наименование (с переносом строки если длинное)
      const nameLines = splitTextToFit(productName, 250)
      nameLines.forEach((line, lineIndex) => {
        ctx.fillText(line, 80, yPosition + (lineIndex * 12))
      })
      
      // Количество
      ctx.fillText(quantity.toString(), 350, yPosition)
      
      // Цена
      ctx.fillText(`${price} ${userCurrency}`, 450, yPosition)
      
      // Сумма
      ctx.fillText(`${amount} ${userCurrency}`, 550, yPosition)
      
      yPosition += Math.max(15, nameLines.length * 12)
    })
    
    // Итоговая линия
    ctx.strokeStyle = '#000000'
    ctx.lineWidth = 1
    ctx.beginPath()
    ctx.moveTo(40, yPosition)
    ctx.lineTo(700, yPosition)
    ctx.stroke()
    yPosition += 15
    
    // Итоговая сумма
    ctx.font = 'bold 12px Arial'
    ctx.fillText(`Итого: ${totalAmount} ${userCurrency}`, 550, yPosition)
  }
  
  // Конвертируем canvas в PDF
  const pdf = new jsPDF('p', 'mm', 'a4')
  const imgData = canvas.toDataURL('image/png')
  
  // Вычисляем размеры для PDF
  const pdfWidth = pdf.internal.pageSize.getWidth()
  const pdfHeight = pdf.internal.pageSize.getHeight()
  const imgWidth = canvas.width
  const imgHeight = canvas.height
  const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight)
  
  const imgX = (pdfWidth - imgWidth * ratio) / 2
  const imgY = 10
  
  pdf.addImage(imgData, 'PNG', imgX, imgY, imgWidth * ratio, imgHeight * ratio)
  pdf.save(filename)
}

/**
 * Генерирует PDF для инвентаризации с правильной поддержкой кириллицы
 * @param {Object} inventoryData - Данные инвентаризации
 * @param {string} filename - Имя файла для скачивания
 * @param {string} userCurrency - Валюта пользователя
 */
export function generateInventoryPDFWithCanvas(inventoryData, filename, userCurrency = 'UZS') {
  // Создаем временный canvas для рендеринга текста
  const canvas = document.createElement('canvas')
  const ctx = canvas.getContext('2d')
  
  // Устанавливаем размер canvas
  canvas.width = 800
  canvas.height = 1200
  
  // Устанавливаем стили для правильного рендеринга
  ctx.fillStyle = '#ffffff'
  ctx.fillRect(0, 0, canvas.width, canvas.height)
  
  ctx.fillStyle = '#000000'
  ctx.font = '16px Arial'
  
  let yPosition = 30
  
  // Заголовок
  ctx.font = 'bold 20px Arial'
  ctx.fillText(`Инвентаризация: ${inventoryData.name}`, 40, yPosition)
  yPosition += 30
  
  // Дата создания
  ctx.font = '12px Arial'
  ctx.fillText(`Дата создания: ${new Date().toLocaleString('ru-RU')}`, 40, yPosition)
  yPosition += 30
  
  // Основная информация
  ctx.font = 'bold 14px Arial'
  ctx.fillText('Основная информация:', 40, yPosition)
  yPosition += 20
  
  ctx.font = '12px Arial'
  
  // Функция для отображения строки "тип: значение"
  const addInfoRow = (label, value) => {
    if (value) {
      ctx.fillText(`${label}: ${value}`, 40, yPosition)
      yPosition += 18
    }
  }
  
  // Название
  addInfoRow('Название', inventoryData.name)
  
  // Склад
  addInfoRow('Склад', inventoryData.warehouse_name)
  
  // Статус
  if (inventoryData.status) {
    const statusText = getInventoryStatusText(inventoryData.status)
    addInfoRow('Статус', statusText)
  }
  
  // Создано
  addInfoRow('Создано', inventoryData.created_by)
  
  // Комментарий
  addInfoRow('Комментарий', inventoryData.comment)
  
  // Валюта
  addInfoRow('Валюта', userCurrency)
  
  yPosition += 20
  
  // Позиции в виде таблицы
  if (inventoryData.items && inventoryData.items.length > 0) {
    ctx.font = 'bold 14px Arial'
    ctx.fillText('Позиции:', 40, yPosition)
    yPosition += 20
    
    // Заголовки таблицы
    ctx.font = 'bold 11px Arial'
    ctx.fillText('№', 40, yPosition)
    ctx.fillText('Наименование', 80, yPosition)
    ctx.fillText('Фактическое', 350, yPosition)
    ctx.fillText('Системное', 450, yPosition)
    ctx.fillText('Разница', 550, yPosition)
    yPosition += 15
    
    // Разделительная линия
    ctx.strokeStyle = '#000000'
    ctx.lineWidth = 1
    ctx.beginPath()
    ctx.moveTo(40, yPosition)
    ctx.lineTo(700, yPosition)
    ctx.stroke()
    yPosition += 10
    
    ctx.font = '10px Arial'
    
    inventoryData.items.forEach((item, index) => {
      if (yPosition > 1100) {
        // Если достигли конца страницы, начинаем новую
        ctx.fillStyle = '#ffffff'
        ctx.fillRect(0, 0, canvas.width, canvas.height)
        ctx.fillStyle = '#000000'
        yPosition = 30
      }
      
      const productName = item.product_name || item.name || 'Товар'
      const actualQuantity = item.actual_quantity || 0
      const systemQuantity = item.system_quantity || 0
      const difference = actualQuantity - systemQuantity
      
      // Номер
      ctx.fillText(`${index + 1}`, 40, yPosition)
      
      // Наименование (с переносом строки если длинное)
      const nameLines = splitTextToFit(productName, 250)
      nameLines.forEach((line, lineIndex) => {
        ctx.fillText(line, 80, yPosition + (lineIndex * 12))
      })
      
      // Фактическое количество
      ctx.fillText(actualQuantity.toString(), 350, yPosition)
      
      // Системное количество
      ctx.fillText(systemQuantity.toString(), 450, yPosition)
      
      // Разница
      ctx.fillText(difference.toString(), 550, yPosition)
      
      yPosition += Math.max(15, nameLines.length * 12)
    })
  }
  
  // Конвертируем canvas в PDF
  const pdf = new jsPDF('p', 'mm', 'a4')
  const imgData = canvas.toDataURL('image/png')
  
  // Вычисляем размеры для PDF
  const pdfWidth = pdf.internal.pageSize.getWidth()
  const pdfHeight = pdf.internal.pageSize.getHeight()
  const imgWidth = canvas.width
  const imgHeight = canvas.height
  const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight)
  
  const imgX = (pdfWidth - imgWidth * ratio) / 2
  const imgY = 10
  
  pdf.addImage(imgData, 'PNG', imgX, imgY, imgWidth * ratio, imgHeight * ratio)
  pdf.save(filename)
}

/**
 * Получает текст статуса инвентаризации
 * @param {string} status - Статус
 * @returns {string} Текст статуса
 */
function getInventoryStatusText(status) {
  const statusMap = {
    'draft': 'Черновик',
    'in_progress': 'В процессе',
    'completed': 'Завершена',
    'cancelled': 'Отменена'
  }
  return statusMap[status] || status
}

/**
 * Разбивает текст на строки, чтобы поместиться в заданную ширину
 * @param {string} text - Текст для разбивки
 * @param {number} maxWidth - Максимальная ширина в пикселях
 * @returns {string[]} Массив строк
 */
function splitTextToFit(text, maxWidth) {
  const ctx = document.createElement('canvas').getContext('2d')
  ctx.font = '10px Arial'
  
  const words = text.split(' ')
  const lines = []
  let currentLine = ''
  
  words.forEach(word => {
    const testLine = currentLine + (currentLine ? ' ' : '') + word
    const metrics = ctx.measureText(testLine)
    
    if (metrics.width > maxWidth && currentLine) {
      lines.push(currentLine)
      currentLine = word
    } else {
      currentLine = testLine
    }
  })
  
  if (currentLine) {
    lines.push(currentLine)
  }
  
  return lines
}

/**
 * Печатает оприходование с валютой
 * @param {Object} receiptData - Данные оприходования
 * @param {string} userCurrency - Валюта пользователя
 */
export function printReceipt(receiptData, userCurrency = 'UZS') {
  // Создаем временное окно для печати
  const printWindow = window.open('', '_blank', 'width=800,height=600')
  
  // Создаем HTML контент
  const html = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Печать оприходования</title>
      <meta charset="UTF-8">
      <style>
        body {
          font-family: Arial, sans-serif;
          margin: 20px;
          line-height: 1.6;
          color: #000 !important;
        }
        * {
          color: #000 !important;
          background-color: transparent !important;
        }
        .header {
          font-size: 18px;
          font-weight: bold;
          margin-bottom: 20px;
        }
        .section {
          margin-bottom: 15px;
        }
        .section-title {
          font-weight: bold;
          margin-bottom: 10px;
        }
        .info-row {
          margin-bottom: 8px;
        }
        .positions {
          margin-top: 20px;
        }
        .positions-table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 10px;
        }
        .positions-table th,
        .positions-table td {
          border: 1px solid #000;
          padding: 8px;
          text-align: left;
        }
        .positions-table th {
          background-color: #f5f5f5 !important;
          font-weight: bold;
        }
        .total {
          font-weight: bold;
          margin-top: 20px;
          font-size: 14px;
          text-align: right;
        }
        @media print {
          body { margin: 0; }
          .no-print { display: none; }
        }
      </style>
    </head>
    <body>
      <div class="header">
        Оприходование №${receiptData.number}
      </div>
      
      <div class="section">
        <div class="section-title">Основная информация:</div>
        <div class="info-row">Номер: ${receiptData.number}</div>
        <div class="info-row">Дата: ${new Date(receiptData.date).toLocaleDateString('ru-RU')}</div>
        <div class="info-row">Организация: ${receiptData.organization}</div>
        <div class="info-row">Склад: ${receiptData.warehouse_name}</div>
        <div class="info-row">Статус: ${receiptData.status === 'posted' ? 'Проведено' : 'Черновик'}</div>
        <div class="info-row">Создано: ${receiptData.created_by}</div>
        <div class="info-row">Валюта: ${userCurrency}</div>
      </div>
      
      <div class="positions">
        <div class="section-title">Позиции:</div>
        <table class="positions-table">
          <thead>
            <tr>
              <th>№</th>
              <th>Наименование</th>
              <th>Количество</th>
              <th>Цена</th>
              <th>Сумма</th>
            </tr>
          </thead>
          <tbody>
            ${receiptData.positions ? receiptData.positions.map((position, index) => {
              const productName = position.product_name || position.name || 'Товар'
              const quantity = position.quantity || 0
              const price = position.price || 0
              const amount = quantity * price
              return `
                <tr>
                  <td>${index + 1}</td>
                  <td>${productName}</td>
                  <td>${quantity}</td>
                  <td>${price} ${userCurrency}</td>
                  <td>${amount} ${userCurrency}</td>
                </tr>
              `
            }).join('') : ''}
          </tbody>
        </table>
      </div>
      
      <div class="total">
        Итого: ${receiptData.positions ? receiptData.positions.reduce((total, position) => {
          const quantity = position.quantity || 0
          const price = position.price || 0
          return total + (quantity * price)
        }, 0) : 0} ${userCurrency}
      </div>
    </body>
    </html>
  `
  
  // Записываем HTML в новое окно
  printWindow.document.write(html)
  printWindow.document.close()
  
  // Ждем загрузки и печатаем
  printWindow.onload = function() {
    setTimeout(() => {
      printWindow.print()
      setTimeout(() => {
        printWindow.close()
      }, 1000)
    }, 500)
  }
}

/**
 * Извлекает структурированные данные из HTML элемента
 * @param {HTMLElement} element - HTML элемент
 * @returns {Object} Структурированные данные
 */
function extractStructuredData(element) {
  const data = {
    basicInfo: {},
    positions: []
  }
  
  // Создаем временный элемент
  const tempDiv = element.cloneNode(true)
  
  // Удаляем кнопки и иконки
  const buttons = tempDiv.querySelectorAll('button, .print-hide, svg')
  buttons.forEach(button => button.remove())
  
  // Извлекаем основную информацию из различных структур
  // 1. Из dl/dt/dd элементов
  const dlElements = tempDiv.querySelectorAll('dl')
  dlElements.forEach(dl => {
    const dts = dl.querySelectorAll('dt')
    const dds = dl.querySelectorAll('dd')
    dts.forEach((dt, index) => {
      if (dds[index]) {
        const label = dt.textContent?.trim() || ''
        const value = dds[index].textContent?.trim() || ''
        if (label && value) {
          data.basicInfo[label] = value
        }
      }
    })
  })
  
  // 2. Из div с классами
  const infoDivs = tempDiv.querySelectorAll('div')
  infoDivs.forEach(div => {
    const text = div.textContent?.trim() || ''
    if (text.includes(':') && !div.querySelector('*')) {
      const parts = text.split(':')
      if (parts.length >= 2) {
        const label = parts[0].trim()
        const value = parts.slice(1).join(':').trim()
        if (label && value) {
          data.basicInfo[label] = value
        }
      }
    }
  })
  
  // 3. Из таблиц
  const tables = tempDiv.querySelectorAll('table')
  tables.forEach(table => {
    const rows = table.querySelectorAll('tr')
    rows.forEach((row, rowIndex) => {
      const cells = row.querySelectorAll('td, th')
      if (cells.length >= 2) {
        // Если это заголовок таблицы
        if (rowIndex === 0) {
          // Пропускаем заголовки
          return
        }
        
        // Если это данные позиций
        const name = cells[0]?.textContent?.trim() || ''
        const quantity = cells[1]?.textContent?.trim() || ''
        const price = cells[2]?.textContent?.trim() || ''
        
        if (name) {
          data.positions.push({
            name: name,
            quantity: quantity,
            price: price
          })
        }
      }
    })
  })
  
  // 4. Из списков
  const lists = tempDiv.querySelectorAll('ul, ol')
  lists.forEach(list => {
    const items = list.querySelectorAll('li')
    items.forEach(item => {
      const text = item.textContent?.trim() || ''
      if (text) {
        data.positions.push({
          name: text,
          quantity: '',
          price: ''
        })
      }
    })
  })
  
  return data
}

/**
 * Извлекает текстовое содержимое из HTML элемента
 * @param {HTMLElement} element - HTML элемент
 * @returns {string} Текстовое содержимое
 */
function extractTextContent(element) {
  // Создаем временный элемент
  const tempDiv = element.cloneNode(true)
  
  // Удаляем кнопки и иконки
  const buttons = tempDiv.querySelectorAll('button, .print-hide, svg')
  buttons.forEach(button => button.remove())
  
  // Извлекаем текст
  return tempDiv.textContent || tempDiv.innerText || ''
}

/**
 * Печатает содержимое HTML элемента
 * @param {HTMLElement} element - HTML элемент для печати
 */
export function printElement(element) {
  // Создаем временное окно для печати
  const printWindow = window.open('', '_blank', 'width=800,height=600')
  
  // Копируем содержимое
  const content = element.cloneNode(true)
  
  // Удаляем кнопки и иконки из контента для печати
  const buttons = content.querySelectorAll('button, .print-hide, svg')
  buttons.forEach(button => button.remove())
  
  // Очищаем проблемные CSS стили
  const allElements = content.querySelectorAll('*')
  allElements.forEach(el => {
    if (el.style) {
      // Удаляем современные CSS свойства
      el.style.removeProperty('color')
      el.style.removeProperty('background-color')
      el.style.removeProperty('border-color')
      el.style.removeProperty('outline-color')
      el.style.removeProperty('background')
      el.style.removeProperty('border')
      el.style.removeProperty('box-shadow')
      el.style.removeProperty('text-shadow')
      
      // Устанавливаем простые стили
      el.style.color = '#000'
      el.style.backgroundColor = 'transparent'
      el.style.borderColor = '#ccc'
    }
  })
  
  // Создаем HTML документ
  const html = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Печать документа</title>
      <meta charset="UTF-8">
      <style>
        body {
          font-family: Arial, sans-serif;
          margin: 20px;
          line-height: 1.6;
          color: #000 !important;
        }
        * {
          color: #000 !important;
          background-color: transparent !important;
        }
        table {
          width: 100%;
          border-collapse: collapse;
          margin: 10px 0;
        }
        th, td {
          border: 1px solid #ccc;
          padding: 8px;
          text-align: left;
        }
        th {
          background-color: #f5f5f5 !important;
          font-weight: bold;
        }
        .header {
          margin-bottom: 20px;
        }
        .section {
          margin-bottom: 15px;
        }
        .section-title {
          font-weight: bold;
          margin-bottom: 10px;
        }
        @media print {
          body { margin: 0; }
          .no-print { display: none; }
        }
      </style>
    </head>
    <body>
      ${content.outerHTML}
    </body>
    </html>
  `
  
  // Записываем HTML в новое окно
  printWindow.document.write(html)
  printWindow.document.close()
  
  // Ждем загрузки и печатаем
  printWindow.onload = function() {
    setTimeout(() => {
      printWindow.print()
      setTimeout(() => {
        printWindow.close()
      }, 1000)
    }, 500)
  }
}

/**
 * Форматирует данные для печати/PDF
 * @param {Object} data - Данные для форматирования
 * @param {string} type - Тип документа (inventory, receipt, writeoff)
 */
export function formatDataForPrint(data, type) {
  const formatted = {
    title: '',
    sections: []
  }
  
  switch (type) {
    case 'inventory':
      formatted.title = `Инвентаризация: ${data.name}`
      formatted.sections = [
        {
          title: 'Основная информация',
          content: [
            { label: 'Название', value: data.name },
            { label: 'Дата создания', value: new Date(data.created_at).toLocaleString('ru-RU') },
            { label: 'Склад', value: data.warehouse_name },
            { label: 'Статус', value: getStatusText(data.status) },
            { label: 'Создал', value: data.created_by_name }
          ]
        },
        {
          title: 'Статистика',
          content: [
            { label: 'Всего товаров', value: data.items_count || 0 },
            { label: 'Норма', value: data.items?.filter(item => item.excess_shortage === 'normal').length || 0 },
            { label: 'Недостача', value: data.items?.filter(item => item.excess_shortage === 'shortage').length || 0 },
            { label: 'Избыток', value: data.items?.filter(item => item.excess_shortage === 'excess').length || 0 }
          ]
        }
      ]
      break
      
    case 'receipt':
      formatted.title = `Оприходование №${data.number}`
      formatted.sections = [
        {
          title: 'Основная информация',
          content: [
            { label: 'Номер', value: data.number },
            { label: 'Дата', value: new Date(data.date).toLocaleString('ru-RU') },
            { label: 'Организация', value: data.organization },
            { label: 'Склад', value: data.warehouse_name },
            { label: 'Статус', value: data.status === 'posted' ? 'Проведено' : 'Черновик' },
            { label: 'Создано', value: data.created_by }
          ]
        }
      ]
      break
      
    case 'writeoff':
      formatted.title = `Списание №${data.number}`
      formatted.sections = [
        {
          title: 'Основная информация',
          content: [
            { label: 'Номер', value: data.number },
            { label: 'Дата', value: new Date(data.date).toLocaleString('ru-RU') },
            { label: 'Организация', value: data.organization },
            { label: 'Склад', value: data.warehouse_name },
            { label: 'Статус', value: data.status === 'posted' ? 'Проведено' : 'Черновик' },
            { label: 'Создано', value: data.created_by }
          ]
        }
      ]
      break
  }
  
  return formatted
}

function getStatusText(status) {
  const statusMap = {
    'draft': 'Черновик',
    'in_progress': 'В процессе',
    'completed': 'Завершена',
    'cancelled': 'Отменена'
  }
  return statusMap[status] || status
} 