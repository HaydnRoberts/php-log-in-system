<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "log-in-system";

$connection = mysqli_connect($hostname, $username, $password, $dbname) or die("Database connection not estblished");


class User {
    public $email = "";
    public $password = "";
    public $password_hash = "";
    public $token = "";
    public $authenticated = false;
    public $connection;

    function __construct($connection, $email, $password) {
        $this->email = mysqli_escape_string($connection, $email);
        $this->password = mysqli_escape_string($connection, $password);
        
        $this->token = md5(rand().time());
        $this->password_hash = password_hash($password, PASSWORD_BCRYPT);

        $this->connection = $connection;
    }

    function insert() {
        $sql = "
            INSERT INTO users(
                email,
                password,
                token,
                is_active
            ) VALUES (
                '{$this->email}',
                '{$this->password_hash}',
                '{$this->token}',
                '0'
            )
        ";

        $sqlQuery = $this->connection->query($sql);
        if (! $sqlQuery) {
            die("MySql query failed" . mysqli_error($this->connection));
        };
    }

    function authenticate() {
        $sql = "
        SELECT id, email, password, token, is_active
        From users
        WHERE email=\"{$this->email}\"
        ";
        $result = $this->connection->query($sql);
        if ($row = $result->fetch_assoc()) {
            if (password_verify($this->password, $row["password"])) {
                $this->authenticated = true;
            }
        }
    }

    function is_logged_in() {
        return $this->authenticated;
    }
}
