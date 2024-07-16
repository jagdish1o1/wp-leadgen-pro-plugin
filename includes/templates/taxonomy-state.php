<?php
/**
 * Template for displaying state taxonomy archive.
 *
 * This template adapts to the current theme's design automatically.
 */

get_header();

$term = get_queried_object();
$service_args = array(
    'post_type' => 'services',
    'tax_query' => array(
        array(
            'taxonomy' => 'state',
            'field' => 'term_id',
            'terms' => $term->term_id,
        ),
    ),
    'meta_query' => array(
        array(
            'key' => 'rating',
            'value' => 5,
            'compare' => '='
        )
    ),
    'orderby' => 'title',
    'order' => 'ASC',
    'posts_per_page' => 12,
);
$services = get_posts($service_args);

$cities_args = array(
    'post_type' => 'listings',
    'tax_query' => array(
        array(
            'taxonomy' => 'state',
            'field' => 'term_id',
            'terms' => $term->term_id
        ),
    ),
    'meta_query' => array(
        array(
            'key' => 'city_name',
            'compare' => 'EXISTS'
        )
    ),
    'orderby' => 'meta_value',
    'meta_key' => 'city_name',
    'order' => 'ASC',
    'posts_per_page' => -1
);

$cities = get_posts($cities_args);


?>
<div class="state-container">

    <div class="state-box">
        <div class="services">
            <h1><?php echo $term->name; ?></h1>
            <?php if (is_array($cities) && count($cities) > 0): ?>

                <input type="text" name="city-search" id="city-search" placeholder="Search your city here" />
                <div class="cities">
                    <?php foreach ($cities as $city): ?>
                            <a href="<?php echo home_url(get_field('custom_uri', $city->ID)); ?>/"
                                class="city-name button" target="_blank"><?php echo the_field('city_name', $city->ID); ?></a>
                    <?php endforeach; ?>
                </div>

                <script>
                    document.getElementById('city-search').addEventListener('input', function (e) { var searchValue = e.target.value.toLowerCase(); var cities = document.querySelectorAll('.city-name'); cities.forEach(function (city) { var cityName = city.textContent.toLowerCase(); if (cityName.includes(searchValue)) { city.style.display = '' } else { city.style.display = 'none' } }) })
                </script>

            <?php endif; ?>

            <?php if (is_array($services) && count($services) > 0): ?>
                <h2>5-Start Rated Businesses in <?php echo $term->name; ?></h2>
                <div class="businesses">
                    <?php foreach ($services as $service): ?>
                        <div class="single-business">
                            <div class="single-business-image">

                                <a href="<?php echo get_permalink($service); ?>">
                                    <img src="<?php the_field('image', $service->ID); ?>"
                                        alt="<?php the_field('name', $service->ID); ?>" lazy />
                                </a>
                            </div>
                            <div class="single-business-content">
                                <a href="<?php echo get_permalink($service); ?>">
                                    <h3><?php the_field('name', $service->ID); ?></h3>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
get_footer();
