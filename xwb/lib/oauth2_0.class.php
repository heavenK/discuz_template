<?php
/*
 
 */
  
$GLOBALS['__CLASS']['ns_OAuthRequest']['__STATIC'] = array(
'POST_INPUT' => 'php://input',
'boundary'=>''
);

if (! function_exists ( '___throwException' )) {
	function ___throwException($str) {
		trigger_error ( $str, 256 );
	}
}

/**
 * @ignore
 */
class ns_OAuthRequest {
    var $parameters; 
    var $http_method; 
    var $http_url; 
    // for debug purposes 
    var $base_string; 
    var $key_string;
   
    function __construct($http_method, $http_url, $parameters=NULL) {
        $this->ns_OAuthRequest($http_method, $http_url, $parameters);
    }

    function ns_OAuthRequest($http_method, $http_url, $parameters=NULL) { 
        @$parameters or $parameters = array(); 
        $this->parameters = $parameters; 
        $this->http_method = $http_method; 
        $this->http_url = $http_url; 
    }


    /**
     * attempt to build up a request from what was passed to the server
     */
    function from_request($http_method=NULL, $http_url=NULL, $parameters=NULL) {
        $scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on")
            ? 'http'
            : 'https';
        @$http_url or $http_url = $scheme .
            '://' . $_SERVER['HTTP_HOST'] .
            ':' .
            $_SERVER['SERVER_PORT'] .
            $_SERVER['REQUEST_URI'];
        @$http_method or $http_method = $_SERVER['REQUEST_METHOD'];

        // We weren't handed any parameters, so let's find the ones relevant to
        // this request.
        // If you run XML-RPC or similar you should use this to provide your own
        // parsed parameter-list
        if (!$parameters) {
            // Find request headers
            $request_headers = ns_OAuthUtil::get_headers();

            // Parse the query-string to find GET parameters
            $parameters = ns_OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']);

            // It's a POST request of the proper content-type, so parse POST
            // parameters and add those overriding any duplicates from GET
            if ($http_method == "POST"
                && @strstr($request_headers["Content-Type"],
                    "application/x-www-form-urlencoded")
            ) {
                $post_data = ns_OAuthUtil::parse_parameters(
                    file_get_contents($GLOBALS['__CLASS']['ns_OAuthRequest']['__STATIC']['POST_INPUT']) 
                );
                $parameters = array_merge($parameters, $post_data);
            }

            // We have a Authorization-header with OAuth data. Parse the header
            // and add those overriding any duplicates from GET or POST
            if (@substr($request_headers['Authorization'], 0, 6) == "OAuth ") {
                $header_parameters = ns_OAuthUtil::split_header(
                    $request_headers['Authorization']
                );
                $parameters = array_merge($parameters, $header_parameters);
            }

        }

        return new ns_OAuthRequest($http_method, $http_url, $parameters);
    }

    /**
     * pretty much a helper function to set up the request
     */
    function from_consumer_and_token( $token, $http_method, $http_url, $parameters=NULL) {
        @$parameters or $parameters = array();
        $defaults = array();
      	if ($token)
        	$defaults['access_token'] = $token;
      	$parameters = array_merge($defaults, $parameters); 
        return new ns_OAuthRequest($http_method, $http_url, $parameters);
    }

    function set_parameter($name, $value, $allow_duplicates = true) {
        if ($allow_duplicates && isset($this->parameters[$name])) {
            // We have already added parameter(s) with this name, so add to the list
            if (is_scalar($this->parameters[$name])) {
                // This is the first duplicate, so transform scalar (string)
                // into an array so we can add the duplicates
                $this->parameters[$name] = array($this->parameters[$name]);
            }

            $this->parameters[$name][] = $value;
        } else {
            $this->parameters[$name] = $value;
        }
    }

