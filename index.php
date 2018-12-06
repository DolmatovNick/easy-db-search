<?php

require 'vendor/autoload.php';

/** @var $pdo PDO */
$pdo = require('db/pdo.php');

// Show feedback table
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT 
        name, 
        DATE_FORMAT(created_at, '%d.%m.%Y %H : %i : %s ') created_at, 
        CONCAT(email_local, '@', email_domain) email, 
        text 
    FROM site_feedback";

    $where = '';
    $whereValues = [];
    if (!empty($_GET['search-email'])) {
        $email = trim(htmlspecialchars_decode($_GET['search-email']));
        $email  = explode('@', $email);

        $where = 'WHERE email_local = :email_local AND email_domain = :email_domain';
        $whereValues = [':email_local' => $email[0], ':email_domain' => $email[1]];
    }

    $_GET['page'] = (int)$_GET['page'] < 0 ? 0 : (int)$_GET['page'];
    $limit = 'LIMIT 25 OFFSET ' . $_GET['page'] * 25;

    $sql .= ' ' . $where . ' ' . $limit;
    $sth = $pdo->prepare($sql);
    $sth->execute($whereValues);

    $feedback = $sth->fetchAll(PDO::FETCH_ASSOC);

    require 'public/index.html';
}

// Save feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $text = trim($_POST['text']);

    // validate
    $errors = getValidateErrors($name, $email, $text);
    if (count($errors) > 0) {
        exit(getResponseJson('fail', 'Введенные данные не могут быть сохранены:<br>'.implode('<br>', $errors)));
    }

    $sth = $pdo->prepare(
        'INSERT INTO site_feedback (name, email_local, email_domain, text) VALUES (:name, :email_local, :email_domain, :text)'
    );

    try {
        $sth->bindValue(':name', $name);
        $email = explode('@', $email);
        $sth->bindValue(':email_local', $email[0]);
        $sth->bindValue(':email_domain', $email[1]);
        $sth->bindValue(':text', $text);
        if ($sth->execute() === true) {
            exit(getResponseJson('ok', 'Ваш запрос успешно отправлен'));
        }
    } catch (\Exception $e) {
        exit(getResponseJson('fail', 'Ошибка не сервере - сохранение не удалось'));
    }

    exit(getResponseJson('fail', 'Ошибка не сервере - сохранение не удалось'));
}

function getValidateErrors($name, $email, $text): array
{
    $errors = [];
    if (mb_strlen($name) < 1) {
        $errors[] = 'Имя должно быть более 1 символа.';
    };
    if (mb_strlen($name) > 63) {
        $errors[] = 'Имя должно быть менее 63 символа.';
    };
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'Email не валидный.';
    }
    if (mb_strlen($text) < 1) {
        $errors[] = 'Текст должен быть более 1 символа.';
    }
    if (mb_strlen($text) > 1024) {
        $errors[] = 'Текст должен быть менее 1024 символов.';
    };
    return $errors;
}

function getResponseJson($status, $text): string
{
    return '{"status":"'.$status.'", "text": "'.$text.'"}';
}
