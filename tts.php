<?php
if ( isset( $_REQUEST['csurl'] ) ) {
    $request_url = urldecode( $_REQUEST['csurl'] );
    //print_r($_GET);exit;
    if(count($_GET)) {
		foreach ($_GET as $key => $value) {
			if($key!="csurl")
			{
				 $request_url .= "&".$key."=".urlencode($value);
			}
		}
	}

} 
$request_url  = "http://".$request_url;
$ch = curl_init( $request_url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );	 // return response
curl_setopt( $ch, CURLOPT_HEADER, true );	   // enabled response headers
// retrieve response (headers and content)
$response = curl_exec( $ch );
curl_close( $ch );


// split response to header and content
list($response_headers, $response_content) = preg_split( '/(\r\n){2}/', $response, 2 );

// (re-)send the headers
$response_headers = preg_split( '/(\r\n){1}/', $response_headers );
foreach ( $response_headers as $key => $response_header ) {
	// Rewrite the `Location` header, so clients will also use the proxy for redirects.
	if ( preg_match( '/^Location:/', $response_header ) ) {
		list($header, $value) = preg_split( '/: /', $response_header, 2 );
		$response_header = 'Location: ' . $_SERVER['REQUEST_URI'] . '?csurl=' . $value;
	}
	if ( !preg_match( '/^(Transfer-Encoding):/', $response_header ) ) {
		header( $response_header, false );
	}
}

// finally, output the content
print( $response_content );
