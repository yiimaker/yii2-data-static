<?php
namespace ymaker\data\statics;

use ymaker\configuration\translation\ConfigurationTranslation;

/**
 * Class StaticDataTranslation
 * @package ymaker\data\statics
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
class StaticDataTranslation extends StaticData
{
    public static function getDefaultLanguage()
    {
        return \Yii::$app->language;
    }

    /**
     * @var string
     */
    private $language;

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }


    public function loadAttributes($skipDefined = true)
    {
        $code = $this->language ?: self::getDefaultLanguage();
        /** @var ConfigurationTranslation $config */
        $config = $this->getConfiguration();
        $attributes = $this->getAttributes();
        if ($skipDefined) {
            $attributes = array_filter($attributes, function ($value) {
                return empty($value);
            });
        }

        foreach ($attributes as $key => $value) {
            $this->{$key} = $config->getTranslation($this->getAttributeName($key), $code);
        }
    }

    public static function getInstance($config = [])
    {
        $config['class'] = get_called_class();


        /** @var StaticDataTranslation $instance */
        $instance = \Yii::createObject($config);

        $instance->loadAttributes(false);

        return $instance;
    }


    public function save()
    {
        $code = $this->language;

        $this->beforeSave();
        if (!$this->validate()) {
            return false;
        }
        /** @var ConfigurationTranslation $config */
        $config = $this->getConfiguration();
        $attributes = $this->getAttributes();

        $isSaved = true;
        foreach ($attributes as $key => $value) {
            if (!$config->setTranslation($this->getAttributeName($key), $value, $code)) {
                $this->addError($key, 'Something went wrong. More details in the logs.');
                $isSaved = false;
            }
        }
        $this->afterSave();
        return $isSaved;
    }

}