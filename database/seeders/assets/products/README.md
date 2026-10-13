# Ảnh mẫu sản phẩm MiniGrove

Thư mục này chứa ảnh sản phẩm dùng chung cho các thành viên trong nhóm.

## Quy tắc thư mục

Mỗi sản phẩm có một thư mục mang tên **đúng bằng SKU** trong `ProductSeeder`
hoặc `StudioProductSeeder`.

Ví dụ:

```text
database/seeders/assets/products/
├── MG-FOREST-001/
│   ├── 01.jpg
│   ├── 02.jpg
│   └── 03.jpg
├── MG-FAIRY-001/
│   ├── 01.webp
│   └── 02.webp
└── ST-PLANT-FITTONIA-001/
    └── 01.jpg
```

## Quy tắc ảnh

- Hỗ trợ: JPG, JPEG, PNG, WEBP, GIF.
- Tối đa 8 ảnh cho mỗi sản phẩm.
- Ảnh được sắp xếp theo tên file.
- Ảnh đầu tiên sẽ là **ảnh chính**.
- Nên đặt tên: `01.jpg`, `02.jpg`, `03.jpg`...
- Không đặt file không phải ảnh trong thư mục SKU.

## Cách thêm một sản phẩm mới

1. Thêm Product vào `database/seeders/ProductSeeder.php` hoặc
   `StudioProductSeeder.php` bằng `updateOrCreate()`.
2. SKU phải duy nhất, ví dụ `MG-GREEN-001`.
3. Tạo thư mục:
   `database/seeders/assets/products/MG-GREEN-001/`.
4. Cho ảnh vào thư mục theo thứ tự:
   `01.jpg`, `02.jpg`, `03.jpg`...
5. Commit cả Seeder và thư mục ảnh lên Git.

## Máy khác sau khi pull

Chạy:

```bash
php artisan migrate
php artisan db:seed --class=ProductSeeder
php artisan db:seed --class=StudioProductSeeder
php artisan db:seed --class=ProductImageSeeder
php artisan storage:link
```

Nếu chỉ thay đổi ảnh mà không thay đổi dữ liệu Product thì chỉ cần:

```bash
php artisan db:seed --class=ProductImageSeeder
```

## Lưu ý an toàn

`ProductImageSeeder` chỉ xóa/ghi lại ảnh nằm trong:

```text
storage/app/public/products/seeded/<SKU>/
```

Ảnh Admin upload thủ công ở đường dẫn khác không bị xóa.

Không dùng `php artisan migrate:fresh --seed` trên database đang có dữ liệu cần giữ.
