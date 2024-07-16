<?php
function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}


function generate_star_rating($rating)
{
    // Define the SVG code for an empty star, a half-filled star, and a filled star
    $empty_star = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="grey" stroke="grey" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.81l2.94 6.658 6.573.575-4.743 4.488 1.419 6.575-5.643-3.246-5.643 3.246 1.419-6.575-4.743-4.488 6.573-.575z"/></svg>';
    $half_star = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FFD700" stroke="#FFD700" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19.539 9.455l-5.514-.479-1.974-4.488L10.92 8.976l-5.272.458 4.02 3.807-1.021 4.716L12 16.165v6.09l2.548-1.508 6.033-3.567-1.021-4.716 4.02-3.807-5.273-.458z"/></svg>';
    $filled_star = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FFD700" stroke="#FFD700" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.81l2.94 6.658 6.573.575-4.743 4.488 1.419 6.575-5.643-3.246-5.643 3.246 1.419-6.575-4.743-4.488 6.573-.575z"/></svg>';

    // Define the HTML code for the star rating
    $stars_html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating - 0.5) {
            $stars_html .= $filled_star;
        } else if ($i <= $rating) {
            $stars_html .= $half_star;
        } else {
            $stars_html .= $empty_star;
        }
    }

    // Return the HTML code for the star rating
    return $stars_html;
}