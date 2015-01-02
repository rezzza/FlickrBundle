FlickrBundle
===========

A simple wrapper for flickr api.

```yaml
rezzza_flickr:
    key: 'my api key'
    secret: 'my api secret'

# or

rezzza_flickr:
    default_client: default
    clients:
        default:
            key: 'my api key'
            secret: 'my api secret'
        second:
            key: 'my second api key'
            secret: 'my second api secret'
```

All services from flickr are currently called with oauth security, by this way, at this moment, you can't use this bundle without oauth authentication.


```php
$client = $this->get('rezzza_flickr.client');
$client->getMetadata()->setOauthAccess('access token', 'access token secret');
// then use it ...
```
