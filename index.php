<?php

// User class representing a user in the system
class User
{
    private $username;
    private $email;
    private $password;
    private $role;

    public function __construct($username, $email, $password, $role)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    // Getters and setters

    public function getUsername()
    {
        return $this->username;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getRole()
    {
        return $this->role;
    }
}

// User database class responsible for user CRUD operations
class UserDatabase
{
    private $users = []; // Simulating user storage (replace with database connection)

    public function createUser($username, $email, $password, $role)
    {
        $user = new User($username, $email, $password, $role);
        $this->users[] = $user; // Simulating user creation (replace with database insertion)
        return $user;
    }

    public function getUserByUsername($username)
    {
        foreach ($this->users as $user) {
            if ($user->getUsername() === $username) {
                return $user;
            }
        }
        return null;
    }
}

// Authentication class responsible for user login and token generation
class Authentication
{
    private $userDatabase;

    public function __construct($userDatabase)
    {
        $this->userDatabase = $userDatabase;
    }

    public function login($username, $password)
    {
        $user = $this->userDatabase->getUserByUsername($username);
        if ($user && $user->getPassword() === $password) {
            
            return $this->generateToken();
        }
        return null;
    }

    private function generateToken()
    {
        return bin2hex(random_bytes(16)); // Generate a random token
    }
}

// Authorization class responsible for user role-based access control
class Authorization
{
    public const ROLE_ADMIN = 'administrator';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_READER = 'reader';

    public function hasAccess($user, $requiredRole)
    {
        $userRole = $user->getRole();
        switch ($requiredRole) {
            case self::ROLE_ADMIN:
                return $userRole === self::ROLE_ADMIN;
            case self::ROLE_EDITOR:
                return $userRole === self::ROLE_ADMIN || $userRole === self::ROLE_EDITOR;
            default:
            return $userRole === self::ROLE_ADMIN
                || $userRole === self::ROLE_EDITOR
                || $userRole === self::ROLE_READER;
        }
    }
}

// Usage example

// Initialize the user database
$userDatabase = new UserDatabase();

// Create a few users
$userDatabase->createUser('admin', 'admin@example.com', 'admin123', Authorization::ROLE_ADMIN);
$userDatabase->createUser('editor', 'editor@example.com', 'editor123', Authorization::ROLE_EDITOR);
$userDatabase->createUser('reader', 'reader@example.com', 'reader123', Authorization::ROLE_READER);

// Initialize the authentication and authorization modules
$authentication = new Authentication($userDatabase);
$authorization = new Authorization();

// User login
$token = $authentication->login('admin', 'admin123');

// Access control example
$user = $userDatabase->getUserByUsername('admin');
if ($user && $authorization->hasAccess($user, Authorization::ROLE_ADMIN)) {
    echo "User has admin access.";
} else {
    echo "User does not have admin access.";
}

class Article
{
    private $id;
    private $title;
    private $content;
    private $headerImage;
    private $contentImage;
    private $createDate;
    private $author;

    public function __construct($id, $data)
    {
        $this->id = $id;
        $this->title = $data['title'];
        $this->content = $data['content'];
        $this->headerImage = $data['headerImage'];
        $this->contentImage = $data['contentImage'];
        $this->createDate = $data['createDate'];
        $this->author = $data['author'];
    }

    // Getters and Setters

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getHeaderImage()
    {
        return $this->headerImage;
    }

    public function setHeaderImage($headerImage)
    {
        $this->headerImage = $headerImage;
    }

    public function getContentImage()
    {
        return $this->contentImage;
    }

    public function setContentImage($contentImage)
    {
        $this->contentImage = $contentImage;
    }

    public function getCreateDate()
    {
        return $this->createDate;
    }

    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }
}

class ArticleManager
{
    private $articles = [];

    public function createArticle($data)
    {
        $id = uniqid(); // Generate a unique ID for the article
        $article = new Article($id, $data);
        $this->articles[$id] = $article;

        return $article;
    }

    public function editArticle($id, $data)
    {
        if (isset($this->articles[$id])) {
            $article = $this->articles[$id];
            $article->setTitle($data['title']);
            $article->setContent($data['content']);
            $article->setHeaderImage($data['headerImage']);
            $article->setContentImage($data['contentImage)']);
            $article->setCreateDate($data['createDate']);
            $article->setAuthor($data['author']);

            return $article;
        }

        return null;
    }

    public function deleteArticle($id)
    {
        if (isset($this->articles[$id])) {
            unset($this->articles[$id]);
        }
    }

    public function getArticleList()
    {
        return $this->articles;
    }

    public function getArticleDetail($id)
    {
        if (isset($this->articles[$id])) {
            return $this->articles[$id];
        }

        return null;
    }
}

// Usage Example

$articleManager = new ArticleManager();

$data = [
    'title' => 'First Article',
    'content' => 'This is the content of the first article.',
    'headerImage' => 'header1.jpg',
    'contentImage' => 'content1.jpg',
    'createDate' => '2023-05-25',
    'author' => 'John Doe'
];

// Create new article
$article1 = $articleManager->createArticle($data);

// Edit existing article
$articleManager->editArticle($article1->getId(), $data);

// Delete existing article
$articleManager->deleteArticle($article1->getId());

// Get article list
$articles = $articleManager->getArticleList();

// Get article detail
$article2 = $articleManager->getArticleDetail($article1->getId());

// Print article list
foreach ($articles as $article) {
    echo "Title: " . $article->getTitle() . "\n";
    echo "Content: " . $article->getContent() . "\n";
    // Print other article details
}

// Print article detail
echo "Title: " . $article2->getTitle() . "\n";
echo "Content: " . $article2->getContent() . "\n";
// Print other article details

