<?php
function replace_wordpress_howdy($wp_admin_bar)
{
    $my_account = $wp_admin_bar->get_node('my-account');
    $newtext = str_replace('Howdy,', 'Welcome,', $my_account->title);
    $wp_admin_bar->add_node(array(
        'id' => 'my-account',
        'title' => $newtext,
    ));
}
add_filter('admin_bar_menu', 'replace_wordpress_howdy', 25);


function mytheme_register_nav_menu()
{
    register_nav_menus(
        array(
            'my_header_menu' => __('Header Menu', 'text_domain'),
            'my_footer_menu1' => __('Footer Menu1', 'text_domain'),
            'my_footer_menu2' => __('Footer Menu2', 'text_domain'),
            'my_footer_menu3' => __('Footer Menu3', 'text_domain'),
            'my_footer_menu' => __('Footer Menu', 'text_domain'),
            'my_menu' => __('Main Menu', 'text_domain'),
            'new_menu' => __('New Menu', 'text_domain'),

        )
    );
}
add_action('after_setup_theme', 'mytheme_register_nav_menu', 0);

if (function_exists('acf_add_options_page')) {

    acf_add_options_page(
        array(
            'page_title' => 'Theme General Settings',
            'menu_title' => 'Theme Settings',
            'menu_slug' => 'theme-general-settings',
            'capability' => 'edit_posts',
            // 'redirect'      => false
        )
    );

    acf_add_options_sub_page(
        array(
            'page_title' => 'Theme Header Settings',
            'menu_title' => 'Header',
            'parent_slug' => 'theme-general-settings',
        )
    );

    acf_add_options_sub_page(
        array(
            'page_title' => 'Theme Footer Settings',
            'menu_title' => 'Footer',
            'parent_slug' => 'theme-general-settings',
        )
    );

    acf_add_options_sub_page(
        array(
            'page_title' => 'Theme Common Settings',
            'menu_title' => 'Common',
            'parent_slug' => 'theme-general-settings',

        )
    );
}
function add_file_types_to_uploads($file_types)
{
    $new_filetypes = array();
    $new_filetypes['svg'] = 'image/svg+xml';
    $file_types = array_merge($file_types, $new_filetypes);
    return $file_types;
}
add_filter('upload_mimes', 'add_file_types_to_uploads');
function remove_parent_style()
{
    wp_dequeue_style('twenty-twenty-one-style');
}
add_action('wp_enqueue_scripts', 'remove_parent_style', 20);
function my_login_logo()
{ ?>
    <style type="text/css">
        #login h1 a,
        .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo.png);
            height: 82px;
            width: 320px;
            margin: auto;
            background-color: black;
            text-align: center;
            background-size: auto;
            background-repeat: no-repeat;
            background-position: center;

        }
    </style>
<?php }
add_action('login_enqueue_scripts', 'my_login_logo');
function custom_login_styles()
{ ?>
    <style>
        body.login {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/07/images.jpg);
            background-size: contain;
        }
    </style>
    <?php }
add_action('login_enqueue_scripts', 'custom_login_styles');
function my_hide_shipping_when_free_is_available($rates)
{
    $free = array();
    foreach ($rates as $rate_id => $rate) {
        if ('free_shipping' === $rate->method_id) {
            $free[$rate_id] = $rate;
            break;
        }
    }
    return !empty($free) ? $free : $rates;
}

