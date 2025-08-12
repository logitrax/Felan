<?php
if (!defined("ABSPATH")) {
	exit();
}
if (!class_exists("Felan_LinkedIn")) {
	class Felan_LinkedIn
	{
		public static $app_id;
		public static $app_secret;
		public static $callback;
		public static $csrf;
		public static $scopes;
		public static $ssl;
		public static $accessToken;
		public function __construct(string $app_id, string $app_secret, string $callback, string $scopes, bool $ssl = true)
		{
			self::$app_id = $app_id;
			self::$app_secret = $app_secret;
			self::$scopes =  $scopes;
			self::$csrf = random_int(111111, 99999999999);
			self::$callback = $callback;
			self::$ssl = $ssl;
		}
		public static function getAuthUrl()
		{
			$_SESSION['linkedincsrf'] = self::$csrf;
			$params = [
				'response_type' => 'code',
				'client_id' => self::$app_id,
				'redirect_uri' => self::$callback,
				'state' => self::$csrf,
				'scope' => self::$scopes,
			];
			return "https://www.linkedin.com/oauth/v2/authorization?" . http_build_query($params);
		}
		public static function getAccessToken($code)
		{
			$url = "https://www.linkedin.com/oauth/v2/accessToken";
			$params = [
				'grant_type' => 'authorization_code',
				'code' => $code,
				'redirect_uri' => self::$callback,
				'client_id' => self::$app_id,
				'client_secret' => self::$app_secret,
			];
            $response = self::curl($url, http_build_query($params), "application/x-www-form-urlencoded");
            $responseData = json_decode($response);

            if (isset($responseData->access_token)) {
                $accessToken = $responseData->access_token;
                self::$accessToken = $accessToken;
                return $accessToken;
            } else {
                error_log("Error fetching access token: " . $response);
            }
		}
		public static function getPerson($accessToken)
		{
			$url = 'https://api.linkedin.com/v2/userinfo?oauth2_access_token=' . $accessToken;
			$params = [];
			$response = self::curl($url, http_build_query($params), "application/x-www-form-urlencoded", false);
			$person = json_decode($response, true);
			return $person;
		}

        protected static function curl($url, $parameters, $content_type, $post = true) {
            $args = [
                'headers' => [
                    'Content-Type' => $content_type,
                ],
                'body' => $parameters,
                'timeout' => 15,
                'sslverify' => self::$ssl,
            ];

            if ($post) {
                $response = wp_remote_post($url, $args);
            } else {
                $response = wp_remote_get($url, $args);
            }

            if (is_wp_error($response)) {
                return $response->get_error_message();
            }

            return wp_remote_retrieve_body($response);
        }
	}
}
