<?php
require_once( 'C:\Users\alezu\Local Sites\alezux-members\app\public\wp-load.php' );

$args = array(
    'post_type' => 'sfwd-lessons',
    'posts_per_page' => -1,
);
$lessons = get_posts($args);

foreach ($lessons as $lesson) {
    if (strpos($lesson->post_title, 'Separador') !== false || strpos($lesson->post_title, '[') !== false) {
        echo "Title: " . $lesson->post_title . "\n";
    }
}
