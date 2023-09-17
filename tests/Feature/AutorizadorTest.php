<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento
 * Date: 03/04/19
 * Time: 14:55
 */

namespace Tests\Feature;

use Tests\TestCase;
use Tests\traits\DataFactory;

class AutorizadorTest extends TestCase
{
    use DataFactory;

    public function testCreateNewPet()
    {
        $pet = $this->makePetData(1);

        $this->assertNotNull($pet, "O pet não foi criado.");
        $this->assertEquals(true, $pet->ativo);
        $this->assertNotNull($pet->id_pets_planos, "O plano do pet não foi vinculado.");
        $this->assertNotNull($pet->cliente, "O pet não possui um cliente vinculado");
    }

    public function testeCreateNewCliente()
    {
        $cliente = $this->makeClienteData(1);

        $this->assertNotNull($cliente, "O cliente não foi criado.");
        $this->assertTrue($cliente->ativo, "O cliente deveria estar ativo.");
    }
}
