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
    private $language;

    /**
     * Return configuration
     * @return array|string|object
     */
    public function getConfiguration()
    {
        return 'translationConfig';
    }

    /**
     * @return string get App language
     */
    public static function getAppLanguage()
    {
        return \Yii::$app->language;
    }


    /**
     * @return string language code
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language language code
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * change language for model
     * @param $language string language code
     * @param bool $reload If true, then all attributes will be overwritten
     */
    public function changeLanguage($language, $reload = true)
    {
        $this->setLanguage($language);
        if ($reload) {
            $this->reload();
        }
    }

    /**
     * load model
     * @param bool $skipDefined If true, then the attributes of the set value will not be overwritten
     */
    public function loadAttributes($skipDefined = true)
    {
        $code = $this->language ?: self::getAppLanguage();
        /** @var ConfigurationTranslation $config */
        $config = $this->configurationInit();
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

    /**
     * save model
     * @return bool
     */
    public function save()
    {
        $code = $this->language;
        $this->beforeSave();
        if (!$this->validate()) {
            return false;
        }

        /** @var ConfigurationTranslation $config */
        $config = $this->configurationInit();
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