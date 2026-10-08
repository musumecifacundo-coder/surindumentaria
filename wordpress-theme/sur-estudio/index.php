<?php get_header(); ?>
<main class="content-page container">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>><h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1><?php the_content(); ?></article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?><h1>Por ahora no hay contenido.</h1><?php endif; ?>
</main>
<?php get_footer(); ?>
