<style>
    .all-cities {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin: 20px 0px;
        width: 100%;
    }

    .cities {
        display: flex;
        flex-direction: row;
        gap: 10px;
        flex-wrap: wrap;
        height: 100px;
        overflow: scroll;
        align-items: center;
        justify-content: center;
    }

    a.city-name.button {
        text-decoration: none;
    }
</style>
<div class="all-cities">
    <input type="text" name="city-search" id="city-search" placeholder="Search your city here" />
    <div class="cities">
        <?php foreach ($cities as $city): ?>
            <a href="<?php echo esc_url(get_permalink($city)); ?>/" class="city-name button"
                target="_blank"><?php the_field('city_name', $city); ?></a>
        <?php endforeach; ?>
    </div>
</div>
<script>
    document.getElementById('city-search').addEventListener('input', function (e) { var searchValue = e.target.value.toLowerCase(); var cities = document.querySelectorAll('.city-name'); cities.forEach(function (city) { var cityName = city.textContent.toLowerCase(); if (cityName.includes(searchValue)) { city.style.display = '' } else { city.style.display = 'none' } }) })
</script>