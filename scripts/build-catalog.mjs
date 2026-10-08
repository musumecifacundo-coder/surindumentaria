import { readFile, writeFile, access } from 'node:fs/promises';
import { fileURLToPath } from 'node:url';
import { dirname, join, resolve } from 'node:path';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const catalogDir = join(root, 'wordpress-theme/sur-estudio/assets/catalog');
const imageDir = join(root, 'wordpress-theme/sur-estudio/assets/images');
const products = JSON.parse(await readFile(join(catalogDir, 'products.json'), 'utf8'));
const counts = { sastreria: 0, esenciales: 0, accesorios: 0 };
const ids = new Set();
const skus = new Set();

for (const product of products) {
  if (!Object.hasOwn(counts, product.category)) throw new Error(`Categoría inválida: ${product.category}`);
  if (ids.has(product.id) || skus.has(product.sku)) throw new Error(`Producto duplicado: ${product.id}`);
  if (!Number.isInteger(product.price) || product.price <= 0) throw new Error(`Precio inválido: ${product.id}`);
  ids.add(product.id);
  skus.add(product.sku);
  counts[product.category] += 1;
  await access(join(imageDir, product.image));
  if (!Array.isArray(product.gallery) || product.gallery.length === 0) throw new Error(`Falta foto secundaria: ${product.id}`);
  for (const galleryImage of product.gallery || []) await access(join(imageDir, galleryImage));
}
if (products.length !== 24 || Object.values(counts).some(count => count !== 8)) {
  throw new Error(`Se esperan 24 productos, 8 por categoría: ${JSON.stringify(counts)}`);
}
await writeFile(join(catalogDir, 'catalog-data.js'), 'window.SUR_CATALOG = ' + JSON.stringify(products) + ';\n');
console.log(`Catálogo válido: ${products.length} productos, 8 por categoría.`);
