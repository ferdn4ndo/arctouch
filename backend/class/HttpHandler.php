<?php

/**
 * ArcTouch TMDb Interface
 * 
 * HTTP requests handler
 */
class HttpHandler {
	/**
	 * Execute a GET method request over a given URL with given parameters
	 *
	 * @param      string  $url     The url to be requested
	 * @param      array   $params  The parameters of the URL
	 *
	 * @return     string  The raw output from the curl request
	 */
	public function execGET($url, $params = []){
		#Parse URL params
		$url = $this->parseURL($url, $params);
		#Initialize a CURL session. 
		$ch = curl_init();
		#Set body (empty on GET)
		$curlBody = json_encode([]);
		#Set curl headers
		$curlHeaders = [
			'User-Agent: ' . $this->getRandAgent(), #Set the user agent with a random-selected real one
		];
		#Set CURL options
		curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders); #Set headers
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); #Retrieve output instead of displaying it
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); #Set timeout to 30 seconds
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); #Disable SSL host verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); #Disable SSL peer verification
		curl_setopt($ch, CURLOPT_URL, $url); #Set URL
		#Return raw CURL request output
		return curl_exec($ch);
	}

	/**
	 * Parse a URL with a given set o key-value parameters, replacing existing key-values and inserting new ones.
	 *
	 * @param      string  $url     The url to be parsed
	 * @param      array   $params  The parameters to be updated/inserted on URL
	 *
	 * @return     string  The parsed URL
	 */
	public function parseURL($url, $params = []){
		#Break URL into parts
		$urlParts = parse_url($url);

		#Retrieve parameters from the original query
		parse_str($urlParts['query'], $originalParams);


		#Replace/insert the values given in params
		foreach ($params as $paramTag => $paramValue) $originalParams[$paramTag] = $paramValue;

		#Update URL parts as query (will encode the values)
		$urlParts['query'] = http_build_query($originalParams);

		#Return the final URL
		return $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'] . '?' . $urlParts['query'];
	}

	/**
	 * Gets a random real user-agent.
	 * 
	 * The list was retrieved from: https://deviceatlas.com/blog/list-of-user-agent-strings
	 */
	public function getRandAgent(){
		#List with some real user agents
		$userAgents = [
			# Android Mobile User Agents
			'Mozilla/5.0 (Linux; Android 8.0.0; SM-G960F Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.84 Mobile Safari/537.36', # Samsung Galaxy S9
			'Mozilla/5.0 (Linux; Android 7.0; SM-G892A Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/60.0.3112.107 Mobile Safari/537.36', # Samsung Galaxy S8
			'Mozilla/5.0 (Linux; Android 7.0; SM-G930VC Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36', # Samsung Galaxy S7
			'Mozilla/5.0 (Linux; Android 6.0.1; SM-G935S Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36', # Samsung Galaxy S7 Edge
			'Mozilla/5.0 (Linux; Android 6.0.1; SM-G920V Build/MMB29K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.36', # Samsung Galaxy S6
			'Mozilla/5.0 (Linux; Android 5.1.1; SM-G928X Build/LMY47X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.83 Mobile Safari/537.36', # Samsung Galaxy S6 Edge Plus
			'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 6P Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.83 Mobile Safari/537.36', # Nexus 6P
			'Mozilla/5.0 (Linux; Android 7.1.1; G8231 Build/41.2.A.0.219; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/59.0.3071.125 Mobile Safari/537.36', # Sony Xperia XZ
			'Mozilla/5.0 (Linux; Android 6.0.1; E6653 Build/32.2.A.0.253) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.36', # Sony Xperia Z5
			'Mozilla/5.0 (Linux; Android 6.0; HTC One X10 Build/MRA58K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/61.0.3163.98 Mobile Safari/537.36', # HTC One X10
			'Mozilla/5.0 (Linux; Android 6.0; HTC One M9 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.3', # HTC One M9
			# iPhone User Agents
			'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1', # Apple iPhone XR (Safari)
			'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/69.0.3497.105 Mobile/15E148 Safari/605.1', # Apple iPhone XS (Chrome)
			'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/13.2b11866 Mobile/16A366 Safari/605.1.15', # Apple iPhone XS Max (Firefox)
			'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1', # Apple iPhone X
			'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.34 (KHTML, like Gecko) Version/11.0 Mobile/15A5341f Safari/604.1', # Apple iPhone 8
			'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A5370a Safari/604.1', # Apple iPhone 8 Plus
			'Mozilla/5.0 (iPhone9,3; U; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1', # Apple iPhone 7
			'Mozilla/5.0 (iPhone9,4; U; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1', # Apple iPhone 7 Plus
			'Mozilla/5.0 (Apple-iPhone7C2/1202.466; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543 Safari/419.3', # Apple iPhone 6
			# MS Windows Phone User Agents
			'Mozilla/5.0 (Windows Phone 10.0; Android 6.0.1; Microsoft; RM-1152) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Mobile Safari/537.36 Edge/15.15254', # Microsoft Lumia 650
			'Mozilla/5.0 (Windows Phone 10.0; Android 4.2.1; Microsoft; RM-1127_16056) AppleWebKit/537.36(KHTML, like Gecko) Chrome/42.0.2311.135 Mobile Safari/537.36 Edge/12.10536', # Microsoft Lumia 550
			'Mozilla/5.0 (Windows Phone 10.0; Android 4.2.1; Microsoft; Lumia 950) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2486.0 Mobile Safari/537.36 Edge/13.1058', # Microsoft Lumia 950
			# Tablet User Agents
			'Mozilla/5.0 (Linux; Android 7.0; Pixel C Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/52.0.2743.98 Safari/537.36', # Google Pixel C
			'Mozilla/5.0 (Linux; Android 6.0.1; SGP771 Build/32.2.A.0.253; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/52.0.2743.98 Safari/537.36', # Sony Xperia Z4 Tablet
			'Mozilla/5.0 (Linux; Android 6.0.1; SHIELD Tablet K1 Build/MRA58K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Safari/537.36', # Nvidia Shield Tablet K1
			'Mozilla/5.0 (Linux; Android 7.0; SM-T827R4 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.116 Safari/537.36', # Samsung Galaxy Tab S3
			'Mozilla/5.0 (Linux; Android 5.0.2; SAMSUNG SM-T550 Build/LRX22G) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/3.3 Chrome/38.0.2125.102 Safari/537.36', # Samsung Galaxy Tab A
			'Mozilla/5.0 (Linux; Android 4.4.3; KFTHWI Build/KTU84M) AppleWebKit/537.36 (KHTML, like Gecko) Silk/47.1.79 like Chrome/47.0.2526.80 Safari/537.36', # Amazon Kindle Fire HDX 7
			'Mozilla/5.0 (Linux; Android 5.0.2; LG-V410/V41020c Build/LRX22G) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/34.0.1847.118 Safari/537.36', # LG G Pad 7.0
			# Desktop User Agents
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246', # Windows 10-based PC using Edge browser
			'Mozilla/5.0 (X11; CrOS x86_64 8172.45.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.64 Safari/537.36', # Chrome OS-based laptop using Chrome browser (Chromebook)
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.9 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.9', # Mac OS X-based computer using a Safari browser
			'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36', # Windows 7-based PC using a Chrome browser
			'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1', # Linux-based PC using a Firefox browser
		];
		#Retrieve a random item from the list
		return $userAgents[mt_rand(0,count($userAgents)-1)];
	}
}
