<?php
declare(strict_types=1);

namespace Rosamarsky\LaravelDoctrineOdm\Types;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Doctrine\ODM\MongoDB\Types\ClosureToPHP;
use Doctrine\ODM\MongoDB\Types\Type;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;

class CarbonDateType extends Type
{
    use ClosureToPHP;

    public const CARBON = 'carbon';

    /**
     * @param Carbon|null $value
     * @return UTCDateTime|null
     */
    public function convertToDatabaseValue($value): ?UTCDateTime
    {
        if (! $value) {
            return null;
        }

        return new UTCDateTime(strtotime($value->toDateTimeString()) * 1000);
    }

    /**
     * @param UTCDateTime|null $value
     *
     * @return Carbon|null
     * @throws \Exception
     */
    public function convertToPHPValue($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof UTCDateTime) {
            return Carbon::instance($value->toDateTime());
        }

        if (isset($value['date']) && ($value['date'] instanceof Timestamp)) {
            return Carbon::createFromTimestamp($value['date']->getTimestamp());
        }

        try {
            if (is_string($value)) {
                return Carbon::createFromTimestamp(strtotime($value));
            }
        } catch (InvalidFormatException $e) {}

        throw new \Exception('Invalid date format in mongodb storage.');
    }
}