<?php

namespace Taskovich\VkConnector\utils;

class Requests {

	public static function get(string $url, array $params = [], array $headers = []): ?string {
		if (!empty($params)) {
			$url .= "?" . http_build_query($params);
		}

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);

		if ((!$response) or curl_errno($ch) or curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
			$response = null;
		}

		curl_close($ch);
		return $response;
	}
}
