<?php

namespace App\Cezary;

class PeselValidator {
    private const PESEL_NUMBER_FACTORS = [1,3,7,9,1,3,7,9,1,3];

    public const ERROR_CODE_INVALID_LENGTH = 1;
    public const ERROR_CODE_INVALID_FORMAT = 2;
    public const ERROR_CODE_INVALID_DATE = 3;
    public const ERROR_CODE_INVALID_FACTOR = 4;

    protected static function validateLength(string $pslNumber): bool
    {
        return strlen($pslNumber) === 11;
    }

    protected static function validateFormat(string $pslNumber): bool
    {
        return is_numeric($pslNumber);
    }

    protected static function validateDate(string $pslNumber): bool
    {
        $year = substr($pslNumber, 0, 2);
        $month = substr($pslNumber, 2, 2);
        $day = substr($pslNumber, 4, 2);


        switch (true) {
            case $month <= 12:
                $year = "19{$year}";
                break;
            case $month >= 21 && $month <= 32:
                $year = "20{$year}";
                $month -= 20;
                break;
            case $month >= 41 && $month <= 52:
                $year = "21{$year}";
                $month -= 40;
                break;
            case $month >= 61 && $month <= 72:
                $year = "22{$year}";
                $month -= 60;
                break;
            case $month >= 81 && $month <= 92:
                $year = "18{$year}";
                $month -= 80;
                break;
        }

        return checkdate($month, $day, $year);
    }

    protected static function validateFactors(string $pslNumber): bool
    {
        $index = 0;
        $factorySum = array_reduce(str_split(substr($pslNumber, 0, 10)), function ($stock, $number) use (&$index) {
            if (!isset(self::PESEL_NUMBER_FACTORS[$index])) return 0; // To avoid some issues.
            $stock += $number * self::PESEL_NUMBER_FACTORS[$index];
            $index += 1;
            return $stock;
        });

        if ($factorySum <= 0) return false;
        $lastPslDigit = substr($pslNumber, 10, 1);
        $modulo = $factorySum % 10;
        $lastFactor = $modulo === 0 ? 0 : (10 - $modulo);

        return $lastFactor === (int)$lastPslDigit;
    }

    /**
     * Validate PESEL number.
     *
     * @param string|int $pslNumber
     * @return array
     */
    public static function validate(string|int $pslNumber): array
    {
        $pslNumber = (string)$pslNumber;

        if (!self::validateLength($pslNumber)) return self::response(false, self::ERROR_CODE_INVALID_LENGTH);

        if (!self::validateFormat($pslNumber)) return self::response(false, self::ERROR_CODE_INVALID_FORMAT);

        if (!self::validateDate($pslNumber)) return self::response(false, self::ERROR_CODE_INVALID_DATE);

        if (!self::validateFactors($pslNumber)) return self::response(false, self::ERROR_CODE_INVALID_FACTOR);

        return self::response(true);
    }

    private static function response(bool $valid, int $code = null): array
    {
        return [
            'valid' => $valid,
            'code' => $code
        ];
    }
}