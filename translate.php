<?php
// Set the content type to JSON for proper response handling
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input data from the request
    $input = json_decode(file_get_contents('php://input'), true);
    $text = $input['text'];
    $targetLanguage = $input['targetLanguage'];

    // Your Google Translate API Key
    // Replace 'YOUR_API_KEY' with your actual API Key
    $apiKey = 'AIzaSyC0V46p7QqvZkGW2Pfl0_kwAYShkT7Zb6A';

    // Google Translate API URL
    $url = 'https://translation.googleapis.com/language/translate/v2?key=' . $apiKey;

    // Data to be sent in the API request
    $data = array(
        'q' => $text,
        'target' => $targetLanguage
    );

    // HTTP context options for the API request
    $options = array(
        'http' => array(
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ),
    );

    // Create and execute the context for the HTTP request
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    // Check if the API request was successful
    if ($result === FALSE) {
        // Return an error message if the request failed
        echo json_encode(['error' => 'Translation API request failed']);
        exit;
    }

    // Decode the JSON response from the API
    $response = json_decode($result, true);

    // Check if the expected data exists in the response
    if (isset($response['data']['translations'][0]['translatedText'])) {
        // Return the translated text
        echo json_encode(['translatedText' => $response['data']['translations'][0]['translatedText']]);
    } else {
        // Return an error message if the response structure is not as expected
        echo json_encode(['error' => 'Invalid response structure from Google Translate']);
    }
} else {
    // Return an error message if the request method is not POST
    echo json_encode(['error' => 'Invalid request method']);
}
?>

