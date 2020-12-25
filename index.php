<?PHP

require_once('config.php');
require_once('functions.php');

$dbh = connectDb();

$sql3 = 'SELECT * FROM branches';
$stmt = $dbh->prepare($sql3);
$stmt->execute();
$branches = $stmt->fetchALL(PDO::FETCH_ASSOC);

$sql2 = 'SELECT * FROM staffs';
$stmt = $dbh->prepare($sql2);
$stmt->execute();
$staffs = $stmt->fetchALL(PDO::FETCH_ASSOC);

$year = $_GET['year'];
$branch = $_GET['branch'];
$staff = $_GET['staff'];

$sql = <<< EOM
SELECT
    s.year,
    s.month,
    st.id AS staffs,
    st.name AS staffs,
    b.id AS branches,
    b.name AS branches,
    s.sale
FROM
    sales s
INNER JOIN
    staffs st
ON
    s.staff_id = st.id
INNER JOIN
    branches b
ON
    st.branch_id = b.id
EOM;

$sql_order = <<< EOM

ORDER BY
    s.year ASC,
    s.month ASC,
    st.id ASC,
    b.id ASC
EOM;

$where = '';
if ($year || $branch || $staff) {
    if ($year) {
        $where = 's.year = :year';
    }
    if ($branch) {
        if ($where) {
            $where .= ' AND ';
        }
        $where .= 'b.id = :branch';
    }
    if ($staff) {
        if ($where) {
            $where .= ' AND ';
        }
        $where .= 'st.id = :staff';
    }
    $where = ' WHERE ' . $where;
}

$sql .= $where . $sql_order;

$stmt = $dbh->prepare($sql);
if ($year) {
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
}
if ($branch) {
    $stmt->bindParam(':branch', $branch, PDO::PARAM_STR);
}
if ($staff) {
    $stmt->bindParam(':staff', $staff, PDO::PARAM_STR);
}
$stmt->execute();
$sales = $stmt->fetchALL(PDO::FETCH_ASSOC);
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
                        <option value="<?= h($branch['id']) ?>" <?= $_GET['branch'] == $branch['id'] ? 'selected' : '' ?>>
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
                        <option value="<?= h($staff['id']) ?>" <?= $_GET['staff'] == $staff['id'] ? 'selected' : '' ?>>
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
            <thead>
                <tr>
                    <th>年</th>
                    <th>月</th>
                    <th>支店</th>
                    <th>従業員</th>
                    <th>売上</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale) : ?>
                    <tr>
                        <td><?= $sale['year'] ?></td>
                        <td><?= $sale['month'] ?></td>
                        <td><?= $sale['branches'] ?></td>
                        <td><?= $sale['staffs'] ?></td>
                        <td><?= $sale['sale'] ?></td>
                    </tr>
                    <?php $sum += $sale['sale'] ?>
                <?php endforeach ?>
            </tbody>
        </table>
        <h2>合計:<?= number_format($sum) ?>万円</h2>
    </div>
</body>

</html>