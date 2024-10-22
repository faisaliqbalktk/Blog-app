<?php

function uploadImage()
{
    $imagename = $_FILES['image']['name'];
    $imagetmp = $_FILES['image']['tmp_name'];

    // Allowed file extensions
    $allowed = array('jpeg', 'png', 'jpg', 'webp');

    // Get the file extension of the uploaded image
    $ext = pathinfo($imagename, PATHINFO_EXTENSION);

    // Check if the uploaded file has an allowed extension
    if (in_array($ext, $allowed)) {
        // Move the uploaded file to the "images" folder
        $target = "images/" . $imagename;
        if (move_uploaded_file($imagetmp, $target)) {
            return $imagename; // Return the image name to be saved in the database
        } else {
            echo "Failed to upload image.";
            return false;
        }
    } else {
        echo "Only png, jpg, jpeg, and webp formats are allowed.";
        return false;
    }
}

function createSlug($string)
{
    // Moved the hyphen to the end of the character class to avoid range issues
    $slug = preg_replace('/[^A-Za-z0-9]/', '-', $string); 
    return strtolower(trim($slug, '-')); // Optional: convert to lowercase and remove trailing hyphens
}
