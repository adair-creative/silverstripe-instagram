<?php

namespace Prisma;

use Error;
use Prisma\Instagram\API;
use Prisma\Instagram\Auth;
use Psr\SimpleCache\CacheInterface;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

class Instagram {
	public static function getPost($instagramId, $isChild = false): ArrayData {
		API::get("https://graph.instagram.com/$instagramId", $isChild ? ["access_token=" . Auth::accessToken(), "fields=media_type,media_url,permalink,thumbnail_url,timestamp"] : ["access_token=" . Auth::accessToken(), "fields=media_type,media_url,caption,children,permalink,thumbnail_url,timestamp"], $media);

		return new ArrayData([
			"InstagramID" => $instagramId,
			"Type" => $media->media_type,
			"ISOTimestamp" => $media->timestamp,
			"URL" => $media->media_url,
			"ThumbnailURL" => $media->thumbnail_url,
			"PermaLink" => $media->permalink,
			"Caption" => property_exists($media, "caption") ? $media->caption : "",
			"Children" => property_exists($media, "children") ? new ArrayList(array_map(function ($child) { return self::getPost($child->id, true); }, $media->children->data)) : null
		]);
	}

	public static function getMedia(int $limit = 5, bool $allowCache = true): ArrayList {
		$cache = Injector::inst()->get(CacheInterface::class . ".Prisma.Instagram");

		if ($allowCache && Controller::curr()->getRequest()->getVar("flush") !== "all" && $cache->has("expiration") && $cache->get("expiration") > time() && $cache->get("media_count") >= $limit) {
			return $cache->get("media")->limit($limit);
		}
		else {
			$posts = new ArrayList([]);

			if (Auth::valid()) {
				API::get("https://graph.instagram.com/me/media", ["access_token=" . Auth::accessToken(), "fields=id", "limit=$limit"], $json);

				if (property_exists($json, "data")) {
					foreach ($json->data as $post) {
						$posts->add(self::getPost($post->id, false));
					}

					$cache->set("expiration", time() + 43200);
					$cache->set("media", $posts);
					$cache->set("media_count", $limit);
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}

			return $posts;
		}
	}
}
