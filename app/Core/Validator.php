<?php
declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data   = [];

    /**
     * Validate $data against $rules.
     *
     * Rule string format examples:
     *   'required'
     *   'required|email'
     *   'required|min:6|max:255'
     *   'required|unique:users:email'
     *   'required|confirmed'    (expects data[$field.'_confirmation'])
     *
     * @param array<string, mixed>  $data
     * @param array<string, string> $rules
     * @return array<string, string>  field => first error message
     */
    public function validate(array $data, array $rules): array
    {
        $this->data   = $data;
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value      = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                [$ruleName, $ruleParam] = $this->parseRule($rule);

                // Skip all other rules for empty non-required fields
                if ($ruleName !== 'required' && ($value === null || $value === '')) {
                    continue;
                }

                $this->applyRule($field, $value, $ruleName, $ruleParam);

                // Stop checking remaining rules for this field after first error
                if (isset($this->errors[$field])) {
                    break;
                }
            }
        }

        return $this->errors;
    }

    public function passes(array $data, array $rules): bool
    {
        return empty($this->validate($data, $rules));
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function parseRule(string $rule): array
    {
        $parts = explode(':', $rule, 2);
        return [$parts[0], $parts[1] ?? null];
    }

    private function applyRule(string $field, mixed $value, string $rule, ?string $param): void
    {
        $label = ucfirst(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    $this->addError($field, "{$label} is required.");
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "{$label} must be a valid email address.");
                }
                break;

            case 'min':
                $min = (int) $param;
                if (is_string($value) && mb_strlen($value) < $min) {
                    $this->addError($field, "{$label} must be at least {$min} characters.");
                } elseif (is_numeric($value) && (float) $value < $min) {
                    $this->addError($field, "{$label} must be at least {$min}.");
                }
                break;

            case 'max':
                $max = (int) $param;
                if (is_string($value) && mb_strlen($value) > $max) {
                    $this->addError($field, "{$label} must not exceed {$max} characters.");
                } elseif (is_numeric($value) && (float) $value > $max) {
                    $this->addError($field, "{$label} must not exceed {$max}.");
                }
                break;

            case 'numeric':
                if (!is_numeric($value)) {
                    $this->addError($field, "{$label} must be a number.");
                }
                break;

            case 'alpha_num':
                if (!ctype_alnum((string) $value)) {
                    $this->addError($field, "{$label} may only contain letters and numbers.");
                }
                break;

            case 'unique':
                // Format: unique:table:column
                $parts  = explode(':', $param ?? '', 2);
                $table  = $parts[0];
                $column = $parts[1] ?? $field;
                if (!$this->isUnique($table, $column, $value)) {
                    $this->addError($field, "{$label} is already taken.");
                }
                break;

            case 'confirmed':
                $confirmation = $this->data[$field . '_confirmation'] ?? null;
                if ($value !== $confirmation) {
                    $this->addError($field, "{$label} confirmation does not match.");
                }
                break;
        }
    }

    private function isUnique(string $table, string $column, mixed $value): bool
    {
        try {
            $db  = Database::getInstance();
            $row = $db->fetch(
                "SELECT COUNT(*) AS cnt FROM `{$table}` WHERE `{$column}` = ?",
                [$value]
            );
            return ($row['cnt'] ?? 1) === 0;
        } catch (\Throwable) {
            return true; // Fail open – do not block if DB is unavailable
        }
    }

    private function addError(string $field, string $message): void
    {
        // Only store the first error per field
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }
}
