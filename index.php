<?php

// User class representing a user in the system
class User
{
    private $username;
    private $email;
    private $password;
    private $role;

    public function construct($username, $email, $password, $role)
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

    public function construct($userDatabase)
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

class Article
{
    private $id;
    private $title;
    private $content;
    private $headerImage;
    private $contentImage;
    private $createDate;
    private $author;
    private $ratings;
    private $reviews;

    public function construct($id, $data)
    {
        $this->id = $id;
        $this->title = $data['title'];
        $this->content = $data['content'];
        $this->headerImage = $data['headerImage'];
        $this->contentImage = $data['contentImage'];
        $this->createDate = $data['createDate'];
        $this->author = $data['author'];
        $this->ratings = [];
        $this->reviews = [];
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
    public function getAverageRating()
    {
        $totalRatings = count($this->ratings);
        if ($totalRatings > 0) {
            $sum = array_reduce($this->ratings, function ($carry, $rating) {
                return $carry + $rating;
            });
            return $sum / $totalRatings;
        } else {
            return 0;
        }
    }

    public function addRating($rating)
    {
        $this->ratings[] = $rating;
    }

    public function addReview($review)
    {
        $this->reviews[] = $review;
    }

    public function getReviews()
    {
        return $this->reviews;
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

    public function searchArticles($keyword)
    {
        $results = [];
        foreach ($this->articles as $article) {
            if (stripos($article->getTitle(), $keyword)
                !== false || stripos($article->getContent(), $keyword)
                !== false) {
                $results[] = $article;
            }
        }
        return $results;
    }

    public function filterArticlesByCategory($categoryId)
    {
        $results = [];
        foreach ($this->articles as $article) {
            if ($article->getCategory() && $article->getCategory()->getId() === $categoryId) {
                $results[] = $article;
            }
        }
        return $results;
    }

    public function sortArticlesByDate($order = 'asc')
    {
        usort($this->articles, function ($a, $b) use ($order) {
            $date1 = strtotime($a->getCreateDate());
            $date2 = strtotime($b->getCreateDate());

            if ($order === 'desc') {
                return $date2 - $date1;
            } else {
                return $date1 - $date2;
            }
        });
    }
}

class Category
{
    private $id;
    private $name;

    public function construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    // Getters and Setters

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}

class CategoryManager
{
    private $categories = [];

    public function createCategory($name)
    {
        $id = uniqid(); // Generate a unique ID for the category
        $category = new Category($id, $name);
        $this->categories[$id] = $category;

        return $category;
    }

    public function editCategory($id, $name)
    {
        if (isset($this->categories[$id])) {
            $category = $this->categories[$id];
            $category->setName($name);

            return $category;
        }

        return null;
    }

    public function deleteCategory($id)
    {
        if (isset($this->categories[$id])) {
            unset($this->categories[$id]);
        }
    }

    public function getCategoryList()
    {
        return $this->categories;
    }

    public function getCategoryDetail($id)
    {
        if (isset($this->categories[$id])) {
            return $this->categories[$id];
        }

        return null;
    }
}

class Comment
{
    private $id;
    private $articleId;
    private $content;
    private $author;
    private $createDate;

    public function construct($id, $articleId, $content, $author, $createDate)
    {
        $this->id = $id;
        $this->articleId = $articleId;
        $this->content = $content;
        $this->author = $author;
        $this->createDate = $createDate;
    }

    // Getters and Setters

    public function getId()
    {
        return $this->id;
    }

    public function getArticleId()
    {
        return $this->articleId;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getCreateDate()
    {
        return $this->createDate;
    }

    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    }
}

class CommentManager
{
    private $comments = [];

    public function createComment($articleId, $content, $author)
    {
        $id = uniqid(); // Generate a unique ID for the comment
        $createDate = date('Y-m-d H:i:s'); // Get the current date and time
        $comment = new Comment($id, $articleId, $content, $author, $createDate);
        $this->comments[$id] = $comment;

        return $comment;
    }

    public function editComment($id, $content)
    {
        if (isset($this->comments[$id])) {
            $comment = $this->comments[$id];
            $comment->setContent($content);

            return $comment;
        }

        return null;
    }

    public function deleteComment($id)
    {
        if (isset($this->comments[$id])) {
            unset($this->comments[$id]);
        }
    }

    public function getCommentList()
    {
        return $this->comments;
    }

    public function getCommentDetail($id)
    {
        if (isset($this->comments[$id])) {
            return $this->comments[$id];
        }

        return null;
    }
}

class Rating
{
    private $articleId;
    private $rating;

    const ARTICLE_NOT_FOUND = 'Article not found';

    public function construct($articleId, $rating)
    {
        $this->articleId = $articleId;
        $this->rating = $rating;
    }

    public function getArticleId()
    {
        return $this->articleId;
    }

    public function getRating()
    {
        return $this->rating;
    }
}

class Review
{
    private $articleId;
    private $review;

    public function construct($articleId, $review)
    {
        $this->articleId = $articleId;
        $this->review = $review;
    }

    public function getArticleId()
    {
        return $this->articleId;
    }

    public function getReview()
    {
        return $this->review;
    }
}

class UserFeed
{
    
    private $users;
    private $articles;

    public function construct($users, $articles)
    {
        $this->users = $users;
        $this->articles = $articles;
    }

    // Method to get the article list based on user preferences
    public function getUserFeed($userId)
    {
        $preferences = $this->users[$userId];
        $filteredArticles = array();
        
        foreach ($this->articles as $article) {
            if ($this->isArticleMatchPreferences($article, $preferences)) {
                $filteredArticles[] = $article;
            }
        }
        
        return $filteredArticles;
    }
    
    // Method to check if an article matches the user preferences
    public function isArticleMatchPreferences($article, $preferences)
    {
        $categoryMatch = in_array($article['category'], $preferences['favoriteCategory']);
        $authorMatch = in_array($article['author'], $preferences['favoriteAuthor']);
        $countryMatch = in_array($article['country'], $preferences['favoriteCountry']);
        
        return $categoryMatch && $authorMatch && $countryMatch;
    }
    
    // Method to register a new user with their preferences
    public function registerUser($userId, $preferences)
    {
        $this->users[$userId] = $preferences;
    }

    // Method to retrieve user preferences
    public function getUserPreferences()
    {
        return $this->users;
    }
}

class UserFeedManager
{
    private $userFeed; // Instance of the UserFeed
    
    public function construct($userId, $article)
    {
        $this->userFeed = new UserFeed($userId, $article);
    }
    
    // Method to register a new user with their preferences
    public function registerUser($userId, $preferences)
    {
        $this->userFeed->registerUser($userId, $preferences);
    }
    
    // Method to send notification to users when new articles match their preferences
    public function notifyUsers($article)
    {
        foreach ($this->userFeed->getUserPreferences() as $userId => $preferences) {
            if ($this->userFeed->isArticleMatchPreferences($article, $preferences)) {
                $this->sendNotification($userId, $article);
            }
        }
    }
    
    // Method to send notification to a user
    public function sendNotification($userId, $article)
    {
        echo "Notification sent to user $userId: New article published - {$article['title']}\n";
    }

    public function getCountries()
    {

        $countries = array(
            'AF' =>'Afghanistan',
            'AL' =>'Albania',
            'DZ' =>'Algeria',
            'AS' =>'American Samoa',
            'AD' =>'Andorra',
            'AO' =>'Angola',
            'AI' =>'Anguilla',
            'AQ' =>'Antarctica',
            'AG' =>'Antigua and Barbuda',
            'AR' =>'Argentina',
            'AM' =>'Armenia',
            'AW' =>'Aruba',
            'AU' =>'Australia',
            'AT' =>'Austria',
            'AZ' =>'Azerbaijan',
            'BS' =>'Bahamas',
            'BH' =>'Bahrain',
            'BD' =>'Bangladesh',
            'BB' =>'Barbados',
            'BY' =>'Belarus',
            'BE' =>'Belgium',
            'BZ' =>'Belize',
            'BJ' =>'Benin',
            'BM' =>'Bermuda',
            'BT' =>'Bhutan',
            'BO' =>'Bolivia',
            'BA' =>'Bosnia and Herzegovina',
            'BW' =>'Botswana',
            'BV' =>'Bouvet Island',
            'BR' =>'Brazil',
            'BQ' =>'British Antarctic Territory',
            'IO' =>'British Indian Ocean Territory',
            'VG' =>'British Virgin Islands',
            'BN' =>'Brunei',
            'BG' =>'Bulgaria',
            'BF' =>'Burkina Faso',
            'BI' =>'Burundi',
            'KH' =>'Cambodia',
            'CM' =>'Cameroon',
            'CA' =>'Canada',
            'CT' =>'Canton and Enderbury Islands',
            'CV' =>'Cape Verde',
            'KY' =>'Cayman Islands',
            'CF' =>'Central African Republic',
            'TD' =>'Chad',
            'CL' =>'Chile',
            'CN' =>'China',
            'CX' =>'Christmas Island',
            'CC' =>'Cocos [Keeling] Islands',
            'CO' =>'Colombia',
            'KM' =>'Comoros',
            'CG' =>'Congo - Brazzaville',
            'CD' =>'Congo - Kinshasa',
            'CK' =>'Cook Islands',
            'CR' =>'Costa Rica',
            'HR' =>'Croatia',
            'CU' =>'Cuba',
            'CY' =>'Cyprus',
            'CZ' =>'Czech Republic',
            'CI' =>'Côte d’Ivoire',
            'DK' =>'Denmark',
            'DJ' =>'Djibouti',
            'DM' =>'Dominica',
            'DO' =>'Dominican Republic',
            'NQ' =>'Dronning Maud Land',
            'DD' =>'East Germany',
            'EC' =>'Ecuador',
            'EG' =>'Egypt',
            'SV' =>'El Salvador',
            'GQ' =>'Equatorial Guinea',
            'ER' =>'Eritrea',
            'EE' =>'Estonia',
            'ET' =>'Ethiopia',
            'FK' =>'Falkland Islands',
            'FO' =>'Faroe Islands',
            'FJ' =>'Fiji',
            'FI' =>'Finland',
            'FR' =>'France',
            'GF' =>'French Guiana',
            'PF' =>'French Polynesia',
            'TF' =>'French Southern Territories',
            'FQ' =>'French Southern and Antarctic Territories',
            'GA' =>'Gabon',
            'GM' =>'Gambia',
            'GE' =>'Georgia',
            'DE' =>'Germany',
            'GH' =>'Ghana',
            'GI' =>'Gibraltar',
            'GR' =>'Greece',
            'GL' =>'Greenland',
            'GD' =>'Grenada',
            'GP' =>'Guadeloupe',
            'GU' =>'Guam',
            'GT' =>'Guatemala',
            'GG' =>'Guernsey',
            'GN' =>'Guinea',
            'GW' =>'Guinea-Bissau',
            'GY' =>'Guyana',
            'HT' =>'Haiti',
            'HM' =>'Heard Island and McDonald Islands',
            'HN' =>'Honduras',
            'HK' =>'Hong Kong SAR China',
            'HU' =>'Hungary',
            'IS' =>'Iceland',
            'IN' =>'India',
            'ID' =>'Indonesia',
            'IR' =>'Iran',
            'IQ' =>'Iraq',
            'IE' =>'Ireland',
            'IM' =>'Isle of Man',
            'IL' =>'Israel',
            'IT' =>'Italy',
            'JM' =>'Jamaica',
            'JP' =>'Japan',
            'JE' =>'Jersey',
            'JT' =>'Johnston Island',
            'JO' =>'Jordan',
            'KZ' =>'Kazakhstan',
            'KE' =>'Kenya',
            'KI' =>'Kiribati',
            'KW' =>'Kuwait',
            'KG' =>'Kyrgyzstan',
            'LA' =>'Laos',
            'LV' =>'Latvia',
            'LB' =>'Lebanon',
            'LS' =>'Lesotho',
            'LR' =>'Liberia',
            'LY' =>'Libya',
            'LI' =>'Liechtenstein',
            'LT' =>'Lithuania',
            'LU' =>'Luxembourg',
            'MO' =>'Macau SAR China',
            'MK' =>'Macedonia',
            'MG' =>'Madagascar',
            'MW' =>'Malawi',
            'MY' =>'Malaysia',
            'MV' =>'Maldives',
            'ML' =>'Mali',
            'MT' =>'Malta',
            'MH' =>'Marshall Islands',
            'MQ' =>'Martinique',
            'MR' =>'Mauritania',
            'MU' =>'Mauritius',
            'YT' =>'Mayotte',
            'FX' =>'Metropolitan France',
            'MX' =>'Mexico',
            'FM' =>'Micronesia',
            'MI' =>'Midway Islands',
            'MD' =>'Moldova',
            'MC' =>'Monaco',
            'MN' =>'Mongolia',
            'ME' =>'Montenegro',
            'MS' =>'Montserrat',
            'MA' =>'Morocco',
            'MZ' =>'Mozambique',
            'MM' =>'Myanmar [Burma]',
            'NA' =>'Namibia',
            'NR' =>'Nauru',
            'NP' =>'Nepal',
            'NL' =>'Netherlands',
            'AN' =>'Netherlands Antilles',
            'NT' =>'Neutral Zone',
            'NC' =>'New Caledonia',
            'NZ' =>'New Zealand',
            'NI' =>'Nicaragua',
            'NE' =>'Niger',
            'NG' =>'Nigeria',
            'NU' =>'Niue',
            'NF' =>'Norfolk Island',
            'KP' =>'North Korea',
            'VD' =>'North Vietnam',
            'MP' =>'Northern Mariana Islands',
            'NO' =>'Norway',
            'OM' =>'Oman',
            'PC' =>'Pacific Islands Trust Territory',
            'PK' =>'Pakistan',
            'PW' =>'Palau',
            'PS' =>'Palestinian Territories',
            'PA' =>'Panama',
            'PZ' =>'Panama Canal Zone',
            'PG' =>'Papua New Guinea',
            'PY' =>'Paraguay',
            'YD' =>'People\'s Democratic Republic of Yemen',
            'PE' =>'Peru',
            'PH' =>'Philippines',
            'PN' =>'Pitcairn Islands',
            'PL' =>'Poland',
            'PT' =>'Portugal',
            'PR' =>'Puerto Rico',
            'QA' =>'Qatar',
            'RO' =>'Romania',
            'RU' =>'Russia',
            'RW' =>'Rwanda',
            'BL' =>'Saint Barthélemy',
            'SH' =>'Saint Helena',
            'KN' =>'Saint Kitts and Nevis',
            'LC' =>'Saint Lucia',
            'MF' =>'Saint Martin',
            'PM' =>'Saint Pierre and Miquelon',
            'VC' =>'Saint Vincent and the Grenadines',
            'WS' =>'Samoa',
            'SM' =>'San Marino',
            'SA' =>'Saudi Arabia',
            'SN' =>'Senegal',
            'RS' =>'Serbia',
            'CS' =>'Serbia and Montenegro',
            'SC' =>'Seychelles',
            'SL' =>'Sierra Leone',
            'SG' =>'Singapore',
            'SK' =>'Slovakia',
            'SI' =>'Slovenia',
            'SB' =>'Solomon Islands',
            'SO' =>'Somalia',
            'ZA' =>'South Africa',
            'GS' =>'South Georgia and the South Sandwich Islands',
            'KR' =>'South Korea',
            'ES' =>'Spain',
            'LK' =>'Sri Lanka',
            'SD' =>'Sudan',
            'SR' =>'Suriname',
            'SJ' =>'Svalbard and Jan Mayen',
            'SZ' =>'Swaziland',
            'SE' =>'Sweden',
            'CH' =>'Switzerland',
            'SY' =>'Syria',
            'ST' =>'São Tomé and Príncipe',
            'TW' =>'Taiwan',
            'TJ' =>'Tajikistan',
            'TZ' =>'Tanzania',
            'TH' =>'Thailand',
            'TL' =>'Timor-Leste',
            'TG' =>'Togo',
            'TK' =>'Tokelau',
            'TO' =>'Tonga',
            'TT' =>'Trinidad and Tobago',
            'TN' =>'Tunisia',
            'TR' =>'Turkey',
            'TM' =>'Turkmenistan',
            'TC' =>'Turks and Caicos Islands',
            'TV' =>'Tuvalu',
            'UM' =>'U.S. Minor Outlying Islands',
            'PU' =>'U.S. Miscellaneous Pacific Islands',
            'VI' =>'U.S. Virgin Islands',
            'UG' =>'Uganda',
            'UA' =>'Ukraine',
            'SU' =>'Union of Soviet Socialist Republics',
            'AE' =>'United Arab Emirates',
            'GB' =>'United Kingdom',
            'US' =>'United States',
            'ZZ' =>'Unknown or Invalid Region',
            'UY' =>'Uruguay',
            'UZ' =>'Uzbekistan',
            'VU' =>'Vanuatu',
            'VA' =>'Vatican City',
            'VE' =>'Venezuela',
            'VN' =>'Vietnam',
            'WK' =>'Wake Island',
            'WF' =>'Wallis and Futuna',
            'EH' =>'Western Sahara',
            'YE' =>'Yemen',
            'ZM' =>'Zambia',
            'ZW' =>'Zimbabwe',
            'AX' =>'Åland Islands',
        );
        
        /**
         * Filter the countries before returning
         *
         * @param array $countries countries array for filtering
         */
        $countries = countryFilters('example_countries_filters', $countries);
    
        /**
         * Return the translated and filtered countries
         */
        return $countries;
    }

    public function countryFilters($countries)
    {
    		// add a translatable country:
    		$countries['XX'] = ('Country Name');
    		// remove a country:
    		unset($countries['YY']);
    		// modify a country:
    		$countries['US'] = ('United States of America');
    		// always return countries!
    		return $countries;
    }
}

//Dummy Data
$countriesDummy=array(
    "Afghanistan",
    "Albania",
    "Algeria",
    "American Samoa",
    "Andorra",
    "Angola",
    "Anguilla",
    "Antarctica",
    "Antigua and Barbuda",
    "Argentina",
    "Armenia",
    "Aruba",
    "Australia",
    "Austria",
    "Azerbaijan",
    "Bahamas",
    "Bahrain",
    "Bangladesh",
    "Barbados",
    "Belarus",
    "Belgium",
    "Belize",
    "Benin",
    "Bermuda",
    "Bhutan",
    "Bolivia",
    "Bosnia and Herzegowina",
    "Botswana",
    "Bouvet Island",
    "Brazil",
    "British Indian Ocean Territory",
    "Brunei Darussalam",
    "Bulgaria",
    "Burkina Faso",
    "Burundi",
    "Cambodia",
    "Cameroon",
    "Canada",
    "Cape Verde",
    "Cayman Islands",
    "Central African Republic",
    "Chad",
    "Chile",
    "China",
    "Christmas Island",
    "Cocos (Keeling) Islands",
    "Colombia",
    "Comoros",
    "Congo",
    "Congo, the Democratic Republic of the",
    "Cook Islands",
    "Costa Rica",
    "Cote d'Ivoire",
    "Croatia (Hrvatska)",
    "Cuba",
    "Cyprus",
    "Czech Republic",
    "Denmark",
    "Djibouti",
    "Dominica",
    "Dominican Republic",
    "East Timor",
    "Ecuador",
    "Egypt",
    "El Salvador",
    "Equatorial Guinea",
    "Eritrea",
    "Estonia",
    "Ethiopia",
    "Falkland Islands (Malvinas)",
    "Faroe Islands",
    "Fiji",
    "Finland",
    "France",
    "France Metropolitan",
    "French Guiana",
    "French Polynesia",
    "French Southern Territories",
    "Gabon",
    "Gambia",
    "Georgia",
    "Germany",
    "Ghana",
    "Gibraltar",
    "Greece",
    "Greenland",
    "Grenada",
    "Guadeloupe",
    "Guam",
    "Guatemala",
    "Guinea",
    "Guinea-Bissau",
    "Guyana",
    "Haiti",
    "Heard and Mc Donald Islands",
    "Holy See (Vatican City State)",
    "Honduras",
    "Hong Kong",
    "Hungary",
    "Iceland",
    "India",
    "Indonesia",
    "Iran (Islamic Republic of)",
    "Iraq",
    "Ireland",
    "Israel",
    "Italy",
    "Jamaica",
    "Japan",
    "Jordan",
    "Kazakhstan",
    "Kenya",
    "Kiribati",
    "Korea, Democratic People's Republic of",
    "Korea, Republic of",
    "Kuwait",
    "Kyrgyzstan",
    "Lao, People's Democratic Republic",
    "Latvia",
    "Lebanon",
    "Lesotho",
    "Liberia",
    "Libyan Arab Jamahiriya",
    "Liechtenstein",
    "Lithuania",
    "Luxembourg",
    "Macau",
    "Macedonia, The Former Yugoslav Republic of",
    "Madagascar",
    "Malawi",
    "Malaysia",
    "Maldives",
    "Mali",
    "Malta",
    "Marshall Islands",
    "Martinique",
    "Mauritania",
    "Mauritius",
    "Mayotte",
    "Mexico",
    "Micronesia, Federated States of",
    "Moldova, Republic of",
    "Monaco",
    "Mongolia",
    "Montserrat",
    "Morocco",
    "Mozambique",
    "Myanmar",
    "Namibia",
    "Nauru",
    "Nepal",
    "Netherlands",
    "Netherlands Antilles",
    "New Caledonia",
    "New Zealand",
    "Nicaragua",
    "Niger",
    "Nigeria",
    "Niue",
    "Norfolk Island",
    "Northern Mariana Islands",
    "Norway",
    "Oman",
    "Pakistan",
    "Palau",
    "Panama",
    "Papua New Guinea",
    "Paraguay",
    "Peru",
    "Philippines",
    "Pitcairn",
    "Poland",
    "Portugal",
    "Puerto Rico",
    "Qatar",
    "Reunion",
    "Romania",
    "Russian Federation",
    "Rwanda",
    "Saint Kitts and Nevis",
    "Saint Lucia",
    "Saint Vincent and the Grenadines",
    "Samoa",
    "San Marino",
    "Sao Tome and Principe",
    "Saudi Arabia",
    "Senegal",
    "Seychelles",
    "Sierra Leone",
    "Singapore",
    "Slovakia (Slovak Republic)",
    "Slovenia",
    "Solomon Islands",
    "Somalia",
    "South Africa",
    "South Georgia and the South Sandwich Islands",
    "Spain",
    "Sri Lanka",
    "St. Helena",
    "St. Pierre and Miquelon",
    "Sudan",
    "Suriname",
    "Svalbard and Jan Mayen Islands",
    "Swaziland",
    "Sweden",
    "Switzerland",
    "Syrian Arab Republic",
    "Taiwan, Province of China",
    "Tajikistan",
    "Tanzania, United Republic of",
    "Thailand",
    "Togo",
    "Tokelau",
    "Tonga",
    "Trinidad and Tobago",
    "Tunisia",
    "Turkey",
    "Turkmenistan",
    "Turks and Caicos Islands",
    "Tuvalu",
    "Uganda",
    "Ukraine",
    "United Arab Emirates",
    "United Kingdom",
    "United States",
    "United States Minor Outlying Islands",
    "Uruguay",
    "Uzbekistan",
    "Vanuatu",
    "Venezuela",
    "Vietnam",
    "Virgin Islands (British)",
    "Virgin Islands (U.S.)",
    "Wallis and Futuna Islands",
    "Western Sahara",
    "Yemen",
    "Yugoslavia",
    "Zambia",
    "Zimbabwe"
);

// Usage Example
$userDatabase = new UserDatabase();
$articleManager = new ArticleManager();
$articleManager = new ArticleManager();
$categoryManager = new CategoryManager();

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

$data = [
    'title' => 'First Article',
    'content' => 'This is the content of the first article.',
    'headerImage' => 'header1.jpg',
    'contentImage' => 'content1.jpg',
    'createDate' => '2023-05-25',
    'author' => 'Otong'
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
    echo "Title List: " . $article->getTitle() . "\n";
    echo "Content List: " . $article->getContent() . "\n";
    // Print other article details
}

// Print article detail
echo "Title Detail: " . $article2->getTitle() . "\n";
echo "Content Detail: " . $article2->getContent() . "\n";
// Print other article details

// Create new category
$category1 = $categoryManager->createCategory('Technology');

// Edit existing category
$categoryManager->editCategory($category1->getId(), 'Science and Technology');

// Delete existing category
$categoryManager->deleteCategory($category1->getId());

// Get category list
$categories = $categoryManager->getCategoryList();

// Get category detail
$category2 = $categoryManager->getCategoryDetail($category1->getId());

// Print category list
foreach ($categories as $category) {
    echo "Category ID: " . $category->getId() . "\n";
    echo "Category Name: " . $category->getName() . "\n";
    // Print other category details
}

// Print category detail
echo "Category ID: " . $category2->getId() . "\n";
echo "Category Name: " . $category2->getName() . "\n";
// Print other category details


$data1 = [
    'title' => 'First Article',
    'content' => 'This is the content of the first article.',
    'createDate' => '2023-05-25',
    'author' => 'John Doe'
];

$data2 = [
    'title' => 'Second Article',
    'content' => 'This is the content of the second article.',
    'createDate' => '2023-05-26',
    'author' => 'Jane Smith'
];

$data3 = [
    'title' => 'Third Article',
    'content' => 'This is the content of the third article.',
    'createDate' => '2023-05-24',
    'author' => 'John Doe'
];

// Create new articles
$article1 = $articleManager->createArticle($data1);
$article2 = $articleManager->createArticle($data2);
$article3 = $articleManager->createArticle($data3);

// Search articles by keyword
$searchResults = $articleManager->searchArticles('second');
foreach ($searchResults as $article) {
    echo "Search by Title: " . $article->getTitle() . "\n";
    echo "Search by Content: " . $article->getContent() . "\n";
}

// Filter articles by category
$filteredResults = $articleManager->filterArticlesByCategory($categoryId);
foreach ($filteredResults as $article) {
    echo "Filter by Title: " . $article->getTitle() . "\n";
    echo "Filter by Content: " . $article->getContent() . "\n";
}

// Sort articles by date in ascending order
$articleManager->sortArticlesByDate();
$sortedResultsAsc = $articleManager->getArticleList();
foreach ($sortedResultsAsc as $article) {
    echo "Sort by Title Ascending: " . $article->getTitle() . "\n";
    echo "Sorted Ascending Content: " . $article->getContent() . "\n";
}

// Sort articles by date in descending order
$articleManager->sortArticlesByDate('desc');
$sortedResultsDesc = $articleManager->getArticleList();
foreach ($sortedResultsDesc as $article) {
    echo "Sort by Title Descending: " . $article->getTitle() . "\n";
    echo "Sorted Content Descending: " . $article->getContent() . "\n";
}

// Initialize the comment manager
$commentManager = new CommentManager();

// Create new comment
$comment = $commentManager->createComment($articleId, $content, $author);

// Edit existing comment
$commentManager->editComment($comment->getId(), $newContent);

// Delete existing comment
$commentManager->deleteComment($comment->getId());

// Get comment list
$comments = $commentManager->getCommentList();

// Get comment detail
$comment = $commentManager->getCommentDetail($commentId);

// Print comment list
foreach ($comments as $comment) {
    echo "Comment ID List: " . $comment->getId() . "\n";
    echo "Content List: " . $comment->getContent() . "\n";
    // Print other comment details
}

// Print comment detail
echo "Comment ID Detail: " . $comment->getId() . "\n";
echo "Content Detail: " . $comment->getContent() . "\n";

// Rate an article
$app->post('/articles/{id}/rate', function ($request, $response, $args) {
    $articleId = $args['id'];
    $rating = $request->getParam('rating');

    // Check if the article exists
    // Fetch the article from the database or any other storage mechanism
    $article = $this->getArticleById($articleId);

    if ($article) {
        $ratingObj = new Rating($articleId, $rating);
        $article->addRating($ratingObj);

        // Save the updated article to the database or storage mechanism

        // Return a success response
        return $response->withJson(['message' => 'Article rated successfully']);
    } else {
        // Return an error response if the article doesn't exist
        return $response->withStatus(404)->withJson(['error' => $this::ARTICLE_NOT_FOUND]);
    }
});

// Create a review for an article
$app->post('/articles/{id}/reviews', function ($request, $response, $args) {
    $articleId = $args['id'];
    $review = $request->getParam('review');

    // Check if the article exists
    // Fetch the article from the database or any other storage mechanism
    $article = $this->getArticleById($articleId);

    if ($article) {
        $reviewObj = new Review($articleId, $review);
        $article->addReview($reviewObj);

        // Save the updated article to the database or storage mechanism

        // Return a success response
        return $response->withJson(['message' => 'Review created successfully']);
    } else {
        // Return an error response if the article doesn't exist
        return $response->withStatus(404)->withJson(['error' => $this::ARTICLE_NOT_FOUND]);
    }
});

// Show the average rating and reviews list for an article
$app->get('/articles/{id}', function ($request, $response) {
    $articleId = $request['id'];

    // Check if the article exists
    // Fetch the article from the database or any other storage mechanism
    $article = $this->getArticleById($articleId);

    if ($article) {
        $averageRating = $article->getAverageRating();
        $reviews = $article->getReviews();

        // Return the average rating and reviews as a JSON response
        return $response->withJson([
            'average_rating' => $averageRating,
            'reviews' => $reviews
        ]);
    } else {
        // Return an error response if the article doesn't exist
        return $response->withStatus(404)->withJson(['error' => $this::ARTICLE_NOT_FOUND]);
    }
});

// Get user feeds
$user1Feed = $userFeed->getUserFeed(1);
$user2Feed = $userFeed->getUserFeed(2);

// Print user feeds
echo "User 1 Feed:\n";
foreach ($user1Feed as $article) {
    echo "{$article['title']} - {$article['category']} - {$article['author']} - {$article['country']}\n";
}

echo "\nUser 2 Feed:\n";
foreach ($user2Feed as $article) {
    echo "{$article['title']} - {$article['category']} - {$article['author']} - {$article['country']}\n";
}

// Example usage of notifyUsers function
$articleToNotify = [
    'title' => 'Breaking News',
    'category' => 'Politics',
    'author' => 'John Smith',
    'country' => $countriesDummy['13']
];

$userFeed->notifyUsers($articleToNotify);