add_filter('woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100);
add_action('wp_ajax_load_more_posts', 'load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'load_more_posts');

function load_more_posts()
{
    $paged = $_POST['paged'];

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 3,
        'orderby' => 'name',
        'order' => 'asc',
        'paged' => $paged,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<div class="load-more-target">';
        echo '<div class="row">';
        while ($query->have_posts()) {
            $query->the_post();
            global $product;
    ?>
            <div class="col-md-4">
                <ul class="card" style="border:2px solid gray;">
                    <li class="card-item">
                        <div class="product" style="text-align:center;">
                            <div class="pr_img">
                                <?php echo $product->get_image(); ?>
                            </div><br>
                            <h2>
                                <a href="<?php echo $product->get_permalink(); ?>"><?php echo $product->get_title(); ?></a>
                            </h2>
                            <h3>
                                <?php echo $product->get_price_html(); ?>
                            </h3>
                            <h4>
                                <?php echo $product->get_stock_status(); ?>
                            </h4>
                            <?php if ($product->get_type() == 'simple') { ?>
                                <a class="btn" href="<?php echo $product->add_to_cart_url(); ?>">Add to Cart</a>
                            <?php } else { ?>
                                <a class="btn" href="<?php echo $product->get_permalink(); ?>">Select Options</a>
                            <?php } ?>
                        </div>
                    </li>
                </ul>
            </div>
    <?php
        }
        echo '</div>';
        echo '</div>';
        wp_reset_postdata();
    }

    die();
}


// Custom Pagination AJAX Handler
add_action('wp_ajax_custom_pagination', 'custom_pagination');
add_action('wp_ajax_nopriv_custom_pagination', 'custom_pagination');
function custom_pagination()
{ ?>
    <?php $paged = $_GET['paged'];
    // The Loop using WP_Query (same as in the template)   
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 3,
        'paged' => $paged,
    );
    $custom_query = new WP_Query($args);
    ob_start();
    if ($custom_query->have_posts()) {
        while ($custom_query->have_posts()) {
            $custom_query->the_post();
            // Display your product content here
            global $product; // Define the global product variable inside the loop
    ?>
            <div class="col-md-4">
                <ul class="card" style="border:2px solid gray;">
                    <li class="card-item">
                        <div class="product" style="text-align:center;">
                            <div class="pr_img">
                                <?php echo $product->get_image(); ?>
                            </div><br>
                            <h2>
                                <a href="<?php echo $product->get_permalink(); ?>">
                                    <?php echo $product->get_title(); ?></a>
                            </h2>
                            <h3>
                                <?php echo $product->get_price_html(); ?>
                            </h3>
                            <h4>
                                <?php echo $product->get_stock_status(); ?>
                            </h4>
                            <?php if ($product->get_type() == 'simple') { ?>
                                <a class="btn" href="<?php echo $product->add_to_cart_url(); ?>">Add to Cart</a>
                            <?php } else { ?>
                                <a class="btn" href="<?php echo $product->get_permalink(); ?>">Select Options</a>
                            <?php } ?>
                        </div>
                    </li>
                </ul>
            </div>
        <?php
        } ?>
        <?php
    } else {
        echo 'No products found.';
    }
    $loop_content = ob_get_clean();

    $pagination = paginate_links(
        array(
            'total' => $custom_query->max_num_pages,
            'current' => $paged, // Use the same paged value
            'prev_text' => __('<< Previous'),
            'next_text' => __('Next >>'),
            'before_page_number' => '<span class="page-numbers" data-page="%#%">',
            'after_page_number' => '</span>',
        )
    );

    $response = array(
        'content' => $loop_content,
        'pagination' => $pagination,
    );

    wp_send_json($response);

    wp_reset_postdata();
    die();
}

// require_once(plugin_dir_path(__FILE__) . '/custom-elementor-widget-test/my_widget.php');


// Add custom discount type
add_filter('woocommerce_coupon_discount_types', 'custom_discount_types', 10, 1);
function custom_discount_types($types)
{
    $types['custom'] = __('Rate as coupon price', 'woocommerce');
    return $types;
}


add_filter('woocommerce_coupon_is_valid_for_product', 'validate_custom_coupon', 10, 4);
function validate_custom_coupon($valid, $product, $coupon, $values)
{
    if (!$coupon->is_type('custom')) {
        return $valid;
    }
    return true;
}

add_filter('woocommerce_coupon_get_discount_amount', 'custom_discount_type_price', 10, 5);
function custom_discount_type_price($discount, $discounting_amount, $cart_item, $single, $coupon)
{
    if ($coupon->is_type('custom')) {
        $coupon_price = $coupon->get_amount();
        // $discount = $coupon_price * $single;
        $discount = $discounting_amount - $coupon_price;
    }
    return $discount;
}

//ACF Load More 
add_action('wp_ajax_load_more_acf', 'load_more_acf');
add_action('wp_ajax_nopriv_load_more_acf', 'load_more_acf');

function load_more_acf()
{
    $show = 1;
    $start = $_POST['offset'];
    $end = $start + $show;
    $post_id = $_POST['post_id'];

    ob_start();
    if (have_rows('section', $post_id)) {
        $total = count(get_field('section', $post_id));
        $count = 0;
        while (have_rows('section', $post_id)) {
            the_row();
            if ($count < $start) {
                $count++;
                continue;
            }
        ?>
            <div class="col-md-12">
                <h2 style="color:black;"><?php echo the_sub_field('heading') ?></h2>
            </div>

            <?php if (have_rows('posts')) {
                while (have_rows('posts')) {
                    the_row(); ?>
                    <div class="col-md-6">
                        <h3> <?php echo the_sub_field('title') ?> </h3><br>
                        <img style="width:500px;height: 300px;" src="<?php echo the_sub_field('image') ?>" alt="image"><br><br>
                        <p><?php echo the_sub_field('description') ?></p>
                    </div>

            <?php }
            } ?>
            <div class="col-md-12">
                <img id="banner" style=" width: 1075px;height: 300px;" src="<?php echo the_sub_field('image') ?>" alt="image">
            </div>
            <div class="col-md-12" style="margin:20px;"></div>
    <?php
            $count++;
            if ($count == $end) {

                break;
            }
        }
    }
    $content = ob_get_clean();
    $more = false;
    if ($total > $count) {
        $more = true;
    }
    // wp_send_json($response);
    echo json_encode(array('content' => $content, 'more' => $more, 'offset' => $end));
    exit;
}

