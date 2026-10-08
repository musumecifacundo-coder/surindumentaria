<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header" id="inicio">
  <div class="nav-inner container">
    <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Sur Estudio, inicio">SUR<span>ESTUDIO</span></a>
    <button class="menu-toggle" id="menu-toggle" aria-label="Abrir menú" aria-controls="nav-links" aria-expanded="false">☰ Menú</button>
    <nav class="nav-links" id="nav-links" aria-label="Menú principal">
      <?php if ( has_nav_menu( 'primary' ) ) : ?>
        <?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'items_wrap' => '%3$s', 'depth' => 1 ) ); ?>
      <?php else : ?>
        <a href="<?php echo esc_url( sur_estudio_shop_url() ); ?>">Tienda</a>
        <a href="<?php echo esc_url( home_url( '/#categories' ) ); ?>">Colecciones</a>
        <a href="<?php echo esc_url( sur_estudio_product_category_url( 'sastreria' ) ); ?>">Sastrería</a>
        <a href="<?php echo esc_url( sur_estudio_product_category_url( 'esenciales' ) ); ?>">Esenciales</a>
        <a href="<?php echo esc_url( sur_estudio_product_category_url( 'accesorios' ) ); ?>">Accesorios</a>
      <?php endif; ?>
    </nav>
    <div class="nav-actions">
      <button id="open-search" class="icon-button" aria-label="Buscar productos" aria-controls="search-panel" aria-expanded="false"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.5"/><path d="m16 16 5 5"/></svg></button>
      <a class="bag-link" href="<?php echo esc_url( sur_estudio_cart_url() ); ?>" aria-label="Ver bolsa"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8h16l-1.2 12H5.2L4 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg><span class="bag-count"><?php echo esc_html( sur_estudio_cart_count() ); ?></span></a>
    </div>
  </div>
  <form class="search-panel" id="search-panel" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search" method="get" hidden><label class="sr-only" for="site-search">Buscar productos</label><input id="site-search" name="s" type="search" placeholder="¿Qué estás buscando?"><input type="hidden" name="post_type" value="product"><button type="submit">Buscar ↗</button></form>
</header>
