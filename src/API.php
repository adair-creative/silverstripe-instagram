<?php

namespace Prisma\Instagram;

class API {
	public static function get(string $url, array $fields = [], &$json): bool {
		$access_token = Auth::accessToken();
		$extra_fields = $fields ? implode("&", array_merge(["access_token=$access_token"], $fields)) : "";

		$ch = curl_init("https://graph.instagram.com$url?${extra_fields}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		if ($response = curl_exec($ch)) {
			$json = json_decode($response);

			curl_close($ch);

			return true;
		}
		else {
			$error = curl_error($ch);
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$json = json_decode("{error:\"$error\",status:$status}");

			curl_close($ch);

			return false;
		}
	}

	public static function post(string $url, array $fields = [], &$json): bool {
		$post_fields = [];

		foreach ($fields as $key => $value) {
			array_push($post_fields, "$key=" . urlencode($value));
		}

		$ch = curl_init("https://api.instagram.com$url");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&", $post_fields));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [ "Content-Type: application/x-www-form-urlencoded" ]);

		if ($response = curl_exec($ch)) {
			$json = json_decode($response);

			curl_close($ch);

			return true;
		}
		else {
			$error = curl_error($ch);
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$json = json_decode("{error:\"$error\",status:$status}");

			curl_close($ch);

			return false;
		}
	}
}