    function get_parameter($name) {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    function get_parameters() {
        return $this->parameters;
    }

    function unset_parameter($name) {
        unset($this->parameters[$name]);
    }

    /**
     * The request parameters, sorted and concatenated into a normalized string.
     * @return string
     */
    function get_signable_parameters() {
        // Grab all parameters
        $params = $this->parameters;

        // remove pic
        if (isset($params['pic'])) {
            unset($params['pic']);
        }

        if (isset($params['image'])) {
            unset($params['image']);
        }

        return ns_OAuthUtil::build_http_query($params);
    }

    /**
     * Returns the base string of this request
     *
     * The base string defined as the method, the url
     * and the parameters (normalized), each urlencoded
     * and the concated with &.
     */
    function get_signature_base_string() {
        $parts = array(
            $this->get_normalized_http_method(),
            $this->get_normalized_http_url(),
            $this->get_signable_parameters()
        );

        //print_r( $parts );

        $parts = ns_OAuthUtil::urlencode_rfc3986($parts);

        return implode('&', $parts);
    }

    /**
     * just uppercases the http method
     */
    function get_normalized_http_method() {
        return strtoupper($this->http_method);
    }

    /**
     * parses the url and rebuilds it to be
     * scheme://host/path
     */
    function get_normalized_http_url() {
        $parts = parse_url($this->http_url);
        
        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $path = @$parts['path'];
        
        if(isset($parts['port']) && $parts['port']){
        	$port = $parts['port'];
        }else{
        	$port = ($scheme == 'https') ? '443' : '80';
        }

        if (($scheme == 'https' && $port != '443')
            || ($scheme == 'http' && $port != '80')) {
                $host = "$host:$port";
            } 
        return "$scheme://$host$path";
    }

    /**
     * builds a url usable for a GET request
     */
    function to_url() {
        $post_data = $this->to_postdata();
        $out = $this->get_normalized_http_url();
        if ($post_data) {
            $out .= '?'.$post_data;
        }
        return $out;
    }

    /**
     * builds the data one would send in a POST request
     */
    function to_postdata( $multi = false ) {
	    //echo "multi=" . $multi . '`';
	    if( $multi )
	    	return ns_OAuthUtil::build_http_query_multi($this->parameters);
	    else
	        return ns_OAuthUtil::build_http_query($this->parameters);
    }

    /**
     * builds the Authorization: header
     */
    function to_header() {
        $out ='Authorization: OAuth realm=""';
        $total = array();
        foreach ($this->parameters as $k => $v) {
            if (substr($k, 0, 5) != "oauth") continue;
            if (is_array($v)) {
                ___throwException('Arrays not supported in headers');
            }
            $out .= ',' .
                ns_OAuthUtil::urlencode_rfc3986($k) .
                '="' .
                ns_OAuthUtil::urlencode_rfc3986($v) .
                '"';
        }
        return $out;
    }

    function __toString() {
        return $this->to_url();
    }
 

    /**
     * util function: current timestamp
     */
    function generate_timestamp() {
        //return 1273566716;
		return time();
    }

    /**
     * util function: current nonce
     */
    function generate_nonce() {
        //return '462d316f6f40c40a9e0eef1b009f37fa';
		$mt = microtime();
        $rand = mt_rand();

        return md5($mt . $rand); // md5s look nicer than numbers
    }
}
 
 

/**
 * @ignore
 */
class ns_OAuthUtil {

	//public static $boundary = '';

    function urlencode_rfc3986($input) {
        if (is_array($input)) {
            return array_map(array('ns_OAuthUtil', 'urlencode_rfc3986'), $input);
        } else if (is_scalar($input)) {
            return str_replace(
                '+',
                ' ',
                str_replace('%7E', '~', rawurlencode($input))
            );
        } else {
            return '';
        }
    }


    // This decode function isn't taking into consideration the above
    // modifications to the encoding process. However, this method doesn't
    // seem to be used anywhere so leaving it as is.
    function urldecode_rfc3986($string) {
        return urldecode($string);
    }

    // Utility function for turning the Authorization: header into
    // parameters, has to do some unescaping
    // Can filter out any non-oauth parameters if needed (default behaviour)
    function split_header($header, $only_allow_oauth_parameters = true) {
        $pattern = '/(([-_a-z]*)=("([^"]*)"|([^,]*)),?)/';
        $offset = 0;
        $params = array();
        while (preg_match($pattern, $header, $matches, PREG_OFFSET_CAPTURE, $offset) > 0) {
            $match = $matches[0];
            $header_name = $matches[2][0];
            $header_content = (isset($matches[5])) ? $matches[5][0] : $matches[4][0];
            if (preg_match('/^oauth_/', $header_name) || !$only_allow_oauth_parameters) {
                $params[$header_name] = ns_OAuthUtil::urldecode_rfc3986($header_content);
            }
            $offset = $match[1] + strlen($match[0]);
        }

        if (isset($params['realm'])) {
            unset($params['realm']);
        }

        return $params;
    }

