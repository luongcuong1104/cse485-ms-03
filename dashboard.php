<?php
// dashboard.php
session_start();
require_once 'data.php';

// Kiểm tra đăng nhập (Guard)
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: login.php');
    exit;
}

// Xử lý thêm đơn hàng đặt thử (Nhiệm vụ về nhà #4)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_order') {
    $orderSku = trim($_POST['sku'] ?? '');
    $orderQty = (int)($_POST['order_qty'] ?? 0);

    if (!empty($orderSku) && $orderQty > 0) {
        $_SESSION['orders'][] = [
            'sku' => $orderSku,
            'qty' => $orderQty,
            'created_at' => date('H:i:s d/m/Y')
        ];
    }

    // Tránh việc gửi lại form khi F5 (PRG pattern)
    header('Location: dashboard.php');
    exit;
}

// Map danh mục sang mảng phụ để hiển thị
$categoryMap = [];
foreach ($categoryObjects as $cat) {
    $categoryMap[$cat->id] = $cat->name;
}

// Tính tổng giá trị kho bằng các hàm của đối tượng Product
$totalInventoryValue = 0;
$productCount = count($productObjects);

foreach ($productObjects as $p) {
    $totalInventoryValue += $p->lineTotal();
}

// Hàm xếp hạng giá trị kho
function rankInventory(int $totalValue): string {
    if ($totalValue < 15000000) {
        return "Nho";
    } elseif ($totalValue < 35000000) {
        return "Trung binh";
    } else {
        return "Lon";
    }
}
$inventoryRank = rankInventory($totalInventoryValue);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển — MiniShop</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1><?php echo htmlspecialchars(STORE_NAME); ?> — Dashboard</h1>
                <p>Xin chào, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Hệ thống đang hoạt động.</p>
            </div>
            <a href="logout.php" class="btn-logout">Đăng xuất</a>
        </div>

        <!-- Bảng sản phẩm -->
        <div class="table-container">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 12%;">SKU</th>
                            <th style="width: 18%;">Danh mục</th>
                            <th style="width: 25%;">Tên sản phẩm</th>
                            <th style="text-align: right; width: 15%;">Giá bán</th>
                            <th style="text-align: right; width: 8%;">SL</th>
                            <th style="text-align: center; width: 12%;">Mức tồn</th>
                            <th style="text-align: right; width: 10%;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productObjects as $p): ?>
                            <?php 
                                $catName = $categoryMap[$p->categoryId] ?? 'Khác';
                                $status = $p->stockLevel();
                                $statusClass = 'status-default';
                                if ($status === 'Du') {
                                    $statusClass = 'status-ok';
                                } elseif ($status === 'Sap het') {
                                    $statusClass = 'status-warning';
                                } else {
                                    $statusClass = 'status-danger';
                                }
                            ?>
                            <tr>
                                <td>
                                    <span class="sku-badge">
                                        <?php echo htmlspecialchars($p->sku); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="category-name" style="font-weight: 500; color: var(--text-muted);">
                                        <?php echo htmlspecialchars($catName); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="product-name">
                                        <?php echo htmlspecialchars($p->name); ?>
                                    </span>
                                </td>
                                <td class="price-col">
                                    <?php echo number_format($p->price, 0, ',', '.'); ?> ₫
                                </td>
                                <td class="qty-col">
                                    <?php echo number_format($p->qty); ?>
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </td>
                                <td class="total-col">
                                    <?php echo number_format($p->lineTotal(), 0, ',', '.'); ?> ₫
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Báo cáo nhanh -->
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat-card">
                <span class="stat-label">Số sản phẩm</span>
                <span class="stat-value" id="product_count">
                    <?php echo $productCount; ?>
                </span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Tổng giá trị kho</span>
                <span class="stat-value" id="inventory_value" style="color: var(--primary);">
                    <?php echo number_format($totalInventoryValue, 0, ',', '.'); ?> ₫
                </span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Quy mô kho</span>
                <span class="stat-value" id="inventory_rank" style="color: #0d9488;">
                    <?php echo htmlspecialchars($inventoryRank); ?>
                </span>
            </div>
        </div>

        <!-- Phần đặt thử hàng (Session State) -->
        <div class="table-container order-section">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">Đặt thử hàng (Lưu trữ trong Session)</h2>
            
            <form action="dashboard.php" method="POST" class="order-form">
                <input type="hidden" name="action" value="add_order">
                
                <div class="form-group">
                    <label for="sku" style="font-size: 0.85rem; color: var(--text-muted);">Chọn sản phẩm (SKU)</label>
                    <select name="sku" id="sku" class="form-control" required style="height: 44px; padding: 0.5rem 1rem;">
                        <?php foreach ($productObjects as $p): ?>
                            <option value="<?php echo htmlspecialchars($p->sku); ?>">
                                <?php echo htmlspecialchars($p->sku . ' — ' . $p->name); ?> (Tồn: <?php echo $p->qty; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="max-width: 150px;">
                    <label for="order_qty" style="font-size: 0.85rem; color: var(--text-muted);">Số lượng đặt</label>
                    <input type="number" name="order_qty" id="order_qty" class="form-control" min="1" value="1" required style="height: 44px;">
                </div>
                
                <button type="submit" class="btn-submit">Đặt thử</button>
            </form>

            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-main);">Danh sách đơn đặt thử hiện tại:</h3>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 25%;">Thời gian</th>
                            <th style="width: 25%;">SKU</th>
                            <th style="width: 30%;">Tên sản phẩm</th>
                            <th style="text-align: right; width: 20%;">Số lượng đặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($_SESSION['orders'])): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">Chưa có đơn đặt thử nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($_SESSION['orders'] as $ord): ?>
                                <?php
                                    // Tìm tên sản phẩm theo sku
                                    $pName = 'Không tìm thấy';
                                    foreach ($productObjects as $p) {
                                        if ($p->sku === $ord['sku']) {
                                            $pName = $p->name;
                                            break;
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ord['created_at']); ?></td>
                                    <td>
                                        <span class="sku-badge">
                                            <?php echo htmlspecialchars($ord['sku']); ?>
                                        </span>
                                    </td>
                                    <td><span class="product-name"><?php echo htmlspecialchars($pName); ?></span></td>
                                    <td class="qty-col"><?php echo number_format($ord['qty']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<!-- MS_EXPECT inventory_value=41380000 rank=Lon -->
