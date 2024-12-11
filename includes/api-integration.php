<?php
if (!defined('ABSPATH')) exit;

class WP_InShape_API_Integration {

    public function __construct() {

    }

    function set_apiuri() {
        return get_option('gemini_api_url');
    }

    function set_apikey() {
        return get_option('gemini_api_key');
    }

    function set_prompt_post() {
        return 
        "Perform the following steps:
            
            1.You are a fitness personal consultant.  Given the following client data, generate the BMI and a detailed fitness plan, Use natural language that feels human and relatable.
                The plan must include the original goals, monthly goals, exercises (step by step on how to do exercises), and sugested equipment to complete the execises:
            2. Structure the output using basic HTML tags:
            Use <h1> for the product title or headline.
            Use <p> for detailed paragraphs describing the item.
            Use <ul> and <li> for listing key features or benefits.
            Exclude the <html>, <head>, or <body> tags in the response.";
    }

    function generate_plan_description ( $title, $attributes ) {

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
                            'inputs: $attributes"
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