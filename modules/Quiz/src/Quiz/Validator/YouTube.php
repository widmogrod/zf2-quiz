<?php
/**
 * @author gabriel
 */
namespace Quiz\Validator;

use Zend\Validator\Regex;

class YouTube extends Regex
{
    /**
     * Regular expression pattern
     * @var string
     */
    const PATTERN = '/http:\/\/(?:www\.)?youtube.*watch\?v=([a-zA-Z0-9\-_]+)/';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "Invalid type given. String, integer or float expected",
        self::NOT_MATCH => "'%value%' is not valid YouTube link",
        self::ERROROUS  => "There was an internal error while using the pattern '%pattern%'",
    );

    public function __construct()
    {
        parent::__construct(self::PATTERN);
    }
}