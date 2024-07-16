<div style="display: flex;flex-wrap:wrap;gap:5px;">
    <?php foreach ($cities as $city): ?>
        <a class="button"
            href="<?php echo get_permalink($city->ID); ?>"><?php echo the_field('city_name', $city->ID); ?></a>
    <?php endforeach; ?>
</div>