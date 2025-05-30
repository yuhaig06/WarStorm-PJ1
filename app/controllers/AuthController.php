<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Core\Database;
use PDOException;
use InvalidArgumentException;
use RuntimeException;

class AuthController extends Controller
{
    private $dbConnection;  
    private $userModel;

    public function __construct()
    {
        parent::__construct(); 
        $this->dbConnection = \App\Core\Database::getInstance()->getConnection();
        $this->userModel = new UserModel($this->dbConnection);
    }

    private function checkUserLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Đăng ký tài khoản mới
     */
    public function register()
    {
        try {
            // Ưu tiên lấy dữ liệu từ form POST (giao diện web)
            if (!empty($_POST)) {
                $data = [
                    'username' => $_POST['username'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'password' => $_POST['password'] ?? '',
                    'full_name' => $_POST['full_name'] ?? '',
                    'phone' => $_POST['phone'] ?? ''
                ];
            } else {
                // Nếu không có POST, thử lấy từ JSON (API)
            $data = json_decode(file_get_contents('php://input'), true);
            }

            // Validate required fields
            $required = ['username', 'email', 'password', 'full_name', 'phone'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    // Nếu là form, render lại view và báo lỗi
                    if (!empty($_POST)) {
                        $error = "Vui lòng nhập đầy đủ thông tin!";
                        require APPROOT . '/app/views/users/register.php';
                        return;
                    }
                    // Nếu là API, trả về JSON
                    return $this->jsonResponse([
                        'status' => 'error',
                        'message' => "Field $field is required"
                    ], 400);
                }
            }

            // Check if username or email already exists
            if ($this->userModel->findByUsername($data['username'])) {
                if (!empty($_POST)) {
                    $error = "Tên đăng nhập đã tồn tại!";
                    require APPROOT . '/app/views/users/register.php';
                    return;
                }
                return $this->jsonResponse([
                    'status' => 'error',
                    'message' => 'Username already exists'
                ], 400);
            }

            if ($this->userModel->findByEmail($data['email'])) {
                if (!empty($_POST)) {
                    $error = "Email đã tồn tại!";
                    require APPROOT . '/app/views/users/register.php';
                    return;
                }
                return $this->jsonResponse([
                    'status' => 'error',
                    'message' => 'Email already exists'
                ], 400);
            }

            // Create user
            $userId = $this->userModel->create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'full_name' => $data['full_name'],
                'phone' => $data['phone'],
                'role' => 'user'
            ]);

            // Nếu là form, chuyển hướng về trang đăng nhập
            if (!empty($_POST)) {
                header('Location: ' . URLROOT . '/users/login');
                exit;
            }

