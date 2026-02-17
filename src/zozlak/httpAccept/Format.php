<?php

/*
 * The MIT License
 *
 * Copyright 2026 zozlak.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace zozlak\httpAccept;

/**
 * Represents a format used in HTTP Accept header
 *
 * @author zozlak
 */
class Format {

    /**
     * Creates a MIME format description from string (e.g. application/xml;q=0.5)
     */
    static public function fromString(string $format): self {
        list($types, $quality) = explode(';', $format . ';');

        list($type, $subtype) = explode('/', trim($types) . '/');
        $type    = !empty($type) ? $type : '*';
        $subtype = !empty($subtype) ? $subtype : '*';

        $quality = trim($quality);
        $q       = preg_replace('/^q=([0-9]+[.]?([0-9]+)?)$/', '\\1', $quality);
        if ($q !== $quality) {
            $quality = (float) $q;
        } else {
            $quality = 1;
        }

        return new self($type, $subtype, $quality);
    }

    /**
     * Creates a MIME format description
     * 
     */
    public function __construct(public readonly string $type = '*',
                                public readonly string $subtype = '*',
                                public readonly float $quality = 1.0) {
        
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
     * @return bool
     */
    public function matches(Format $accept): bool {
        $type    = $accept->type === $this->type || $accept->type === '*' || $this->type === '*';
        $subtype = $accept->subtype === $this->subtype || $accept->subtype === '*' || $this->subtype === '*';
        return $type && $subtype;
    }
}
