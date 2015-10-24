<?php

namespace tdt4237\webapp;

use tdt4237\webapp\models\User;

class Sql
{
    static $pdo;

    function __construct()
    {
    }

    /**
     * Create tables.
     */
    static function up()
    {
        $q1 = "CREATE TABLE users(userId INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR(50) UNIQUE, password VARCHAR(255), email varchar(50) default null, fullname varchar(50), address varchar(50), postcode INTEGER (4), age INTEGER, bio varchar(150), isadmin INTEGER DEFAULT 0, isdoctor INTEGER DEFAULT 0, accountNumber VARCHAR(255) DEFAULT NULL);";
        $q6 = "CREATE TABLE posts(postId INTEGER PRIMARY KEY AUTOINCREMENT, userId INTEGER NOT NULL, title TEXT NOT NULL, content TEXT NOT NULL, paidQuestion INTEGER DEFAULT 0, timestamp DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(userId) REFERENCES users(userId));";
        $q7 = "CREATE TABLE comments(commentId INTEGER PRIMARY KEY AUTOINCREMENT, timestamp DATETIME DEFAULT CURRENT_TIMESTAMP, userId INTEGER NOT NULL, text INTEGER NOT NULL, postId INTEGER NOT NULL, FOREIGN KEY(postId) REFERENCES posts(postId), FOREIGN KEY(userId) REFERENCES users(userId));";
        $q8 = "CREATE TABLE throttling(userId INTEGER NOT NULL, ip VARCHAR(255), timestamp DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(userId) REFERENCES users(userId));";
        $q9 = "CREATE TABLE payments(doctorId INTEGER, postId INTEGER, FOREIGN KEY(doctorId) REFERENCES users(userId), FOREIGN KEY(postId) REFERENCES posts(postId), UNIQUE(postId));";
        $q10 = "CREATE TABLE banned(ip VARCHAR(255));";

        self::$pdo->exec($q1);
        self::$pdo->exec($q6);
        self::$pdo->exec($q7);
        self::$pdo->exec($q8);
        self::$pdo->exec($q9);  
        self::$pdo->exec($q10);  

        print "[tdt4237] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
        self::insertPosts();
        self::insertComments();
    }

    static function insertDummyUsers()
    {
        $hash1 = '$2y$10$5dpceShC5ooqBKChQ/axT.QWAxD6QsdDBMkeB6B8nEPwG2ni5LXU.';
        $hash2 = Hash::make('Testuser123');

        $q4 = "INSERT INTO users(username, password, isadmin, fullname, address, postcode) VALUES ('admin', '$hash1', 1, 'Admin', 'Admin street 1337', '1337')";
        $q5 = "INSERT INTO users(username, password, isadmin, fullname, address, postcode) VALUES ('testuser', '$hash2', 1, 'Testuser', 'Test Street 1337', '1337')";

        self::$pdo->exec($q4);
        self::$pdo->exec($q5);


        print "[tdt4237] Done inserting dummy users.".PHP_EOL;
    }

    static function insertPosts() {
        $q4 = "INSERT INTO posts(userId, timestamp, title, content) VALUES (1, '2015-03-05 12:05:00', 'I have a problem', 'I have a generic problem I think its embarrasing to talk about. Someone help?')";
        $q5 = "INSERT INTO posts(userId, timestamp, title, content) VALUES (2, '2015-03-04 13:10:00', 'I also have a problem', 'I generally fear very much for my health')";

        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        print "[tdt4237] Done inserting posts.".PHP_EOL;

    }

    static function insertComments() {
        $q1 = "INSERT INTO comments(userId, timestamp, text, postId) VALUES (2, '2015-03-05 12:05:00', 'Don''t be shy! No reason to be afraid here',0)";
        $q2 = "INSERT INTO comments(userId, timestamp, text, postId) VALUES (1, '2015-03-05 12:05:00', 'I wouldn''t worry too much, really. Just relax!',1)";
        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        print "[tdt4237] Done inserting comments.".PHP_EOL;
    }

    static function down()
    {
        $q1 = "DROP TABLE users";
        $q4 = "DROP TABLE posts";
        $q5 = "DROP TABLE comments";
        $q6 = "DROP TABLE throttling";
        $q10 = "DROP TABLE payments";
        $q11 = "DROP TABLE banned";
        
        self::$pdo->exec($q1);
        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        self::$pdo->exec($q6);
        self::$pdo->exec($q10);
        self::$pdo->exec($q11);
        print "[tdt4237] Done deleting all SQL tables.".PHP_EOL;
    }
}

try {
    $log_dir = realpath(__DIR__ . '/../../log');
    chmod($log_dir, 0777);
} catch (\Exception $e) {

}

try {
    // Create (connect to) SQLite database in file
    Sql::$pdo = new \PDO('sqlite:app.db');
    // Set errormode to exceptions
    Sql::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    echo $e->getMessage();
    exit();
}
