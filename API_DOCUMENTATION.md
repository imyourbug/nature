# API Documentation - Sàn B2B Marketplace

## Base URL
```
http://your-domain.com/api
```

## Authentication

Một số endpoints yêu cầu JWT authentication. Sử dụng Bearer token trong header:

```
Authorization: Bearer admin-token
```

**Lưu ý:** Hiện tại chỉ chấp nhận mock token `admin-token` để test.

---

## Products API

### 1. GET /api/products
Lấy danh sách sản phẩm với pagination và filtering.

**Query Parameters:**
- `brand` (optional): Lọc theo thương hiệu (ví dụ: `?brand=Dell`)
- `status` (optional): Lọc theo trạng thái (`active`, `inactive`, `out_of_stock`)
- `limit` (optional): Số lượng items mỗi trang (mặc định: 10)
- `page` (optional): Trang hiện tại (mặc định: 1)

**Example Request:**
```bash
GET /api/products?brand=Dell&status=active&limit=10
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Laptop Dell XPS 15",
      "brand": "Dell",
      "price": "35000000.00",
      "specs": {
        "processor": "Intel Core i7-12700H",
        "ram": "16GB DDR4"
      },
      "status": "active",
      "vendor_id": 1,
      "vendor": {
        "id": 1,
        "name": "Công ty TNHH Thương mại ABC"
      }
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 5,
    "last_page": 1
  }
}
```

---

### 2. POST /api/products
Tạo sản phẩm mới.

**Request Body:**
```json
{
  "name": "Laptop Dell XPS 15",
  "brand": "Dell",
  "price": 35000000,
  "specs": {
    "processor": "Intel Core i7-12700H",
    "ram": "16GB DDR4",
    "storage": "512GB SSD"
  },
  "status": "active",
  "vendor_id": 1
}
```

**Validation Rules:**
- `name`: required, string, max 255
- `brand`: required, string, max 100
- `price`: required, numeric, min 0.01
- `specs`: optional, array
- `status`: optional, enum (active/inactive/out_of_stock)
- `vendor_id`: optional, must exist in vendors table

**Example Response (Success - 201):**
```json
{
  "success": true,
  "message": "Sản phẩm đã được tạo thành công",
  "data": {
    "id": 6,
    "name": "Laptop Dell XPS 15",
    "brand": "Dell",
    "price": "35000000.00",
    "specs": {...},
    "status": "active",
    "vendor_id": 1
  }
}
```

**Example Response (Error - 400):**
```json
{
  "success": false,
  "message": "Tên sản phẩm là bắt buộc",
  "error": "Bad Request"
}
```

---

### 3. GET /api/products/{id}
Lấy thông tin chi tiết một sản phẩm.

**Example Request:**
```bash
GET /api/products/1
```

**Example Response (Success - 200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Laptop Dell XPS 15",
    "brand": "Dell",
    "price": "35000000.00",
    "specs": {...},
    "status": "active",
    "vendor_id": 1,
    "vendor": {...}
  }
}
```

**Example Response (Error - 404):**
```json
{
  "success": false,
  "message": "Sản phẩm không tồn tại",
  "error": "Not Found"
}
```

---

## RFQs API

### 1. GET /api/rfqs
Lấy danh sách RFQ với filtering.

**Query Parameters:**
- `vendor_id` (optional): Lọc theo nhà bán (ví dụ: `?vendor_id=1`)
- `status` (optional): Lọc theo trạng thái (`pending`, `accepted`, `rejected`)

**Example Request:**
```bash
GET /api/rfqs?vendor_id=1&status=pending
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "vendor_id": 1,
      "quantity": 50,
      "status": "pending",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "product": {
        "id": 1,
        "name": "Laptop Dell XPS 15",
        "brand": "Dell"
      },
      "vendor": {
        "id": 1,
        "name": "Công ty TNHH Thương mại ABC"
      }
    }
  ]
}
```

---

### 2. POST /api/rfqs
Tạo RFQ mới. **Yêu cầu JWT authentication.**

**Headers:**
```
Authorization: Bearer admin-token
```

**Request Body:**
```json
{
  "product_id": 1,
  "quantity": 50
}
```

**Validation Rules:**
- `product_id`: required, must exist in products table
- `quantity`: required, integer, min 1

**Note:** `vendor_id` sẽ tự động được lấy từ sản phẩm.

**Example Response (Success - 201):**
```json
{
  "success": true,
  "message": "RFQ đã được tạo thành công",
  "data": {
    "id": 3,
    "product_id": 1,
    "vendor_id": 1,
    "quantity": 50,
    "status": "pending",
    "product": {...},
    "vendor": {...}
  }
}
```

**Example Response (Error - 401):**
```json
{
  "success": false,
  "message": "Token không được cung cấp",
  "error": "Unauthorized"
}
```

---

### 3. PUT /api/rfqs/{id}/accept
Chấp nhận một RFQ. **Yêu cầu JWT authentication.**

**Headers:**
```
Authorization: Bearer admin-token
```

**Example Request:**
```bash
PUT /api/rfqs/1/accept
```

**Example Response (Success - 200):**
```json
{
  "success": true,
  "message": "RFQ đã được chấp nhận thành công",
  "data": {
    "id": 1,
    "product_id": 1,
    "vendor_id": 1,
    "quantity": 50,
    "status": "accepted",
    "product": {...},
    "vendor": {...}
  }
}
```

**Example Response (Error - 400):**
```json
{
  "success": false,
  "message": "RFQ này đã được xử lý",
  "error": "Bad Request"
}
```

**Example Response (Error - 404):**
```json
{
  "success": false,
  "message": "RFQ không tồn tại",
  "error": "Not Found"
}
```

---

## Error Responses

Tất cả các lỗi đều trả về format JSON thống nhất:

### 400 Bad Request
```json
{
  "success": false,
  "message": "Thông báo lỗi cụ thể",
  "error": "Bad Request"
}
```

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Token không hợp lệ",
  "error": "Unauthorized"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource không tồn tại",
  "error": "Not Found"
}
```

---

## Testing với cURL

### Lấy danh sách sản phẩm
```bash
curl -X GET "http://localhost:8000/api/products?brand=Dell&status=active"
```

### Tạo sản phẩm mới
```bash
curl -X POST "http://localhost:8000/api/products" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop Test",
    "brand": "Dell",
    "price": 20000000,
    "specs": {"ram": "8GB"},
    "status": "active",
    "vendor_id": 1
  }'
```

### Tạo RFQ (cần authentication)
```bash
curl -X POST "http://localhost:8000/api/rfqs" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer admin-token" \
  -d '{
    "product_id": 1,
    "quantity": 50
  }'
```

### Chấp nhận RFQ (cần authentication)
```bash
curl -X PUT "http://localhost:8000/api/rfqs/1/accept" \
  -H "Authorization: Bearer admin-token"
```

