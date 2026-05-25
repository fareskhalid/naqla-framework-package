<?php

namespace NaqlaSehia\Validation;

use NaqlaSehia\Validation\Rules\MaxRule;
use NaqlaSehia\Validation\Rules\EmailRule;
use NaqlaSehia\Validation\Rules\UniqueRule;
use NaqlaSehia\Validation\Rules\BetweenRule;
use NaqlaSehia\Validation\Rules\AlphaNumRule;
use NaqlaSehia\Validation\Rules\RequiredRule;
use NaqlaSehia\Validation\Rules\ConfirmedRule;

trait RulesMapper
{
    protected static array $map = [
        'required' => RequiredRule::class,
        'alnum' => AlphaNumRule::class,
        'max' => MaxRule::class,
        'between' => BetweenRule::class,
        'email' => EmailRule::class,
        'confirmed' => ConfirmedRule::class,
        'unique' => UniqueRule::class,
    ];

    public static function resolve(string $rule, $options)
    {
        return new static::$map[$rule](...$options);
    }
}
