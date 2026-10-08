const catalog = window.SUR_CATALOG || [];
const products = Object.fromEntries(catalog.map(product => [product.id, product]));
const money = value => '$ ' + new Intl.NumberFormat('es-AR').format(value);
const imageUrl = image => 'wordpress-theme/sur-estudio/assets/images/' + image;
const escapeHtml = value => String(value).replace(/[&<>"']/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[character]);
const categoryNames = { all: 'toda la colección', sastreria: 'sastrería', esenciales: 'esenciales', accesorios: 'accesorios' };
const cart = {};
const drawer = document.querySelector('#cart-drawer');
const backdrop = document.querySelector('#drawer-backdrop');
const notice = document.querySelector('#notice');
const filterFeedback = document.querySelector('#filter-feedback');
let noticeTimer;

function showNotice(message) {
  notice.textContent = message;
  notice.classList.add('show');
  clearTimeout(noticeTimer);
  noticeTimer = setTimeout(() => notice.classList.remove('show'), 3500);
}
function renderProducts() {
  document.querySelector('#product-grid').innerHTML = catalog.map(product => `
    <article class="product-card" data-id="${escapeHtml(product.id)}" data-category="${escapeHtml(product.category)}">
      <div class="product-image">
        <a href="#shop" class="product-detail" data-open="${escapeHtml(product.id)}" aria-label="Ver ${escapeHtml(product.name)}"><img src="${imageUrl(product.image)}" alt="${escapeHtml(product.name)}, ${escapeHtml(product.detail)}" loading="lazy"></a>
        ${product.label ? `<span class="product-tag">${escapeHtml(product.label)}</span>` : ''}
        <button class="quick-add" data-quick="${escapeHtml(product.id)}">${product.sizes.length ? 'Elegir talle ↗' : 'Agregar a la bolsa +'}</button>
      </div>
      <div class="product-meta"><div><h3>${escapeHtml(product.name)}</h3><p>${escapeHtml(product.detail)}</p></div><span class="price">${money(product.price)}</span></div>
    </article>`).join('');
}
renderProducts();
const productCards = [...document.querySelectorAll('.product-card')];

function toggleCart(open) {
  drawer.classList.toggle('open', open);
  backdrop.classList.toggle('open', open);
  drawer.setAttribute('aria-hidden', String(!open));
  document.body.style.overflow = open ? 'hidden' : '';
  if (open) document.querySelector('#close-cart').focus();
}
function renderCart() {
  const entries = Object.entries(cart).filter(([, line]) => line.quantity > 0);
  document.querySelector('#cart-count').textContent = entries.reduce((total, [, line]) => total + line.quantity, 0);
  document.querySelector('#cart-total').textContent = money(entries.reduce((total, [, line]) => total + products[line.id].price * line.quantity, 0));
  document.querySelector('#cart-items').innerHTML = entries.length ? entries.map(([key, line]) => {
    const product = products[line.id];
    return `<div class="drawer-item"><img src="${imageUrl(product.image)}" alt=""><div><h3>${escapeHtml(product.name)}</h3><p>${escapeHtml(product.detail)}${line.size ? '<br>Talle: ' + escapeHtml(line.size) : ''}<br>Cantidad: ${line.quantity}</p><button data-remove="${escapeHtml(key)}">Quitar uno</button></div><strong>${money(product.price * line.quantity)}</strong></div>`;
  }).join('') : '<p>Tu bolsa está vacía por ahora.</p>';
}
function addProduct(id, size = '') {
  const product = products[id];
  if (!product || (product.sizes.length && !size)) return;
  const key = id + '::' + size;
  cart[key] = { id, size, quantity: (cart[key]?.quantity || 0) + 1 };
  renderCart();
  toggleCart(true);
}

const detailDialog = document.createElement('dialog');
detailDialog.className = 'product-dialog';
detailDialog.setAttribute('aria-label', 'Detalle del producto');
document.body.append(detailDialog);
function openProduct(id) {
  const product = products[id];
  let selectedSize = '';
  const photos = [product.image, ...(product.gallery || [])];
  const sizeMarkup = product.sizes.length ? `<fieldset class="size-selector"><legend>Elegí tu talle</legend><div class="size-options">${product.sizes.map(size => `<button type="button" data-size="${escapeHtml(size)}" aria-pressed="false">${escapeHtml(size)}</button>`).join('')}</div></fieldset>` : '';
  const photoMarkup = `<div class="dialog-gallery"><img class="dialog-main-image" src="${imageUrl(photos[0])}" alt="${escapeHtml(product.name)} · foto 1">${photos.length > 1 ? `<div class="dialog-thumbs" aria-label="Fotos del producto">${photos.map((photo, index) => `<button type="button" data-photo="${index}" aria-label="Ver foto ${index + 1} de ${escapeHtml(product.name)}" aria-pressed="${index === 0}"><img src="${imageUrl(photo)}" alt=""></button>`).join('')}</div>` : ''}</div>`;
  detailDialog.innerHTML = `<button class="dialog-close" aria-label="Cerrar detalle">×</button>${photoMarkup}<div class="dialog-copy"><p class="section-kicker">${escapeHtml(categoryNames[product.category])}</p><h2>${escapeHtml(product.name)}</h2><p class="dialog-detail">${escapeHtml(product.detail)}</p><strong>${money(product.price)}</strong><p>${escapeHtml(product.description)}</p>${sizeMarkup}<button class="button" id="modal-add">Agregar a la bolsa +</button><small>Producto de muestra · sin pago habilitado</small></div>`;
  detailDialog.showModal();
  detailDialog.querySelector('.dialog-close').addEventListener('click', () => detailDialog.close());
  detailDialog.querySelectorAll('[data-photo]').forEach(button => button.addEventListener('click', () => {
    const index = Number(button.dataset.photo);
    const mainImage = detailDialog.querySelector('.dialog-main-image');
    mainImage.src = imageUrl(photos[index]);
    mainImage.alt = `${product.name} · foto ${index + 1}`;
    detailDialog.querySelectorAll('[data-photo]').forEach(option => option.setAttribute('aria-pressed', String(option === button)));
  }));
  detailDialog.querySelectorAll('[data-size]').forEach(button => button.addEventListener('click', () => {
    selectedSize = button.dataset.size;
    detailDialog.querySelectorAll('[data-size]').forEach(option => option.setAttribute('aria-pressed', String(option === button)));
  }));
  detailDialog.querySelector('#modal-add').addEventListener('click', () => {
    if (product.sizes.length && !selectedSize) { showNotice('Elegí un talle para continuar.'); return; }
    detailDialog.close();
    addProduct(id, selectedSize);
  });
}
detailDialog.addEventListener('click', event => { if (event.target === detailDialog) detailDialog.close(); });
document.querySelector('#product-grid').addEventListener('click', event => {
  const detailLink = event.target.closest('[data-open]');
  if (detailLink) { event.preventDefault(); openProduct(detailLink.dataset.open); return; }
  const quickButton = event.target.closest('[data-quick]');
  if (!quickButton) return;
  const product = products[quickButton.dataset.quick];
  if (product.sizes.length) openProduct(product.id);
  else addProduct(product.id);
});

document.querySelector('#open-cart').addEventListener('click', () => toggleCart(true));
document.querySelector('#close-cart').addEventListener('click', () => toggleCart(false));
backdrop.addEventListener('click', () => toggleCart(false));
document.addEventListener('keydown', event => { if (event.key === 'Escape') toggleCart(false); });
document.querySelector('#cart-items').addEventListener('click', event => {
  const key = event.target.dataset.remove;
  if (!key || !cart[key]) return;
  cart[key].quantity -= 1;
  renderCart();
});
document.querySelector('#checkout-demo').addEventListener('click', () => showNotice('El checkout se activa en la versión WordPress con WooCommerce y Mercado Pago.'));
document.querySelector('#newsletter-form').addEventListener('submit', event => { event.preventDefault(); event.target.reset(); showNotice('Formulario de muestra. En una tienda real se conecta a una lista de correo.'); });
const menuToggle = document.querySelector('#menu-toggle');
const navLinks = document.querySelector('#nav-links');
menuToggle.addEventListener('click', () => { const open = navLinks.classList.toggle('open'); menuToggle.setAttribute('aria-expanded', String(open)); });
navLinks.querySelectorAll('a').forEach(link => link.addEventListener('click', () => { navLinks.classList.remove('open'); menuToggle.setAttribute('aria-expanded', 'false'); }));

function showCategory(category) {
  productCards.forEach(card => { card.hidden = category !== 'all' && card.dataset.category !== category; });
  document.querySelectorAll('[data-tab]').forEach(button => button.setAttribute('aria-pressed', String(button.dataset.tab === category)));
  filterFeedback.textContent = category === 'all' ? 'Mostrando las 24 piezas de la colección.' : 'Mostrando ' + categoryNames[category] + ' · 8 piezas';
}
document.querySelectorAll('[data-filter]').forEach(link => link.addEventListener('click', () => showCategory(link.dataset.filter)));
document.querySelectorAll('[data-tab]').forEach(button => button.addEventListener('click', () => showCategory(button.dataset.tab)));
const searchPanel = document.querySelector('#search-panel');
const searchButton = document.querySelector('#open-search');
searchButton.addEventListener('click', () => {
  searchPanel.hidden = !searchPanel.hidden;
  searchButton.setAttribute('aria-expanded', String(!searchPanel.hidden));
  if (!searchPanel.hidden) document.querySelector('#site-search').focus();
});
searchPanel.addEventListener('submit', event => {
  event.preventDefault();
  const query = document.querySelector('#site-search').value.trim().toLocaleLowerCase('es-AR');
  productCards.forEach(card => {
    const item = products[card.dataset.id];
    card.hidden = !!query && !(item.name + ' ' + item.detail + ' ' + item.description + ' ' + categoryNames[item.category]).toLocaleLowerCase('es-AR').includes(query);
  });
  document.querySelectorAll('[data-tab]').forEach(button => button.setAttribute('aria-pressed', 'false'));
  const count = productCards.filter(card => !card.hidden).length;
  filterFeedback.textContent = query ? (count ? count + ' resultado' + (count === 1 ? '' : 's') + ' para “' + query + '”' : 'No encontramos productos para “' + query + '”.') : '';
  searchPanel.hidden = true;
  searchButton.setAttribute('aria-expanded', 'false');
  document.querySelector('#shop').scrollIntoView({ behavior: 'smooth' });
});
