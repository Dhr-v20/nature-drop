<?php
// cart-api.php — AJAX endpoint for all cart operations
require_once 'includes/db.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'msg'=>'Login required','redirect'=>'login.php']);
    exit;
}

$raw    = file_get_contents('php://input');
$data   = json_decode($raw, true) ?? [];
$action = $data['action']     ?? '';
$pid    = (int)($data['product_id'] ?? 0);
$qty    = (int)($data['quantity']   ?? 1);
$uid    = currentUserId();
$pdo    = getDB();

function cartCount(PDO $pdo, int $uid): int {
    $s = $pdo->prepare("SELECT COALESCE(SUM(quantity),0) FROM cart WHERE user_id=?");
    $s->execute([$uid]);
    return (int)$s->fetchColumn();
}
function cartTotal(PDO $pdo, int $uid): float {
    $s = $pdo->prepare("SELECT COALESCE(SUM(c.quantity*p.price),0) FROM cart c JOIN products p ON p.id=c.product_id WHERE c.user_id=?");
    $s->execute([$uid]);
    return (float)$s->fetchColumn();
}

switch ($action) {
    case 'add':
        if ($pid<1||$qty<1){echo json_encode(['ok'=>false,'msg'=>'Invalid']);exit;}
        $ps=$pdo->prepare("SELECT name,stock FROM products WHERE id=?");
        $ps->execute([$pid]);$product=$ps->fetch();
        if(!$product){echo json_encode(['ok'=>false,'msg'=>'Product not found']);exit;}
        if($product['stock']<1){echo json_encode(['ok'=>false,'msg'=>'Out of stock']);exit;}
        $pdo->prepare("INSERT INTO cart (user_id,product_id,quantity) VALUES(?,?,?) ON DUPLICATE KEY UPDATE quantity=quantity+VALUES(quantity)")->execute([$uid,$pid,$qty]);
        echo json_encode(['ok'=>true,'msg'=>htmlspecialchars($product['name']).' added to cart!','cart_count'=>cartCount($pdo,$uid),'cart_total'=>number_format(cartTotal($pdo,$uid),2)]);
        break;

    case 'update':
        if($pid<1){echo json_encode(['ok'=>false,'msg'=>'Invalid']);exit;}
        if($qty<1){$pdo->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?")->execute([$uid,$pid]);}
        else{$pdo->prepare("UPDATE cart SET quantity=? WHERE user_id=? AND product_id=?")->execute([$qty,$uid,$pid]);}
        echo json_encode(['ok'=>true,'cart_count'=>cartCount($pdo,$uid),'cart_total'=>number_format(cartTotal($pdo,$uid),2)]);
        break;

    case 'remove':
        $pdo->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?")->execute([$uid,$pid]);
        echo json_encode(['ok'=>true,'cart_count'=>cartCount($pdo,$uid),'cart_total'=>number_format(cartTotal($pdo,$uid),2)]);
        break;

    case 'count':
        echo json_encode(['ok'=>true,'cart_count'=>cartCount($pdo,$uid),'cart_total'=>number_format(cartTotal($pdo,$uid),2)]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['ok'=>false,'msg'=>'Unknown action']);
}