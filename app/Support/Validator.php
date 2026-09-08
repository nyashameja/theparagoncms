<?php

namespace App\Support;

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): self
    {
        $v = new self($data);
        $v->validate($rules);
        return $v;
    }

    public function validate(array $rules): void
    {
        foreach ($rules as $field => $ruleString) {
            $rules_list = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules_list as $rule) {
                [$ruleName, $ruleParam] = array_pad(explode(':', $rule, 2), 2, null);

                switch ($ruleName) {
                    case 'required':
                        if ($value === null || $value === '') {
                            $this->addError($field, 'This field is required.');
                        }
                        break;

                    case 'email':
                        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->addError($field, 'Please enter a valid email address.');
                        }
                        break;

                    case 'min':
                        if ($value !== null && mb_strlen((string)$value) < (int)$ruleParam) {
                            $this->addError($field, "Minimum {$ruleParam} characters required.");
                        }
                        break;

                    case 'max':
                        if ($value !== null && mb_strlen((string)$value) > (int)$ruleParam) {
                            $this->addError($field, "Maximum {$ruleParam} characters allowed.");
                        }
                        break;

                    case 'numeric':
                        if ($value !== null && $value !== '' && !is_numeric($value)) {
                            $this->addError($field, 'This field must be a number.');
                        }
                        break;

                    case 'integer':
                        if ($value !== null && $value !== '' && !ctype_digit(ltrim((string)$value, '-'))) {
                            $this->addError($field, 'This field must be an integer.');
                        }
                        break;

                    case 'url':
                        if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
                            $this->addError($field, 'Please enter a valid URL.');
                        }
                        break;

                    case 'in':
                        $allowed = explode(',', $ruleParam ?? '');
                        if ($value !== null && $value !== '' && !in_array($value, $allowed, true)) {
                            $this->addError($field, 'Invalid selection.');
                        }
                        break;

                    case 'confirmed':
                        $confirm = $this->data[$field . '_confirmation'] ?? null;
                        if ($value !== $confirm) {
                            $this->addError($field, 'Confirmation does not match.');
                        }
                        break;

                    case 'boolean':
                        if ($value !== null && $value !== '' && !in_array($value, [0, 1, '0', '1', true, false], true)) {
                            $this->addError($field, 'This field must be true or false.');
                        }
                        break;

                    case 'regex':
                        if ($value && !preg_match($ruleParam, $value)) {
                            $this->addError($field, 'Invalid format.');
                        }
                        break;

                    case 'unique':
                        [$table, $column, $except] = array_pad(explode(',', $ruleParam ?? ''), 3, null);
                        if ($value) {
                            $sql = "SELECT id FROM $table WHERE $column = ?";
                            $params = [$value];
                            if ($except) {
                                $sql .= ' AND id != ?';
                                $params[] = $except;
                            }
                            $row = Database::selectOne($sql, $params);
                            if ($row) {
                                $this->addError($field, 'This value is already taken.');
                            }
                        }
                        break;

                    case 'nullable':
                        if ($value === null || $value === '') break 2;
                        break;

                    case 'sometimes':
                        if (!isset($this->data[$field])) break 2;
                        break;
                }
            }
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function validated(): array
    {
        return $this->data;
    }
}
