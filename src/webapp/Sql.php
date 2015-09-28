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
        $q1 = "CREATE TABLE users(userId INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR(50) UNIQUE, password VARCHAR(255), email varchar(50) default null, fullname varchar(50), address varchar(50), postcode INTEGER (4), age INTEGER, bio varchar(150), isadmin INTEGER, isdoctor INTEGER DEFAULT 0);";
        $q6 = "CREATE TABLE posts(postId INTEGER PRIMARY KEY AUTOINCREMENT, userId INTEGER NOT NULL, title TEXT NOT NULL, content TEXT NOT NULL, timestamp DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(userId) REFERENCES users(userId));";
        $q7 = "CREATE TABLE comments(commentId INTEGER PRIMARY KEY AUTOINCREMENT, timestamp DATETIME DEFAULT CURRENT_TIMESTAMP, userId INTEGER NOT NULL, text INTEGER NOT NULL, postId INTEGER NOT NULL, FOREIGN KEY(postId) REFERENCES posts(postId), FOREIGN KEY(userId) REFERENCES users(userId));";
        $q8 = "CREATE TABLE throttling(userId INTEGER NOT NULL, ip VARCHAR(255), timestamp DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(userId) REFERENCES users(userId));";

        self::$pdo->exec($q1);
        self::$pdo->exec($q6);
        self::$pdo->exec($q7);
        self::$pdo->exec($q8);

        print "[tdt4237] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
        self::insertPosts();
        self::insertComments();
    }

    static function insertDummyUsers()
    {
        $hash1 = Hash::make('dolanduck');
        $hash2 = Hash::make('bobdylan');
        $hash3 = Hash::make('liverpool');

        $q1 = "INSERT INTO users(username, password, isadmin, fullname, address, postcode) VALUES ('admin', '$hash1', 1, 'admin', 'homebase', '9090')";
        $q2 = "INSERT INTO users(username, password, isadmin, fullname, address, postcode) VALUES ('bob', '$hash2', 1, 'Robert Green', 'Greenland Grove 9', '2010')";
        $q3 = "INSERT INTO users(username, password, isadmin, fullname, address, postcode) VALUES ('bjarni', '$hash3', 1, 'Bjarni Torgmund', 'Hummerdale 12', '4120')";

        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        self::$pdo->exec($q3);


        print "[tdt4237] Done inserting dummy users.".PHP_EOL;
    }

    static function insertPosts() {
        $q4 = "INSERT INTO posts(userId, timestamp, title, content) VALUES (2, '2015-03-05 12:05:00', 'I have a problem', 'I have a generic problem I think its embarrasing to talk about. Someone help?')";
        $q5 = "INSERT INTO posts(userId, timestamp, title, content) VALUES (3, '2015-03-04 13:10:00', 'I also have a problem', 'I generally fear very much for my health')";

        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        print "[tdt4237] Done inserting posts.".PHP_EOL;

    }

    static function insertComments() {
        $q1 = "INSERT INTO comments(userId, timestamp, text, postId) VALUES (3, '2015-03-05 12:05:00', 'Don''t be shy! No reason to be afraid here',0)";
        $q2 = "INSERT INTO comments(userId, timestamp, text, postId) VALUES (2, '2015-03-05 12:05:00', 'I wouldn''t worry too much, really. Just relax!',1)";
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
        self::$pdo->exec($q1);
        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        self::$pdo->exec($q6);
        print "[tdt4237] Done deleting all SQL tables.".PHP_EOL;
    }
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
