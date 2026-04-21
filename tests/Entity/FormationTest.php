<?php

namespace App\Tests\Entity;

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{
    public function testGetPublishedAtStringReturnsFormattedDate(): void
    {
        $formation = new Formation();
        $formation->setPublishedAt(new \DateTime('2009-04-05'));

        $this->assertSame('05/04/2009', $formation->getPublishedAtString());
    }

    public function testGetPublishedAtStringReturnsEmptyStringWhenDateIsNull(): void
    {
        $formation = new Formation();

        $this->assertSame('', $formation->getPublishedAtString());
    }
}
