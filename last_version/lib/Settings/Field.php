<?php

declare(strict_types=1);

namespace Vasoft\Core\Settings;

/**
 * Абстрактный класс для создания представлений для различных типов настроек.
 */
abstract class Field
{
    protected string $note = '';

    /**
     * @prop string $code Код параметра
     * @prop string $title Наименование параметра
     * @prop string getter Гетер для получения значения параметра
     */
    public function __construct(
        public readonly string $code,
        public readonly string $title,
        public readonly \Closure $getter,
    ) {}

    /**
     * Метод выполняющий рендер представления в админке.
     */
    public function render(): string
    {
        $input = $this->renderInput();
        $content = <<<HTML
                        <tr>
                            <td style="width:50%;vertical-align:top">{$this->title}</td>
                            <td style="width:50%;vertical-align:top">{$input}</td>
                        </tr>
            HTML;
        if ('' !== $this->note) {
            $content .= <<<HTML
                <tr><td colspan="2" style="padding: 8px 10%">{$this->note}</td></tr>
                HTML;
        }

        return $content;
    }

    abstract protected function renderInput(): string;

    /**
     * Возвращает код свойства.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Конфигурация примечания к параметру, которое выводится ниже поля.
     */
    public function configureNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }
}
