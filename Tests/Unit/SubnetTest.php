<?php

namespace hschulz\Network\Tests;

use \PHPUnit\Framework\TestCase;
use \hschulz\Network\Subnet;

final class SubnetTest extends TestCase {

    /**
     *
     * @var Subnet
     */
    protected $subnet = null;

    protected function setUp() {
        $this->subnet = new Subnet(0, Subnet::NOTATION_INVALID);
    }

    protected function tearDown() {
        $this->subnet = null;
    }

    public function testInitializedWithInvalidParameters() {
        $this->assertFalse($this->subnet->isValid());
    }

    public function testCanBeCreatedWithDotDecimalValue() {
        $this->subnet = new Subnet('255.0.0.0', Subnet::NOTATION_DOT);

        $this->assertTrue($this->subnet->isValid());
    }

    public function testCanBeCreatedWithCidrValue() {
        $this->subnet = new Subnet('13', Subnet::NOTATION_CIDR);

        $this->assertTrue($this->subnet->isValid());
    }

    public function testCanBeCreatedWithBinaryValue() {
        $this->subnet = new Subnet('11111111.11110000.00000000.00000000', Subnet::NOTATION_BINARY);

        $this->assertTrue($this->subnet->isValid());
    }

    public function testCanBeModifiedWithDotDecimalValue() {

        $this->subnet->fromDot('255.255.128.0');

        $this->assertEquals('255.255.128.0', $this->subnet->toDot());

        $this->assertTrue($this->subnet->isValid());
    }

    public function testCanBeModifiedWithCidrValue() {

        $this->subnet->fromCIDR(18);

        $this->assertEquals('255.255.192.0', $this->subnet->toDot());

        $this->assertTrue($this->subnet->isValid());
    }

    public function testCanBeModifiedWithBinaryValue() {

        $this->subnet->fromBin('11111111.11111111.11111111.11111100');

        $this->assertEquals('255.255.255.252', $this->subnet->toDot());
    }

    public function testCanBeConvertedToDotDecimalValue() {

        $this->subnet->fromCIDR(29);

        $this->assertEquals('255.255.255.248', $this->subnet->toDot());
    }

    public function testCanBeConvertedToCidrValue() {

        $this->subnet->fromDot('255.255.255.0');

        $this->assertEquals(24, $this->subnet->toCIDR());
    }

    public function testCanBeConvertedToBinaryValue() {

        $this->subnet->fromCIDR(8);

        $this->assertEquals('11111111.00000000.00000000.00000000', $this->subnet->toBin());
    }

    public function testCanBeCastAsString() {

        $this->subnet->fromCIDR(1);

        $this->assertEquals('128.0.0.0', (string) $this->subnet);
    }

    public function testCanBeClassA() {

        $this->subnet->fromCIDR(8);

        $this->assertTrue($this->subnet->isValid());
        $this->assertTrue($this->subnet->isClassA());
        $this->assertFalse($this->subnet->isClassB());
        $this->assertFalse($this->subnet->isClassC());
        $this->assertFalse($this->subnet->isClassD());
        $this->assertFalse($this->subnet->isClassE());
    }

    public function testCanBeClassB() {

        $this->subnet->fromCIDR(16);

        $this->assertTrue($this->subnet->isValid());
        $this->assertFalse($this->subnet->isClassA());
        $this->assertTrue($this->subnet->isClassB());
        $this->assertFalse($this->subnet->isClassC());
        $this->assertFalse($this->subnet->isClassD());
        $this->assertFalse($this->subnet->isClassE());
    }

    public function testCanBeClassC() {

        $this->subnet->fromCIDR(24);

        $this->assertTrue($this->subnet->isValid());
        $this->assertFalse($this->subnet->isClassA());
        $this->assertFalse($this->subnet->isClassB());
        $this->assertTrue($this->subnet->isClassC());
        $this->assertFalse($this->subnet->isClassD());
        $this->assertFalse($this->subnet->isClassE());
    }

    public function testCanBeClassD() {

        $this->subnet->fromCIDR(3);

        $this->assertTrue($this->subnet->isValid());
        $this->assertFalse($this->subnet->isClassA());
        $this->assertFalse($this->subnet->isClassB());
        $this->assertFalse($this->subnet->isClassC());
        $this->assertTrue($this->subnet->isClassD());
        $this->assertFalse($this->subnet->isClassE());
    }

    public function testCanBeClassE() {

        $this->subnet->fromCIDR(32);

        $this->assertTrue($this->subnet->isValid());
        $this->assertFalse($this->subnet->isClassA());
        $this->assertFalse($this->subnet->isClassB());
        $this->assertFalse($this->subnet->isClassC());
        $this->assertFalse($this->subnet->isClassD());
        $this->assertTrue($this->subnet->isClassE());
    }
}
