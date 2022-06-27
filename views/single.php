<?php
        $args = array(
            'p' => $single,
            'post_type' => 'book',
            'post_status' => 'publish',
            'posts_per_page' => 8, 
            'orderby' => 'title', 
            'order' => 'ASC', 
        );
        
        $loop = new WP_Query( $args ); 

        // echo '<pre>';
        // print_r($loop);

        while ( $loop->have_posts() ) : $loop->the_post(); 
        
        $author = get_post_meta( get_the_ID(), '_author', true );

        $background = get_post_meta( get_the_ID(), '_background', true );

        ?>
        <div class="container" style="background-color: <?= $background; ?>">
            <h3><?= the_title(); ?></h3>
            <?php if (get_the_post_thumbnail_url(get_the_ID()) ) : ?>
                <img src="<?= get_the_post_thumbnail_url(get_the_ID()); ?>" alt="<?= the_title(); ?>" />
            <?php endif; ?>
            <p class="author">By <?= $author; ?></p>
        </div>
    <?php
        endwhile;

        wp_reset_postdata(); 
?>