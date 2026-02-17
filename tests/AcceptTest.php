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
 * Description of HeaderTest
 *
 * @author zozlak
 */
class AcceptTest extends \PHPUnit\Framework\TestCase {

    private const TEST_HEADER = 'text/html, application/xml;q=0.9, */*;q=0.8,application/xhtml+xml';

    public function testGetAll(): void {
        $header   = new Accept(self::TEST_HEADER);
        $formats  = $header->get();
        $formats  = array_map(fn($x) => (string) $x, $formats);
        $this->assertEquals(4, count($formats));
        $expected = [
            'text/html;q=1',
            'application/xhtml+xml;q=1',
            'application/xml;q=0.9',
            '*/*;q=0.8',
        ];
        $this->assertEquals($expected, $formats);
    }

    public function testFromHeader(): void {
        $_SERVER['HTTP_ACCEPT'] = self::TEST_HEADER;
        $header                 = Accept::fromHeader('accept');
        $formats                = $header->get();
        $formats                = array_map(fn($x) => (string) $x, $formats);
        $this->assertEquals(4, count($formats));
        $expected               = [
            'text/html;q=1',
            'application/xhtml+xml;q=1',
            'application/xml;q=0.9',
            '*/*;q=0.8',
        ];
        $this->assertEquals($expected, $formats);
    }

    public function testBestMatchExact(): void {
        $header = new Accept(self::TEST_HEADER);
        $best   = $header->getBestMatch(['application/json', 'application/xml']);
        $this->assertEquals('application/xml;q=0.9', (string) $best);
    }

    public function testBestMatchWildcard(): void {
        $header = new Accept(self::TEST_HEADER);
        $best   = $header->getBestMatch(['application/json', 'application/*']);
        $this->assertEquals('application/*;q=1', (string) $best);
    }

    public function testBestMatchAny(): void {
        $header = new Accept(self::TEST_HEADER);
        $best   = $header->getBestMatch(['application/json', 'application/n-triples']);
        $this->assertEquals('application/json;q=0.8', (string) $best);
    }

    public function testBestMatchException(): void {
        $header = new Accept('text/html,application/xhtml+xml,application/xml;q=0.9');
        $this->expectException(NoMatchException::class);
        $this->expectExceptionCode(406);
        $header->getBestMatch(['application/json', 'application/n-triples']);
    }

    public function testAcceptEncoding(): void {
        $header = new Accept('gzip, deflate;q=0.8, identity');
        $best   = $header->getBestMatch(['deflate']);
        $this->assertEquals('deflate/*;q=0.8', (string) $best);
    }
}
