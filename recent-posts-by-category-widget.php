<?php
/**
 * Plugin Name: Recent Posts by Category Widget (Fixed)
 * Description: A custom widget to display recent posts from a selected category.
 * Version: 2.0
 * Author: Aadam Hashmi
 */

class Fixed_Recent_Posts_By_Category_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'fixed_recent_posts_by_category_widget',
            __('Recent Posts by Category (Fixed)', 'text_domain'),
            ['description' => __('Displays recent posts from a selected category.', 'text_domain')]
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title      = apply_filters('widget_title', $instance['title']);
        $category   = !empty($instance['category']) ? absint($instance['category']) : 0;
        $num_posts  = !empty($instance['num_posts']) ? absint($instance['num_posts']) : 5;
        $show_thumb = isset($instance['show_thumb']) ? (bool) $instance['show_thumb'] : true;

        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        $query = new WP_Query([
            'posts_per_page' => $num_posts,
            'cat'            => $category,
            'post_status'    => 'publish',
        ]);

        if ($query->have_posts()) {
            echo '<ul class="recent-posts-by-category">';
            while ($query->have_posts()) {
                $query->the_post();
                echo '<li>';
                if ($show_thumb && has_post_thumbnail()) {
                    the_post_thumbnail('thumbnail', ['style' => 'margin-bottom:5px;']);
                }
                echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                echo '<br><span class="post-date">' . get_the_date() . '</span>';
                echo '</li>';
            }
            echo '</ul>';
            wp_reset_postdata();
        } else {
            echo '<p>No posts found in this category.</p>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title      = isset($instance['title']) ? $instance['title'] : '';
        $category   = isset($instance['category']) ? $instance['category'] : '';
        $num_posts  = isset($instance['num_posts']) ? $instance['num_posts'] : 5;
        $show_thumb = isset($instance['show_thumb']) ? (bool) $instance['show_thumb'] : true;

        $categories = get_terms([
            'taxonomy'   => 'category',
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
                if (!empty($categories) && !is_wp_error($categories)) {
                    foreach ($categories as $cat) {
                        echo '<option value="' . esc_attr($cat->term_id) . '" ' . selected($category, $cat->term_id, false) . '>';
                        echo esc_html($cat->name);
                        echo '</option>';
                    }
                } else {
                    echo '<option disabled>No categories found</option>';
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

function register_fixed_recent_posts_by_category_widget() {
    register_widget('Fixed_Recent_Posts_By_Category_Widget');
}
add_action('widgets_init', 'register_fixed_recent_posts_by_category_widget');
