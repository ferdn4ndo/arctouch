<?php

/**
 * ArcTouch TMDb Interface
 * 
 * Main endpoint requests handler
 */
class EndpointHandler {
	private $endpointURL = ""; #Used to store current endpoint URL
	private $apiKey = ""; #Used to store API key
	private $requestLang = ""; #Used to store language for requests

	#List of available endpoints
	public $availableEndpoints = [
		"upcoming", #This endpoint retrieves a list with the first 20 entries (basic data) of the upcoming movies
		"upcoming/{page}", #This endpoint retrieves a list with the 20 entries (basic data) of the upcoming movies, starting from {page} (which is a zero-based increasing number)
		"movie/{id}", #This endpoint retrieves the full data of a given movie ID (positive integer)
		"search/{terms}", #This endpoint retrieves a list with the first 20 entries (basic data) of the upcoming movies (based on a given search term, which is a string)
		"search/{terms}/{page}", #This endpoint retrieves a list with the 20 entries (basic data) of the upcoming movies (based on a given search term, which is a string), starting from {page} (which is a zero-based increasing number)
	];
	
	/**
	 * Main class constructor
	 *
	 * @param      string  $endpointURL  The endpoint URL
	 * @param      string  $apiKey       The TMDb API key
	 * @param      string  $lang         The ISO 639-1 language used on requests
	 */
	function __construct($endpointURL = "", $apiKey = "", $lang = "en-US"){
		$this->endpointURL = $this->sanitizeURL($endpointURL);
		$this->apiKey = $apiKey;
		$this->requestLang = $lang;
	}

	/**
	 * Sanitize and enpoint URL by trimming it from space-like characters on
	 * both sides and also stripping (possible multiple) last directory
	 * separator on URL (for endpoint string match)
	 *
	 * @param      string  $endpointURL  The original endpoint URL
	 *
	 * @return     string  The sanitized endpoint URL
	 */
	public function sanitizeURL($endpointURL = ""){
		$endpointURL = trim($endpointURL);
		$endpointURL = rtrim($endpointURL, "/");
		return $endpointURL;
	}

	/**
	 * Proccess current endpoint and return its result
	 *
	 * @return     array  The bypassed result of the command or an error array with a message
	 */
	public function proccess(){
		#Get endpoint params
		$endpointParams = $this->getEndpointParams();
		#Check if endpoint was read
		if($endpointParams == []) return ["success" => 0, "msg" => "The requested endpoint ({$this->endpointURL}) didn't match any expected command. Please give a check on <a href='index.php'>index.php</a> for a list of available endpoints."];
		#Get current command (the first part of endpoint) and also removes it from the arguments array
		$currentCommand = array_shift($endpointParams);
		#Execute command and return output
		switch ($currentCommand) {
			#List upcoming movies
			case 'upcoming': return $this->retrieveUpcomingMovies($endpointParams);
			#Get movie info
			case 'movie': return $this->retrieveMovie($endpointParams);
			#Search for movies using terms
			case 'search': return $this->retrieveSearch($endpointParams);
			#Fallback
			default: return ["success" => 0, "msg" => "The command '{$currentCommand}' is unknown! Please give a check on <a href='index.php'>index.php</a> for a list of available endpoints."];
		}
	}

	/**
	 * Gets current endpoint parameters.
	 *
	 * @return     array  an key-value array with the parameters (if found) or an empty array if endpoint is unknown.
	 */
	public function getEndpointParams(){
		#Explode current endpoint into parts
		$currentEndpointParts = explode("/", $this->endpointURL);
		#Iterate
		foreach ($this->availableEndpoints as $endpointItem) {
			#Explode the possible endpoint match
			$itemParts = explode("/", $endpointItem);
			#Check if count parts match, skip otherwise
			if(count($currentEndpointParts) != count($itemParts)) continue;
			#Prepare parts match flag
			$partsMatched = true;
			#Prepare resulting associative array
			$returnArray = [];
			#Check for each part
			foreach ($itemParts as $partIDX => $partTag) {
				#Flag to check if part is argument
				$partIsArg = preg_match("/^{[a-zA-Z0-9_-]+}$/", $partTag);
				#Update flag
				$partsMatched = $partsMatched && ($partIsArg ? 1 : ($partTag == $currentEndpointParts[$partIDX]));
				#Skip next checks if didn't matched
				if(!$partsMatched) break;
				#Insert into result
				$returnArray[str_replace(["{","}"], "", $partTag)] = $currentEndpointParts[$partIDX];
			}
			#If matched return item data (an associative array with the named endpoint parts being the keys and the requested endpoint parts being the values)
			if($partsMatched) return $returnArray;
		}
		#Fallback return empty
		return [];
	}

