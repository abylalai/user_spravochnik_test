<?php
require 'sql/connect.php'; // Подключаем RedBeanPHP


// Устанавливаем заголовки
header('Content-Type: application/json');

// Получаем действие из запроса
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getUsers':
        $users = R::findAll('users');
        $roles = R::findAll('roles');
        $roleMap = [];
        foreach ($roles as $role) {
            $roleMap[$role->id] = $role->role_name;
        }

        $result = [];
        foreach ($users as $user) {
            $result[] = [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'login' => $user->login,
                'role_name' => $roleMap[$user->role_id] ?? '',
                'is_blocked' => (bool)$user->is_blocked
            ];
        }
        echo json_encode($result);
        break;

    case 'getRoles':
        $roles = R::findAll('roles');
        $result = [];
        foreach ($roles as $role) {
            $result[] = ['id' => $role->id, 'value' => $role->role_name];
        }
        echo json_encode($result);
        break;

    case 'createUser':

        $data = json_decode(file_get_contents('php://input'), true);
    
        // Отладка: Проверьте входящие данные
        file_put_contents('php_debug.log', print_r($data, true), FILE_APPEND);
    
        if (empty($data['full_name']) || empty($data['login']) || empty($data['role_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
            exit;
        }
    
        $user = R::dispense('users');
        $user->full_name = $data['full_name'];
        $user->login = $data['login'];
        $user->password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->role_id = $data['role_id'];
        $user->is_blocked = false;
        R::store($user);
        echo json_encode(['status' => 'success']);
        break;
    
    case 'blockUser':
        $id = $_GET['id'];
        $user = R::load('users', $id);
        if ($user->id) {
            $user->is_blocked = !$user->is_blocked;
            R::store($user);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
