<?php get_header(); ?>
<main class="content-page container">
  <div class="section-head"><div><p class="eyebrow">Resultados de búsqueda</p><h1><?php echo esc_html( get_search_query() ); ?></h1></div></div>
  <?php if ( have_posts() ) : ?>
    <div class="product-grid">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php $product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : false; ?>
        <?php if ( $product ) : ?>
          <article class="product-card"><div class="product-image"><a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( 'Ver ' . $product->get_name() ); ?>"><?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ) ); ?></a></div><div class="product-meta"><div><h3><a href="<?php the_permalink(); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3></div><span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span></div></article>
        <?php endif; ?>
      <?php endwhile; ?>
    </div>
    <?php the_posts_pagination(); ?>
  <?php else : ?>
    <p>No encontramos productos con esa búsqueda.</p><a class="button" href="<?php echo esc_url( sur_estudio_shop_url() ); ?>">Ver toda la tienda ↗</a>
  <?php endif; ?>
</main>
<?php get_footer(); ?>
