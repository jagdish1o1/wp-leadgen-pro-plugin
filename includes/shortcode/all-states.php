<div style="display: flex;flex-wrap:wrap;gap:5px;">
    <?php foreach ($terms as $term): ?>
        <a class="button" href="<?php echo get_term_link($term->term_id); ?>"><?php echo $term->name; ?></a>
    <?php endforeach; ?>
</div>