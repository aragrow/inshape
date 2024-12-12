<?php
/*
This class is responsible for integrating with the InShape API (likely the Gemini API) to generate fitness plans based on 
user data. It also includes a method for calling a local Python script that presumably performs image analysis.
*/

/*
Purpose: Prevents direct access to the file. The check ensures that the file is only executed within the WordPress environment 
(not directly accessed via the browser).
*/
if (!defined('ABSPATH')) exit;

/*
This class is responsible for integrating with the InShape API (likely the Gemini API) to generate fitness plans based on user data.
It also includes a method for calling a local Python script that presumably performs image analysis.
*/
class WP_InShape_API_Integration {

    public function __construct() {

    }

    /* Purpose: Fetches the Gemini API URL from the WordPress options table. This URL is used when making requests to the Gemini API.
    Returns: The API URL stored in WordPress options. */
    function set_apiuri() {
        return get_option('gemini_api_url');
    }

    /* Purpose: Fetches the API key from the WordPress options table. The API key is needed for authentication when making requests to the Gemini API.
    Returns: The API key stored in WordPress options. */
    function set_apikey() {
        return get_option('gemini_api_key');
    }

    /* Purpose: Defines the prompt that will be used to guide the API to generate a fitness plan. This prompt instructs the API to generate a fitness plan in a specific format using HTML tags.
    Returns The prompt string for use in the API request payload. */
    function set_prompt_post() {
        return 
        "Perform the following steps:
            
            1.You are a fitness personal consultant.  Given the following client data, generate the BMI and a detailed fitness plan, Use natural language that feels human and relatable.
                The plan must include the original goals, monthly goals, exercises (step by step on how to do exercises), and sugested equipment to complete the execises:
            2. Structure the output using basic HTML tags:
            Use <h1> for the product title or headline.
            Use <p> for detailed paragraphs describing the item.
            Use <ul> and <li> for listing key features or benefits.
            Exclude the <html>, <head>, or <body> tags in the response.
            List all the provided data points";
    }

    /* Purpose: Sends a request to the Gemini API to generate a fitness plan based on the provided $title and $datapoints. The API response is parsed, and an answer is returned. If the request fails, an error is logged, and a WP_Error is displayed.
    Flow:
    Retrieves the API URL and key.
    Builds the request payload with the fitness plan prompt and provided data points.
    Makes the request using cURL.
    Parses the response and extracts the generated fitness plan.
    Handles errors if the API doesn't respond as expected.
    Returns: An array with the status and the generated fitness plan text, or a WP_Error in case of failure. */
    function generate_plan_description ( $title, $datapoints ) {

        error_log("Exec->generate_plan_description()");

        $api_url = $this->set_apiuri();
    
        // Set the API key from the environment variable
        $api_key = getenv('GEMINI_API_KEY');
        if (!$api_key) {
            $api_key = $this->set_apikey();
            if (!$api_key) 
                die("API_KEY eis not set.\n");
        }

        $prompt =  $this->set_prompt_post();


        $apiURI = $api_url."?key=$api_key";
    
        //error_log("apiURI : $apiURI");

        // Define the payload for the request
        $requestPayload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => "$prompt,
                            'title' => $title,
                            'data points: $datapoints"
                        ]
                    ]
                ]
            ]
        ];
    
        //error_log("requestPayload : ".print_r($requestPayload,true));

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiURI); // Replace with the correct endpoint
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestPayload),);
    
        // Disable SSL verification (for local testing only)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        // Execute the request
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            error_log("cURL Error: " . curl_error($ch));
            curl_close($ch);
            return;
        }
    
        $responseJson = json_decode($response, true);
    
        if (isset($responseJson['error'])) {
            error_log("API Error: " . $responseJson['error']['message']);
        } else {
            $item = $responseJson['candidates'][0]['content']['parts'][0]['text'];
            if (isset($item) ) {
                $anwser = $item;
            } else {
                $anwser = '';  // Handle case where resume is missing
            }
        }
    
        curl_close($ch);
        
        $anwser = [
            'status' => (isset($responseData['error']))?false:true,
            'anwser' => $anwser
        ];

        //error_log(print_r($anwser,true));

        if(empty($anwser['anwser'])) {

            // Create a WP_Error instance
            $error = new WP_Error('api_response', 'The API could not retrieve an anwser.');

            // Display the error and exit the script
            wp_die($error->get_error_message(), 'Error', [
                'response' => 502, // HTTP response code
            ]);
        
        }

        return $anwser;
    
    }

    /* Purpose: Sends a GET request to a local Python API (presumably running a Flask server) to analyze an image. The image URI is passed as a query parameter.
    Flow:
    Builds the full URL with the image URI.
    Makes the GET request to the Python API using cURL.
    Logs the response for debugging.
    Returns: The parsed JSON response from the Python API. */
    function call_python_script($uri) {
        
        error_log("Exec->call_python_script()");

        $python_api_uri = "http://127.0.0.1:5000/describe-image";
        $fullUrl = $python_api_uri . "?image=" . urlencode($uri);
        $requestPayload = [];


        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullUrl); // Replace with the correct endpoint
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_HTTPGET, true); // Specify GET method (optional)
        //curl_setopt($ch, CURLOPT_POST, true); // Specify GET method (optional)
        //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestPayload),);

        // Disable SSL verification (for local testing only)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute the request
        $response = curl_exec($ch);
        error_log('------ Response --------');
        error_log($response);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            error_log("cURL Error: " . curl_error($ch));
            curl_close($ch);
            return;
        }

        $responseJson = json_decode($response, true);

        return $responseJson;
    }

}