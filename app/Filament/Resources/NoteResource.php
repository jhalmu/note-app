<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Filament\Resources\NoteResource\RelationManagers;
use App\Models\Note;
use Carbon\Carbon;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class NoteResource extends Resource
{

    protected static ?string $model = Note::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([

                    /* Choose category */
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->label('Julkaisun kategoria')
                        /*->disablePlaceholderSelection()*/
                        ->required(),

                    /* add picture */
                    SpatieMediaLibraryFileUpload::make('image')
                        ->collection('notes')
                        ->label('Artikkelin kuva')
                        ->image()
                        ->responsiveImages()
                        ->enableOpen()
                        ->visibility('is_published'),
                ]),
                Card::make()->schema([

                    TextInput::make('title')
                        ->maxLength(50)
                        ->default('Max 50 chr')
                        ->label('Otsikko')
                        ->reactive()
                        /* Make slug automatically */

                        ->afterStateUpdated(function (Closure $set, $state) {
                            $set('slug', Str::slug($state));
                        })->required(),
                    TextInput::make('slug')
                        ->required()
                        ->disabled()
                        ->label('Slug'),
                ])->columns(),

                Card::make()->schema([

                    /* Editor */
                    RichEditor::make('content')
                        ->maxLength(5000)
                        ->default('Max 5000 chr')
                        ->required()
                        ->label('Sisältö'),

                    Textarea::make('plaintext')->hidden(),
                ]),


                Card::make()->schema([

                    Radio::make('type')
                        ->label('Julkaisun tyyppi')
                        ->options([
                            'Article' => 'Artikkeli',
                            'Note' => 'Muistio'
                        ])->inline()->default('Article'),

                    Radio::make('locale')
                        ->label('Julkaisun kieli')
                        ->options([
                            'FI' => 'FI',
                            'EN' => 'EN',
                            'SE' => 'SE'
                        ])->inline()->default('FI'),

                    Toggle::make('is_published')
                        ->default(true)
                        ->label('Julkaistu'),
                ])->columns(3),

                DateTimePicker::make('created_at')
                    ->default(Carbon::now())
                    ->withoutSeconds()
                    ->timezone('Europe/Helsinki')
                    ->label('Julkaistaan'),

                /* Date when updated id NOW() */

                DateTimePicker::make('updated_at')
                    ->withoutSeconds()
                    ->timezone('Europe/Helsinki')
                    ->label('Viimeksi päivitetty')
                    ->default(Carbon::now())
                    ->disabled(),

                Card::make()->schema([

                    /* Choose tags */

                    Select::make('tag_id')
                        ->relationship('tags', 'name')
                        ->label('Lisää tagit')
                        ->multiple()
                        ->preload(),

                    /* Show writer/author of the text */

                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Kirjoittaja')
                        ->default(Filament::auth()->user()->getAuthIdentifier())
                        ->disabled()
                        ->preload(),

                    Select::make('updater_id')
                        ->relationship('user', 'name')
                        ->label('Viimeksi päivittänyt')
                        ->default(Filament::auth()->user()->getAuthIdentifier())
                        ->preload(),

                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable()->label('id'),
                TextColumn::make('title')->limit('25')->sortable()->searchable()->label('otsikko'),
                TextColumn::make('category.name')->sortable()->searchable()->label('kategoria'),
                TextColumn::make('locale')->sortable()->searchable()->label('kieli'),
                TextColumn::make('type')->sortable()->label('tyyppi'),
                SpatieMediaLibraryImageColumn::make('image')->collection('notes')->label('kuva')->square(),
                TextColumn::make('user.name')->sortable()->label('omistaja')->limit(10),
                TextColumn::make('created_at')->sortable()->searchable()->label('julkaisupäivä')->date('D j.n.y G:H'),
                TextColumn::make('updated_at')->sortable()->label('päivitetty')->date('D j.n.y G:H'),
                ToggleColumn::make('is_published')->sortable()->searchable()->label('julkaistu'),
            ])
            ->filters([
                Filter::make('Julkaistut')
                    ->query(fn(Builder $query): Builder => $query->where('is_published', true)),
                Filter::make('Artikkelit')
                    ->query(fn(Builder $query): Builder => $query->where('type', 'Article')),
                Filter::make('Muistiot')
                    ->query(fn(Builder $query): Builder => $query->where('type', 'Note')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNote::route('/create'),
            'edit' => Pages\EditNote::route('/{record}/edit'),
        ];
    }
}
