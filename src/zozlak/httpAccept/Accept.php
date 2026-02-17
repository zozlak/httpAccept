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
 * Description of Header
 *
 * @author zozlak
 */
class Accept {

    static public function fromHeader(string $name = 'accept'): self {
        $value = $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $name))] ?? '';
        return new self($value);
    }

    /**
     * 
     * @var array<Format>
     */
    public readonly array $list;

    public function __construct(string $value = '*/*') {
        $formats   = $qualities = [];
        foreach (explode(',', $value) as $n => $i) {
            $tmp           = Format::fromString($i);
            $formats[$n]   = $tmp;
            $qualities[$n] = $tmp->quality;
        }
        arsort($qualities);

        $sorted = [];
        foreach (array_keys($qualities) as $i) {
            $sorted[] = $formats[$i];
        }
        $this->list = $sorted;
    }

    /**
     * Gets a list of MIME formats accepted by the client sorted from most to less wanted.
     * 
     * @return array<Format>
     */
    public function get(): array {
        return $this->list;
    }

    /**
     * Returns a MIME format among provided ones which matches the best formats
     * accepted by this object.
     * 
     * Returned MIME format quality value is the quality of the matched client format.
     * 
     * Raises an exception when there is no match.
     * 
     * @param array<string|Format> $formats a list of formats to be matched against
     * @throws NoMatchException
     */
    public function getBestMatch(array $formats): Format {
        $possible = [];
        foreach ($formats as $i) {
            if (is_string($i)) {
                $i = Format::fromString($i);
            }
            $possible[] = $i;
        }

        foreach ($this->list as $i) {
            foreach ($possible as $j) {
                if ($j->matches($i)) {
                    return new Format($j->type, $j->subtype, $i->quality);
                }
            }
        }
        throw new NoMatchException();
    }
}
