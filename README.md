# Instagram Integration for SilverStripe

```composer install adair-creative/instagram```

A simple toolkit that allows for one-click CMS auth for an Instagram account

## Getting Started

1.  Setup a Facebook app with **Instagram Basic Display** [See Facebook Docs](https://developers.facebook.com/docs/instagram-basic-display-api/getting-started)

- **Setup Notes:**
    - The CMS integration only works with https, however manually generated test tokens work locally.
  - Valid OAuth Redirect URIs should direct to `https://{your-domain}/prisma.instagram/authorize`
  - Deauthorize Callback URL should direct to `https://{your-domain}/prisma.instagram/deauthorize`
  - Data Deletion Request Callback URL should direct to `https://{your-domain}/prisma.instagram/delete_data`

2. Assign your app keys:
```yml
# mysite.yml

Prisma\Instagram:
  app_id: {Instagram app ID}
  app_secret: {Instagram app secret}
```

3. You or the client can then link an Instagram account by clicking on **Link Account** inside of **Admin** > **Settings** > **Instagram**

## Reference

class **Prisma\Instagram**

- static function **getMedia**(*int* **$limit** = *5*) *ArrayList*
