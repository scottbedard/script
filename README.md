# Script Manager

A Lavavel package for concatenating and minifying javascript assets.

https://packagist.org/packages/bedard/script

##### Adding assets

To add a file to your javascript pipeline, call the add() method and pass in the path to your file(s). By default, these files will use base_path() as it's root.

```php
Script::add(
	'path/to/some/asset/foo.js',
	'some/other/asset/bar.js'
);
```

##### Minifying
By default, your assets will be minified in production environments. If this is not what you would like, there are two ways you can adjust this.

The simplest way to do this, is by disabling minification using the minify() method
```php
Script::minify(FALSE);
```

The other way you could do this is by changing the environment manually. If this method is not called, the environment will be automatically detected.
```php
Script::environment('local');
```

##### Building your asset file
To put all the pieces together, you must run this command before passing to your view.
```php
Script::build();
```

One important thing to remember, is that this method will only write a new asset file if it does not already exist. In development environments, a new filename will be created whenever any of the asset files change. In a production environment, a new filename will be created whenever the *file name* of one of the asset files changes.

The best ways to utilize this, is to clear the output directory of old files when you push changes to production, or to tag your js file names with version numbers.

##### Referencing the output
Use the following method to get the location of the finalized javascript file
```php
Script::output();
```
