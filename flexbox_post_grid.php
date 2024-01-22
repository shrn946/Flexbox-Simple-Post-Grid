<?php
/*
Plugin Name: Flexbox Simple Post Grid
Description: A simple WordPress plugin to display posts in a flexbox grid. [latest_posts_card_grid] [latest_posts_card_grid number_of_posts="3" category_slug="your-category-slug"]
Version: 1.0
Author: Hassan Naqvi
*/

// Enqueue the stylesheet
function flexbox_simple_post_grid_enqueue_styles() {
    wp_enqueue_style('flexbox-simple-post-grid-style', plugins_url('style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'flexbox_simple_post_grid_enqueue_styles');



function latest_posts_card_grid($atts) {
    // Shortcode attributes
    $atts = shortcode_atts(
        array(
            'number_of_posts' => -1, // Change this to the desired number of posts
            'category_slug'   => '', // Added category_slug attribute
        ),
        $atts,
        'latest_posts_card_grid'
    );

    // Query the latest posts with category filter only if category_slug is provided
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $atts['number_of_posts'],
    );

    // Add category filter if category_slug is provided
    if (!empty($atts['category_slug'])) {
        $args['category_name'] = $atts['category_slug'];
    }

    $latest_posts = new WP_Query($args);

    // Output the HTML structure
    ob_start();
    ?>
    <section class="cards">
        <?php
        $count = 1;
        while ($latest_posts->have_posts()) :
            $latest_posts->the_post();
            $post_id            = get_the_ID();
            $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
            $unique_class_id    = 'card--' . $count;
            ?>
            <article class="card <?php echo esc_attr($unique_class_id); ?>">
             
                <div class="card__img"></div>
                <a href="<?php the_permalink(); ?>" class="card_link">
                    <div class="card__img--hover" style="background-image: url('<?php echo esc_url($featured_image_url); ?>');"></div>
                </a>
                <div class="card__info">
                    <span class="card__category"><?php the_category(', '); ?></span>
                    <h3 class="card__title"><?php the_title(); ?></h3>
                    <span class="card__by"><?php esc_html_e('by', 'text-domain'); ?> <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="card__author" title="<?php echo esc_attr(get_the_author()); ?>"><?php echo esc_html(get_the_author()); ?></a></span>
                </div>
            </article>
            <?php
            $count++;
        endwhile;
        wp_reset_postdata();
        ?>
    </section>
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('latest_posts_card_grid', 'latest_posts_card_grid');