	/**
	 * Retrieves upcoming movies from TMDb.
	 *
	 * A future improvement could consist on error checking from a valid TMDb
	 * response, for better handling in frontend. Another future improvement
	 * could consist on page number validation, based on "total_pages" result
	 * key. A language selection based on user data could also be done.
	 *
	 * @param      array  $args   The arguments array (for page setting)
	 */
	public function retrieveUpcomingMovies($args=[]){
		#Page argument: if not set, fallback to 1 (default)
		$page = $args["page"] ?? 1;
		#Define the URL based on api documentation (https://developers.themoviedb.org/3/movies/get-upcoming)
		$url = "https://api.themoviedb.org/3/movie/upcoming?api_key={$this->apiKey}&language={$this->requestLang}&page={$page}";
		#Instantiate a new HTTP handler class
		$HttpHandler = new HttpHandler();
		#Retrieve raw output from the HTTP handler over the URL
		$outputRaw = $HttpHandler->execGET($url);
		#Bypass the consumed output
		return $this->consumeRawOutput($outputRaw);
	}

	/**
	 * Retrieves information from a given movie ID from TMDb.
	 *
	 * A future improvement could consist on error checking from a valid TMDb
	 * response, for better handling in frontend. A language selection based on
	 * user data could also be done.
	 *
	 * @param      array  $args   The arguments array (for ID setting)
	 *
	 * @return     array  The result from comsumeRawOuput if success, an error array if fail
	 */
	public function retrieveMovie($args=[]){
		#ID argument: if not set, fallback to 0
		$id = $args["id"] ?? 0;
		#If no ID given, return error
		if(!$id) return ["success" => 0, "msg" => "Empty response from TMDb"];
		#Define the URL based on api documentation (https://developers.themoviedb.org/3/movies/get-movie-details)
		$url = "https://api.themoviedb.org/3/movie/{$id}?api_key={$this->apiKey}&language={$this->requestLang}";
		#Instantiate a new HTTP handler class
		$HttpHandler = new HttpHandler();
		#Retrieve raw output from the HTTP handler over the URL
		$outputRaw = $HttpHandler->execGET($url);
		#Bypass the consumed output
		return $this->consumeRawOutput($outputRaw);
	}

	/**
	 * Retrieves the search results from TMDb based on a given term.
	 *
	 * A future improvement could consist on error checking from a valid TMDb
	 * response, for better handling in frontend. Another future improvement
	 * could consist on page number validation, based on "total_pages" result
	 * key. An adult filter based on user data from backend could also be done,
	 * as language selection for results.
	 *
	 * @param      array  $args   The arguments array (for ID setting)
	 *
	 * @return     array  The result from comsumeRawOuput if success, an error array if fail
	 */
	public function retrieveSearch($args=[]){
		#Page argument: if not set, fallback to 1 (default)
		$page = $args["page"] ?? 1;
		#Search terms argument: if not set, fallback to empty string
		$terms = $args["terms"] ?? '';
		#If no term given, return error
		if(!$terms) return ["success" => 0, "msg" => "Empty search term"];
		#Prepare terms (URI-encoded)
		$terms = urlencode($terms);
		#Define the URL based on api documentation (https://developers.themoviedb.org/3/search/search-movies)
		$url = "https://api.themoviedb.org/3/search/movie?api_key={$this->apiKey}&language={$this->requestLang}&page={$page}&include_adult=true&query={$terms}";
		#Instantiate a new HTTP handler class
		$HttpHandler = new HttpHandler();
		#Retrieve raw output from the HTTP handler over the URL
		$outputRaw = $HttpHandler->execGET($url);
		#Bypass the consumed output
		return $this->consumeRawOutput($outputRaw);
	}

	/**
	 * Proccess the raw ouput from a HTTP request over TMDb into an associative
	 * array read as JSON.
	 *
	 * @param      string  $outputRaw  The raw output from the HTTP request
	 *
	 * @return     array   An succes array with the data or an error array with a message
	 */
	public function consumeRawOutput($outputRaw=''){
		#If for some unexpected reason it's an empty response, return error message (for future improvement: log the url as it can be an functional error)
		if(!strlen($outputRaw)) return ["success" => 0, "msg" => "Empty response from TMDb"];
		#If for some unexpected reason it's not a JSON response, return error message (for future improvement: log the url and the output as it can be an functional error)
		if((substr($outputRaw,0,1)!='{') && (substr($outputRaw,0,1)!='[')) return ["success" => 0, "msg" => "Not a JSON response from TMDb"];
		#Decode the raw JSON output into an associative array
		$outputData = json_decode($outputRaw,TRUE);
		#If there is an error on JSON parsing, return error message (for future improvement: log the url and the output as it can be an functional error)
		if(json_last_error() != JSON_ERROR_NONE) return ["success" => 0, "msg" => "Not a valid JSON response from TMDb (Error ".json_last_error().")"];
		#Return the data
		return ["success" => 1, "data" => $outputData];
	}
}