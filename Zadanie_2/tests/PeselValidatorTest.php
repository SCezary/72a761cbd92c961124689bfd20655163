<?php

namespace App\tests;

use App\Cezary\PeselValidator;
use PHPUnit\Framework\TestCase;

final class PeselValidatorTest extends TestCase
{
    public function testPeselValidatorNumberLength()
    {
        $result = PeselValidator::validate('5503010119');
        $this->assertFalse($result['valid']);
        $this->assertEquals(PeselValidator::ERROR_CODE_INVALID_LENGTH, $result['code']);

        $result = PeselValidator::validate('550301011930');
        $this->assertFalse($result['valid']);
        $this->assertEquals(PeselValidator::ERROR_CODE_INVALID_LENGTH, $result['code']);

        $result = PeselValidator::validate('5503010119a');
        $this->assertFalse($result['valid']);
        $this->assertNotEquals(PeselValidator::ERROR_CODE_INVALID_LENGTH, $result['code']);
    }

    public function testPeselValidatorNumberFormat()
    {
        $result = PeselValidator::validate('55030lo1193');
        $this->assertFalse($result['valid']);
        $this->assertEquals(PeselValidator::ERROR_CODE_INVALID_FORMAT, $result['code']);

        $result = PeselValidator::validate(55030991199);
        $this->assertFalse($result['valid']);
        $this->assertNotEquals(PeselValidator::ERROR_CODE_INVALID_FORMAT, $result['code']);
    }

    public function testPeselValidatorForDate()
    {
        $result = PeselValidator::validate('99133100004');
        $this->assertFalse($result['valid']);
        $this->assertEquals(PeselValidator::ERROR_CODE_INVALID_DATE, $result['code']);
    }

    public function testPeselValidatorForNumberFactor()
    {
        $result = PeselValidator::validate('55030101239');
        $this->assertFalse($result['valid']);
        $this->assertEquals(PeselValidator::ERROR_CODE_INVALID_FACTOR, $result['code']);

        $result = PeselValidator::validate('55030101193');
        $this->assertTrue($result['valid']);
        $this->assertEquals(null, $result['code']);

        $result = PeselValidator::validate('55030101230');
        $this->assertTrue($result['valid']);
        $this->assertEquals(null, $result['code']);
    }
}