facebook4ci
===========

Facebook SDK Package for Code Igniter

Usage
-----

Put the project in the Third Party folder (./application/third_party/). 

In your controller load de third party package, the facebook config and the facebook class, like that.

```php
	$this->load->add_package_path(APPPATH.'third_party/facebook4ci/');
	$config = $this->load->config('facebook');
	$this->load->library('facebook', $config);
```

Facebook SDK
------------

The Facebook SDK is built by **Facebook(c)**. To instructs how to use, go to Facebook's Developer [Documentation](http://developers.facebook.com/docs/reference/php/). 