# Sur Estudio — muestra de e-commerce

Tienda ficticia de indumentaria y accesorios creada para mostrar diseño y desarrollo de una experiencia de compra. Su recorrido toma como referencia la estructura de [Houseplant](https://www.houseplant.com/): campaña de portada, categorías visuales, historia de marca, favoritos y una segunda campaña. La identidad, los textos y las imágenes de Sur Estudio son originales. Incluye dos entregables:

- [`index.html`](./index.html): vista navegable que se puede abrir en un navegador o publicar como sitio estático en Vercel. Las categorías y la búsqueda filtran 24 productos de muestra, ocho por categoría. Cada ficha tiene una foto principal y otra centrada en el producto; el carrito funciona como demostración en la sesión del navegador. No procesa pedidos, pagos ni suscripciones.
- [`wordpress-theme/sur-estudio`](./wordpress-theme/sur-estudio): tema clásico de WordPress preparado para WooCommerce. Cuando WooCommerce está instalado, muestra productos reales, usa sus páginas de catálogo, ficha, carrito y checkout, y puede usar el plugin oficial de Mercado Pago.

Los productos, precios, textos e imágenes son de muestra. Las imágenes fueron generadas para este proyecto e incluyen distintas modelos y fotos de accesorios apoyados en superficies reales.

## Ver la muestra

Abrí `index.html` en el navegador. Para publicarla en Vercel, importá esta carpeta como proyecto estático, sin comando de compilación. Esa publicación es solo una muestra visual: no habilita WooCommerce ni Mercado Pago.

## Convertirla en una tienda WordPress

1. Prepará un alojamiento para WordPress con PHP, base de datos y HTTPS. Vercel puede servir la muestra estática, pero el sitio WordPress necesita un entorno que ejecute WordPress y WooCommerce.
2. Instalá WordPress y el plugin WooCommerce. En la configuración de la tienda elegí Argentina y moneda **ARS**; configurá zonas y métodos de envío reales.
3. Copiá la carpeta `wordpress-theme/sur-estudio` a `wp-content/themes/` y activala desde **Apariencia → Temas**. Si preferís subir un ZIP desde el panel, generá `sur-estudio-theme.zip` con esa carpeta y cargalo desde **Apariencia → Temas → Añadir tema → Subir tema**.
4. Cargá productos desde el panel de WooCommerce. Si tenés WP-CLI, con el tema activo podés crear los 24 productos de muestra, sus variantes de talle y sus imágenes con:

   ```bash
   wp eval-file /ruta/absoluta/a/este/proyecto/scripts/seed-demo-products.php
   ```

   El script busca los SKU de muestra antes de crear productos, así que volver a ejecutarlo no duplica los que ya existen. Si ya se habían cargado los cuatro productos de la versión anterior, actualiza sus fotos y talles. También crea las categorías **Sastrería**, **Esenciales** y **Accesorios** para la navegación. Antes de vender, reemplazá precios, fotos, descripciones, stock y políticas por los datos del comercio.
5. Instalá **Mercado Pago payments for WooCommerce** desde **Plugins → Añadir nuevo**. Vinculá la cuenta de Mercado Pago del comercio y configurá **Checkout Pro** si querés que el comprador complete el pago en Mercado Pago. El tema no guarda datos de tarjeta ni implementa pagos por cuenta propia.
6. Hacé las pruebas de compra y confirmación de pedidos que indica Mercado Pago antes de poner la tienda en producción. Revisá también que impuestos, envíos, correos y páginas legales respondan a ese negocio.

La cuenta de Mercado Pago debe ser del comercio que recibe el dinero. La integración y las credenciales se administran desde el plugin y Mercado Pago, no desde el código del tema.

## Alcance técnico

El tema usa el catálogo de WooCommerce y estilos propios para sus páginas. La portada muestra hasta 24 productos publicados; si la tienda todavía está vacía, enseña las 24 tarjetas visuales sin botones de compra. El carrito y el checkout reales aparecen solo con WooCommerce configurado. El catálogo fuente está en `wordpress-theme/sur-estudio/assets/catalog/products.json`; `node scripts/build-catalog.mjs` actualiza los datos de la vista estática.

En este entorno no hay PHP ni una instalación de WordPress, por lo que la ejecución completa del tema y una transacción de Mercado Pago quedan pendientes de probar en un sitio WordPress de ensayo. La vista estática sí se probó en escritorio y móvil.

## Referencias oficiales

- [Integración de WooCommerce con Mercado Pago](https://www.mercadopago.com.ar/developers/es/docs/woocommerce/overview)
- [Instalación del plugin de Mercado Pago](https://www.mercadopago.com.ar/developers/es/docs/woocommerce/installation/wordpress)
- [Pruebas de pago en WooCommerce](https://www.mercadopago.com.ar/developers/es/docs/woocommerce/integration-test)
- [Guía de temas clásicos de WooCommerce](https://developer.woocommerce.com/docs/theming/theme-development/classic-theme-developer-handbook)
