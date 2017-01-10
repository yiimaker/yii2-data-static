<?php

namespace ymaker\data\statics;

use yii\base\Event;
use yii\base\Model;
use yii\di\Instance;
use ymaker\configuration\Configuration;

/**
 *
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
class StaticData extends Model
{
    const EVENT_BEFORE_SAVE = 'beforeSave';
    const EVENT_AFTER_SAVE = 'afterSave';

    /**
     * @param array $config
     * @return StaticData
     */
    public static function getInstance($config = [])
    {
        $config['class'] = get_called_class();

        /** @var StaticData $instance */
        $instance = \Yii::createObject($config);

        $instance->loadAttributes(false);

        return $instance;
    }

    /**
     * @return array|string|object
     */
    public function getConfiguration()
    {
        return 'config';
    }

    /**
     * @return array|object|string
     */
    protected function configurationInit()
    {
        $config = $this->getConfiguration();
        if (is_array($config)) {
            $config = Instance::ensure($config);
        } elseif (is_string($config)) {
            $config = Instance::of($config)->get();
        }
        return $config;
    }

    /**
     * load model
     * @param bool $skipDefined If true, then the attributes of the set value will not be overwritten
     */
    public function loadAttributes($skipDefined = true)
    {
        /** @var Configuration $config */
        $config = $this->configurationInit();
        $attributes = $this->getAttributes();
        if ($skipDefined) {
            $attributes = array_filter($attributes, function ($value) {
                return empty($value);
            });
        }
        $keys = array_keys($attributes);
        $keyList = [];
        foreach ($keys as $key) {
            $keyList[$this->getAttributeName($key)] = $key;
        }

        $values = $config->getMultiply(array_keys($keyList));

        foreach ($values as $key => $value) {
            $this->{$keyList[$key]} = $value;
        }
    }

    /**
     * reload model
     */
    public function reload()
    {
        $this->loadAttributes(false);
    }


    /**
     * save model
     * @return bool
     */
    public function save()
    {
        $this->beforeSave();
        if (!$this->validate()) {
            return false;
        }

        /** @var Configuration $config */
        $config = $this->configurationInit();
        $attributes = $this->getAttributes();
        $isSaved = true;
        foreach ($attributes as $key => $value) {
            if ($config->safeSet($this->getAttributeName($key), $value)) {
                $this->addError($key, 'Something went wrong. More details in the logs.');
                $isSaved = false;
            }
        }
        $this->afterSave();
        return $isSaved;
    }

    /**
     * @return string get class name
     */
    protected function getName()
    {
        $reflection = new \ReflectionClass(get_called_class());
        return $reflection->getShortName();
    }

    /**
     * @param string $attribute
     * @return string return new attribute name. Algorithm: [ClassName][AttributeName]
     */
    protected function getAttributeName($attribute)
    {
        $name = $this->getName();
        return $name . ucfirst($attribute);
    }

    /**
     * Before save trigger
     */
    protected function beforeSave()
    {
        $this->trigger(self::EVENT_BEFORE_SAVE, new Event());
    }

    /**
     * after save trigger
     */
    protected function afterSave()
    {
        $this->trigger(self::EVENT_AFTER_SAVE, new Event());
    }
}