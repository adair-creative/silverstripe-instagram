<?php

namespace Prisma\Instagram;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;

class SiteConfigExtension extends DataExtension {
	private static $db = [
		"Prisma_Instagram_AccessToken" => "Varchar(512)",
		"Prisma_Instagram_AccessTokenExpiration" => "Int"
	];

	public function updateCMSFields(FieldList $fields) {
		$fields->findOrMakeTab("Root.Instagram", "Instagram");

		$link = Auth::authorizationLink();
		$text = Auth::valid() ? "Link New Account" : "Link Account";

		$fields->addFieldToTab("Root.Instagram", new LiteralField("Link Account", "<a href=\"$link\" target=\"_blank\">$text</a>"));

		return $fields;
	}
}