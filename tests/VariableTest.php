<?php

use \arabcoders\variables\Variables;

class VariableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Variables
     */
    protected $v;
    protected $options = [ ];

    public function setUp()
    {
        $this->SetOptions( 'GET' );
    }

    public function testGetString()
    {

        $this->assertSame( $this->v->get( 'string' ), 'thisIsString', 'Couldnt Assert type string' );
    }

    public function testFloat()
    {
        $this->assertSame( $this->v->get( 'float' ), (float) 3.5, 'Couldnt Assert type float' );

    }

    public function testInteger()
    {
        $this->assertSame( $this->v->get( 'integer' ), 100100100, 'Couldnt Assert type integer' );
    }

    public function testIntegerMaxInt()
    {
        $this->assertSame( $this->v->get( 'maxInt' ), PHP_INT_MAX, 'Couldnt Assert type int as PHP_INT_MAX' );
    }

    public function testIntegerMax()
    {
        $this->assertSame( $this->v->get( 'HigherThenMaxInt' ), (string) ( PHP_INT_MAX + 1 ), 'Couldnt Keep Integer as String after reaching MAX_INT' );
    }

    public function testTypeArray()
    {
        $this->assertSame( $this->v->get( 'array' ), [ 'a' => 'a', 'b' => 'b' ], 'Couldnt Assert type array' );

    }

    public function testArrayDefaultOut()
    {
        $this->assertSame( $this->v->post( 'array', [ 'a' => 'a', 'b' => 'b' ] ), [ 'a' => 'a', 'b' => 'b' ], 'Couldnt defualt out to array' );
    }

    public function testArray()
    {
        $this->assertArrayHasKey( 'b', $this->v->get( 'array' ), 'Couldnt get array back' );
        $this->assertArrayHasKey( 'b', $this->v->get( 'array' ), 'Couldnt get back variable from array' );
    }

    public function testWeirdPassword()
    {
        $this->assertSame( $this->v->get( 'weiredPass' ), '1234567891012345678910', 'Pure int higher then MAX_INT passwords fails.' );
    }

    private function SetOptions( $type )
    {
        $this->options = [
            'method' => $type,
            'mock'   => [
                'string'           => (string) 'thisIsString',
                'float'            => (float) 3.5,
                'integer'          => (integer) 100100100,
                'weiredPass'       => '1234567891012345678910',
                'maxInt'           => PHP_INT_MAX,
                'HigherThenMaxInt' => (string) ( PHP_INT_MAX + 1 ),
                'array'            => [
                    'a' => 'a',
                    'b' => 'b'
                ]
            ]
        ];

        $this->v = new Variables( $this->options );
    }

    /**
     * @expectedException \arabcoders\variables\Exceptions\VariablesException
     * @expectedExceptionMessage Unable to Parse Request Type [NOMETHOD]
     */
    public function testException()
    {
        $this->v = new Variables( [ 'method' => 'NOMETHOD' ] );
    }
}
