<?php

/*
 * NimbleApi class is written to intract with Nimble CRM Api
 * @name: NimbleApi
 * @version: 1.0
 */
 
class NimbleApi{
	
	  const
	    OAUTH_ACCESS_TOKEN_URL = "https://api.nimble.com/oauth/token?",
	    OAUTH_AUTHORIZE_URL = "https://api.nimble.com/oauth/authorize?",
	    OAUTH_REQUEST_URL = "https://api.nimble.com/api/v1/"
	  ;
	
	  private $headers;
	  
	  public function __construct()
	  {
	    $this->config = array(
	      'api_key' => 'ABCDEF',
	      'secret_key' => '12345',
	      'redirect_uri' => 'http://www.example.com/nimble/authorize'
	    );
	    
	    $this->headers = array(
	    		'Accept' => 'application/json',
	    		'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
	    );
	        
	  }
	
	  public function requestAuthGrantCodeUrl()
	  {
	    $params = array(
	      'client_id' => $this->config['api_key'],
	      'redirect_uri' => $this->config['redirect_uri'],
	      'response_type' => 'code'
	    );
	
	    return sprintf('%s%s', self::OAUTH_AUTHORIZE_URL, http_build_query($params, '', '&'));
	  }
	
	  public function requestAuthGrantCode()
	  {
	
	
	    $curl_handler = curl_init();
	    curl_setopt($curl_handler, CURLOPT_URL, $this->requestAuthGrantCodeUrl());
	    curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl_handler, CURLOPT_FOLLOWLOCATION, TRUE);
	    curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $this->headers);
	
	    $output = curl_exec($curl_handler);
	
	    curl_close($curl_handler);
	    return $output;
	  }
	
	  public function requestAccessToken($code)
	  {
	
	
	    $params = array(
	      'client_id' => $this->config['api_key'],
	      'client_secret' => $this->config['secret_key'],
	      'redirect_uri' =>$this->config['redirect_uri'],
	      'code' => $code,
	      'grant_type' => 'authorization_code',
	    );
	
	    $curl_handler = curl_init();
	    curl_setopt($curl_handler, CURLOPT_URL, self::OAUTH_ACCESS_TOKEN_URL);
	    curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl_handler, CURLOPT_POST, 1);
	    curl_setopt($curl_handler, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
	    curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $this->headers);
	
	    $output = curl_exec($curl_handler);
	    $response_code = curl_getinfo($curl_handler, CURLINFO_HTTP_CODE);
	    curl_close($curl_handler);
	
	    $json = json_decode($output, true);
	
	    if($json['error']){
	    	throw new Exception($json['error'].': '.$json['error_description']);
	    }
	    elseif($response_code == 200)
	    {
	    	return $json;
	    }
	    
	    return false;
	  }
	
	  public function requestRefreshToken($refresh_token)
	  {
	  
	  		
	  	$params = array(
	  			'client_id' => $this->config['api_key'],
	  			'client_secret' => $this->config['secret_key'],
	  			'redirect_uri' =>$this->config['redirect_uri'],
	  			'refresh_token' => $refresh_token,
	  			'grant_type' => 'refresh_token',
	  	);
	  		
	  	$curl_handler = curl_init();
	  	curl_setopt($curl_handler, CURLOPT_URL, self::OAUTH_ACCESS_TOKEN_URL);
	  	curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
	  	curl_setopt($curl_handler, CURLOPT_POST, 1);
	  	curl_setopt($curl_handler, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
	  	curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $this->headers);
	  		
	  	$output = curl_exec($curl_handler);
	  	$response_code = curl_getinfo($curl_handler, CURLINFO_HTTP_CODE);
	  	curl_close($curl_handler);
	  		
	  		
	  	$json = json_decode($output, true);
	  		
	  	if($json['error']){
	  		throw new Exception($json['error'].': '.$json['error_description']);
	  	}
	  	elseif($response_code == 200)
	  	{
	  		return $json;
	  	}
	  		
	  		
	  	return false;
	  }
	
	  
	  public function getContactList($access_token)
	  {
	
	
	    $params = array(
	      'tags' => 1,
	      'record_type' => 'all',
	      'access_token' => $access_token
	    );
	
	    $curl_handler = curl_init();
	    $url = sprintf('%scontacts/list?%s', self::OAUTH_REQUEST_URL, http_build_query($params, '', '&'));
	    curl_setopt($curl_handler, CURLOPT_URL, $url);
	    curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $this->headers);
	    $output = curl_exec($curl_handler);
	    curl_close($curl_handler);
	
	    $json = json_decode($output, true);
	    return $json;
	  }
	  
	
	  public function searchContact($access_token,$query=array()){
	  
	  	$params = array(
	  			'access_token' => $access_token
	  	);
	  	
		if(!is_array($query)){
			throw new Exception('Search Query Must be in array format');
		}
	  	
	    $curl_handler = curl_init();
	    $url = sprintf('%scontacts/?%s&query=%s', self::OAUTH_REQUEST_URL, http_build_query($params, '', '&'),rawurlencode(json_encode($query)));
	
	    
	    curl_setopt($curl_handler, CURLOPT_URL, $url);
	    curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $this->headers);
	    $output = curl_exec($curl_handler);
	    curl_close($curl_handler);
	    
	    $json = json_decode($output, true);
	    return $json;
	  
	  }
	  
	  //@todo : add nimble contact functions is just initated, not yet functioning
	  public function addContact($access_token,$query=array()){
	  
	  	$params = array(
	  			'access_token' => $access_token
	  	);
	  
	  	if(!is_array($query)){
	  		throw new Exception('Search Query Must be in array format');
	  	}
	  	 
	  	 
	  }  
    
}
