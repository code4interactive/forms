<?php
namespace Code4\Forms\Fields;

class htmlTag extends AbstractField {
    protected $_view = 'htmlTag';
    protected $_type = 'htmlTag';

    protected $tag = 'div';
    protected $content = '';


    public function __construct($itemId, $config) {

        if (array_key_exists('tag', $config)) {
            $this->tag = $config['tag'];
            unset($config['tag']);
        }

        parent::__construct($itemId, $config);

    }

    /**
     * Sets html tag for element
     * @param null $tag
     * @return $this|array|string
     */
    public function tag($tag = null) {
        if (is_null($tag)) {
            return $this->tag;
        }
        $this->tag = $tag;
        return $this;
    }

    /**
     * Sets element content
     * @param null $content
     * @return $this|string
     */
    public function content($content = null) {
        if (is_null($content)) {
            return $this->content;
        }
        $this->content = $content;
        return $this;
    }
}