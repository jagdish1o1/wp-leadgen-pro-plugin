<div id="all-services">
    <?php foreach ($service_providers as $provider_id): ?>

        <div class="service">
            <div class="service-image">
                <img src="<?php echo the_field('image', $provider_id); ?>"
                    alt="<?php echo the_field('name', $provider_id); ?>" lazy>
            </div>
            <div class="service-content">
                <a href="<?php echo esc_url(get_permalink($provider_id)); ?>">
                    <h4><?php echo the_field('name', $provider_id); ?></h4>
                </a>
                <address><?php echo the_field('address', $provider_id); ?></address>
                <rating>
                    Rating:
                    <?php echo the_field('rating', $provider_id); ?>
                    <?php echo generate_star_rating(get_field('rating', $provider_id)); ?>
                </rating>
            </div>
            <div class="service-cta">
                <a href="tel:+<?php echo esc_attr(LGP_SettingsPage::get_custom_option('phone_number')); ?>" class="button">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="white"
                        version="1.1" id="Capa_1" width="20px" height="20px" viewBox="0 0 342.514 342.514"
                        xml:space="preserve">
                        <path
                            d="M171.254,0C76.826,0,0,76.825,0,171.26c0,94.434,76.819,171.254,171.254,171.254c94.434,0,171.26-76.82,171.26-171.254   C342.514,76.825,265.682,0,171.254,0z M241.225,137.474l-18.76,18.777c-4.383,4.377-11.444,4.377-15.816,0l-6.737-6.731   c-0.606-0.609-1.351-0.937-2.132-1.147c-6.209-4.603-14.994-4.155-20.614,1.459l-27.322,27.322   c-5.632,5.62-6.083,14.417-1.465,20.614c0.21,0.781,0.544,1.52,1.138,2.132l6.752,6.737c4.359,4.371,4.371,11.445,0,15.805   l-18.771,18.783c-4.387,4.371-11.445,4.371-15.817,0l-13.478-13.487c-16.727-16.705-16.714-43.907,0-60.621l58.925-58.919   c16.715-16.708,43.91-16.708,60.622,0l13.475,13.472C245.596,126.046,245.596,133.111,241.225,137.474z">
                        </path>
                    </svg>
                    Call Now
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>