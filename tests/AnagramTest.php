<?php
use PHPUnit\Framework\TestCase;

$root = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
require $root.'/anagrams.php';

class AnagramTest extends TestCase
{
    public function testResultTrue()
    {
        $string1 = 'amor';
        $string2 = 'Roma';
        $this->assertTrue(isAnagram($string1, $string2));

        $string1 = 'admirer';
        $string2 = 'married';
        $this->assertTrue(isAnagram($string1, $string2));

        $string1 = 'AstroNomers';
        $string2 = 'no more stars';
        $this->assertTrue(isAnagram($string1, $string2));
    }

    public function testResultFalse()
    {
        $string1 = '';
        $string2 = '';
        $this->assertNotTrue(isAnagram($string1, $string2));

        $string1 = 'Astro-Nomers';
        $string2 = 'no_more_stars';
        $this->assertNotTrue(isAnagram($string1, $string2));

        $string1 = '123456';
        $string2 = '654321';
        $this->assertNotTrue(isAnagram($string1, $string2));
    }
}
