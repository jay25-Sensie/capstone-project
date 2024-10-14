    <?php
    header("Content-Type: application/json");
    include 'connection.php';
    // Check if the PDO object exists
    if (!$pdo) {
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }

    // Fetch HTTP request method (GET, POST, PUT, DELETE)
    $method = $_SERVER['REQUEST_METHOD'];

    // Sanitize incoming data (if applicable)
    function sanitizeInput($data)
    {
        return htmlspecialchars(strip_tags($data));
    }

    // ValuserIDate required fields
    function valuserIDateFields($data, $fields)
    {
        foreach ($fields as $field) {
            if (empty($data->$field)) {
                return false;
            }
        }
        return true;
    }

    // Error response helper
    function errorResponse($code, $message)
    {
        http_response_code($code);
        echo json_encode([
            'status' => $code,
            'message' => $message,
            'data' => null
        ]);
        exit;
    }

    switch ($method) {
        case 'GET':
            // ValuserIDate userID for GET request (if provuserIDed)
            if (isset($_GET['userID'])) {
                $userID = intval($_GET['userID']);
                if ($userID <= 0) {
                    errorResponse(400, "InvaluserID userID provuserIDed");
                }

                $stmt = $pdo->prepare("SELECT * FROM users WHERE userID = ?");
                $stmt->execute([$userID]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    http_response_code(200);
                    echo json_encode($user);
                } else {
                    errorResponse(404, "User not found");
                }
            } else {
                // No specific userID, return all users
                $stmt = $pdo->query("SELECT * FROM users");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);
                echo json_encode($users);
            }
            break;

        case 'POST':
            // Decode JSON input
            $data = json_decode(file_get_contents("php://input"));

            // ValuserIDate required fields: username, email, password
            if (!$data || !valuserIDateFields($data, ['username', 'password'])) {
                errorResponse(400, "Missing required fields");
            }

            // Sanitize inputs
            $username = sanitizeInput($data->username);
            $email = filter_var($data->email, FILTER_VALuserIDATE_EMAIL);
            $password = sanitizeInput($data->password);

            // ValuserIDate email format
            if (!$email) {
                errorResponse(400, "InvaluserID email format");
            }

            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?, ?)");
            $result = $stmt->execute([$username, $email, $password]);

            if ($result) {
                http_response_code(201);
                echo json_encode(["success" => true, "message" => "User created successfully"]);
            } else {
                errorResponse(500, "Failed to create user");
            }
            break;

        case 'PUT':
            // Decode JSON input
            $data = json_decode(file_get_contents("php://input"));

            // ValuserIDate required fields: userID, username, email, password
            if (!$data || !valuserIDateFields($data, ['userID', 'username', 'password'])) {
                errorResponse(400, "Missing required fields");
            }

            // Sanitize inputs
            $userID = intval($data->userID);
            $username = sanitizeInput($data->username);
            $password = sanitizeInput($data->password);

            // ValuserIDate userID and email format
            if ($userID <= 0) {
                errorResponse(400, "InvaluserID userID provuserIDed");
            }
            if (!$email) {
                errorResponse(400, "InvaluserID email format");
            }

            $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE userID = ?");
            $result = $stmt->execute([$username, $password, $userID]);

            if ($result) {
                http_response_code(200);
                echo json_encode(["success" => true, "message" => "User updated successfully"]);
            } else {
                errorResponse(500, "Failed to update user");
            }
            break;

        case 'DELETE':
            if (isset($_GET['userID'])) {
                $userID = intval($_GET['userID']);
                if ($userID <= 0) {
                    errorResponse(400, "InvaluserID userID provuserIDed");
                }

                $stmt = $pdo->prepare("DELETE FROM users WHERE userID = ?");
                $result = $stmt->execute([$userID]);

                if ($result) {
                    http_response_code(200);
                    echo json_encode(["success" => true, "message" => "User deleted successfully"]);
                } else {
                    errorResponse(404, "User not found");
                }
            } else {
                errorResponse(400, "userID is required for deletion");
            }
            break;

        default:
            errorResponse(405, "Method Not Allowed");
            break;
    }