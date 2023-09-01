<?php
class User {
    private $data;

    private static $instance;
    private $connection;

    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'test';
    public function __construct() {

        $this->data = [];

        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);

        if (mysqli_connect_error()) {
            die("Database connection failed: " . mysqli_connect_error());
        }
    }
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function clone (){}


    public function handleRequest() {
        switch ($_SERVER["REQUEST_METHOD"]) {
            case 'POST':
                foreach ($_POST as $key => $value) {
                    $this->data[$key] = $value;
                }
                $this->saveUser();
                break;
            case 'GET':
                $this->getUsers();
                break;
            case 'DELETE':
                $this->deleteUser();
                break;
        }
    }

    public function getData($key = null) {
        if ($key !== null && isset($this->data[$key])) {
            return $this->data[$key];
        }

        return false;
    }

    public function saveUser () {
        $name = $this->getData('name');
        $job = $this->getData('job');
        if ($name && $job) {
            $stmt = $this->connection->prepare("INSERT INTO users (`name`, `job`) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $job);
            $stmt->execute();
        }
    }
    public function deleteUser () {
        $rawData = file_get_contents("php://input");
        $id = preg_replace('/[^0-9]/', '', $rawData);

        if (!empty($id)) {
            $stmt = $this->connection->prepare("DElETE FROM users WHERE id = ? LIMIT 1");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }
    public function getUsers () {
        $html = '';
        $sql = 'SELECT u.id, u.name, j.name job FROM users u
JOIN `jobs` j on u.job = j.id ORDER BY u.id';

        $result = $this->connection->query($sql)->fetch_all();

        if ($result) {
            $html .= '<table>';
            foreach ($result as $row) {
                $html .= '<tr><td>'.$row[1].'</td><td>'.$row[2].'</td><td><button name="delete" value="'.$row[0].'">Delete</button></td></tr>';
            }
            $html .= '</table>';
        }

        echo $html;

    }
}

$User = new User();

$User->handleRequest();
?>