<?php

namespace Prisma\Instagram;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\SiteConfig\SiteConfig;

class Auth {
	public static function accessToken(): ?string {
		$config = SiteConfig::current_site_config();

		if ($config->Prisma_Instagram_AccessTokenExpiration - time() <= 0) {
			if ($config->Prisma_Instagram_AccessToken) {
				API::get("https://graph.instagram.com/refresh_access_token", ["grant_type=ig_refresh_token", "access_token=" . $config->Prisma_Instagram_AccessToken], $json);

				if (property_exists($json, "access_token")) {
					$config->Prisma_Instagram_AccessToken = $json->access_token;
					$config->Prisma_Instagram_AccessTokenExpiration = time() + 2592000;

					$config->write();
				}
				else {
					return null;
				}
			}
			else {
				return null;
			}
		}

		return $config->Prisma_Instagram_AccessToken;
	}

	public static function appID(): ?string {
		return Config::inst()->get("Prisma\\Instagram", "app_id");
	}

	public static function appSecret(): ?string {
		return Config::inst()->get("Prisma\\Instagram", "app_secret");
	}

	public static function authorizationLink(): string {
		$request = Controller::curr()->getRequest();
		$host = $request->getHost();
		$app_id = Auth::appID();

		return "https://api.instagram.com/oauth/authorize?client_id=$app_id&redirect_uri=https://$host/prisma.instagram/authorize&scope=user_profile,user_media&response_type=code";
	}

	public static function valid(): bool {
		return !!Auth::accessToken();
	}
}
