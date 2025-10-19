<?php

namespace test\webfilesframework;

use PHPUnit\Framework\TestCase;

abstract class MAbstractWebfilesFramworkTest extends TestCase {

    protected function setUp(): void
    {
        parent::setUp();
        //echo "\n[setUp] Starte Test: " . $this->getName() . "\n";
    }

    protected function tearDown(): void
    {
        //echo "[tearDown] Beende Test: " . $this->getName() . "\n";
        parent::tearDown();
    }

}