# Xcurd

[![N|Solid](https://cldup.com/dTxpPi9lDf.thumb.png)](https://nodesource.com/products/nsolid)

[![Build Status](https://travis-ci.org/joemccann/dillinger.svg?branch=master)](https://travis-ci.org/joemccann/dillinger)

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
  php artisan make:xmodel Blog
  ```
3. Make changes in your model like form fields and display table fields
```PHP
class Blog extends Model
{
    //can't be canged after initialize but if you have to change then you have to regenerate xcurd with this model
    $form_fileds=[
        'name'=>'text',
        'body'=>'textarea|richText',
        'user' => 'model|User'
    ];
    $table_fields=[
        'name','body|max=250','user|name'
    ];

}
```


