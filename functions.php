<?PHP

function connectDb() {
    try {
        return new PDO(DSN, USER, PASSWORD);
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}

function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}