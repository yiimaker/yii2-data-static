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

StaticData
----------
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

StaticDataTranslation
---------------------
```php
class AboutUs extends ymaker\data\statics\StaticDataTranslation
{
    public $address;

    public function rules()
    {
        return [
            [['address'], 'required'],
            ['address', 'string', 'max' => 255],
        ];
    }
}
```

```php
$aboutUs = new AboutUs(['language' => 'en-US']);
// $about
```
#### Save Data

```php
$aboutUs->address = 'Kiev, Ukraine';
$aboutUs->save();
$aboutUs->setLanguage('ru-RU');
$aboutUs->address = 'Киев, Украина';
$aboutUs->save();
```

#### Load Data

```php
$aboutUs->loadAttributes();
echo $aboutUs->address; // 'Киев, Украина'
$aboutUs->changeLanguage('en-US');
echo $aboutUs->address; // 'Kiev, Ukraine'
```
or

```php
$aboutUs = AboutUs::getInstance(['language' => 'en-US']);
```

#### Reload Data

```php
$aboutUs->loadAttributes();
$aboutUs->address = 'Лондон, Великобритания';
$aboutUs->reload();

echo $aboutUs->address; // 'Киев, Украина'
```
#### change language

```php
    /**
     * change language for model
     * @param $language string language code
     * @param bool $reload If true, then all attributes will be overwritten
     */
    public function changeLanguage($language, $reload = true);
```

```php
echo $aboutUs->address; // 'Киев, Украина'
$aboutUs->changeLanguage('en-US');
echo $aboutUs->address; // 'Kiev, Ukraine'

$aboutUs->changeLanguage('ru-RU', false);
echo $aboutUs->address; // 'Kiev, Ukraine'

$aboutUs->reload();
echo $aboutUs->address; // 'Киев, Украина'
```
