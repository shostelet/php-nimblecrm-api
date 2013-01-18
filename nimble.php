<?php

class ApiNimble
{
  const
    OAUTH_ACCESS_TOKEN_URL = "https://api.nimble.com/oauth/token?",
    OAUTH_AUTHORIZE_URL = "https://api.nimble.com/oauth/authorize?",
    OAUTH_REQUEST_URL = "https://api.nimble.com/api/v1/"
  ;

  public function __construct()
  {
    $this->config = array(
      'api_key' => 'ABCDEF',
      'secret_key' => '12345',
      'redirect_uri' => 'http://www.mywebsite.com/nimble/authorize'
    );
  }

  public function requestAuthGrantCodeUrl()
  {
    $params = array(
      'client_id' => $this->config['api_key'],
      'redirect_uri' => $this->config['redirect_uri'],
      'response_type' => 'code'
    );

    return sprintf('%s%s', self::OAUTH_AUTHORIZE_URL, http_build_query($params, '', '&'))
  }

  public function requestAuthGrantCode()
  {
    $headers = array(
      'Accept' => 'application/json',
      'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
    );

    $curl_handler = curl_init();
    curl_setopt($curl_handler, CURLOPT_URL, $this->requestAuthGrantCodeUrl());
    curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $headers);

    $output = curl_exec($curl_handler);

    curl_close($curl_handler);
    return $output;
  }

  public function requestAccessToken($code)
  {
    $headers = array(
      'Accept' => 'application/json',
      'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
    );

    $params = array(
      'client_id' => $this->config['api_key'],
      'client_secret' => $this->config['secret_key'],
      'code' => $code,
      'grant_type' => 'authorization_code',
    );

    $curl_handler = curl_init();
    curl_setopt($curl_handler, CURLOPT_URL, self::OAUTH_ACCESS_TOKEN_URL);
    curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handler, CURLOPT_POST, 1);
    curl_setopt($curl_handler, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
    curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $headers);

    $output = curl_exec($curl_handler);
    $response_code = curl_getinfo($curl_handler, CURLINFO_HTTP_CODE);
    curl_close($curl_handler);

    $json = json_decode($output, true);

    if($response_code == 200)
    {
      $response['access_token'] = isset($json['access_token']) ? $json['access_token'] : '';
      $response['expires_in'] = isset($json['expires_in']) ? $json['expires_in'] : '';
      $response['refresh_token'] = isset($json['refresh_token']) ? $json['refresh_token'] : '';
      $_SESSION['nimble'] = $response;
    }
    elseif(isset($json['int_err_code']))
    {
      throw new vacNimbleException($json['msg'], $json['int_err_code']);
    }

    return $response;
  }

  public function getContactList($access_token)
  {
    $headers = array(
      'Accept' => 'application/json',
      'Content-Type' => 'application/json; charset=UTF-8'
    );

    $params = array(
      'tags' => 1,
      'record_type' => 'all',
      'access_token' => $access_token
    );

    $curl_handler = curl_init();
    $url = sprintf('%scontacts/list?%s', self::OAUTH_REQUEST_URL, http_build_query($params, '', '&'));
    curl_setopt($curl_handler, CURLOPT_URL, $url);
    curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($curl_handler);
    curl_close($curl_handler);

    $json = json_decode($output, true);
    return $json;
  }
}