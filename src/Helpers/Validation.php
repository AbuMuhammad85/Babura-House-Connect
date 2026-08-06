<?php

namespace App\Helpers;

class Validation
{
    protected array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $rulesList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($rulesList as $rule) {
                $ruleName = $rule;
                $ruleValue = null;

                if (strpos($rule, ':') !== false) {
                    list($ruleName, $ruleValue) = explode(':', $rule, 2);
                }

                $this->applyRule($field, $value, $ruleName, $ruleValue);
            }
        }

        return empty($this->errors);
    }

    protected function applyRule(string $field, $value, string $ruleName, $ruleValue): void
    {
        switch ($ruleName) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->addError($field, "The " . str_replace('_', ' ', $field) . " field is required.");
                }
                break;
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "The " . str_replace('_', ' ', $field) . " must be a valid email address.");
                }
                break;
            case 'min':
                if (!empty($value) && strlen($value) < (int)$ruleValue) {
                    $this->addError($field, "The " . str_replace('_', ' ', $field) . " must be at least {$ruleValue} characters.");
                }
                break;
            case 'max':
                if (!empty($value) && strlen($value) > (int)$ruleValue) {
                    $this->addError($field, "The " . str_replace('_', ' ', $field) . " may not be greater than {$ruleValue} characters.");
                }
                break;
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "The " . str_replace('_', ' ', $field) . " must be a number.");
                }
                break;
        }
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public static function sanitize(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = filter_var_array($value, FILTER_SANITIZE_SPECIAL_CHARS);
            } else {
                $sanitized[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $sanitized;
    }
}
