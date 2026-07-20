# Phiếu 03 — OOP MiniShop + Đăng nhập Session

| | |
|---|---|
| **Buổi** | 3 |
| **Thời lượng** | ≥ 45 phút |
| **Repo** | `cse485-ms-03` |
| **Nhận từ Phiếu 02** | Logic tính lineTotal / stockLevel / map category |
| **Mang sang Phiếu 04** | Ý tưởng Category & Product; nhu cầu lưu DB thay mảng |
| **Dataset** | [CANONICAL-DATA](./CANONICAL-DATA.md) — tổng kho **41380000** |

### Chuẩn đầu ra (CLO)

1. Class `Product`/`Category` đủ method bắt buộc.  
2. Login chuẩn `admin` / `MiniShop@03` bằng POST + Session.  
3. Guard dashboard khi chưa đăng nhập.  
4. In 8 SP + tổng 41380000 bằng **method**, không tính trực tiếp ở view.

> **Lưu ý nộp bài:** Phần trên lớp có thể tạm mở dashboard; **bản nộp về nhà bắt buộc có login/guard đầy đủ**.

---

## 1. Tóm tắt lý thuyết

### Hướng cấu trúc (Buổi 1–2) vs Hướng đối tượng (Buổi 3)

| | Hướng cấu trúc | Hướng đối tượng (OOP) |
|--|----------------|------------------------|
| Tổ chức | Mảng + hàm rời (`helpers.php`) | Class gom dữ liệu + hành vi |
| Thành tiền | `lineTotal($p)` | `$p->lineTotal()` |
| Ai giữ quy tắc? | Lập trình viên nhớ gọi hàm đúng | Object tự biết method của mình |
| MiniShop trước đây | `$products` + `stockLevel($p)` | `new Product(...)` + `$p->stockLevel()` |

> Đọc kỹ mục **2** trong [Buổi 03](../03-buoi-03-oop-session.md) trước khi code phiếu này.

### OOP + HTTP + Session cần nhớ

- Class / Object / `__construct` / `$this`.
- `public` vs `private` (encapsulation).
- GET vs POST; Session `session_start()`.
- Escape output; không tin dữ liệu form.
- Credential nộp bài: `admin` / `MiniShop@03` — tổng kho CORE **41380000**.

---

## 2. Bài trên lớp (~20') — Hai class cốt lõi

### File bắt buộc

```
minishop-03/
├── src/Category.php
├── src/Product.php
├── data.php          ← tạo mảng object từ dữ liệu Phiếu 01
├── login.php
├── dashboard.php
└── logout.php
```

### Class `Category`

```text
properties: id (int), name (string)
method: label(): string  → "[id] name"
```

### Class `Product`

```text
properties: sku, name, categoryId, price, qty
methods:
  lineTotal(): int
  stockLevel(): string   // cùng quy tắc Phiếu 02
  toArray(): array       // phục vụ debug
```

### Trên lớp

1. Trong `data.php`: tạo `$categoryObjects` (3) và `$productObjects` (8) bằng `new`.
2. `dashboard.php` (tạm chưa chặn login): in bảng từ **object** (gọi method, không tính `price*qty` trực tiếp ở view).

**Checkpoint:** không dùng mảng thuần trong vòng lặp in bảng — phải `$p->lineTotal()`.

---

## 3. Bài về nhà (~30') — Cổng Admin giả lập bằng Session

### Tài khoản cứng

| username | password |
|----------|----------|
| `admin` | `MiniShop@03` |

### Luồng bắt buộc

1. `login.php`: form **POST**; sai → thông báo; đúng →  
   `$_SESSION['auth'] = true`, `$_SESSION['username'] = 'admin'` → redirect dashboard.
2. `dashboard.php`: nếu chưa `auth` → redirect login.  
   Nội dung: chào user + bảng 8 Product + tổng giá trị kho (dùng method).
3. `logout.php`: hủy session → login.
4. Thêm form nhỏ trên dashboard (POST): chọn SKU (select) + số lượng muốn “đặt thử”, lưu vào `$_SESSION['orders'][]` rồi in danh sách order dưới bảng.  
   (Chứng minh Session giữ được state giữa các request.)

### Ràng buộc kỹ thuật

- Mọi output tên/SKU qua `htmlspecialchars`.
- Password **không** in ra HTML / URL.
- Dùng `header('Location: ...'); exit;` đúng chỗ.

### Output kỳ vọng

| Tình huống | Kết quả |
|------------|---------|
| Vào `/dashboard.php` lúc chưa login | Về login |
| Sai mật khẩu | Ở lại login + lỗi |
| Đúng | Dashboard thấy 8 SP, tổng 41380000 |
| Thêm 2 order rồi F5 | Danh sách order vẫn còn |
| Logout rồi vào dashboard | Bị chặn |

---

## 4. Câu hỏi thuyết trình trong video (bắt buộc trả lời)

1. Hướng cấu trúc vs OOP: khác nhau chỗ nào? Ví dụ bằng `lineTotal($p)` và `$p->lineTotal()`.  
2. Class và object khác nhau thế nào trong bài của bạn?  
3. Vì sao dùng Session chứ không chỉ biến PHP thường?

---

## 5. Box nộp bài

```
╔══════════════════════════════════════════════════════════════╗
║  NỘP PHIẾU 03                                                ║
╠══════════════════════════════════════════════════════════════╣
║  1. Repo đủ class + login/dashboard/logout                   ║
║  2. Video liên tục: sai pass → đúng pass → order Session →   ║
║     logout → bị chặn vào dashboard                           ║
║  3. Zoom code method lineTotal() và chỗ session_start()      ║
╚══════════════════════════════════════════════════════════════╝
```
