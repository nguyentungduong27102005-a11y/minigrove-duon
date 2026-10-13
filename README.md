# MiniGrove

MiniGrove là hệ thống thương mại điện tử Terrarium kết hợp chức năng tự thiết kế Terrarium, quản lý sản phẩm, voucher, giỏ hàng, đặt hàng, thanh toán, đánh giá và tư vấn realtime.

## Yêu cầu môi trường

- PHP 8.1 trở lên
- Composer 2
- Node.js + npm
- MySQL / MariaDB
- Git

Khuyến nghị trên Windows: XAMPP + PHP 8.2.

## Cài đặt project trên máy mới

### 1. Clone project

```bash
git clone https://github.com/menne25122005-sketch/minigrove.git
cd minigrove
```

### 2. Cài dependency PHP và Node

```bash
composer install
npm install
```

### 3. Tạo file môi trường

Windows CMD:

```bat
copy .env.example .env
```

PowerShell:

```powershell
Copy-Item .env.example .env
```

Sau đó:

```bash
php artisan key:generate
```

### 4. Tạo database

Tạo một database MySQL, ví dụ:

```text
laravel
```

Mặc định `.env.example` dùng:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

Nếu máy sử dụng thông tin khác, sửa trong file `.env`.

### 5. Tạo bảng và dữ liệu mẫu

Trên database mới hoàn toàn:

```bash
php artisan migrate --seed
```

Lệnh này tạo role, tài khoản demo, phương thức thanh toán, danh mục, sản phẩm, sản phẩm Studio, voucher và dữ liệu chăm sóc.

> Không dùng `migrate:fresh` trên database đang có dữ liệu cần giữ.

Nếu chỉ cần cập nhật dữ liệu seed trên database đang dùng:

```bash
php artisan migrate
php artisan db:seed
```

### 6. Tạo storage link

```bash
php artisan storage:link
```

### 7. Build frontend

```bash
npm run build
```

Trong lúc phát triển có thể dùng:

```bash
npm run dev
```

### 8. Chạy Laravel

```bash
php artisan serve
```

Mặc định truy cập:

```text
http://127.0.0.1:8000
```

## Chat realtime bằng Laravel Reverb

Mở thêm một terminal tại project:

```bash
php artisan reverb:start
```

Các thông số Reverb local mẫu đã có trong `.env.example`.

Nếu đang chạy `npm run dev`, giữ terminal Vite chạy song song.

## Tài khoản demo sau khi seed

### Admin

```text
Email: admin@minigrove.com
Mật khẩu: Admin@12345
```

### Customer

```text
Email: customer@minigrove.com
Mật khẩu: Customer@12345
```

Các tài khoản này chỉ dùng cho môi trường học tập/demo.

## Dịch vụ bên thứ ba

Các key thật **không được commit lên GitHub**.

Sau khi copy `.env.example` sang `.env`, điền key riêng nếu cần kiểm thử các chức năng tương ứng.

### Google Login

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

Redirect URI trong Google Cloud phải trùng với URI trong `.env`.

### PayOS

```env
PAYOS_CLIENT_ID=
PAYOS_API_KEY=
PAYOS_CHECKSUM_KEY=
PAYOS_WEBHOOK_URL=
```

Nếu không có key PayOS, các phần khác của website vẫn có thể chạy; chỉ thanh toán PayOS không hoạt động.

### GHN

```env
GHN_BASE_URL=https://online-gateway.ghn.vn/shiip/public-api
GHN_TOKEN=
GHN_SHOP_ID=
```

Nếu không cấu hình GHN, chức năng phụ thuộc API GHN có thể không lấy được dữ liệu/phí vận chuyển thực tế.

## Đồng bộ sản phẩm và ảnh giữa các thành viên

Dữ liệu sản phẩm mẫu được quản lý bằng Seeder:

```text
database/seeders/ProductSeeder.php
database/seeders/StudioProductSeeder.php
database/seeders/ProductImageSeeder.php
```

Ảnh mẫu dùng chung đặt tại:

```text
database/seeders/assets/products/<SKU>/
```

Ví dụ:

```text
database/seeders/assets/products/MG-GREEN-001/
├── 01.jpg
├── 02.jpg
└── 03.jpg
```

Ảnh đầu tiên là ảnh chính. Tối đa 8 ảnh/sản phẩm.

Sau khi pull dữ liệu sản phẩm/ảnh mới:

```bash
php artisan db:seed --class=ProductSeeder
php artisan db:seed --class=StudioProductSeeder
php artisan db:seed --class=ProductImageSeeder
```

Chi tiết xem:

```text
database/seeders/assets/products/README.md
```

## Các thư mục không commit

Git cố ý không lưu:

```text
.env
vendor/
node_modules/
public/storage/
```

Đây là đúng quy trình Laravel. Máy mới tạo/cài lại chúng bằng các bước ở trên.

## Lệnh kiểm tra nhanh

```bash
php artisan route:list
php artisan view:cache
npm run build
```

Nếu các lệnh trên chạy thành công thì project đã được cài đúng ở mức source/build.
