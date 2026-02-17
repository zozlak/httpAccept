<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace zozlak\httpAccept;

/**
 * Description of HttpAcceptTest
 *
 * @author zozlak
 */
class FormatTest extends \PHPUnit\Framework\TestCase {

    public function testEmpty(): void {
        $format = Format::fromString('');
        $this->assertEquals('*', $format->type);
        $this->assertEquals('*', $format->subtype);
        $this->assertEquals('*/*', $format->getFullType());
        $this->assertEquals('*/*;q=1', (string) $format);
    }

    public function testFull(): void {
        $format = Format::fromString('application/n-triples;q=0.25');
        $this->assertEquals('application', $format->type);
        $this->assertEquals('n-triples', $format->subtype);
        $this->assertEquals('application/n-triples', $format->getFullType());
        $this->assertEquals('application/n-triples;q=0.25', (string) $format);
    }

    public function testStrange1(): void {
        $format = Format::fromString('/;q=60.');
        $this->assertEquals('*', $format->type);
        $this->assertEquals('*', $format->subtype);
        $this->assertEquals('*/*', $format->getFullType());
        $this->assertEquals('*/*;q=60', (string) $format);
    }

    public function testStrange2(): void {
        $format = Format::fromString('a/;q=-0.3');
        $this->assertEquals('a', $format->type);
        $this->assertEquals('*', $format->subtype);
        $this->assertEquals('a/*', $format->getFullType());
        $this->assertEquals('a/*;q=1', (string) $format);
    }

    public function testMatchesExact(): void {
        $accept1 = Format::fromString('a/b;q=1');
        $accept2 = Format::fromString('a/b;q=0');
        $this->assertTrue($accept1->matches($accept2));
        $this->assertTrue($accept2->matches($accept1));
    }

    public function testMatchesWildcard(): void {
        $accept1 = Format::fromString('a/*;q=1');
        $accept2 = Format::fromString('*/b;q=0');
        $this->assertTrue($accept1->matches($accept2));
        $this->assertTrue($accept2->matches($accept1));
    }
}
