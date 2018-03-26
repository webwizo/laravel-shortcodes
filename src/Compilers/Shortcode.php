<?php namespace Webwizo\Shortcodes\Compilers;

use Illuminate\Contracts\Support\Arrayable;

class Shortcode implements Arrayable
{
    /**
     * Shortcode name
     *
     * @var string
     */
    protected $name;

    /**
     * Shortcode Attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Shortcode content
     *
     * @var string
     */
    public $content;

    /**
     * Constructor
     *
     * @param string $name
     * @param array  $attributes
     * @param string $content
     */
    public function __construct($name, $attributes = [], $content)
    {
        $this->name = $name;
        $this->attributes = $attributes;
        $this->content = $content;
    }

    /**
     * Get html attribute
     *
     * @param  string $attribute
     *
     * @return string|null
     */
    public function get($attribute, $fallback = null)
    {
        $value = $this->{$attribute};
        if (!is_null($value)) {
            return $attribute . '="' . $value . '"';
        } elseif (!is_null($fallback)) {
            return $attribute . '="' . $fallback . '"';
        }
    }

    /**
     * Get shortcode name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get shortcode attributes
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Return array of attributes;
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Dynamically get attributes
     *
     * @param  string $param
     *
     * @return string|null
     */
    public function __get($param)
    {
        return isset($this->attributes[$param]) ? $this->attributes[$param] : null;
    }
}
