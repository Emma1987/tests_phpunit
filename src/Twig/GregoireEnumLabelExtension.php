<?php

namespace App\Twig;

use Greg0ire\Enum\Bridge\Symfony\Translator\GetLabel;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GregoireEnumLabelExtension extends AbstractExtension
{
    private GetLabel $label;

    public function __construct(TranslatorInterface $translator)
    {
        $this->label = new GetLabel($translator);
    }

    public function getFilters(): array
    {
        return [new TwigFilter('enum_label', [$this, 'label'])];
    }

    public function label($value, string $class, $translationDomain = null): string
    {
        return ($this->label)($value, $class, $translationDomain, null, null);
    }
}
