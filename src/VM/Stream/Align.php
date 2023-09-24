<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

use RubyVM\VM\Exception\RubyVMException;

/**
 * This class emulates some one in stdalign.h.
 */
class Align
{
    /**
     * @param SizeOf[] $structures
     */
    public static function alignOf(array $structures): SizeOf
    {
        $high = SizeOf::CHAR;

        if ($structures === []) {
            return $high;
        }

        // Validate structure
        foreach ($structures as $structure) {
            if (!($structure instanceof SizeOf) && !is_int($structure)) {
                throw new RubyVMException('The Align::alignOf accepts processing instantiated by SizeOf or integer property');
            }
        }

        // Pick-up higher bytes
        foreach ($structures as $structure) {
            if (is_int($structure)) {
                // When reached int, which property to be CHAR
                $structure = SizeOf::CHAR;
            }

            if ($structure->size() <= $high->size()) {
                continue;
            }

            $high = $structure;
        }

        return $high;
    }
}
