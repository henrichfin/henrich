<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Database connection
$host = 'localhost';
$dbname = 'henrich';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Parse the path to get the endpoint
$pathParts = explode('/', trim($path, '/'));
$endpoint = end($pathParts);

switch ($method) {
    case 'GET':
        if ($endpoint === 'health') {
            echo json_encode(['status' => 'ok']);
        } elseif ($endpoint === 'tasks') {
            try {
                $stmt = $pdo->query("SELECT id, title, description, status, created_at FROM tasks ORDER BY created_at DESC");
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($tasks);
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to fetch tasks']);
            }
        }
        break;
        
    case 'POST':
        if ($endpoint === 'tasks') {
            $input = json_decode(file_get_contents('php://input'), true);
            $title = trim($input['title'] ?? '');
            $description = trim($input['description'] ?? '');
            $status = $input['status'] ?? 'pending';
            
            if (empty($title)) {
                http_response_code(400);
                echo json_encode(['error' => 'Title is required']);
                exit;
            }
            
            $validStatuses = ['pending', 'in_progress', 'done'];
            if (!in_array($status, $validStatuses)) {
                $status = 'pending';
            }
            
            try {
                $stmt = $pdo->prepare("INSERT INTO tasks (title, description, status) VALUES (?, ?, ?)");
                $stmt->execute([$title, $description, $status]);
                $taskId = $pdo->lastInsertId();
                
                $stmt = $pdo->prepare("SELECT id, title, description, status, created_at FROM tasks WHERE id = ?");
                $stmt->execute([$taskId]);
                $task = $stmt->fetch(PDO::FETCH_ASSOC);
                
                http_response_code(201);
                echo json_encode($task);
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create task']);
            }
        }
        break;
        
    case 'PUT':
        if (preg_match('/\/tasks\/(\d+)/', $path, $matches)) {
            $taskId = $matches[1];
            $input = json_decode(file_get_contents('php://input'), true);
            
            try {
                // Check if task exists
                $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
                $stmt->execute([$taskId]);
                $existingTask = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$existingTask) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Task not found']);
                    exit;
                }
                
                $title = $input['title'] ?? $existingTask['title'];
                $description = $input['description'] ?? $existingTask['description'];
                $status = $input['status'] ?? $existingTask['status'];
                
                $validStatuses = ['pending', 'in_progress', 'done'];
                if (!in_array($status, $validStatuses)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid status']);
                    exit;
                }
                
                $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, status = ? WHERE id = ?");
                $stmt->execute([$title, $description, $status, $taskId]);
                
                $stmt = $pdo->prepare("SELECT id, title, description, status, created_at FROM tasks WHERE id = ?");
                $stmt->execute([$taskId]);
                $task = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode($task);
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update task']);
            }
        }
        break;
        
    case 'DELETE':
        if (preg_match('/\/tasks\/(\d+)/', $path, $matches)) {
            $taskId = $matches[1];
            
            try {
                $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
                $stmt->execute([$taskId]);
                
                if ($stmt->rowCount() === 0) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Task not found']);
                } else {
                    http_response_code(204);
                }
            } catch(PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete task']);
            }
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
