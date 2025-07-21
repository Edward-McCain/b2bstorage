import { Client } from 'pg';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const client = new Client({
  host: '5.35.85.110',
  port: 5432,
  user: 'b2buser',
  password: 'B2B_Storage_2024!',
  database: 'b2bstorage',
});

async function main() {
  await client.connect();

  // Получаем все категории
  const categoriesRes = await client.query(`
    SELECT category_id, name, name_ru, name_en, name_uz
    FROM categories
    ORDER BY name_ru, name
  `);

  // Получаем все подкатегории
  const subcategoriesRes = await client.query(`
    SELECT subcategory_id, name, name_ru, name_en, name_uz, category_id
    FROM subcategories
    ORDER BY name_ru, name
  `);

  // Группируем подкатегории по category_id
  const subMap = {};
  for (const sub of subcategoriesRes.rows) {
    if (!subMap[sub.category_id]) subMap[sub.category_id] = [];
    subMap[sub.category_id].push({
      subcategory_id: sub.subcategory_id,
      name: sub.name,
      name_ru: sub.name_ru,
      name_en: sub.name_en,
      name_uz: sub.name_uz,
    });
  }

  // Формируем итоговую структуру
  const result = categoriesRes.rows.map(cat => ({
    category_id: cat.category_id,
    name: cat.name,
    name_ru: cat.name_ru,
    name_en: cat.name_en,
    name_uz: cat.name_uz,
    subcategories: subMap[cat.category_id] || []
  }));

  // Сохраняем в файл
  const outPath = path.join(__dirname, 'cats.json');
  fs.writeFileSync(outPath, JSON.stringify(result, null, 2), 'utf8');
  console.log(`cats.json успешно создан: ${outPath}`);

  await client.end();
}

main().catch(e => {
  console.error('Ошибка:', e);
  process.exit(1);
}); 