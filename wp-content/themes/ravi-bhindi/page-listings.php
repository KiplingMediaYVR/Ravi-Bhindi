<?php
/*
Template Name: Current Listings
*/
?>

<?php get_header(); ?>

<div id="listings-content">
    <div class="container">

        <header>
            <h1><?php the_title(); ?></h1>
        </header>

        <div class="row">

            <?php
            $query = new WP_Query(array(
                'post_type' => 'presales',
                'posts_per_page' => -1,
                'post__status' => 'published',
                'offset' => 0,
            ));
            ?>

            <?php while ($query->have_posts()) : $query->the_post(); ?>

                <div class="col-12 col-md-4">
                    <div class="current-listings-item">
                        <div class="current-listings-item-img">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('presales-thumb'); ?>
                            </a>
                        </div>
                        <div class="current-listings-item-body">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </div>
                    </div>
                    <!-- /.current-listings-item -->
                </div>

            <?php endwhile; ?>
            <?php wp_reset_query(); ?>

        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</div>
<!-- /#listings-content -->

<?php get_footer(); ?>
