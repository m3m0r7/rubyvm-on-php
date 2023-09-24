<?php

declare(strict_types=1);

namespace RubyVM\VM\Stream;

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

        // Pick-up higher bytes
        foreach ($structures as $structure) {
            if ($structure->size() <= $high->size()) {
                continue;
            }

            $high = $structure;
        }

        return $high;
    }
}
