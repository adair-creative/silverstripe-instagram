<?php

namespace Prisma;

use Prisma\Instagram\API;
use Prisma\Instagram\Auth;
use Psr\SimpleCache\CacheInterface;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\Debug;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

class Instagram {
	public static function getMedia(int $limit = 5): ArrayList {
		$cache = Injector::inst()->get(CacheInterface::class . ".Prisma.Instagram");

		if (Controller::curr()->getRequest()->getVar("flush") !== "all" && $cache->has("expiration") && $cache->get("expiration") > time() && $cache->get("media_count") >= $limit) {
			return $cache->get("media")->limit($limit);
		}
		else {
			$posts = new ArrayList([]);

			function decodeMedia(string $id, bool $is_child): ArrayData {
				API::get("/$id", $is_child ? ["fields=media_type,media_url"] : ["fields=media_type,media_url,caption,children"], $media);

				return new ArrayData([
					"Type" => $media->media_type,
					"URL" => $media->media_url,
					"Caption" => property_exists($media, "caption") ? $media->caption : "",
					"Children" => property_exists($media, "children") ? new ArrayList(array_map(function ($child) { return decodeMedia($child->id, true); }, $media->children->data)) : null
				]);
			}

			if (Auth::valid()) {
				API::get("/me/media", ["fields=id", "limit=$limit"], $json);

				foreach ($json->data as $post) {
					$posts->add(decodeMedia($post->id, false));
				}
			}

			$cache->set("expiration", time() + 43200);
			$cache->set("media", $posts);
			$cache->set("media_count", $limit);

			return $posts;
		}
	}
}