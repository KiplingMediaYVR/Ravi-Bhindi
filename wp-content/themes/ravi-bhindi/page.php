<?php /* Template Name: Find an Agent */ ?>

<?php get_header(); ?>

<div id="main-content">
    <div class="container">
        <div class="row justify-content-center">

            <div class="page-title col-12">
                <h1><?php the_title(); ?></h1>
            </div>
            <!-- /.col-12 -->

            <div class="col-8">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <?php the_content(); ?>
                <?php endwhile; else : ?>
                <?php endif; ?>
            </div>

        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</div>
<!-- /#main-content -->

<?php get_footer(); ?>
