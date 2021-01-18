<?php

namespace Prisma\Instagram;

use SilverStripe\Control\Controller as ControlController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\SiteConfig\SiteConfig;

class Controller extends ControlController {
	private static $allowed_actions = [
		"authorize",
		"deauthorize",
		"delete_data"
	];

	public function authorize(HTTPRequest $request) {
		$request = Controller::curr()->getRequest();
		$host = $request->getHost();

		$code = $request->getVar("code");

		API::post("/oauth/access_token", [
			"client_id" => Auth::appID(),
			"client_secret" => Auth::appSecret(),
			"grant_type" => "authorization_code",
			"redirect_uri" => "https://$host/prisma.instagram/authorize",
			"code" => $code
		], $json);

		$config = SiteConfig::current_site_config();

		$config->Prisma_Instagram_AccessToken = $json->access_token;
		$config->Prisma_Instagram_AccessTokenExpiration = time() + 2592000;

		$config->write();

		return $this->redirect("/admin/settings");
	}

	public function deauthorize(HTTPRequest $request) {
		
	}

	public function delete_data(HTTPRequest $request) {
		
	}
}