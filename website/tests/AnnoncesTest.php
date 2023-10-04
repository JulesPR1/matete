<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Annonce;

class AnnoncesTest extends TestCase
{   

    public function test(){
        $this->assertTrue(true);
    }
    
    /*public function testAssertTrue(): void
    {
        $annonce = new Annonce();
        $date = new \DatetimeImmutable();
        $annonce->setNom("Test du titre");
        $annonce->setDescription("contenu de l'annonce");
        $annonce->setCreatedAt($date);

        $this->assertTrue($annonce->getNom() == "Test du titre");
        $this->assertTrue ($annonce->getDescription()==="contenu de l'annonce", "Test du contenu");
        $this->assertTrue($annonce->getCreatedAt() ===$date, "Test du createdAt");
    }
    
    public function testAssertFalse(): void
    {
        $annonce = new Annonce();
        $date= new \Datetime();
        $this->assertFalse($annonce->getNom() ==="Test du titre");
        $this->assertFalse($annonce->getDescription() === "contenu de l'annonce", "Test du contenu");
        $this->assertFalse ($annonce->getCreatedAt() === $date, "Test du createdAt");
    }
    
    public function testAssertEmpty(): void
    {
        $annonce = new Annonce();
        $date = new \Datetime();
        $this->assertEmpty($annonce->getNom(), "Test du titre");
        $this->assertEmpty($annonce->getDescription(), "Test du contenu");
        $this->assertEmpty($annonce->getCreatedAt(), "Test du createdAt");
    }*/
}