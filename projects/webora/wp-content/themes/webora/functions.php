<?php
if (!defined('ABSPATH')) { exit; }
add_action('after_setup_theme', function () { add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('html5', ['search-form','gallery','caption','style','script']); });
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('webora', get_stylesheet_uri(), [], filemtime(get_template_directory() . '/style.css'));
    wp_enqueue_script('webora', get_template_directory_uri() . '/site.js', [], filemtime(get_template_directory() . '/site.js'), true);
});
function webora_contact($key) {
    $defaults = ['email'=>'sanazkhaniakbary@gmail.com', 'instagram'=>'https://www.instagram.com/webora.aii/', 'linkedin'=>'https://www.linkedin.com/in/sanaz-khaniakbary-1634952a3/', 'github'=>'https://github.com/sanazakbari-web/sanaz-khaniakbary'];
    return get_theme_mod('webora_' . $key, $defaults[$key] ?? '');
}
add_action('customize_register', function ($customizer) {
    $customizer->add_section('webora_contact', ['title'=>'Webora — Contact links', 'priority'=>30]);
    foreach (['email'=>'Email address', 'instagram'=>'Instagram URL', 'linkedin'=>'LinkedIn URL', 'github'=>'GitHub portfolio URL'] as $key=>$label) {
        $customizer->add_setting('webora_'.$key, ['default'=>webora_contact($key), 'sanitize_callback'=>$key==='email'?'sanitize_email':'esc_url_raw']);
        $customizer->add_control('webora_'.$key, ['label'=>$label, 'section'=>'webora_contact', 'type'=>$key==='email'?'email':'url']);
    }
});
function webora_icon($name) {
    $paths = [
      'instagram'=>'<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor" stroke="none"/>',
      'email'=>'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 6 9 7 9-7"/>',
      'linkedin'=>'<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 10v7M7 7v.1M11 17v-7m0 3c0-4 6-4 6 0v4"/>',
      'github'=>'<path d="M9 19c-4 1-4-2-6-2m12 5v-4c0-1-.3-1.6-1-2 3-.3 6-1.5 6-6a5 5 0 0 0-1.4-3.5A5 5 0 0 0 18.5 3S17 2.5 14 4a13 13 0 0 0-6 0C5 2.5 3.5 3 3.5 3a5 5 0 0 0-.1 3.5A5 5 0 0 0 2 10c0 4.5 3 5.7 6 6-.7.4-1 1.2-1 2v4"/>',
      'code'=>'<path d="m8 5-7 7 7 7m8-14 7 7-7 7M14 3l-4 18"/>',
    ];
    return '<svg viewBox="0 0 24 24" aria-hidden="true">'.($paths[$name]??$paths['code']).'</svg>';
}
function webora_socials() {
    echo '<div class="social-links">';
    foreach (['instagram'=>'Instagram','linkedin'=>'LinkedIn','github'=>'GitHub','email'=>'Email'] as $key=>$label) {
        $url = $key==='email'?'mailto:'.webora_contact($key):webora_contact($key);
        echo '<a class="social-link" href="'.esc_url($url).'" aria-label="'.esc_attr($label).'" title="'.esc_attr($label).'"'.($key!=='email'?' target="_blank" rel="noopener noreferrer"':'').'>'.webora_icon($key).'</a>';
    }
    echo '</div>';
}
