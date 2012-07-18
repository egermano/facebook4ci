facebook4ci
===========

Facebook SDK Package for Code Igniter

Usage
-----

Put the project in the Third Party folder (./application/third_party/). 

In your controller load de third party package, the facebook config and the facebook class.

```php
	$this->load->add_package_path(APPPATH.'third_party/facebook/');
	$config = $this->load->config('facebook');
	$this->load->library('facebook', $config);
```