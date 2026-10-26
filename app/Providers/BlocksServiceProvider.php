<?php

namespace App\Providers;

use App\Blocks\AccordionBlock;
use App\Blocks\AlertBlock;
use App\Blocks\BandBlock;
use App\Blocks\ButtonBlock;
use App\Blocks\ButtonGroupBlock;
use App\Blocks\CallToActionBlock;
use App\Blocks\CardBlock;
use App\Blocks\CodeSampleBlock;
use App\Blocks\FeatureBlock;
use App\Blocks\FormBlock;
use App\Blocks\GalleryBlock;
use App\Blocks\GridBlock;
use App\Blocks\HeadingBlock;
use App\Blocks\HeroBlock;
use App\Blocks\ImageBlock;
use App\Blocks\LogoCloudBlock;
use App\Blocks\PageListBlock;
use App\Blocks\ParagraphBlock;
use App\Blocks\PricingBlock;
use App\Blocks\RichTextBlock;
use App\Blocks\StatBlock;
use App\Blocks\StepsBlock;
use App\Blocks\TabsBlock;
use App\Blocks\TeamMemberBlock;
use App\Blocks\TestimonialBlock;
use App\Http\Controllers\BlockPreviewController;
use App\Support\SinceBadge;
use Aurora\Blocks\Blocks;
use Aurora\Models\Page;
use Aurora\Support\SafeUrl;
use Aurora\Support\SettingsTabs;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * This website's page blocks (app/Blocks): heroes, bands, grids, cards, pricing, forms and
 * more, each with its Blade component in resources/views/components/blocks. Also the
 * settings its header and footer read.
 */
class BlocksServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        // In the order the editor's block palette lists them.
        Blocks::register([
            BandBlock::class,
            GridBlock::class,
            ButtonGroupBlock::class,
            HeroBlock::class,
            HeadingBlock::class,
            ParagraphBlock::class,
            RichTextBlock::class,
            ButtonBlock::class,
            AlertBlock::class,
            AccordionBlock::class,
            TabsBlock::class,
            StepsBlock::class,
            CardBlock::class,
            FeatureBlock::class,
            StatBlock::class,
            PricingBlock::class,
            TestimonialBlock::class,
            TeamMemberBlock::class,
            LogoCloudBlock::class,
            CallToActionBlock::class,
            ImageBlock::class,
            GalleryBlock::class,
            PageListBlock::class,
            FormBlock::class,
            CodeSampleBlock::class,
        ]);

        $this->registerSettings();

        // [since 1.2.4] in the docs: the Aurora release a feature needs.
        SinceBadge::register();

        // The blocks' pictures for the visual editor's block list (Concerns\HasPreviewImage).
        Route::middleware('web')->get('aurora/blocks/{type}.webp', BlockPreviewController::class)
            ->where('type', '[a-z0-9-]+')
            ->name('aurora.blocks.preview');
    }

    /**
     * The settings a site's header and footer read: the header's call-to-action button and
     * announcement bar, and the footer's note and social profiles.
     */
    protected function registerSettings(): void
    {
        // Tabs on Settings' Website page.
        SettingsTabs::register('header', fn (): Tab => Tab::make(__('Header'))->icon('phosphor-app-window')->columns(3)->schema([
            Section::make(__('Header'))
                ->icon('phosphor-app-window')
                ->description(__('The button at the right of the main navigation.'))
                ->columnSpan(2)
                ->columns(2)
                ->schema([
                    TextInput::make('header_cta_label')->label(__('Button label'))->placeholder(__('e.g. Start a project')),
                    Select::make('header_cta_page')->label(__('Links to'))
                        ->options(fn (): array => Page::options())
                        ->searchable(),
                ]),
            Section::make(__('Announcement bar'))
                ->icon('phosphor-megaphone')
                ->description(__('A slim banner above the header.'))
                ->columnSpan(1)
                ->schema([
                    Toggle::make('announcement.enabled')->label(__('Show the announcement'))->live(),
                    Textarea::make('announcement.text')->label(__('Message'))->rows(2)->maxLength(140)
                        ->visible(fn (Get $get): bool => (bool) $get('announcement.enabled')),
                    Grid::make(2)->visible(fn (Get $get): bool => (bool) $get('announcement.enabled'))->schema([
                        TextInput::make('announcement.link_label')->label(__('Link label')),
                        TextInput::make('announcement.url')->label(__('Link'))->rule(SafeUrl::rule()),
                    ]),
                ]),
        ]), siteKeys: ['header_cta_label', 'header_cta_page', 'announcement'], page: 'website', publicKeys: ['header_cta_label', 'header_cta_page', 'announcement']);

        SettingsTabs::register('footer', fn (): Tab => Tab::make(__('Footer'))->icon('phosphor-rows')->columns(3)->schema([
            Section::make(__('Footer'))
                ->icon('phosphor-rows')
                ->description(__('Footer links are managed under Menus.'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('footer_note')->label(__('Footer note'))->columnSpanFull(),
                    Repeater::make('socials')
                        ->label(__('Social profiles'))
                        ->schema([
                            // Brand names, the same in every language: what is saved is what the footer shows.
                            Select::make('network')->options(collect(['LinkedIn', 'GitHub', 'Instagram', 'Facebook', 'TikTok', 'YouTube', 'X', 'Bluesky', 'Mastodon'])->mapWithKeys(fn (string $network): array => [$network => $network])->all())->required(),
                            TextInput::make('url')->url()->required(),
                        ])
                        ->columns(2)
                        ->grid(2)
                        ->reorderableWithDragAndDrop()
                        ->addActionLabel(__('Add profile'))
                        ->columnSpanFull(),
                ]),
        ]), siteKeys: ['footer_note', 'socials'], page: 'website', publicKeys: ['footer_note', 'socials']);
    }
}
