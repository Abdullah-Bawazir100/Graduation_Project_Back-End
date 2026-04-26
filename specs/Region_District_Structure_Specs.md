# مواصفات هيكل قسمي Region و District

## قسم Region

### الكيان (Entity)
- **المسار**: `app/Domain/Region/Entities/Region.php`
- **الهيكل**:
  ```php
  class Region
  {
      public function __construct(
          public ?int $id,           // معرّف فريد (قد يكون فارغًا)
          public string $name       // اسم المنطقة
      ) {}
  }
  ```

### النموذج (Model)
- **المسار**: `app/Infrastructure/Persistence/Eloquent/Models/RegionModel.php`
- **الجدول في قاعدة البيانات**: [regions](file:///d:/Archive_Files_Taxpayer_Services/database/migrations/2026_04_16_001319_create_regions_table.php#L15-L21)
- **الحقول القابلة للتعبئة**: ['name'](file:///d:/Archive_Files_Taxpayer_Services/app/Application/User/DTOs/ChangePasswordDTO.php#L10-L10)
- **المزايا**: يحتوي على ميزة تسجيل الأنشطة (LogsActivity)

### هيكل الجدول في قاعدة البيانات
- **ملف الترحيل**: `database/migrations/2026_04_16_001319_create_regions_table.php`
  - [id](file:///d:/Archive_Files_Taxpayer_Services/packages/spatie/laravel-activitylog/src/Models/Activity.php#L15-L15): حقل تلقائي معرّف فريد
  - [name](file:///d:/Archive_Files_Taxpayer_Services/app/Application/User/DTOs/ChangePasswordDTO.php#L10-L10): سلسلة نصية
  - timestamps: حقلان تلقائيان للتاريخ (created_at وupdated_at)

---

## قسم District

### الكيان (Entity)
- **المسار**: `app/Domain/District/Entities/District.php`
- **الهيكل**:
  ```php
  class District
  {
      public function __construct(
          public ?int $id,           // معرّف فريد (قد يكون فارغًا)
          public string $name,       // اسم الحي/المنطقة الفرعية
          public Region $region      // كائن المنطقة الذي ينتمي إليه الحي
      ) {}
  }
  ```

### النموذج (Model)
- **المسار**: `app/Infrastructure/Persistence/Eloquent/Models/DistrictModel.php`
- **الجدول في قاعدة البيانات**: [districts](file:///d:/Archive_Files_Taxpayer_Services/database/migrations/2026_04_23_183511_create_districts_table.php#L15-L21)
- **الحقول القابلة للتعبئة**: ['name'](file:///d:/Archive_Files_Taxpayer_Services/app/Application/User/DTOs/ChangePasswordDTO.php#L10-L10) و['region_id'](file:///d:/Archive_Files_Taxpayer_Services/app/Infrastructure/Persistence/Eloquent/Models/DistrictModel.php#L13-L13)
- **العلاقات**:
  - [region()](file:///d:/Archive_Files_Taxpayer_Services/app/Infrastructure/Persistence/Eloquent/Models/DistrictModel.php#L15-L22): علاقة تربط الحي بمنطقته الأم (RegionModel)
- **المزايا**: يحتوي على ميزة تسجيل الأنشطة (LogsActivity)

### هيكل الجدول في قاعدة البيانات
- **ملف الترحيل**: `database/migrations/2026_04_23_183511_create_districts_table.php`
  - [id](file:///d:/Archive_Files_Taxpayer_Services/packages/spatie/laravel-activitylog/src/Models/Activity.php#L15-L15): حقل تلقائي معرّف فريد
  - [name](file:///d:/Archive_Files_Taxpayer_Services/app/Application/User/DTOs/ChangePasswordDTO.php#L10-L10): سلسلة نصية
  - [region_id](file:///d:/Archive_Files_Taxpayer_Services/app/Infrastructure/Persistence/Eloquent/Models/DistrictModel.php#L13-L13): معرّف خارجي يشير إلى الجدول [regions](file:///d:/Archive_Files_Taxpayer_Services/database/migrations/2026_04_16_001319_create_regions_table.php#L15-L21)
  - timestamps: حقلان تلقائيان للتاريخ (created_at وupdated_at)

---

## العلاقة بين القسمين
- العلاقة بين [District](file:///d:/Archive_Files_Taxpayer_Services/app/Domain/District/Entities/District.php#L8-L18) و[Region](file:///d:/Archive_Files_Taxpayer_Services/app/Domain/Region/Entities/Region.php#L8-L18) هي علاقة تبعية (One-to-Many) حيث:
  - كل منطقة ([Region](file:///d:/Archive_Files_Taxpayer_Services/app/Domain/Region/Entities/Region.php#L8-L18)) يمكن أن تحتوي على عدة أحياء ([District](file:///d:/Archive_Files_Taxpayer_Services/app/Domain/District/Entities/District.php#L8-L18))
  - كل حي ([District](file:///d:/Archive_Files_Taxpayer_Services/app/Domain/District/Entities/District.php#L8-L18)) ينتمي فقط إلى منطقة واحدة ([Region](file:///d:/Archive_Files_Taxpayer_Services/app/Domain/Region/Entities/Region.php#L8-L18))