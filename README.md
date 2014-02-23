Sculpin Related Posts Bundle
============================

Create blocks showing sources with matching tags for Sculpin powered sites.

## Setup

Add this bundle in your ```sculpin.json``` file:

```json
{
    // ...
    "require": {
        // ...
        "tsphethean/sculpin-related-posts-bundle": "@dev"
    }
}
```
and install this bundle running ```sculpin update```.

Now you can register the bundle in ```SculpinKernel``` class available on ```app/SculpinKernel.php``` file:

```php
class SculpinKernel extends \Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel
{
    protected function getAdditionalSculpinBundles()
    {
        return array(
           'Tsphethean\Sculpin\Bundle\RelatedPostsBundle\SculpinRelatedPostsBundle'
        );
    }
}
```

## How to use

In your template:

```php
{% if page.related %}
<ul>
  {% for relate in page.related %}
  <li><a href="{{ relate.url }}">{{ relate.title }}</a></li>
  {% endfor %}
</ul>
{% endif %}
```
