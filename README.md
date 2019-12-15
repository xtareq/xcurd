# Xcurd


Xcurd is crud generetor for laravel.With xcurd you can easily make curd operation of any tipe of model.

  - Type some Markdown on the left
  - See HTML in the right
  - Magic

# Installation
Xcurd can be install by this composer command
  ```php
  composer require tareq/xcurd
  ```
1. Install xcurd package from composer in your laravel app.
2. Create a xmodel by this php artisan command 
  ```php
  php artisan make:xmodel Blog -m
  ```
3. Change migration file and then migrate
```php
php artisan migrate
```
4. Make changes in your model like form fields and display table fields
```PHP
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	  public $routeNamePrefix="";//if route hav prefix
    public $form_fields=['name'=>'text']; //curd form fields
    public $table_fields=['name']; //curd table fields
}

```


