<?php
require_once('vendor/autoload.php');

use Vimeo\Exceptions\VimeoRequestException;
use Vimeo\Exceptions\VimeoUploadException;
use Vimeo\Vimeo;

$config = [
    'client_id' => '9ab98139191a7852da6dd7d6fa2919d6117b0ab0',
    'client_secret' => 'wz1uWN/pC2kczXo8Ruy5YwHMErEHVgESvzo0Cc8Hewgv+xRzg6ThvSM9iklQDKQ25bemK4WMuGSrYvM+dtIZU917ULE0lha/cfcA72QA4jc25zcFlFUmaEL18UGmsCAp',
    'access_token' => '9a3a27f6c50f3f2f70770b2e0ebb2fd4'
];

$lib = new Vimeo($config['client_id'], $config['client_secret']);

if (!empty($config['access_token'])) {
    $lib->setToken($config['access_token']);
    $user = $lib->request('/me');
} else {
    $user = $lib->request('/users/dashron');
}

$file_name = 'sample_video.mp4';

try {
    // Upload the file and include the video title and description.
    $uri = $lib->upload($file_name, array(
        'name' => 'Vimeo API SDK test upload',
        'description' => "This video was uploaded through the Vimeo API's PHP SDK."
    ));

    // Get the metadata response from the upload and log out the Vimeo.com url
    $video_data = $lib->request($uri . '?fields=link');
    echo '"' . $file_name . ' has been uploaded to ' . $video_data['body']['link'] . "\n";

    // Make an API call to edit the title and description of the video.
    $lib->request($uri, array(
        'name' => 'Vimeo API SDK test edit',
        'description' => "This video was edited through the Vimeo API's PHP SDK.",
    ), 'PATCH');

    echo 'The title and description for ' . $uri . ' has been edited.' . "\n";

    // Make an API call to see if the video is finished transcoding.
    $video_data = $lib->request($uri . '?fields=transcode.status');
    echo 'The transcode status for ' . $uri . ' is: ' . $video_data['body']['transcode']['status'] . "\n";
} catch (VimeoUploadException $e) {
    // We may have had an error. We can't resolve it here necessarily, so report it to the user.
    echo 'Error uploading ' . $file_name . "\n";
    echo 'Server reported: ' . $e->getMessage() . "\n";
} catch (VimeoRequestException $e) {
    echo 'There was an error making the request.' . "\n";
    echo 'Server reported: ' . $e->getMessage() . "\n";
}

// echo '<pre>';

// print_r($user);
