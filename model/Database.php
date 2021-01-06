<?php

/**
 * Authors : Adrian Barreira & Simon Guggisberg
 * Date : 25.11.2020
 * Description : Database class interacting with data on MySQL server
 */
include_once 'config.ini.php';

/**
 * Class Database
 */
class Database
{
    private $connector;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        $host = $GLOBALS['database']['host'];
        $port = $GLOBALS['database']['port'];
        $dbname = $GLOBALS['database']['dbname'];
        $charset = $GLOBALS['database']['charset'];
        $username = $GLOBALS['database']['username'];
        $password = $GLOBALS['database']['password'];

        try {
            $this->connector = new PDO('mysql:host=' . $host . ';port=' . $port . ';dbname=' . $dbname . ';charset=' . $charset, $username, $password);
        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    /**
     * @param $query
     * @return false|PDOStatement
     */
    private function querySimpleExecute($query)
    {
        return $this->connector->query($query);
    }

    /**
     * @param $req
     * @return mixed
     */
    private function formatData($req)
    {
        return $req->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     * @param $req
     */
    private function unsetData($req)
    {
        $req->closeCursor();
    }

    /**
     * Merges an array as string as the following : (..., ..., ...) with a char (or a string) being added before and after each element
     * @param string[] $strings
     * @param string $char
     * @return string merged
     */
    function mergeStrings($strings, $char): string
    {
        $stringsAsString = "";
        foreach ($strings as $string) {
            $stringsAsString = $stringsAsString . $char . ', ' . $char . addslashes($string);
        }
        $stringsAsString = substr_replace($stringsAsString, '(', 0, 2);
        return $stringsAsString . $char . ')';
    }

    /**
     * Read a table and return an array with the table's informations
     * @param string $tableName
     * @return array
     */
    function readTable(string $tableName): array
    {
        $results = $this->querySimpleExecute('select * from ' . $tableName);
        $results = $this->formatData($results);
        return $results;
    }

    function readReservationPerDay(string $day)
    {
        $results = $this->querySimpleExecute("select * from t_reservation where resDate between '$day 00:00:00' and '$day 23:59:59' order by resHour ASC, resTable ASC, resMeal ASC");
        $results = $this->formatData($results);
        return $results;
    }

    function getIdUser($username)
    {
        $results = $this->querySimpleExecute("select * from t_user where useUsername='". $username . "'");
        return $results = $this->formatData($results)[0]['idUser'];
    }

    function getUser($userId) {
        $results = $this->querySimpleExecute("select useUsername from t_user where idUser=$userId");
        return $results = $this->formatData($results)[0]['useUsername'];
    }

    function deleteUser($username)
    {
        $this->querySimpleExecute('delete from t_user where useUsername = ' . $username);
    }

#region ExistsAt functions

    /**
     * Checks wether the specified data exists, returns a negative value if the data does not exist, the id otherwise
     * @param mixed $value
     * @param string $table
     * @param string $column
     * @return int
     */
    function dataExistsAt($value, $table, $column): int
    {
        $results = $this->readTable($table);

        foreach ($results as $result) {
            if ($result[$column] == $value) {
                return (int)$result["id" . ucfirst(substr($table, 2, strlen($table)))];
            }
        }
        return -1;
    }

    /**
     * @param string $username
     * @return int
     */
    function userExistsAt($username): int
    {
        return $this->dataExistsAt($username, 't_user', 'useUsername');
    }

    /**
     * @param $date yyyy-mm-dd
     * @param string $table
     * @param int $hour
     * @return int
     */
    function reservationExistsAt($date, $table, $hour): int
    {
        $results = $this->readTable('t_reservation');
        foreach ($results as $result) {
            if (($result['resDate'] == $date) && ($result['resTable'] == $table) && ($result['resHour'] == $hour)) {
                return (int)$result['idReservation'];
            }
        }
        return -1;
    }

#endregion

#region Add functions

    /**
     * Adds some data to the database, returns the id of the data
     * @param string $table
     * @param string[] $columns
     * @param string[] $values
     * @return int id
     */
    function addData($table, $columns, $values): int
    {
        $id = 'id' . ucfirst(substr($table, 2, strlen($table)));
        $this->querySimpleExecute('insert into ' . $table . ' ' . $this->mergeStrings($columns, '') . ' values ' . $this->mergeStrings($values, '\''));
        $results = $this->querySimpleExecute("select max($id) from " . $table);
        $results = $this->formatData($results);
        return (int)($results[0]["max($id)"]);
    }

    /**
     * @param string $username
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password not hashed
     * @param int $role
     * @return int
     */
    function addUser($username, $firstName, $lastName, $email, $password, $role): int
    {
        $password = password_hash($password, PASSWORD_BCRYPT);
        return $this->addData('t_user', ['useUsername', 'useFirstName', 'useLastName', 'useEmail', 'usePassword', 'useRole'], [$username, $firstName, $lastName, $email, $password, $role]);
    }

    /**
     * @param $date yyyy-mm-dd
     * @param int $table
     * @param int $hour
     * @param int $meal
     * @param int $userId
     * @return int
     */
    function addReservation($date, $table, $hour, $meal, $userId): int
    {
        mail ( 'simon.guggisberg@eduvaud.ch' , 'réservation de l\'utilisateur : ' . $userId , $date . ' ' . $table . ' ' . $hour . ' ' . $meal . ' ' . $userId . ' ' ,
        'From: adrian.barreira@eduvaud.ch');

        return $this->addData('t_reservation', ['resDate', 'resTable', 'resHour', 'resMeal', 'fkUser'], [$date, $table, $hour, $meal, $userId]);
    }

#endregion

    public function register($username, $password)
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $insertUser = "INSERT INTO t_user (useUsername, usePassword) VALUES ('" . $username . "' , '" . $passwordHash . "')";

        if ($this->connector->query($insertUser) == TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $insertUser . "<br>";
        }
    }

    public function login($username)
    {
        $userList = $this->connector->query("SELECT * FROM t_user WHERE useUsername = '$username'");
        return $userList;
    }
}