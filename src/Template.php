<?php
/**
 * Created by PhpStorm.
 * User: inventor
 * Date: 04.04.2016
 * Time: 19:00
 */

namespace NewInventor\Template;


use NewInventor\ConfigTool\Config;
use NewInventor\TypeChecker\Exception\ArgumentException;
use NewInventor\TypeChecker\SimpleTypes;
use NewInventor\TypeChecker\TypeChecker;

class Template
{
    /** @var string */
    protected $template;
    /** @var array */
    protected $placeholders = null;
    /** @var array */
    protected $replacements;
    /** @var array */
    protected $borders;

    /**
     * Template constructor.
     *
     * @param string $template
     */
    public function __construct($template)
    {
        TypeChecker::getInstance()
            ->isString($template, 'template')
            ->throwTypeErrorIfNotValid();
        $this->template = $template;

        $this->loadBorders();

        $this->placeholders = $this->parsePlaceholders();
    }

    protected function loadBorders()
    {
        $borders = Config::getInstance()->get(['renderer', 'placeholders', 'borders']);
        if (static::isValidBorders($borders)) {
            $this->borders = $borders;
        }
    }

    /**
     * @param array $borders
     *
     * @return bool
     * @throws ArgumentException
     */
    public static function isValidBorders(array $borders)
    {
        TypeChecker::getInstance()
            ->checkArray($borders, [SimpleTypes::STRING], 'borders')
            ->throwCustomErrorIfNotValid('Граница должна быть строкой.');
        if (!isset($borders[0]) || !isset($borders[1])) {
            throw new ArgumentException('Границы должны быть массивом из 2-х элементов - левой и правой границей заполнителя.',
                'borders');
        }

        return true;
    }

    /**
     * @return array
     */
    protected function parsePlaceholders()
    {
        if ($this->placeholders !== null) {
            return $this->placeholders;
        }
        $regexp = $this->getPlaceholderSearchRegexp();
        $searchRes = preg_match_all($regexp, $this->template, $foundPlaceholders);
        if ($searchRes === 0 || $searchRes === false) {
            return [];
        }

        return $foundPlaceholders;
    }

    /**
     * @return string
     * @throws ArgumentException
     */
    protected function getPlaceholderSearchRegexp()
    {
        $leftBorder = preg_quote($this->borders[0]);
        $rightBorder = preg_quote($this->borders[1]);
        $regexp = "{$leftBorder}([^{$rightBorder}]*){$rightBorder}";
        $regexp = "/{$regexp}/u";

        return $regexp;
    }

    public function getPlaceholders()
    {
        if (isset($this->placeholders[1])) {
            return $this->placeholders[1];
        }
        return [];
    }

    public function setReplacements(array $replacements)
    {
        TypeChecker::getInstance()
            ->checkArray($replacements, [SimpleTypes::STRING], 'replacements')
            ->throwCustomErrorIfNotValid('Тексты для замены должены быть строкой.');
        $this->replacements = $replacements;
    }


    public function getReplaced()
    {
        if (isset($this->placeholders[0])) {
            return str_replace($this->placeholders[0], $this->replacements, $this->template);
        }
        return '';
    }
}