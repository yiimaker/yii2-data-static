<?php

namespace ymaker\data\statics;

use yii\base\Model;
use yii\di\Instance;
use ymaker\configuration\Configuration;

/**
 *
 * @author Ruslan Saiko <ruslan.saiko.dev@gmail.com>
 */
class StaticData extends Model
{
    /**
     * @param array $config
     * @return StaticData
     */
    public static function getInstance($config = [])
    {
        $class = get_called_class();

        /** @var StaticData $instance */
        $instance = new $class($config);\Yii::createObject($class, $config);
        $instance->loadAttributes(false);
        return $instance;
    }
    /**
     * @var string|array
     */
    private $configuration = 'config';

    /**
     * @return array|string
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $config = $this->getConfiguration();
        if (is_array($config)) {
            $this->configuration = Instance::ensure($config);
        } else {
            $this->configuration = Instance::of($config)->get();
        }
    }

    /**
     * load model
     * @param bool $skipDefined
     */
    public function loadAttributes($skipDefined = true)
    {
        /** @var Configuration $config */
        $config = $this->getConfiguration();
        $attributes = $this->getAttributes();

        if ($skipDefined) {
            $attributes = array_filter($attributes, function ($value) {
                return empty($value);
            });
        }

        foreach ($attributes as $key => $value) {
            $this->{$key} = $config->get($this->getAttributeName($key));
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
     * @return bool
     */
    public function save()
    {

        if (!$this->validate()) {
            return false;
        }
        /** @var Configuration $config */
        $config = $this->getConfiguration();
        $attributes = $this->getAttributes();

        foreach ($attributes as $key => $value) {
            $config->set($this->getAttributeName($key), $value);
        }

        return true;
    }

    protected function getName()
    {
        $reflection = new \ReflectionClass(get_called_class());
        return $reflection->getShortName();
    }

    protected function getAttributeName($attribute)
    {
        $name = $this->getName();
        return $name . ucfirst($attribute);
    }
}