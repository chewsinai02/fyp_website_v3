<?php

if (!function_exists('get_profile_image_url')) {
    function get_profile_image_url($path) {
        if (empty($path)) {
            return asset('images/profile.png');
        }
        
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        return asset('storage/' . ltrim($path, '/'));
    }
} 
  