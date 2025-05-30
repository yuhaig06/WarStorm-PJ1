<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use PDO;

class UserModel extends Model
{
    protected $table = 'users';
    protected $fillable = ['username', 'email', 'password'];

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Lấy thông tin người dùng theo ID
     * 
     * @param int $id ID của người dùng
     * @return object|false Đối tượng người dùng hoặc false nếu không tìm thấy
     */
    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Đếm tổng số người dùng
     */
    public function countUsers()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    /**
     * Lấy danh sách tất cả người dùng
     * 
     * @param int $page Trang hiện tại
     * @param int $perPage Số bản ghi mỗi trang
     * @return array
     */
    public function getAllUsers($page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        
        // Đếm tổng số bản ghi
        $countStmt = $this->db->query("SELECT COUNT(*) as total FROM users");
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Lấy dữ liệu phân trang
        $stmt = $this->db->prepare("
            SELECT 
                id, 
                username, 
                full_name,
                email, 
                role,
                is_verified as is_active,
                created_at,
                updated_at 
            FROM users 
            ORDER BY id ASC 
            LIMIT :limit OFFSET :offset
        ");
        
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'data' => $users,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Lấy danh sách người dùng gần đây
     */
    public function getRecentUsers($limit = 5)
    {
        $stmt = $this->db->prepare("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm người dùng theo username hoặc email
     * 
     * @param string $username Tên đăng nhập
     * @param string $email Email
     * @return array|false Thông tin người dùng hoặc false nếu không tìm thấy
     */
    public function getUserByUsernameOrEmail($username, $email)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE username = :username OR email = :email 
            LIMIT 1
        ");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tạo người dùng mới
     * 
     * @param array $userData Dữ liệu người dùng
     * @return int|false ID của người dùng mới tạo hoặc false nếu thất bại
     */
    /**
     * Tạo người dùng mới
     * 
     * @param array $data Dữ liệu người dùng
     * @return int|false ID của người dùng mới tạo hoặc false nếu thất bại
     */
    public function create($data)
    {
        $sql = "INSERT INTO users (
            username, 
            email, 
            password, 
            full_name, 
            role, 
            is_verified,
            verification_token,
            created_at, 
            updated_at
        ) VALUES (
            :username, 
            :email, 
            :password, 
            :full_name, 
            :role, 
            :is_verified,
            :verification_token,
            :created_at, 
            :updated_at
        )";
        
        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute([
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => $data['password'] ?? null,
            ':full_name' => $data['full_name'] ?? null,
            ':role' => $data['role'] ?? 'user',
            ':is_verified' => $data['is_verified'] ?? 1,
            ':verification_token' => $data['verification_token'] ?? null,
            ':created_at' => $data['created_at'] ?? date('Y-m-d H:i:s'),
            ':updated_at' => $data['updated_at'] ?? date('Y-m-d H:i:s')
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Tìm người dùng theo email
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Tìm người dùng theo username
     */
    public function findByUsername($username)
    {
        return $this->findOne(['username' => $username]);
    }

    /**
     * Tìm người dùng theo remember token
     */
    public function findByRememberToken($token)
    {
        return $this->findOne(['remember_token' => $token]);
    }

    /**
     * Tạo người dùng mới
     */
    public function createUser($data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->create($data);
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function updateUser($id, $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->update($id, $data);
    }

    /**
     * Xác thực người dùng
     */
    public function authenticate($email, $password)
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Kiểm tra quyền của người dùng
     */
    public function hasPermission($userId, $permission)
    {
        $sql = "SELECT p.name 
                FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id 
                JOIN users u ON u.role = rp.role 
                WHERE u.id = ? AND p.name = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $permission]);
        return $stmt->fetch() !== false;
    }

    /**
     * Lấy danh sách quyền của người dùng
     */
    public function getUserPermissions($userId)
    {
        $sql = "SELECT p.name 
                FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id 
                JOIN users u ON u.role = rp.role 
                WHERE u.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Tạo phiên đăng nhập
     */
    public function createSession($userId, $sessionId)
    {
        $sql = "INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $userId,
            $sessionId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    /**
     * Xóa phiên đăng nhập
     */
    public function deleteSession($sessionId)
    {
        $sql = "DELETE FROM user_sessions WHERE session_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$sessionId]);
    }

    /**
     * Tạo token đặt lại mật khẩu
     */
    public function createPasswordReset($email)
    {
        $token = bin2hex(random_bytes(32));
        $sql = "INSERT INTO password_resets (email, token) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$email, $token]) ? $token : false;
    }

    /**
     * Kiểm tra token đặt lại mật khẩu
     */
    public function verifyPasswordReset($email, $token)
    {
        $sql = "SELECT * FROM password_resets 
                WHERE email = ? AND token = ? 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email, $token]);
        return $stmt->fetch() !== false;
    }

    /**
     * Xóa token đặt lại mật khẩu
     */
    public function deletePasswordReset($email)
    {
        $sql = "DELETE FROM password_resets WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$email]);
    }

    /**
     * Cập nhật thời gian hoạt động cuối cùng
     */
    public function updateLastActivity($userId)
    {
        return $this->update($userId, ['last_activity' => date('Y-m-d H:i:s')]);
    }

    /**
     * Lấy danh sách người dùng theo vai trò
     */
    public function getUsersByRole($role)
    {
        return $this->findAll(['role' => $role]);
    }

    /**
     * Lấy danh sách người dùng theo trạng thái
     */
    public function getUsersByStatus($status)
    {
        return $this->findAll(['status' => $status]);
    }

    /**
     * Tìm kiếm người dùng
     */
    public function searchUsers($keyword)
    {
        $sql = "SELECT * FROM users 
                WHERE username LIKE ? 
                OR email LIKE ? 
                OR full_name LIKE ?";
        
        $keyword = "%{$keyword}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$keyword, $keyword, $keyword]);
        return $stmt->fetchAll();
    }



    /**
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findByVerificationToken($token)
    {
        $sql = "SELECT * FROM users WHERE verification_token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findByResetToken($token)
    {
        $sql = "SELECT * FROM users WHERE reset_token = :token AND reset_token_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $values[":$key"] = $value;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $values[':id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function updatePassword($id, $password)
    {
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function updateResetToken($id, $token)
    {
        $sql = "UPDATE users SET reset_token = :token, reset_token_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function clearResetToken($id)
    {
        $sql = "UPDATE users SET reset_token = NULL, reset_token_expires = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function verifyEmail($id)
    {
        $sql = "UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function findOne($conditions = []) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $sql .= " AND {$key} = ?";
            $params[] = $value;
        }

        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll($conditions = []) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $sql .= " AND {$key} = ?";
            $params[] = $value;
        }

        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Lấy danh sách người dùng với các tuỳ chọn lọc, phân trang, tìm kiếm
     */
    public function getUsers($limit = 10, $offset = 0, $status = null, $role = null, $search = null)
    {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        if ($role) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }
        if ($search) {
            $sql .= " AND (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        $sql .= " ORDER BY created_at DESC";
        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params[] = (int)$limit;
        }
        if ($offset !== null) {
            $sql .= " OFFSET ?";
            $params[] = (int)$offset;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái người dùng
     */
    public function updateStatus($userId, $status)
    {
        return $this->update($userId, ['status' => $status]);
    }

    /**
     * Cập nhật vai trò người dùng
     */
    public function updateRole($userId, $role)
    {
        return $this->update($userId, ['role' => $role]);
    }

    /**
     * Xóa người dùng theo ID
     * @param int $id ID của người dùng cần xóa
     * @return bool True nếu xóa thành công, False nếu thất bại
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
}
