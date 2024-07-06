<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Imports\ProductImporter;
use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Filament\Admin\Resources\ProductResource\RelationManagers;
use App\Filament\Exports\ProductExporter;
use App\Models\Curriculum;
use App\Models\EducationClass;
use App\Models\EducationLevel;
use App\Models\EducationSubject;
use App\Models\Product;
use App\Models\Semester;
use App\Models\Type;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?int $navigationSort = 8;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Pegangan Guru';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns([
                        'md' => 4,
                        'sm' => 1,
                    ])
                    ->schema([
                        Forms\Components\Select::make('semester_id')
                            ->label('Semester')
                            ->searchable()
                            ->options(
                                Semester::all()->pluck('name', 'id'),
                            )
                            ->columnSpan([
                                'md' => 2,
                                'sm' => 1,
                            ])
                            ->reactive()
                            ->required(),
                        Forms\Components\Select::make('curriculum_id')
                            ->label('Kurikulum')
                            ->searchable()
                            ->options(
                                Curriculum::all()->pluck('name', 'id'),
                            )
                            ->reactive()
                            ->required(),
                        Forms\Components\Select::make('education_level_id')
                            ->label('Jenjang')
                            ->searchable()
                            ->options(
                                EducationLevel::all()->pluck('name', 'id')
                            )
                            ->reactive()
                            ->required(),
                        Forms\Components\Select::make('education_subject_id')
                            ->label('Mata Pelajaran')
                            ->searchable()
                            ->columnSpan([
                                'md' => 2,
                                'sm' => 1,
                            ])
                            ->options(
                                EducationSubject::all()->pluck('name', 'id')
                            )
                            ->reactive()
                            ->required(),
                        Forms\Components\Select::make('education_class_id')
                            ->label('Kelas')
                            ->searchable()
                            ->options(
                                EducationClass::all()->pluck('name', 'id')
                            )
                            ->reactive()
                            ->required(),
                        Forms\Components\Select::make('type_id')
                            ->name('Type')
                            ->searchable()
                            ->reactive()
                            ->options(
                                Type::all()->pluck('name', 'id'),
                            )
                            ->required(),
                    ]),
                Section::make()
                    ->columns(1)
                    ->schema([
                        Forms\Components\Placeholder::make('name')
                            ->label('Nama Produk')
                            ->content(function (callable $set, $get) {
                                $semester_id = $get('semester_id');
                                $curriculum_id = $get('curriculum_id');
                                $education_level_id = $get('education_level_id');
                                $type_id = $get('type_id');

                                $semester = Semester::find($semester_id)->name ?? "-";
                                $semesterCode = Semester::find($semester_id)->code ?? "-";
                                $curriculum = Curriculum::find($curriculum_id)->name ?? "-";
                                $curriculumCode = Curriculum::find($curriculum_id)->code ?? "-";
                                $level = EducationLevel::find($education_level_id)->name ?? "-";
                                $levelCode = EducationLevel::find($education_level_id)->code ?? "-";
                                $class = EducationClass::find($get('education_class_id'))->name ?? "-";
                                $classCode = EducationClass::find($get('education_class_id'))->code ?? "-";
                                $subject = EducationSubject::find($get('education_subject_id'))->name ?? "-";
                                $subjectCode = EducationSubject::find($get('education_subject_id'))->code ?? "-";
                                $type = Type::find($type_id)->name ?? "-";
                                $typeCode = Type::find($type_id)->code ?? "-";

                                /**
                                 * Example:
                                 * 
                                 * "Pegangan Guru - SMK 1 TAHUN - KURIKULUM MERDEKA - IPS ANTROPOLOGI - KELAS 12 - SEMESTER GANJIL 2024/2025 - (MARGO MITRO JOYO)";
                                 */

                                $productName = "Pegangan Guru - {$level} - KURIKULUM {$curriculum} - {$subject} - KELAS {$class} - {$semester} - ({$type})";

                                $code = "P-{$levelCode}{$curriculumCode}{$subjectCode}{$classCode}{$semesterCode}/{$typeCode}";

                                $set('name', $productName);
                                $set('code', $code);

                                return "{$productName} | {$code}";
                            }),
                        Forms\Components\Hidden::make('name')
                            ->unique()
                            ->validationMessages([
                                'unique' => 'The product has already been registered.',
                            ]),
                        Forms\Components\Hidden::make('code')
                            ->unique()
                            ->validationMessages([
                                'unique' => 'The code has already been registered.',
                            ]),
                    ]),
                Section::make()
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('file')
                            ->label('URL Google Drive')
                            ->url()
                            ->columnSpanFull()
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode MMJ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('educationSubject.name')
                    ->label('Mapel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('curriculum.code')
                    ->label('Kurikulum')
                    ->formatStateUsing(function (string $state) {
                        return Curriculum::firstWhere('code', $state)->name;
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('educationClass.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('educationLevel.name')
                    ->label('Jenjang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester.name')
                    ->label('Semester')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('semester')
                    ->relationship('semester', 'name'),
                Tables\Filters\SelectFilter::make('educationClass')
                    ->label('Kelas')
                    ->relationship('educationClass', 'name'),
                Tables\Filters\SelectFilter::make('educationSubject')
                    ->label('Mapel')
                    ->relationship('educationSubject', 'name'),
                Tables\Filters\SelectFilter::make('educationLevel')
                    ->label('Jenjang')
                    ->relationship('educationLevel', 'name'),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->relationship('type', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(ProductImporter::class),
                Tables\Actions\ExportAction::make()
                    ->exporter(ProductExporter::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
