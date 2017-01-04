Yii2 Static Data
================
It is a model for the data that stores configuration.
`StaticData` subject to the same rules as `yii\base\Model`

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
php composer.phar require --prefer-dist yiimaker/yii2-data-static "*"
```

or add

``` json
"yiimaker/yii2-data-static": "*"
```

to the require section of your `composer.json` file.


Usage
-----
1. Configure component `yiimaker/yii2-configuration` in config file or in `StaticData` class. [More information](https://github.com/yiimaker/yii2-configuration#configuration)
2. Inherit the class `ymaker\data\statics\StaticData`, then describe it as a normal model.

Example
-------
```php
class AboutUs extends ymaker\data\statics\StaticData
{
    public $phone;
    public $email;

    public function rules()
    {
        return [
            [['phone', 'email'], 'required'],
            ['phone', 'string', 'max' => 255],
            ['email', 'email']
        ];
    }
}
```

```php
$aboutUs = new AboutUs();
```
#### Save Data

```php
$aboutUs->phone = '+111111111111';
$aboutUs->email = 'test@example.com';
$aboutUs->save();
```

#### Load Data

```php
$aboutUs->loadAttributes();
echo $aboutUs->email; // 'test@example.com';
```
or

```php
$aboutUs = AboutUs::getInstance();
```

#### Reload Data

```php
$aboutUs->loadAttributes();
$aboutUs->email = 'another@example.com';
$aboutUs->reload();

echo $aboutUs->email; // 'test@example.com';
```