    // helper to try to sort out headers for people who aren't running apache
    function get_headers() {
        if (function_exists('apache_request_headers')) {
            // we need this to get the actual Authorization: header
            // because apache tends to tell us it doesn't exist
            return apache_request_headers();
        }
        // otherwise we don't have apache and are just going to have to hope
        // that $_SERVER actually contains what we need
        $out = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) == "HTTP_") {
                // this is chaos, basically it is just there to capitalize the first
                // letter of every word that is not an initial HTTP and strip HTTP
                // code from przemek
                $key = str_replace(
                    " ",
                    "-",
                    ucwords(strtolower(str_replace("_", " ", substr($key, 5))))
                );
                $out[$key] = $value;
            }
        }
        return $out;
    }

    // This function takes a input like a=b&a=c&d=e and returns the parsed
    // parameters like this
    // array('a' => array('b','c'), 'd' => 'e')
    function parse_parameters( $input ) {
        if (!isset($input) || !$input) return array();

        $pairs = explode('&', $input);

        $parsed_parameters = array();
        foreach ($pairs as $pair) {
            $split = explode('=', $pair, 2);
            $parameter = ns_OAuthUtil::urldecode_rfc3986($split[0]);
            $value = isset($split[1]) ? ns_OAuthUtil::urldecode_rfc3986($split[1]) : '';

            if (isset($parsed_parameters[$parameter])) {
                // We have already recieved parameter(s) with this name, so add to the list
                // of parameters with this name

                if (is_scalar($parsed_parameters[$parameter])) {
                    // This is the first duplicate, so transform scalar (string) into an array
                    // so we can add the duplicates
                    $parsed_parameters[$parameter] = array($parsed_parameters[$parameter]);
                }

                $parsed_parameters[$parameter][] = $value;
            } else {
                $parsed_parameters[$parameter] = $value;
            }
        }
        return $parsed_parameters;
    }

    function build_http_query_multi($params) {
        if (!$params) return '';

		//print_r( $params );
		//return null;

        // Urlencode both keys and values
        $keys = array_keys($params);
        $values = array_values($params);
        //$keys = OAuthUtil::urlencode_rfc3986(array_keys($params));
        //$values = OAuthUtil::urlencode_rfc3986(array_values($params));
        $params = array_combine($keys, $values);

        // Parameters are sorted by name, using lexicographical byte value ordering.
        // Ref: Spec: 9.1.1 (1)
        uksort($params, 'strcmp');

        $pairs = array();

        $GLOBALS['__CLASS']['ns_OAuthRequest']['__STATIC']['boundary'] = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

        foreach ($params as $parameter => $value) {
			if( ($parameter == 'pic' || $parameter == 'image') && $value{0} == '@' ){
				$url = ltrim( $value , '@' );
				
				//超时控制
				$ctx_userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; .NET CLR 3.5.30729)';
				if( version_compare(PHP_VERSION, '5.0.0', '>=') ){
					$ctx_header = "Accept: */*\r\nAccept-Language: zh-cn\r\nUser-Agent: {$ctx_userAgent}\r\n";
					$ctx = stream_context_create(array('http'=>array('timeout'=>8,'method'=>'GET','header'=>$ctx_header)));
					$content = file_get_contents( $url, 0, $ctx);
				}else{
					@ini_set('user_agent', $ctx_userAgent);
					$content = file_get_contents( $url );
				}
				
				$filename = reset( explode( '?' , basename( $url ) ));
				$mime = ns_OAuthUtil::get_image_mime($url);
	
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="'.$parameter.'"; filename="' . $filename . '"'. "\r\n";
				$multipartbody .= 'Content-Type: '. $mime . "\r\n\r\n";
				$multipartbody .= $content. "\r\n";
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="'.$parameter."\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
	
			}
        }

        $multipartbody .=  $endMPboundary;
        // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
        // Each name-value pair is separated by an '&' character (ASCII code 38)
        // echo $multipartbody;
        return $multipartbody;
    }

    function build_http_query($params) {
        if (!$params) return '';

        // Urlencode both keys and values
        $keys = ns_OAuthUtil::urlencode_rfc3986(array_keys($params));
        $values = ns_OAuthUtil::urlencode_rfc3986(array_values($params));
        $params = array_combine($keys, $values);

        // Parameters are sorted by name, using lexicographical byte value ordering.
        // Ref: Spec: 9.1.1 (1)
        uksort($params, 'strcmp');

        $pairs = array();
        foreach ($params as $parameter => $value) {
            if (is_array($value)) {
                // If two or more parameters share the same name, they are sorted by their value
                // Ref: Spec: 9.1.1 (1)
                natsort($value);
                foreach ($value as $duplicate_value) {
                    $pairs[] = $parameter . '=' . $duplicate_value;
                }
            } else {
                $pairs[] = $parameter . '=' . $value;
            }
        }
        // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
        // Each name-value pair is separated by an '&' character (ASCII code 38)
        return implode('&', $pairs);
    }

    function get_image_mime( $file )
    {
    	$ext = strtolower(pathinfo( $file , PATHINFO_EXTENSION ));
    	switch( $ext )
    	{
    		case 'jpg':
    		case 'jpeg':
    			$mime = 'image/jpg';
    			break;

    		case 'png':
    			$mime = 'image/png';
    			break;

    		case 'gif':
    		default:
    			$mime = 'image/gif';
    			break;
    	}
    	return $mime;
    }
}
