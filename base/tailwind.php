<?php

header("Content-type: text/css; charset: UTF-8");

function loadCSS($url) {
    $css = file_get_contents($url);
    return $css;
}

$tailwind_url = "https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.19/dist/tailwind.min.css";
$bootstrap_url = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css";

$tailwind_css = loadCSS($tailwind_url);
$bootstrap_css = loadCSS($bootstrap_url);

// Font Awesome CSS link
$font_awesome_css = '<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">';

$combined_css = $tailwind_css . "\n" . $bootstrap_css . "\n" . $font_awesome_css;

echo $combined_css;
?>
