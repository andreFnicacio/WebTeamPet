<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento
 * Date: 06/11/18
 * Time: 16:30
 */

namespace Tests\Unit;

use App\Models\Pets;
use Tests\TestCase;
use Tests\traits\DataFactory;

class PetsTest extends TestCase
{
    use DataFactory;
    private $pet = null;

    protected function setUp()
    {
        parent::setUp();

        $this->pet = Pets::first();
    }

    public function testPetExists()
    {
        $this->assertNotNull($this->pet);
    }
}
