<?PHP

require_once('config.php');
require_once('functions.php');

$dbh = connectDb();

$sql = 'SELECT * FROM branches';
$stmt = $dbh->prepare($sql);
$stmt->execute();
$branches = $stmt->fetchALL(PDO::FETCH_ASSOC);

$sql2 = 'SELECT * FROM staffs';
$stmt = $dbh->prepare($sql2);
$stmt->execute();
$staffs = $stmt->fetchALL(PDO::FETCH_ASSOC);

$year = $_GET['year'];
$branch = $_GET['branch'];
$staff = $_GET['staff'];

if ($year) {
    if ($branch) {
        if ($staff) {
            $sql = "SELECT staffs.id AS staffs, staffs.name AS staffs, year, month, sale,
            branches.id AS branches, branches.name AS branches
            FROM sales
            INNER JOIN staffs ON sales.staff_id = staffs.id
            INNER JOIN branches ON staffs.branch_id = branches.id
            WHERE year = :year AND branches.name = :branch AND staffs.name = :staff
            ORDER BY year ASC, month ASC, staffs.id ASC, branches.id ASC";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':branch', $branch, PDO::PARAM_STR);
            $stmt->bindParam(':staff', $staff, PDO::PARAM_STR);
        } else {
            $sql = "SELECT staffs.id AS staffs, staffs.name AS staffs, year, month, sale,
            branches.id AS branches, branches.name AS branches
            FROM sales
            INNER JOIN staffs ON sales.staff_id = staffs.id
            INNER JOIN branches ON staffs.branch_id = branches.id
            WHERE year = :year AND branches.name = :branch
            ORDER BY year ASC, month ASC, staffs.id ASC, branches.id ASC";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':branch', $branch, PDO::PARAM_STR);
        }
    } else {
        $sql = "SELECT staffs.id AS staffs, staffs.name AS staffs, year, month, sale,
        branches.id AS branches, branches.name AS branches
        FROM sales
        INNER JOIN staffs ON sales.staff_id = staffs.id
        INNER JOIN branches ON staffs.branch_id = branches.id
        WHERE year = :year
        ORDER BY year ASC, month ASC, staffs.id ASC, branches.id ASC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    }
} elseif ($branch) {
    if ($staff) {
        $sql = "SELECT staffs.id AS staffs, staffs.name AS staffs, year, month, sale,
        branches.id AS branches, branches.name AS branches
        FROM sales
        INNER JOIN staffs ON sales.staff_id = staffs.id
        INNER JOIN branches ON staffs.branch_id = branches.id
        WHERE branches.name = :branch AND staffs.name = :staff
        ORDER BY year ASC, month ASC, staffs.id ASC, branches.id ASC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':branch', $branch, PDO::PARAM_STR);
        $stmt->bindParam(':staff', $staff, PDO::PARAM_STR);
    } else {
        $sql = "SELECT staffs.id AS staffs, staffs.name AS staffs, year, month, sale,
        branches.id AS branches, branches.name AS branches
        FROM sales
        INNER JOIN staffs ON sales.staff_id = staffs.id
        INNER JOIN branches ON staffs.branch_id = branches.id
        WHERE branches.name = :branch
        ORDER BY year ASC, month ASC, staffs.id ASC, branches.id ASC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':branch', $branch, PDO::PARAM_STR);
    }
} elseif ($staff) {
    $sql = "SELECT staffs.id AS staffs, staffs.name AS staffs, year, month, sale,
    branches.id AS branches, branches.name AS branches
    FROM sales
    INNER JOIN staffs ON sales.staff_id = staffs.id
    INNER JOIN branches ON staffs.branch_id = branches.id
    WHERE staffs.name = :staff
    ORDER BY year ASC, month ASC, staffs.id ASC, branches.id ASC";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':staff', $staff, PDO::PARAM_STR);
} else {
    $sql = "SELECT staffs.id AS staffs, staffs.name AS staffs, year, month, sale,
    branches.id AS branches, branches.name AS branches
    FROM sales
    INNER JOIN staffs ON sales.staff_id = staffs.id
    INNER JOIN branches ON staffs.branch_id = branches.id
    ORDER BY year ASC, month ASC, staffs.id ASC, branches.id ASC";
    $stmt = $dbh->prepare($sql);
}
$stmt->execute();
$sales = $stmt->fetchALL(PDO::FETCH_ASSOC);

foreach ($sales as $sale) {
    $sum += $sale['sale'];
}
$sum = number_format($sum);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sales_listアプリ</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="wrapper">
        <h1>売上一覧</h1>
        <form action="" method="get">
            <div class="form-item">
                <label for="year">年</label>
                <input type="number" name="year" value="<?= h($_GET['year']) ?>">
            </div>
            <div class="form-item">
                <label for="branch">支店</label>
                <select name="branch" size="1">
                    <option value=""></option>
                    <?php foreach ($branches as $branch) : ?>
                        <option value="<?= h($branch['name']) ?>" <?= h($_GET['branch']) == h($branch['name']) ? 'selected' : '' ?>>
                            <?= h($branch['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-item">
                <label for="staff">従業員</label>
                <select name="staff" size="1">
                    <option value=""></option>
                    <?php foreach ($staffs as $staff) : ?>
                        <option value="<?= h($staff['name']) ?>" <?= h($_GET['staff']) == h($staff['name']) ? 'selected' : '' ?>>
                            <?= h($staff['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="search-button">
                <input type="submit" value="検索">
            </div>
        </form>
        <table>
            <tr>
                <th>年</th>
                <th>月</th>
                <th>支店</th>
                <th>従業員</th>
                <th>売上</th>
            </tr>
            <?php foreach ($sales as $sale) : ?>
                <tr>
                    <td><?= $sale['year'] ?></td>
                    <td><?= $sale['month'] ?></td>
                    <td><?= $sale['branches'] ?></td>
                    <td><?= $sale['staffs'] ?></td>
                    <td><?= $sale['sale'] ?></td>
                </tr>
            <?php endforeach ?>
        </table>
        <h2>合計:<?= $sum ?>万円</h2>
    </div>
</body>

</html>