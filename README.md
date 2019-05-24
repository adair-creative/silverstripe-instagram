# Instagram
```composer install adair-creative/instagram```

A simple toolkit that allows for one-click CMS auth for an Instagram account

## How To Use
### CMS
Go to `admin/settings` and click `Log in to Instagram`

![alt text](https://github.com/adair-creative/instagram/blob/master/Annotation%202019-05-21%20113131.png?raw=true)

### Code
Required Config:
```yml
AdairCreative\Instagram:
  client_secret: <CLIENT SECRET> 
  client_id: <CLIENT ID>
```

Get Media
```php
public function getImages() {
	return Instagram::getUserMedia();
}
```
