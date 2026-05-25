<?php

namespace NaqlaSehia\Validation;

use NaqlaSehia\Validation\Rules\Contract\Rule;

class Validator
{
    protected array $data = [];

    protected array $aliases = [];

    protected array $rules = [];

    protected ErrorBag $errorBag;

    public function make($data): self
    {
        $this->data = $data;
        $this->errorBag = new ErrorBag();
        $this->validate();
        return $this;
    }

    protected function validate(): void
    {
        foreach ($this->rules as $field => $rules) {
            foreach (RulesResolver::make($rules) as $rule) {
                $this->applyRule($field, $rule);
            }
        }
    }

    protected function applyRule($field, Rule $rule): void
    {
        if (!$rule->apply($field, $this->getFieldValue($field), $this->data)) {
            $this->errorBag->add($field, Message::generate($rule, $this->alias($field)));
        }
    }

    protected function getFieldValue($field)
    {
        return $this->data[$field] ?? null;
    }

    public function setRules($rules): self
    {
        $this->rules = $rules;
        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors());
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors($key = null): array
    {
        return $key ? ($this->errorBag->errors[$key] ?? []) : $this->errorBag->errors;
    }

    public function alias($field): string
    {
        return $this->aliases[$field] ?? $field;
    }

    public function setAliases(array $aliases): self
    {
        $this->aliases = $aliases;
        return $this;
    }
}
