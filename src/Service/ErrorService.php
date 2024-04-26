<?php

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ErrorService
{
    /**
     * Parses violations to an array list
     *
     * @param ConstraintViolationListInterface $violationList
     * @return array array list of violations
     */
    public function parseViolations(ConstraintViolationListInterface $violationList): array
    {
        $parsedList = [];

        foreach ($violationList as $item) {
            $parsedList[] = $item->getMessage();
        }

        return $parsedList;
    }
}