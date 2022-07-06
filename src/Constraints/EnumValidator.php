<?php

namespace ZnCore\Enum\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use ZnCore\Validation\Constraints\BaseValidator;
use ZnCore\Enum\Helpers\EnumHelper;

class EnumValidator extends BaseValidator
{

    protected $constraintClass = Enum::class;

    public function validate($value, Constraint $constraint)
    {
        /*if (!$constraint instanceof Enum) {
            throw new UnexpectedTypeException($constraint, Enum::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }*/

        $this->checkConstraintType($constraint);
        if ($this->isEmptyStringOrNull($value)) {
            return;
        }

        /*if (!is_numeric($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'int');
        }*/

        $isValid = EnumHelper::isValid($constraint->class, $value, $constraint->prefix);
        if ( ! $isValid) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
