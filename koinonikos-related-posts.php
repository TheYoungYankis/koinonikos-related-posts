<?php
/**
 * Plugin Name: KOINONIKOS Related Posts
 * Description: Affiche des articles liés basés sur les tags via shortcode : [koinonikos_related_posts]. Pas de dashboard / settings on fait simple et léger, vous modifiez les css directement dans le code ci-dessous pour l'adapter à votre thème si vous le souhaitez.
 * Version: 1.0.0
 * Author: KOINONIKOS, Allen Le Yaouanc
 * Author URI: https://koinonikos.com/
 * License: GPLv2 or later
 * Text Domain: koinonikos-related-posts
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shortcode [koinonikos_related_posts]
 */
function koinonikos_related_posts_shortcode($atts) {
if (!is_singular(['post', 'page'])) {
    return '';
}

    global $post;

    $tags = wp_get_post_tags($post->ID);
    if (!$tags) {
        return '';
    }

    $tag_ids = wp_list_pluck($tags, 'term_id');

    $query = new WP_Query([
        'post_type'           => ['post', 'page'],
        'posts_per_page'      => 8,
        'post__not_in'        => [$post->ID],
        'tag__in'             => $tag_ids,
        'ignore_sticky_posts' => true,
    ]);

    if (!$query->have_posts()) {
        return '';
    }

    ob_start();
    ?>
    <div class="koinonikos-related-posts">
        <div class="koinonikos-related-grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <article class="koinonikos-related-item">
                    <a href="<?php the_permalink(); ?>" class="koinonikos-related-link">
                        <div class="koinonikos-related-thumb">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium'); ?>
                            <?php else : ?>
                                <div class="koinonikos-related-thumb-placeholder"></div>
                            <?php endif; ?>
                        </div>
                        <div class="koinonikos-related-content">
                            <h3 class="koinonikos-related-title"><?php the_title(); ?></h3>
                            <time class="koinonikos-related-date"><?php echo get_the_date(); ?></time>
                        </div>
                    </a>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('koinonikos_related_posts', 'koinonikos_related_posts_shortcode');

/**
 * Styles
 */
function koinonikos_related_posts_styles() {
    ?>
    <style>
        .koinonikos-related-posts {
            margin: 3rem 0;
        }

        .koinonikos-related-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        @media (max-width: 1024px) {
            .koinonikos-related-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .koinonikos-related-grid {
                grid-template-columns: 1fr;
            }
        }

        .koinonikos-related-item {
            background: transparent;
        }

        .koinonikos-related-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .koinonikos-related-thumb {
            position: relative;
            overflow: hidden;
            aspect-ratio: 16 / 9;
            background: #f2f2f2;
        }

        .koinonikos-related-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .koinonikos-related-link:hover .koinonikos-related-thumb img {
            transform: scale(1.05);
        }

        .koinonikos-related-thumb-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #e5e5e5, #f5f5f5);
        }

        .koinonikos-related-content {
            padding-top: 0.75rem;
        }

        .koinonikos-related-title {
    font-family: 'Montserrat', Helvetica, Arial, sans-serif;
    font-size: 1rem;
    font-weight: bold;
    line-height: 1.4;
    margin: 0 0 0.25rem;
    color: #444444;
}

        .koinonikos-related-date {
    font-family: 'Open Sans', Helvetica, Arial, Lucida, sans-serif;
    text-transform: uppercase;
    font-size: 10px;
    letter-spacing: 1px;
    color: #4c4c4c;
}
    </style>
    <?php
}
add_action('wp_footer', 'koinonikos_related_posts_styles');
