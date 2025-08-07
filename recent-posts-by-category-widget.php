<?php
/**
 * Plugin Name: Recent Posts by Category Widget
 * Description: A custom widget to display recent posts from a selected category.
 * Version: 1.2
 * Author: Aadam Hashmi
 */

class Recent_Posts_By_Category_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'recent_posts_by_category_widget',
            __('Recent Posts by Category', 'text_domain'),
            ['description' => __('Displays recent posts from a selected category.', 'text_domain')]
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title      = apply_filters('widget_title', $instance['title']);
        $category   = !empty($instance['category']) ? $instance['category'] : '';
        $num_posts  = !empty($instance['num_posts']) ? absint($instance['num_posts']) : 5;
        $show_thumb = isset($instance['show_thumb']) ? (bool) $instance['show_thumb'] : true;

        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        $query_args = [
            'cat'            => $category,
            'posts_per_page' => $num_posts,
            'post_status'    => 'publish',
        ];

        $posts = get_posts($query_args);

        echo '<ul class="recent-posts-by-category">';
        foreach ($posts as $post) {
            echo '<li>';
            if ($show_thumb && has_post_thumbnail($post->ID)) {
                echo get_the_post_thumbnail($post->ID, 'thumbnail', ['style' => 'margin-bottom:5px;']);
            }
            echo '<a href="' . get_permalink($post->ID) . '">' . esc_html($post->post_title) . '</a>';
            echo '<br><span class="post-date">' . get_the_date('', $post->ID) . '</span>';
            echo '</li>';
        }
        echo '</ul>';

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title      = isset($instance['title']) ? $instance['title'] : '';
        $category   = isset($instance['category']) ? $instance['category'] : '';
        $num_posts  = isset($instance['num_posts']) ? $instance['num_posts'] : 5;
        $show_thumb = isset($instance['show_thumb']) ? (bool) $instance['show_thumb'] : true;

        $categories = get_categories([
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false,
        ]);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>">Category:</label>
            <select class="widefat" id="<?php echo $this->get_field_id('category'); ?>"
                    name="<?php echo $this->get_field_name('category'); ?>">
                <?php
                if (!empty($categories)) {
                    foreach ($categories as $cat) {
                        echo '<option value="' . esc_attr($cat->term_id) . '" ' . selected($category, $cat->term_id, false) . '>';
                        echo esc_html($cat->name);
                        echo '</option>';
                    }
                } else {
                    echo '<option disabled>No categories available</option>';
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('num_posts'); ?>">Number of posts:</label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('num_posts'); ?>"
                   name="<?php echo $this->get_field_name('num_posts'); ?>" type="number"
                   value="<?php echo esc_attr($num_posts); ?>" min="1" max="10">
        </p>
        <p>
            <input class="checkbox" type="checkbox"
                   <?php checked($show_thumb); ?>
                   id="<?php echo $this->get_field_id('show_thumb'); ?>"
                   name="<?php echo $this->get_field_name('show_thumb'); ?>" />
            <label for="<?php echo $this->get_field_id('show_thumb'); ?>">Show thumbnails</label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        return [
            'title'      => sanitize_text_field($new_instance['title']),
            'category'   => absint($new_instance['category']),
            'num_posts'  => absint($new_instance['num_posts']),
            'show_thumb' => !empty($new_instance['show_thumb']) ? 1 : 0,
        ];
    }
}

function register_recent_posts_by_category_widget() {
    register_widget('Recent_Posts_By_Category_Widget');
}
add_action('widgets_init', 'register_recent_posts_by_category_widget');
