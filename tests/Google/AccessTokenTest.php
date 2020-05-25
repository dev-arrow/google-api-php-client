<?php

use GuzzleHttp\Message\Request;
use GuzzleHttp\Client;

/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

class Google_AccessTokenTest extends BaseTest
{
  public function testRevokeAccess()
  {
    $accessToken = "ACCESS_TOKEN";
    $refreshToken = "REFRESH_TOKEN";
    $accessToken2 = "ACCESS_TOKEN_2";
    $token = "";

    $postBody = $this->getMock('GuzzleHttp\Post\PostBodyInterface');
    $postBody->expects($this->exactly(3))
      ->method('replaceFields')
      ->will($this->returnCallback(
            function ($fields) use (&$token) {
              $token = isset($fields['token']) ? $fields['token'] : null;
            }
        ));
    $request = $this->getMock('GuzzleHttp\Message\RequestInterface');
    $request->expects($this->exactly(3))
      ->method('getBody')
      ->will($this->returnValue($postBody));
    $response = $this->getMock('GuzzleHttp\Message\ResponseInterface');
    $response->expects($this->exactly(3))
      ->method('getStatusCode')
      ->will($this->returnValue(200));
    $http = $this->getMock('GuzzleHttp\ClientInterface');
    $http->expects($this->exactly(3))
      ->method('send')
      ->will($this->returnValue($response));
    $http->expects($this->exactly(3))
      ->method('createRequest')
      ->will($this->returnValue($request));

    // Test with access token.
    $t = new Google_AccessToken(
      array(
        'access_token' => $accessToken,
        'created' => time(),
        'expires_in' => '3600'
      ),
      $http
    );
    $this->assertTrue($t->revokeToken());
    $this->assertEquals($accessToken, $token);

    // Test with refresh token.
    $t->setAccessToken(
      array(
        'access_token' => $accessToken,
        'refresh_token' => $refreshToken,
        'created' => time(),
        'expires_in' => '3600'
      )
    );
    $this->assertTrue($t->revokeToken());
    $this->assertEquals($refreshToken, $token);

    // Test with string token
    $t->setAccessToken($accessToken2);
    $this->assertTrue($t->revokeToken());
    $this->assertEquals($accessToken2, $token);
  }

  // /**
  //  * Most of the logic for ID token validation is in AuthTest -
  //  * this is just a general check to ensure we verify a valid
  //  * id token if one exists.
  //  */
  // public function testValidateIdToken()
  // {
  //   $this->checkToken();
  //   $client = $this->getClient();
  //   $token = json_decode($client->getAccessToken());
  //   $segments = explode(".", $token->id_token);
  //   $this->assertEquals(3, count($segments));
  //   // Extract the client ID in this case as it wont be set on the test client.
  //   $data = json_decode(JWT::urlSafeB64Decode($segments[1]));
  //   $oauth = new Google_Auth_OAuth2($client);
  //   $payload = $oauth->verifyIdToken($token->id_token, $data->aud);
  //   $this->assertArrayHasKey('sub', $payload);
  //   $this->assertTrue(strlen($payload['sub']) > 0);

  //   // TODO: Need to be smart about testing/disabling the
  //   // caching for this test to make sense. Not sure how to do that
  //   // at the moment.
  //   $client = $this->getClient();
  //   $data = json_decode(JWT::urlSafeB64Decode($segments[1]));
  //   $oauth = new Google_Auth_OAuth2($client);
  //   $payload = $oauth->verifyIdToken($token->id_token, $data->aud);
  //   $this->assertArrayHasKey('sub', $payload);
  //   $this->assertTrue(strlen($payload['sub']) > 0);
  // }

  // /**
  //  * Test for revoking token when none is opened
  //  */
  // public function testRevokeWhenNoTokenExists()
  // {
  //   $client = new Google_Client();
  //   $this->assertFalse($client->revokeToken());
  // }

  // /**
  //  * Test that the ID token is properly refreshed.
  //  */
  // public function testRefreshTokenSetsValues()
  // {
  //   $client = new Google_Client();
  //   $request = $this->getMock('GuzzleHttp\Message\RequestInterface');
  //   $request->expects($this->once())
  //     ->method('getBody')
  //     ->will($this->returnValue($this->getMock('GuzzleHttp\Post\PostBodyInterface')));
  //   $response = $this->getMock('GuzzleHttp\Message\ResponseInterface');
  //   $response->expects($this->once())
  //     ->method('json')
  //     ->will($this->returnValue(array(
  //         'access_token' => 'xyz',
  //         'id_token' => 'ID_TOKEN',
  //       )));
  //   $response->expects($this->once())
  //     ->method('getBody')
  //     ->will($this->returnValue($this->getMock('GuzzleHttp\Post\PostBody')));
  //   $http = $this->getMock('GuzzleHttp\ClientInterface');
  //   $http->expects($this->once())
  //     ->method('send')
  //     ->will($this->returnValue($response));
  //   $http->expects($this->once())
  //     ->method('createRequest')
  //     ->will($this->returnValue($request));
  //   $client->setHttpClient($http);
  //   $oauth = new Google_Auth_OAuth2($client);
  //   $oauth->refreshToken("REFRESH_TOKEN");
  //   $token = json_decode($oauth->getAccessToken(), true);
  //   $this->assertEquals($token['id_token'], "ID_TOKEN");
  // }
}
