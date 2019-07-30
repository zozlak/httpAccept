<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace zozlak;

/**
 * Description of HttpAcceptTest
 *
 * @author zozlak
 */
class HttpAcceptTest extends \PHPUnit\Framework\TestCase {

    public function testEmpty() {
        $accept = new HttpAccept();
        $this->assertEquals('*', $accept->getType());
        $this->assertEquals('*', $accept->getSubType());
        $this->assertEquals('*/*', $accept->getFullType());
        $this->assertEquals('*/*;q=1', (string) $accept);
    }

    public function testFull() {
        $accept = new HttpAccept('application/n-triples;q=0.25');
        $this->assertEquals('application', $accept->getType());
        $this->assertEquals('n-triples', $accept->getSubType());
        $this->assertEquals('application/n-triples', $accept->getFullType());
        $this->assertEquals('application/n-triples;q=0.25', (string) $accept);
    }

    public function testStrange1() {
        $accept = new HttpAccept('/;q=60.');
        $this->assertEquals('*', $accept->getType());
        $this->assertEquals('*', $accept->getSubType());
        $this->assertEquals('*/*', $accept->getFullType());
        $this->assertEquals('*/*;q=60', (string) $accept);
    }

    public function testStrange2() {
        $accept = new HttpAccept('a/;q=-0.3');
        $this->assertEquals('a', $accept->getType());
        $this->assertEquals('*', $accept->getSubType());
        $this->assertEquals('a/*', $accept->getFullType());
        $this->assertEquals('a/*;q=1', (string) $accept);
    }

    public function testMatchesExact() {
        $accept1 = new HttpAccept('a/b;q=1');
        $accept2 = new HttpAccept('a/b;q=0');
        $this->assertTrue($accept1->matches($accept2));
        $this->assertTrue($accept2->matches($accept1));
    }

    public function testMatchesWildcard() {
        $accept1 = new HttpAccept('a/*;q=1');
        $accept2 = new HttpAccept('*/b;q=0');
        $this->assertTrue($accept1->matches($accept2));
        $this->assertTrue($accept2->matches($accept1));
    }

    public function testGetAll() {
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $accepts                = HttpAccept::get();
        $this->assertEquals(4, count($accepts));
        $this->assertEquals('text/html;q=1', (string) $accepts[0]);
        $this->assertEquals('application/xhtml+xml;q=1', (string) $accepts[1]);
        $this->assertEquals('application/xml;q=0.9', (string) $accepts[2]);
        $this->assertEquals('*/*;q=0.8', (string) $accepts[3]);
    }

    public function testBestMatchExact() {
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $best                   = HttpAccept::getBestMatch(['application/json', 'application/xml']);
        $this->assertEquals('application/xml;q=0.9', (string) $best);
    }

    public function testBestMatchWildcard() {
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        HttpAccept::parse();
        $best                   = HttpAccept::getBestMatch(['application/json', 'application/*']);
        $this->assertEquals('application/*;q=1', (string) $best);
    }

    public function testBestMatchAny() {
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        HttpAccept::parse();
        $best                   = HttpAccept::getBestMatch(['application/json', 'application/n-triples']);
        $this->assertEquals('application/json;q=0.8', (string) $best);
    }

    public function testBestMatchException() {
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9';
        HttpAccept::parse();
        $this->expectExceptionMessage('No matching format');
        HttpAccept::getBestMatch(['application/json', 'application/n-triples']);
    }

}
