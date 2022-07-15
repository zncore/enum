<?php

namespace ZnCore\Enum\Constraints;

use Symfony\Component\Validator\Constraint;
use ZnCore\Enum\Helpers\EnumHelper;
use ZnDomain\Validator\Constraints\BaseValidator;

class EnumValidator extends BaseValidator
{

    protected $constraintClass = Enum::class;

    public function validate($value, Constraint $constraint)
    {
        $this->checkConstraintType($constraint);
        if ($this->isEmptyStringOrNull($value)) {
            return;
        }

        $isValid = EnumHelper::isValid($constraint->class, $value, $constraint->prefix);
        if (!$isValid) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
