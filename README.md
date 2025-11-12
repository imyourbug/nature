## Database Schema - Sàn B2B Marketplace

### Tổng quan

Schema được thiết kế cho hệ thống sàn B2B marketplace với 3 bảng chính: `vendors`, `products`, và `rfqs`.

### Cấu trúc Database

#### 1. Bảng `vendors` (Nhà bán)

**Các trường:**
- `id`: Primary key tự động tăng
- `name`: Tên nhà bán (VARCHAR 255)
- `email`: Email duy nhất (UNIQUE constraint)
- `status`: Trạng thái (active/inactive/suspended)
- `verified`: Đã xác thực hay chưa (BOOLEAN)
- `created_at`, `updated_at`: Timestamps tự động

**Indexes:**
- `idx_vendor_status`: Tối ưu truy vấn theo trạng thái
- `idx_vendor_verified`: Tối ưu lọc nhà bán đã xác thực
- `email`: Unique index tự động từ `->unique()` (hỗ trợ tìm kiếm và đảm bảo tính duy nhất)

**Lý do thiết kế:**
- Email UNIQUE đảm bảo mỗi nhà bán chỉ có một tài khoản
- Status ENUM giúp quản lý trạng thái nhà bán dễ dàng
- Verified flag cho phép phân biệt nhà bán đã được xác thực

#### 2. Bảng `products` (Sản phẩm)

**Các trường:**
- `id`: Primary key tự động tăng
- `name`: Tên sản phẩm
- `brand`: Thương hiệu
- `price`: Giá (DECIMAL 15,2 để hỗ trợ giá trị lớn)
- `specs`: Thông số kỹ thuật (JSON - linh hoạt cho nhiều loại sản phẩm)
- `status`: Trạng thái (active/inactive/out_of_stock)
- `vendor_id`: Foreign key đến bảng vendors (ON DELETE SET NULL)

**Indexes:**
- `idx_product_name`: Tối ưu tìm kiếm theo tên
- `idx_product_brand`: Tối ưu lọc theo thương hiệu
- `idx_product_status`: Tối ưu lọc theo trạng thái
- `idx_product_price`: Tối ưu sắp xếp/lọc theo giá
- `idx_product_search`: FULLTEXT index cho tìm kiếm full-text trên name và brand
- `vendor_id`: Index tự động từ `foreignId()` (hỗ trợ join và foreign key lookup)

**Foreign Keys:**
- `vendor_id` → `vendors(id)`: Mỗi sản phẩm thuộc về một nhà bán
  - `ON DELETE SET NULL`: Khi xóa nhà bán, sản phẩm vẫn tồn tại nhưng vendor_id = NULL
  - Foreign key tự động tạo index cho cột `vendor_id`

**Lý do thiết kế:**
- Specs dùng JSON để linh hoạt với nhiều loại sản phẩm khác nhau (không cần thêm bảng riêng)
- FULLTEXT index cho phép tìm kiếm nhanh theo tên và thương hiệu
- Index trên price hỗ trợ sắp xếp và lọc giá nhanh
- Foreign key đảm bảo tính toàn vẹn dữ liệu

#### 3. Bảng `rfqs` (Request for Quotation - Yêu cầu báo giá)

**Các trường:**
- `id`: Primary key tự động tăng
- `product_id`: Foreign key đến products (NOT NULL)
- `vendor_id`: Foreign key đến vendors (NOT NULL)
- `quantity`: Số lượng yêu cầu
- `status`: Trạng thái (pending/accepted/rejected)
- `created_at`, `updated_at`: Timestamps

**Indexes:**
- `idx_rfq_status`: Tối ưu lọc theo trạng thái RFQ
- `idx_rfq_created`: Tối ưu sắp xếp theo thời gian tạo
- `product_id`: Index tự động từ `foreignId()` (hỗ trợ join và foreign key lookup)
- `vendor_id`: Index tự động từ `foreignId()` (hỗ trợ join và foreign key lookup)

**Foreign Keys:**
- `product_id` → `products(id)`: RFQ liên quan đến sản phẩm cụ thể
  - `ON DELETE CASCADE`: Xóa sản phẩm thì xóa luôn RFQ liên quan
  - Foreign key tự động tạo index cho cột `product_id`
- `vendor_id` → `vendors(id)`: RFQ gửi đến nhà bán cụ thể
  - `ON DELETE CASCADE`: Xóa nhà bán thì xóa luôn RFQ liên quan
  - Foreign key tự động tạo index cho cột `vendor_id`

**Lý do thiết kế:**
- Foreign keys đảm bảo tính toàn vẹn: không thể tạo RFQ cho sản phẩm/nhà bán không tồn tại
- CASCADE delete phù hợp vì RFQ không có ý nghĩa khi sản phẩm/nhà bán đã bị xóa
- Index trên status giúp truy vấn RFQ đang pending nhanh
- Index trên created_at hỗ trợ sắp xếp theo thời gian

### Tối ưu hóa Performance

1. **Indexing Strategy:**
   - Index trên các cột thường dùng để filter (status, brand, verified)
   - Index trên các cột thường dùng để sort (price, created_at)
   - FULLTEXT index cho tìm kiếm sản phẩm
   - **Lưu ý:** 
     - `->unique()` tự động tạo unique index (không cần thêm index riêng)
     - `foreignId()` tự động tạo index cho foreign key (không cần thêm index riêng)
     - Tránh tạo index trùng lặp để tối ưu hiệu suất và tránh lỗi migration

2. **Foreign Key Constraints:**
   - Đảm bảo tính toàn vẹn dữ liệu
   - ON DELETE CASCADE/SET NULL phù hợp với business logic

3. **Data Types:**
   - DECIMAL cho price (chính xác, không mất mát)
   - JSON cho specs (linh hoạt, không cần schema cố định)
   - ENUM cho status (rõ ràng, hiệu quả)

### Mock Data

File `database/mock_data.json` chứa dữ liệu mẫu:
- 3 vendors (nhà bán)
- 5 products (sản phẩm)
- 2 RFQs (yêu cầu báo giá)

Có thể import dữ liệu này để test và phát triển ứng dụng.

### Cách sử dụng

#### Sử dụng Laravel Migrations (Khuyến nghị)

1. **Chạy migrations để tạo bảng:**
   ```bash
   php artisan migrate
   ```
   Lệnh này sẽ tạo 3 bảng theo thứ tự:
   - `vendors` (2024_01_20_000001)
   - `products` (2024_01_20_000002)
   - `rfqs` (2024_01_20_000003)

2. **Seed mock data:**
   ```bash
   php artisan db:seed --class=B2BSeeder
   ```
   Hoặc chạy tất cả seeders:
   ```bash
   php artisan db:seed
   ```

3. **Rollback migrations (nếu cần):**
   ```bash
   php artisan migrate:rollback --step=3
   ```