            // Nếu là API, trả về JSON
            return $this->jsonResponse([
                'status' => 'success',
                'message' => 'Registration successful'
            ]);

        } catch (\PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            if (!empty($_POST)) {
                $error = "Lỗi kết nối CSDL: " . $e->getMessage();
                require APPROOT . '/app/views/users/register.php';
                return;
            }
            return $this->jsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            if (!empty($_POST)) {
                $error = "Lỗi hệ thống: " . $e->getMessage();
                require APPROOT . '/app/views/users/register.php';
                return;
            }
            return $this->jsonResponse([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đăng nhập
     */
    public function login($data = null) 
    {
        try {
            // Ưu tiên lấy dữ liệu từ form POST (giao diện web)
            if (!$data) {
                if (!empty($_POST)) {
                    $data = [
                        'email' => $_POST['email'] ?? '',
                        'password' => $_POST['password'] ?? ''
                    ];
                } else {
                $data = json_decode(file_get_contents('php://input'), true);
            }
            }

            if (empty($data['email']) || empty($data['password'])) {
                if (!empty($_POST)) {
                    $error = "Email và mật khẩu không được để trống";
                    require APPROOT . '/app/views/users/login.php';
                    return;
                }
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Email và mật khẩu không được để trống'
                ], 400);
            }

            error_log('Login data: ' . print_r($data, true));

            $user = $this->userModel->findByEmail($data['email']);
            
            if (!$user) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không đúng'
                ], 401);
            }

            // Tạm thời sửa để chấp nhận mật khẩu 'admin' không cần băm
            if ($data['password'] !== 'admin' && !password_verify($data['password'], $user['password'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không đúng'
                ], 401);
            }

            // Generate token
            $token = bin2hex(random_bytes(32));
            
            // Save user session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['token'] = $token;

            // Remove password from response
            unset($user['password']);

            // Nếu là form, chuyển hướng về trang chủ
            if (!empty($_POST)) {
                // Lưu session đăng nhập
                $_SESSION['user_id'] = $user['id'];
                header('Location: ' . URLROOT . '/home');
                exit;
            }

            // Nếu là API, trả về JSON
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'token' => $token,
                'user' => $user
            ]);

        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Lỗi kết nối cơ sở dữ liệu'
            ], 500);
        } catch (InvalidArgumentException $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (RuntimeException $e) {
            error_log('Runtime error: ' . $e->getMessage());
            return $this->jsonResponse([
                'success' => false, 
                'message' => 'Lỗi xử lý yêu cầu'
            ], 500);
        }
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        $debugFile = __DIR__ . '/../logs/logout_debug.log';
        file_put_contents($debugFile, date('Y-m-d H:i:s') . " - Vao logout\n", FILE_APPEND);
        if (!$this->checkUserLoggedIn()) {
            file_put_contents($debugFile, date('Y-m-d H:i:s') . " - Chua dang nhap\n", FILE_APPEND);
            // Nếu là form POST thì chuyển hướng về trang chủ
            if (!empty($_POST)) {
                file_put_contents($debugFile, date('Y-m-d H:i:s') . " - Redirect ve home (chua dang nhap)\n", FILE_APPEND);
                if (!headers_sent()) {
                    header('Location: /PJ1/BackEnd/public/home');
                    exit;
                } else {
                    file_put_contents($debugFile, date('Y-m-d H:i:s') . " - Headers already sent!\n", FILE_APPEND);
                    exit('Headers already sent!');
                }
            }
            // Nếu là API thì trả về JSON
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        // Xóa phiên đăng nhập
        $sessionId = session_id();
        $this->userModel->deleteSession($sessionId);
        file_put_contents($debugFile, date('Y-m-d H:i:s') . " - Da xoa session\n", FILE_APPEND);

        // Hủy session
        session_destroy();
        file_put_contents($debugFile, date('Y-m-d H:i:s') . " - Da huy session\n", FILE_APPEND);

        // Nếu là form POST thì chuyển hướng về trang chủ
        if (!empty($_POST)) {
            file_put_contents($debugFile, date('Y-m-d H:i:s') . " - Redirect ve home (logout thanh cong)\n", FILE_APPEND);
            if (!headers_sent()) {
                header('Location: /PJ1/BackEnd/public/home');
                exit;
            } else {
                file_put_contents($debugFile, date('Y-m-d H:i:s') . " - Headers already sent!\n", FILE_APPEND);
                exit('Headers already sent!');
            }
        }
        // Nếu là API thì trả về JSON
        return $this->jsonResponse([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * Lấy thông tin người dùng hiện tại
     */
    public function me()
    {
        if (!$this->checkUserLoggedIn()) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->find($userId);

        if (!$user) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ], 404);
        }

        return $this->jsonResponse([
            'success' => true,
            'data' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'avatar' => $user['avatar'],
                'role' => $user['role'],
                'permissions' => $this->userModel->getUserPermissions($userId)
            ]
        ]);
    }

    /**
     * Gửi yêu cầu đặt lại mật khẩu
     */
    public function forgotPassword()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['email'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'Email is required'], 400);
            }

            $user = $this->userModel->findByEmail($data['email']);
            
            if (!$user) {
                return $this->jsonResponse(['success' => false, 'message' => 'Email not found'], 404);
            }

            $resetToken = bin2hex(random_bytes(32));
            $this->userModel->updateResetToken($user['id'], $resetToken);
            
            // Send reset password email
            $this->sendResetPasswordEmail($data['email'], $resetToken);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Password reset instructions have been sent to your email'
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to process request'], 500);
        }
    }

    /**
     * Đặt lại mật khẩu
     */
    public function resetPassword()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['token']) || !isset($data['newPassword'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'Token and new password are required'], 400);
            }

            $user = $this->userModel->findByResetToken($data['token']);
            
            if (!$user) {
                return $this->jsonResponse(['success' => false, 'message' => 'Invalid or expired token'], 400);
            }

            // Thay thế AuthMiddleware::hashPassword bằng password_hash
            $hashedPassword = password_hash($data['newPassword'], PASSWORD_DEFAULT);
            $this->userModel->updatePassword($user['id'], $hashedPassword);
            $this->userModel->clearResetToken($user['id']);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Password has been reset successfully'
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to reset password'], 500);
        }
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    private function validateProfileData($data) {
        $errors = [];
        
        // Validate full name
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Họ tên không được để trống';
        } elseif (strlen($data['full_name']) > 100) {
            $errors['full_name'] = 'Họ tên không được vượt quá 100 ký tự';
        }

        // Validate avatar if exists
        if (isset($_FILES['avatar'])) {
            $file = $_FILES['avatar'];
            // Check file size (max 2MB)
            if ($file['size'] > 2 * 1024 * 1024) {
                $errors['avatar'] = 'Kích thước file không được vượt quá 2MB';
            }
            // Check file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                $errors['avatar'] = 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF)';
            }
        }

        return [
            'success' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }

    public function updateProfile()
    {
        if (!$this->checkUserLoggedIn()) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        // Get and validate data
        $data = [
            'full_name' => $_POST['full_name'] ?? ''
        ];
        
        $validationResult = $this->validateProfileData($data);
        
        if (!$validationResult['success']) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validationResult['errors']
            ], 422);
        }

        $userId = $_SESSION['user_id'];
        $updateData = [
            'full_name' => $data['full_name']
        ];

        // Xử lý upload avatar
        if (isset($_FILES['avatar'])) {
            $avatar = $this->handleFileUpload('avatar', 'avatars');
            if ($avatar['success']) {
                $updateData['avatar'] = $avatar['path'];
            }
        }

        // Cập nhật thông tin
        if (!$this->userModel->updateUser($userId, $updateData)) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Không thể cập nhật thông tin'
            ], 500);
        }

        return $this->jsonResponse([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công'
        ]);
    }

    /**
     * Đổi mật khẩu
     */
    private function validatePasswordData($data) {
        $errors = [];
        
        // Validate current password
        if (empty($data['current_password'])) {
            $errors['current_password'] = 'Mật khẩu hiện tại không được để trống';
        }

        // Validate new password
        if (empty($data['new_password'])) {
            $errors['new_password'] = 'Mật khẩu mới không được để trống';
        } elseif (strlen($data['new_password']) < 6) {
            $errors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        } elseif (strlen($data['new_password']) > 50) {
            $errors['new_password'] = 'Mật khẩu mới không được vượt quá 50 ký tự';
        }

        return [
            'success' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }

    public function changePassword()
    {
        if (!$this->checkUserLoggedIn()) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        // Get and validate password data
        $data = [
            'current_password' => $_POST['current_password'] ?? '',
            'new_password' => $_POST['new_password'] ?? ''
        ];
        
        $validationResult = $this->validatePasswordData($data);
        
        if (!$validationResult['success']) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validationResult['errors']
            ], 422);
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->find($userId);

        // Kiểm tra mật khẩu hiện tại
        if (!password_verify($data['current_password'], $user['password'])) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng'
            ], 400);
        }

        // Cập nhật mật khẩu mới
        if (!$this->userModel->updateUser($userId, [
            'password' => password_hash($data['new_password'], PASSWORD_DEFAULT)
        ])) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Không thể cập nhật mật khẩu'
            ], 500);
        }

        return $this->jsonResponse([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }

    public function socialLogin() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['code']) || !isset($data['provider'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
            }

            $code = $data['code'];
            $provider = $data['provider'];

            switch ($provider) {
                case 'google':
                    $tokenResponse = $this->exchangeGoogleCodeForToken($code);
                    break;
                case 'facebook':
                    $tokenResponse = $this->exchangeFacebookCodeForToken($code);
                    break;
                case 'twitter':
                    $tokenResponse = $this->exchangeTwitterCodeForToken($code);
                    break;
                default:
                    return $this->jsonResponse(['success' => false, 'message' => 'Unsupported provider'], 400);
            }

            if (!isset($tokenResponse['access_token'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'Failed to retrieve access token'], 500);
            }

            // Retrieve user info from the provider
            $userInfo = $this->getUserInfoFromProvider($provider, $tokenResponse['access_token']);

            if (!$userInfo) {
                return $this->jsonResponse(['success' => false, 'message' => 'Failed to retrieve user info'], 500);
            }

            // Check if user exists in the database
            $user = $this->userModel->findByEmail($userInfo['email']);
            if (!$user) {
                // Create a new user if not exists
                $userId = $this->userModel->create([
                    'username' => $userInfo['name'],
                    'email' => $userInfo['email'],
                    'password' => null, // Social login users don't have passwords
                    'avatar' => $userInfo['picture'] ?? null,
                ]);
                $user = $this->userModel->findById($userId);
            }

            // Generate JWT token
            $token = bin2hex(random_bytes(32));

            return $this->jsonResponse([
                'success' => true,
                'access_token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function exchangeGoogleCodeForToken($code) {
        $clientId = $_ENV['GOOGLE_CLIENT_ID'];
        $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];
        $redirectUri = $_ENV['GOOGLE_REDIRECT_URI'];

        $response = $this->httpPost('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        return json_decode($response, true);
    }

    private function exchangeFacebookCodeForToken($code) {
        $clientId = $_ENV['FACEBOOK_CLIENT_ID'];
        $clientSecret = $_ENV['FACEBOOK_CLIENT_SECRET'];
        $redirectUri = $_ENV['FACEBOOK_REDIRECT_URI'];

        $response = $this->httpGet("https://graph.facebook.com/v10.0/oauth/access_token", [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'client_secret' => $clientSecret,
            'code' => $code,
        ]);

        return json_decode($response, true);
    }

    private function exchangeTwitterCodeForToken($code) {
        // Implement Twitter token exchange logic here
        return [];
    }

    private function getUserInfoFromProvider($provider, $accessToken) {
        switch ($provider) {
            case 'google':
                $response = $this->httpGet('https://www.googleapis.com/oauth2/v2/userinfo', [
                    'access_token' => $accessToken,
                ]);
                return json_decode($response, true);
            case 'facebook':
                $response = $this->httpGet('https://graph.facebook.com/me', [
                    'fields' => 'id,name,email,picture',
                    'access_token' => $accessToken,
                ]);
                return json_decode($response, true);
            case 'twitter':
                // Implement Twitter user info retrieval logic here
                return [];
            default:
                return null;
        }
    }

    private function httpPost($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function httpGet($url, $params) {
        $url .= '?' . http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function sendVerificationEmail($email, $token) {
        // Implement email sending logic here
        // You can use PHPMailer or other email libraries
    }

    private function sendResetPasswordEmail($email, $token) {
        // Implement email sending logic here
        // You can use PHPMailer or other email libraries
    }

    public function showLoginForm() {
        require_once __DIR__ . '/../views/users/login.php';
    }

    /**
     * Render giao diện đăng ký
     */
    public function registerForm() {
        require_once __DIR__ . '/../views/users/register.php';
    }

    protected function jsonResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function handleFileUpload($inputName, $uploadDir) {
        if (!isset($_FILES[$inputName])) {
            return [
                'success' => false,
                'message' => 'No file uploaded'
            ];
        }

        $file = $_FILES[$inputName];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        // Validate file
        if ($fileError !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Upload failed with error code: ' . $fileError
            ];
        }

        // Validate file size (max 2MB)
        if ($fileSize > 2097152) {
            return [
                'success' => false,
                'message' => 'File size too large (max 2MB)'
            ];
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($fileTmpName);
        if (!in_array($fileType, $allowedTypes)) {
            return [
                'success' => false,
                'message' => 'Invalid file type'
            ];
        }

        // Generate unique filename
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileExt;

        // Create upload directory if not exists
        $uploadPath = __DIR__ . '/../../../uploads/' . $uploadDir;
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Move uploaded file
        $destination = $uploadPath . '/' . $newFileName;
        if (!move_uploaded_file($fileTmpName, $destination)) {
            return [
                'success' => false,
                'message' => 'Failed to move uploaded file'
            ];
        }

        return [
            'success' => true,
            'path' => '/uploads/' . $uploadDir . '/' . $newFileName
        ];
    }

    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        $user = $this->userModel->find($_SESSION['user_id']);
        require APPROOT . '/app/views/users/profile.php';
    }
}