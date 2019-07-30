<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace zozlak;

use RuntimeException;

/**
 * Description of HttpAccept
 *
 * @author zozlak
 */
class HttpAccept {

    static private $list;

    /**
     * Gets a list of MIME formats accepted by the client sorted from most to less wanted.
     * 
     * @return array
     */
    static public function get(): array {
        if (self::$list === null) {
            self::parse();
        }
        return self::$list;
    }

    /**
     * Returns a MIME format among provided ones which matches the best formats accepted by the client.
     * 
     * Returned MIME format quality value is the quality of the matched client format.
     * 
     * Rises an exception when there is no match.
     * 
     * @param array $formats an array of strings or HttpAccept objects to be matched against formats accepted by the user
     * @return \zozlak\HttpAccept
     * @throws RuntimeException
     */
    static public function getBestMatch(array $formats): HttpAccept {
        if (self::$list === null) {
            self::parse();
        }

        $possible = [];
        foreach ($formats as $i) {
            if (!is_a($i, self::class)) {
                $i = new HttpAccept($i);
            }
            $possible[] = $i;
        }

        foreach (self::$list as $i) {
            foreach ($possible as $j) {
                if ($j->matches($i)) {
                    return new HttpAccept($j->getFullType() . ';q=' . $i->getQuality());
                }
            }
        }
        throw new RuntimeException('No matching format');
    }

    /**
     * Parses the HTTP Accept header.
     * 
     * Used internally and during tests.
     */
    static public function parse() {
        $accepts   = $qualities = [];
        $header    = $_SERVER['HTTP_ACCEPT'] ?? '*/*';
        $header    = explode(',', $header);
        foreach ($header as $n => $i) {
            $tmp           = new HttpAccept($i);
            $accepts[$n]   = $tmp;
            $qualities[$n] = $tmp->getQuality();
        }
        arsort($qualities);
        self::$list = [];
        foreach (array_keys($qualities) as $i) {
            self::$list[] = $accepts[$i];
        }
    }

    private $type    = '*';
    private $subtype = '*';
    private $quality = 1;

    /**
     * Creates a MIME format description from string.
     * 
     * @param string $format MIME format description. 
     *   Can optionally contain a `;q=weight` suffix specyfing a so called quality. 
     *   If not specified, the quality is assumed to be 1.
     */
    public function __construct(string $format = '*/*') {
        $format = explode(';', $format);

        $types = explode('/', trim($format[0]));
        if (!empty($types[0])) {
            $this->type = $types[0];
        }
        if (isset($types[1]) && !empty($types[1])) {
            $this->subtype = $types[1];
        }

        if (count($format) > 1) {
            $format[1] = trim($format[1]);
            $q         = preg_replace('/^q=([0-9]+[.]?([0-9]+)?)$/', '\\1', $format[1]);
            if ($q !== $format[1]) {
                $this->quality = (float) $q;
            }
        }
    }

    /**
     * Returns MIME type's type (the first part of the MIME string)
     * 
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * Returns MIME type's subtype (the second part of the MIME string)
     * 
     * @return string
     */
    public function getSubtype(): string {
        return $this->subtype;
    }

    /**
     * Returns a full MIME type
     * 
     * @return string
     */
    public function getFullType(): string {
        return $this->type . '/' . $this->subtype;
    }

    /**
     * Returns the quality value.
     * 
     * @return string
     */
    public function getQuality(): float {
        return $this->quality;
    }

    /**
     * Serializes MIME type to a string. Includes the quality.
     * 
     * @return string
     */
    public function __toString(): string {
        return $this->getFullType() . ';q=' . $this->quality;
    }

    /**
     * Checks if a given MIME type matches another MIME type.
     * 
     * Matching rules:
     * - both types must match or one of the types must be a wildcard (`*`)
     * - both subtypes must match or one of the subtypes must be a wildcard (`*`)
     * - quality doesn't matter
     * 
     * @param \zozlak\HttpAccept $accept
     * @return bool
     */
    public function matches(HttpAccept $accept): bool {
        $type    = $accept->getType() === $this->getType() || $accept->getType() === '*' || $this->getType() === '*';
        $subtype = $accept->getSubType() === $this->getSubType() || $accept->getSubType() === '*' || $this->getSubType() === '*';
        return $type && $subtype;
    }

}
