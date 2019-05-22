<?php

namespace AdairCreative {
	use SilverStripe\Core\Config\Configurable;
    use SilverStripe\ORM\ArrayList;
    use SilverStripe\Core\Injector\Injector;
    use Psr\SimpleCache\CacheInterface;
    use SilverStripe\Core\Config\Config;
    use SilverStripe\View\ArrayData;

//

	class Instagram {
		use Configurable;

		private static function getCache() {
			return Injector::inst()->get(CacheInterface::class . ".ACG_Instagram");
		}

		private static function arrayToList(array $array): ArrayList {
			$output = [];

			foreach ($array as $item) {
				array_push($output, new ArrayData([
					"URL" => $item
				]));
			}

			return new ArrayList($output);
		}

		public static function getAccessToken(): ?string {
			return Instagram::getCache()->get("access_token");
		}

		public static function setAccessToken(string $token) {
			Instagram::getCache()->set("access_token", $token);
		}

		public static function getClientID(): ?string {
			return Config::inst()->get(Instagram::class, "client_id");
		}

		public static function getClientSecret(): ?string {
			return Config::inst()->get(Instagram::class, "client_secret");
		}

		public static function useSSL(): int {
			return Config::inst()->get(Instagram::class, "use_ssl") != null ? 2 : 0;
		}

		public static function getUserMedia(): ?ArrayList {
			$cache = Instagram::getCache();
			$lastUpdated = $cache->get("last_updated");
			if ($lastUpdated != null && time() - (int)$lastUpdated < 3600) {
				return Instagram::arrayToList(json_decode($cache->get("media")));
			}

			$ch = curl_init("https://api.instagram.com/v1/users/self/media/recent?access_token=" . Instagram::getAccessToken());
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, Instagram::useSSL());
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, Instagram::useSSL());
			$result = curl_exec($ch);
			curl_close($ch);

			if ($result != false) {
				$json = json_decode($result);

				if (key_exists("data", $json)) {
					$media = [];
				
					foreach ($json->data as $data) {
						if (key_exists("images", $data)) {
							if (key_exists("standard_resolution", $data->images)) {
								array_push($media, $data->images->standard_resolution->url);
							}
						}
					}

					$cache->set("last_updated", time());
					$cache->set("media", json_encode($media));

					return Instagram::arrayToList($media);
				}
			}
			else {
				return null;
			}

			return null;
		}
	}
}
