<?php

namespace App\Blocks;

use Aurora\Blocks\Block;
use Aurora\Blocks\Editable;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\ToggleButtons;

class RichTextBlock extends Block
{
    public static function type(): string
    {
        return 'rich-text';
    }

    public static function label(): string
    {
        return __('Rich text');
    }

    public static function description(): string
    {
        return __('Formatted text: headings, lists, links, quotes and tables.');
    }

    public static function icon(): ?string
    {
        return 'phosphor-file-text';
    }

    public static function defaults(): array
    {
        return ['width' => 'prose'];
    }

    /**
     * Written right on the canvas: formatting, links, H2/H3, lists and quotes. Text with a
     * table or a code block stays the inspector's to edit.
     */
    public static function editables(): array
    {
        return ['body' => Editable::richText()->placeholder(__('Start typing…'))];
    }

    public static function aiHints(): ?string
    {
        return 'The "body" field is HTML. Allowed tags: p, h2, h3, h4, strong, em, u, s, a[href], ul, ol, li, blockquote, code, pre, hr, table, thead, tbody, tr, th, td. Best for long-form copy such as articles, policies and FAQs answers.';
    }

    public static function schema(): array
    {
        return [
            RichEditor::make('body')
                ->label(__('Content'))
                ->toolbarButtons([
                    ['bold', 'italic', 'underline', 'strike', 'link', 'highlight'],
                    ['h2', 'h3', 'blockquote', 'codeBlock', 'horizontalRule'],
                    ['bulletList', 'orderedList', 'table'],
                    ['undo', 'redo'],
                ]),
            ToggleButtons::make('width')->label(__('Line length'))->options(['prose' => __('Readable'), 'full' => __('Full width')])->default('prose')->inline()->grouped(),
        ];
    }
}