//ACF Load More by creating Shortcode
add_shortcode('acf_card', 'acf_card');
function acf_card()
{ ?>
    <div class="container" style="text-align:center;" data-ajaxurl="<?= admin_url('admin-ajax.php') ?>">
        <div id="load-more-target">
            <div class="row"><?php
                                if (have_rows('card')) {
                                    while (have_rows('card')) {
                                        the_row();
                                        $count = 0;
                                        $number = 1;
                                ?>
                        <?php if (have_rows('card_image')) {
                                            while (have_rows('card_image')) {
                                                the_row(); ?>
                                <div class="col-md-6">
                                    <img style="width:500px; height : 300px; margin:20px;" loading="lazy" src="<?php echo the_sub_field('image') ?>" alt="<?php echo the_sub_field('image') ?>">
                                </div>
                        <?php }
                                        } ?>


                        <div class="col-md-12" id="banner">
                            <img style=" width: 1075px;height: 300px; display: none;" loading="lazy" src="<?php echo the_sub_field('banner') ?>" alt="<?php echo the_sub_field('banner') ?>">
                        </div>
                <?php
                                        $count++;
                                        if ($count == $number) {
                                            break;
                                        }
                                    }
                                } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button id="load-more" style="background-image:url('http://localhost/medicar/wp-content/uploads/2023/10/spinner.gif');
                                            width:100%;                                
                                            background-position:center;                                
                                            background-repeat: no-repeat;
                                            background-color: rgba(0, 0, 0, 0) !important;
                                            border: none !important;
                                            /* text-indent: -9999px; */
                                            padding : 10px; " data-post-id="<?php
                                                                            global $post;
                                                                            echo $post->ID; ?>"> <!-- 2993 -->
                    <a style="opacity: 0;" href="javascript: load_acf_card();"> Load more </a>
                </button>

            </div>
            <!-- <div class="col-md-12">
                <button id="showBannerButton" style="background-color: rgba(0, 0, 0, 0) !important;
                        border: none !important;
                        /* text-indent: -9999px; */
                       /* opacity: 0; */
                       " class="show_hide">Show Banner
                </button>
            </div> -->
        </div>
    </div>
    <?php
}



add_action('wp_ajax_load_acf_card', 'load_acf_card');
add_action('wp_ajax_nopriv_load_acf_card', 'load_acf_card');
function load_acf_card()
{
    $show = 1;
    $start = $_POST['offset'];
    $end = $start + $show;
    $post_id = $_POST['post_id'];
    ob_start();
    if (have_rows('card', $post_id)) {
        $total = count(get_field('card', $post_id));
        $count = 0;
        $offset_count = 0;
        while (have_rows('card', $post_id)) {
            the_row();
            if ($offset_count < $start) {
                $offset_count++;
                continue;
            } ?>
            <?php if (have_rows('card_image')) {
                while (have_rows('card_image')) {
                    the_row(); ?>
                    <?php
            $image_array = array();
            $image = get_sub_field('image');
            $image_array[] = $image;
            $image_total = count($image_array);    

            if (empty($banner)) {
                $image = $image_array[0];
            }
            ?>
                    <div class="col-md-6">
                        <img style="width:500px; height : 300px; margin:20px;" loading="lazy" src="<?php echo $image ?>" alt="<?php echo the_sub_field('image') ?>">
                    </div>
            <?php }
            } ?>           
            
            <?php
            $banner_array = array();
            $banner = get_sub_field('banner');
            // $banner_array[] = $banner;
            // $banner_total = count($banner_array);    
            // if (empty($banner)) {
                // $banner = $banner_array[0];
            // }
            ?>
            <div class="col-md-12" id="banner">
                <img style=" width: 1075px;height: 300px; display:none;" src="<?php echo $banner; ?>" alt="banner">
                <!-- <img style=" width: 1075px;height: 300px; display:none;" src="<?php // echo the_sub_field('banner') 
                                                                                    ?>" alt="<?php // echo the_sub_field('banner') 
                                                                                                                                    ?>"> -->
            </div>
    <?php
            $count++;
            $offset_count++;
            if ($count == $show) {
                break;
            }
        }
    } ?>

<?php
    $content = ob_get_clean();
    $more = false;
    if ($total > $offset_count) {
        $more = true;
    }
    // wp_send_json($content);
    echo json_encode(array('content' => $content, 'more' => $more, 'offset' => $end));
    exit;
}
