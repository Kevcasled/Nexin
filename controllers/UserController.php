<?php
// Cargar dependencias necesarias
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/Flash.php';
require_once __DIR__ . '/../utils/Csrf.php';

/** Auth, registro y login */
class UserController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new User($db);
    }

    /** Registro de usuarios */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::verify();
            $validator = new Validator();
            
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $validator->required('username', $username, 'El nombre de usuario');
            $validator->required('email', $email, 'El email');
            $validator->email('email', $email);
            $validator->required('password', $password, 'La contraseña');
            $validator->minLength('password', $password, 8, 'La contraseña');
            
            if (!$validator->hasErrors()) {
                if ($this->userModel->findByEmail($email)) {
                    Flash::error('Este email ya está registrado');
                } else {
                    $result = $this->userModel->insert($username, $email, $password);
                    if ($result) {
                        Flash::success('Registro exitoso. Ahora puedes iniciar sesión');
                        header("Location: index.php?action=login");
                        exit();
                    } else {
                        Flash::error('Error al registrar el usuario. Inténtalo de nuevo');
                    }
                }
            }
            
            $this->render('auth/register.php', [
                'errors' => $validator->getErrors(),
                'old' => $_POST
            ]);
            return;
        }
        $this->render('auth/register.php');
    }

    /** Login y sesión */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::verify();
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $this->userModel->findByEmail($email);
            
            if ($user && $this->userModel->verifyPassword($password, $user['password_hash'])) {
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                
                Flash::success('Bienvenido, ' . $user['username']);
                header("Location: index.php?action=posts");
                exit();
            } else {
                Flash::error('Email o contraseña incorrectos');
                $this->render('auth/login.php', ['old' => $_POST]);
                return;
            }
        }
        $this->render('auth/login.php');
    }

    /** Logout */
    public function logout() {
        $_SESSION = [];
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }

    /** Renderiza vista con datos */
    protected function render($view, $data = []) {
        extract($data);
        include __DIR__ . '/../views/' . $view;
    }
}
