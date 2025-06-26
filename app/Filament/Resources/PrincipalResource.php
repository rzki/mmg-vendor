<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Principal;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\CheckboxList;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Console\View\Components\Info;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Infolists\Components\RepeatableEntry;
use App\Filament\Resources\PrincipalResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Fieldset as InfoFieldset;
use App\Filament\Resources\PrincipalResource\RelationManagers;
use App\Filament\Resources\PrincipalResource\Pages\EditPrincipal;
use App\Filament\Resources\PrincipalResource\Pages\ViewPrincipal;
use App\Filament\Resources\PrincipalResource\Pages\ListPrincipals;
use App\Filament\Resources\PrincipalResource\Pages\CreatePrincipal;

class PrincipalResource extends Resource
{
    protected static ?string $model = Principal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // PRINCIPAL DATA
                    Wizard\Step::make('Principal Data')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->label('Principal Name'),
                            Select::make('category')
                                ->options([
                                    'manufacturer' => 'Manufacturer',
                                    'distributor' => 'Distributor',
                                    'others' => 'Others',
                                ])
                                ->required()
                                ->reactive()
                                ->label('Principal Category'),
                            TextInput::make('other_category')
                                ->label('Other Category')
                                ->visible(fn ($get) => $get('category') === 'others')
                                ->required(fn ($get) => $get('category') === 'others'),
                            Section::make('Contact')
                                ->columns(2)
                                ->schema([
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->label('Email'),
                                TextInput::make('phone')
                                    ->tel()
                                    ->required()
                                    ->label('Phone'),
                                ]),
                            Textarea::make('address')
                                ->required()
                                ->label('Address'),
                            TextInput::make('product_name')
                                ->required()
                                ->label('Product Name'),
                            Select::make('payment_type')
                                ->options(options: [
                                    'prepayment' => 'Prepayment',
                                    'credit' => 'Credit',
                                    'others' => 'Others',
                                ])
                                ->required()
                                ->reactive()
                                ->label('Payment Type'),
                            TextInput::make('payment_type_name')
                                ->label('Payment Type Name')
                                ->visible(fn ($get) => $get('payment_type') === 'others')
                                ->required(fn ($get) => $get('payment_type') === 'others'),
                        ]),
                    // PRINCIPAL LEGALITY
                    Wizard\Step::make('Principal Legality')
                        ->schema([
                            Select::make('type')
                            ->options([
                                'domestic' => 'Domestic',
                                'international' => 'International',
                            ])
                            ->reactive()
                            ->required()
                            ->label('Principal Type'),
                            Group::make([
                                Select::make('domestic_nib_status')
                                    ->label('Business Identification Number No. (NIB)')
                                    ->options([
                                        'none' => 'None',
                                        'available' => 'Available',
                                    ])
                                    ->reactive()
                                    ->visible(fn ($get) => $get('type') === 'domestic')
                                    ->required(fn ($get) => $get('type') === 'domestic'),
                                TextInput::make('domestic_nib')
                                    ->label('Domestic NIB')
                                    ->visible(fn ($get) => $get('type') === 'domestic' && $get('domestic_nib_status') === 'available')
                                    ->required(fn ($get) => $get('type') === 'domestic' && $get('domestic_nib_status') === 'available'),
                                Select::make('domestic_certificate_status')
                                ->label('Certificate (Akta)')
                                ->options([
                                    'none' => 'None',
                                    'available' => 'Available',
                                ])
                                ->reactive()
                                ->visible(fn ($get) => $get('type') === 'domestic')
                                ->required(fn ($get) => $get('type') === 'domestic'),
                                TextInput::make('domestic_certificate')
                                    ->label('Domestic Certificate')
                                    ->visible(fn ($get) => $get('type') === 'domestic' && $get('domestic_certificate_status') === 'available')
                                    ->required(fn ($get) => $get('type') === 'domestic' && $get('domestic_certificate_status') === 'available'),
                                Select::make('domestic_related_documents_status')
                                ->label('Related Permits/Certificates')
                                ->options([
                                    'none' => 'None',
                                    'available' => 'Available',
                                ])
                                ->reactive()
                                ->visible(fn ($get) => $get('type') === 'domestic')
                                ->required(fn ($get) => $get('type') === 'domestic'),
                                Repeater::make('domestic_related_documents')
                                    ->label('Domestic Related Documents')
                                    ->schema([
                                        TextInput::make('related_document_certification_name')
                                            ->label('Certification')
                                            ->required(),
                                        TextInput::make('certification_name')
                                            ->label('Document Name')
                                            ->required(),
                                    ])              
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->visible(fn ($get) => $get('type') === 'domestic' && $get('domestic_related_documents_status') === 'available')
                                    ->required(fn ($get) => $get('type') === 'domestic' && $get('domestic_related_documents_status') === 'available'),
                            ]),
                            Group::make([
                                Select::make('international_quality_certification_status')
                                ->label('Quality Certification (ISO / GMP / GDP)')
                                ->options([
                                    'none' => 'None',
                                    'available' => 'Available',
                                ])
                                ->reactive()
                                ->visible(fn ($get) => $get('type') === 'international')
                                ->required(fn ($get) => $get('type') === 'international'),
                                Repeater::make('international_quality_certification')
                                    ->label('International Quality Certification')
                                    ->schema([
                                        Select::make('quality_certification_name')
                                            ->label('Quality Certification Name')
                                            ->options([
                                                'ISO' => 'ISO',
                                                'GMP' => 'GMP',
                                                'GDP' => 'GDP',
                                            ])
                                            ->required(),
                                        // TextInput::make('certification_name')
                                        //     ->label('Certification Name')
                                        //     ->required(),
                                    ])              
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->visible(fn ($get) => $get('type') === 'international' && $get('international_quality_certification_status') === 'available')
                                    ->required(fn ($get) => $get('type') === 'international' && $get('international_quality_certification_status') === 'available'),
                                Select::make('international_safety_certification_status')
                                ->label('FDA / CE-IVD / CE Marked')
                                ->options([
                                    'none' => 'None',
                                    'available' => 'Available',
                                ])
                                ->reactive()
                                ->visible(fn ($get) => $get('type') === 'international')
                                ->required(fn ($get) => $get('type') === 'international'),
                                Repeater::make('international_safety_certification')
                                    ->label('International Safety Certification')
                                    ->schema([
                                        Select::make('safety_certification_name')
                                            ->label('Safety Certification Name')
                                            ->options([
                                                'FDA' => 'FDA',
                                                'CE-IVD' => 'CE-IVD',
                                                'CE Marked' => 'CE Marked',
                                            ])
                                            ->required(),
                                        // TextInput::make('certification_name')
                                        //     ->label('Certification Name')
                                        //     ->required(),
                                    ])              
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->visible(fn ($get) => $get('type') === 'international' && $get('international_safety_certification_status') === 'available')
                                    ->required(fn ($get) => $get('type') === 'international' && $get('international_safety_certification_status') === 'available'),
                            ]),
                        ]),
                    Wizard\Step::make('Principal Evaluation Checklist')
                        ->schema([
                            Repeater::make('principal_checklist')
                                ->label('Principal Checklist')
                                ->schema([
                                    TextInput::make('question')
                                        ->label('Question')
                                        ->readOnly()
                                        ->required(),
                                    Radio::make('result')
                                        ->label('Result')
                                        ->options([
                                            'yes' => 'Yes',
                                            'no' => 'No',
                                        ])
                                        ->required(),
                                    
                                    Fieldset::make('Quality Management System')
                                    ->visible(fn ($get) => $get('question') === 'Does the company have a valid Quality Management System? If yes, please specify:' && $get('result') === 'yes')
                                    ->schema([
                                        Repeater::make('quality_management_system')
                                            ->label('')
                                            ->schema([
                                                TextInput::make('system_name')
                                                    ->label('Name')
                                                    ->required(),
                                            ])
                                            ->addActionLabel('Add New QMS')
                                            ->columnSpanFull()
                                            ->reorderable(false)
                                            ->deletable(false)
                                        ]),

                                    Fieldset::make('Common Product Complaints')
                                    ->visible(fn ($get) => $get('question') === 'Do many customers complain about the product? If yes, please explain the common product complaints:' && $get('result') === 'yes')
                                    ->schema([
                                        TextInput::make('common_complaints')
                                            ->label('Please state the common product complaints')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                                ])
                                ->default(self::defaultQuestions())
                                ->reactive()              
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->required(),
                            Radio::make('conclusion')
                                ->label('Conclusion')
                                ->options([
                                    'recommended' => 'It is recommended to continue work with the Principal',
                                    'not_recommended' => 'It is not recommended to continue work with the Principal',
                                ])
                                ->reactive()
                                ->required(),
                            TextInput::make('follow_up_plan')
                                ->label('Follow Up Plan')
                                ->visible(fn ($get) => $get('conclusion') === 'not_recommended')
                                ->required(fn ($get) => $get('conclusion') === 'not_recommended')
                        ]),
                ])
                ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Principal Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Principal Type')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record) => auth()->user()->hasRole(['Super Admin', 'Admin']) && $record->creator_id === auth()->user()->id)
                    ->successNotification(
                    Notification::make()
                            ->success()
                            ->title('User deleted')
                            ->body('The user has been deleted successfully.'),
                    )
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Principal')
                    ->columns(4)
                    ->tabs([
                        Tab::make('Principal Data')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Principal Name'),
                                TextEntry::make('category')
                                    ->label('Principal Category')
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                TextEntry::make('other_category')
                                    ->label('Other Category')
                                    ->visible(fn ($record) => $record->category === 'others'),
                                TextEntry::make('email')
                                    ->label('Email'),
                                TextEntry::make('phone')
                                    ->label('Phone'),
                                TextEntry::make('address')
                                    ->label('Address'),
                                TextEntry::make('product_name')
                                    ->label('Product Name'),
                                TextEntry::make('payment_type')
                                    ->label('Payment Type')
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                TextEntry::make('payment_type_name')
                                    ->label('Payment Type Name')
                                    ->visible(fn ($record) => $record->payment_type === 'others'),
                            ]),
                        Tab::make('Principal Legality')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextEntry::make('type')
                                    ->label('Principal Type')
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                InfoSection::make('Domestic Legality')
                                    ->visible(fn ($record) => $record->type === 'domestic')
                                    ->schema([
                                        TextEntry::make('domestic_nib_status')
                                            ->label('Business Identification Number No. (NIB)')
                                            ->visible(fn ($record) => $record->type === 'domestic'),
                                        TextEntry::make('domestic_nib')
                                            ->label('Domestic NIB')
                                            ->visible(fn ($record) => $record->type === 'domestic' && $record->domestic_nib_status === 'available'),
                                        TextEntry::make('domestic_certificate_status')
                                            ->label('Certificate (Akta)')
                                            ->visible(fn ($record) => $record->type === 'domestic'),
                                        TextEntry::make('domestic_certificate')
                                            ->label('Domestic Certificate')
                                            ->visible(fn ($record) => $record->type === 'domestic' && $record->domestic_certificate_status === 'available'),
                                        TextEntry::make('domestic_related_documents_status')
                                            ->label('Related Permits/Certificates')
                                            ->visible(fn ($record) => $record->type === 'domestic'),
                                        RepeatableEntry::make('domestic_related_documents')
                                            ->label('Domestic Related Documents')
                                            ->schema([
                                                TextEntry::make('related_document_certification_name')
                                                    ->label('Certification'),
                                                TextEntry::make('certification_name')
                                                    ->label('Document Name'),
                                            ])
                                            ->visible(fn ($record) => $record->type === 'domestic' && $record->domestic_related_documents_status === 'available'),
                                    ]),

                                InfoSection::make('International Legality')
                                    ->visible(fn ($record) => $record->type === 'international')
                                    ->schema([
                                        InfoSection::make('Quality Certification')
                                            ->label('Quality Certification (ISO / GMP / GDP)')
                                            ->columns(2)
                                            ->visible(fn ($record) => $record->type === 'international')
                                            ->schema([
                                                TextEntry::make('international_quality_certification_status')
                                                    ->label('Quality Certification (ISO / GMP / GDP)')
                                                    ->visible(fn ($record) => $record->type === 'international')
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                RepeatableEntry::make('international_quality_certification')
                                                    ->label('International Quality Certification')
                                                    ->schema([
                                                        TextEntry::make('quality_certification_name')
                                                            ->label('Quality Certification Name')
                                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                    ])
                                                    ->visible(fn ($record) => $record->type === 'international' && $record->international_quality_certification_status === 'available'),

                                                ]),
                                        InfoSection::make('Safety Certification')
                                            ->label('Safety Certification (FDA / CE-IVD / CE Marked)')
                                            ->columns(2)
                                            ->visible(fn ($record) => $record->type === 'international')
                                            ->schema([
                                                TextEntry::make('international_safety_certification_status')
                                                    ->label('FDA / CE-IVD / CE Marked')
                                                    ->visible(fn ($record) => $record->type === 'international')
                                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                RepeatableEntry::make('international_safety_certification')
                                                    ->label('International Safety Certification')
                                                    ->schema([
                                                        TextEntry::make('safety_certification_name')
                                                            ->label('Safety Certification Name')
                                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                                    ])
                                                    ->visible(fn ($record) => $record->type === 'international' && $record->international_safety_certification_status === 'available')
                                            ]),
                                    ]),
                                ]),
                        Tab::make('Principal Evaluation Checklist')
                            ->icon('heroicon-o-check-circle')
                            ->columns(1)
                            ->schema([
                                RepeatableEntry::make('principal_checklist')
                                    ->label('Principal Checklist')
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('question')
                                            ->label('Question'),
                                        TextEntry::make('result')
                                            ->label('Result')
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        InfoFieldset::make('Quality Management System')
                                            ->visible(fn ($state) =>
                                                ($state['question'] ?? null) === 'Does the company have a valid Quality Management System? If yes, please specify:' && ($state['result'] ?? null) === 'yes'
                                            )
                                            ->schema([
                                                RepeatableEntry::make('quality_management_system')
                                                    ->label('')
                                                    ->schema([
                                                        TextEntry::make('system_name')
                                                            ->label('Name'),
                                                    ])
                                                    ->columnSpanFull()
                                            ]),
                                        InfoFieldset::make('Common Product Complaints')
                                            ->visible(fn ($state) => 
                                                ($state['question'] ?? null) === 'Do many customers complain about the product? If yes, please explain the common product complaints:' && ($state['result'] ?? null) === 'yes'
                                            )
                                            ->schema([
                                                TextEntry::make('common_complaints')
                                                    ->label('Please state the common product complaints')
                                                    ->columnSpanFull(),
                                            ]),
                                            
                                        ]),
                                    TextEntry::make('conclusion')
                                        ->label('Conclusion')
                                        ->formatStateUsing(fn ($state) => ucfirst($state)),
                                    TextEntry::make('follow_up_plan')
                                        ->label('Follow Up Plan')
                                        ->visible(fn ($record) => $record->conclusion === 'not_recommended' && $record->conclusion !== null),
                            ])
                        ])->columnSpanFull()
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
            'index' => Pages\ListPrincipals::route('/'),
            'create' => Pages\CreatePrincipal::route('/create'),
            'edit' => Pages\EditPrincipal::route('/{record}/edit'),
            'view' => Pages\ViewPrincipal::route('/{record}'),
        ];
    }
    
    protected static function defaultQuestions(): array
    {
        return [
            ['question' => 'Is the product still being produced?'],
            ['question' => 'Does the company have a valid Quality Management System? If yes, please specify:'],
            ['question' => 'Does the provided product still meet the requirements?'],
            ['question' => 'Is the product delivery time in line with expectations?'],
            ['question' => 'Does the product shipping temperature meet the requirements? (specifically for cold chain products)'],
            ['question' => 'Is the received product often found to be non-compliant?'],
            ['question' => 'Are customers satisfied with the product?'],
            ['question' => 'Do many customers complain about the product? If yes, please explain the common product complaints:'],
            ['question' => 'Does the principal respond well to product complaints?'],
        ];
    }
}
