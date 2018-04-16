Project Status Image and Status Page
====================================

Status Image
------------

Most Continuous Integration systems provide a simple image URL that you can use to display your project status on other 
web sites (like Github) - PHP Censor is no different.

You can find the status image at the following location: `http://{PHP_CENSOR_URL}/build-status/image/{PROJECT ID}`

So for example, our instance of PHP Censor is at `php-censor.local`, and our PHP Censor project ID is `2`, so the image 
URL is: `http://php-censor.local/build-status/image/2`.

You can use additional parameters:

* style: plastic | flat (default) | flat-squared | social
* label: build (default)
* logo
* logoWidth
* link
* maxAge

[See more on shields.io site](http://shields.io)

Example:

![](http://php-censor.local/build-status/image/2?style=flat-squared&maxAge=3600)

Status Page
-----------

PHP Censor also provides a public project status page, that is accessible for everyone.

You can find the status page at the following location: `http://{PHP_CENSOR_URL}/build-status/view/{PROJECT ID}`

Example:
http://php-censor.local/build-status/view/2

### Where do I find my project ID?

Go to your instance of PHP Censor, and open the project you are interested in. The project ID is the number in the last 
part of the URL in your browser.

Example:
http://php-censor.local/project/view/2 ~> PROJECT ID: `2`

### Enable/disable status image and page

You can enable or disable access to the public status image and page in your project's settings.
