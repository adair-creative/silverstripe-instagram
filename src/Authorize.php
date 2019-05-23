<?php

namespace AdairCreative\Instagram\AuthControl {
	use SilverStripe\ORM\DataExtension;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\LiteralField;
    use SilverStripe\CMS\Controllers\ContentController;
    use SilverStripe\Control\HTTPRequest;
    use AdairCreative\Instagram;
    use SilverStripe\Dev\Debug;
    use SilverStripe\Control\Director;

//

	class Endpoint extends ContentController {
		private static $allowed_actions = [
			"login"
		];

		public function login(HTTPRequest $request) {
			if ($code = $request->getVar("code")) {
				$ch = curl_init("https://api.instagram.com/oauth/access_token");
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, Instagram::useSSL());
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, Instagram::useSSL());
				curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id=" . Instagram::getClientID() . "&client_secret=" . Instagram::getClientSecret() . "&grant_type=authorization_code&redirect_uri=" . Director::absoluteBaseURL() . "acg_instagram/login&code=" . $code);
				$result = curl_exec($ch);
				$json = json_decode($result);

				if ($result != false) {
					if (key_exists("access_token", $json)) {
						Instagram::setAccessToken($json->access_token);
						$this->redirectBack("/admin/settings");
					} else {
						Debug::dump($json);
					}
				}
				else {
					Debug::dump(curl_error($ch));
					Debug::dump(curl_getinfo($ch));
				}

				curl_close($ch);
			}	
			
		}
	}

	class Authorize extends DataExtension {
		public function updateCMSFields(FieldList $fields) {
			if (($clientId = Instagram::getClientID()) && Instagram::getClientSecret() != "") {
				$fields->addFieldToTab("Root.Main", new LiteralField("", "<a href='https://api.instagram.com/oauth/authorize/?client_id=" . $clientId . "&response_type=code&redirect_uri=" . Director::absoluteBaseURL() . "acg_instagram/login'>Log in to Instagram / Change User</a>"));
			}

			return $fields;
		}
	}
}
